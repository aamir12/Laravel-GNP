<?php

namespace App\Models;

use App\Classes\KpiRepository;
use App\Models\Traits\HasDates;
use App\Models\Traits\HasMetadata;
use App\Models\Traits\RecordsUserstamps;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Competition extends Model implements Auditable
{
    use AuditableTrait;
    use HasDates;
    use HasFactory;
    use HasMetadata;
    use RecordsUserstamps;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'image',
        'type',
        'score_threshold',
        'threshold_operator',
        'period',
        'start_date',
        'end_date',
        'is_lottery',
        'space_count',
        'entry_fee',
        'terms_url',
        'competition',
        'auto_enter_user',
        'image_type',
        'status',
    ];

    protected $hidden = ['pivot'];

    protected $casts = [
        'start_date'  => 'datetime:Y-m-d H:i',
        'end_date' => 'datetime:Y-m-d H:i',
    ];

    public function calcEndDate()
    {
        return $this->period  === 'daily'  ? $this->start_date->addDay()  :
               ($this->period === 'weekly' ? $this->start_date->addWeek() :
                                             $this->start_date->addMonth());
    }

    public function start()
    {
        if ($this->auto_enter_user) {
            $this->enterEligibleUsers();
        }

        $this->state = 'started';
        $this->save();
    }

    public function end()
    {
        $this->pickWinners();

        if ($this->type == 'Rolling') {
            $this->rollOver();
        }

        $this->status = 'archived';
        $this->state = 'ended';
        $this->save();
    }

    public function rollOver()
    {
        $newCompetition = $this->replicate();
        $startDate = Carbon::parse($newCompetition->start_date);
        $endDate = Carbon::parse($newCompetition->end_date);
        $diff = $startDate->diffInMinutes($endDate);
        $newCompetition->start_date = $endDate;
        $newCompetition->end_date = $endDate->copy()->addMinutes($diff);
        $newCompetition->state = 'pending';
        $newCompetition->save();

        foreach ($this->prizes as $prize) {
            $newPrize = $prize->replicate();
            $newPrize->competition_id = $newCompetition->id;
            $newPrize->save();
        }

        $newCompetition->groups()->attach($this->groups->pluck('id'));
    }

    public function getEligibleWinnerIds()
    {
        $eligibleWinnerIds = collect();
        // Get users whose scores reach the comeptition's threshold.
        foreach ($this->entrants as $user) {
            $hasKpiData = $user->scores()
                ->whereBetween('timestamp', [$this->start_date, $this->end_date])
                ->exists();

            $score = KpiRepository::getScoreForUser(
                [
                    'start' => Carbon::parse($this->start_date),
                    'end' => Carbon::parse($this->end_date)
                ],
                $user->id
            );

            if ($hasKpiData && $this->isScoreWithinThreshold($score['score_value'])) {
                $eligibleWinnerIds->push($user->id);
            }
        }
        return $eligibleWinnerIds;
    }

    public function isScoreWithinThreshold($scoreValue)
    {
        switch ($this->threshold_operator) {
            case '==':
                return $scoreValue == $this->score_threshold;
            case '>=':
                return $scoreValue >= $this->score_threshold;
            case '<=':
                return $scoreValue <= $this->score_threshold;
            case '>':
                return $scoreValue > $this->score_threshold;
            case '<':
                return $scoreValue < $this->score_threshold;
            default:
                return $scoreValue >= $this->score_threshold;
        }
    }

    public function pickWinners()
    {
        $eligibleWinnerIds = $this->getEligibleWinnerIds();

        foreach ($this->prizes as $prize) {
            // Choose random winners and remove from eligible winners
            $winnerCount = min($prize->max_winners, $eligibleWinnerIds->count());
            $winnerIds = $eligibleWinnerIds->random($winnerCount);
            $eligibleWinnerIds = $eligibleWinnerIds->diff($winnerIds);

            foreach ($winnerIds as $userId) {
                $deliverable = Deliverable::create(['is_shipped' => 0]);
                Winner::create([
                    'user_id' => $userId,
                    'prize_id' => $prize->id,
                    'deliverable_id' => $deliverable->id
                ]);
            }
        }
    }

    /**
     * Enters eligible users into this competition.
     *
     * Eligible users are those who are allowed to enter the competition. If the
     * competition has groups associated with it, then only the users contained
     * within those groups are eligible to join. Otherwise all active users should be
     * entered into the competition.
     *
     * @return void
     */
    public function enterEligibleUsers()
    {
        $users = $this->getUsersEligibleToEnter();
        $this->entrants()->attach($users->pluck('id'));
    }

    public function getUsersEligibleToEnter()
    {
        if ($this->groups->isNotEmpty()) {
            $users = collect();
            foreach ($this->groups as $group) {
                $users = $users->merge($group->getAllActivatedUsersInSubgroups());
            }
            return $users->unique();
        }
        return User::nonAdmins()->active()->get();
    }

    public function isUserEligibleToEnter(User $user) {
        return $this->getUsersEligibleToEnter()->pluck('id')->contains($user->id);
    }

    /*** Relations ***/
    public function prizes()
    {
        return $this->hasMany(Prize::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'competition_group')->withTimestamps();
    }

    public function entrants()
    {
        return $this->belongsToMany(User::class, 'competition_participants')->withPivot('competition_revealed');
    }
}
