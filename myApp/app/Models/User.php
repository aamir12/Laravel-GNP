<?php

namespace App\Models;

use App\Events\UserCreated;
use App\Events\UserSaving;
use App\Models\Traits\HasDates;
use App\Models\Traits\HasMetadata;
use App\Models\Traits\RecordsUserstamps;
use Carbon\Carbon;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use TaylorNetwork\UsernameGenerator\FindSimilarUsernames;

class User extends Authenticatable implements Auditable
{
    use AuditableTrait;
    use HasApiTokens;
    use HasDates;
    use HasFactory;
    use HasMetadata;
    use Notifiable;
    use RecordsUserstamps;
    use FindSimilarUsernames;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'username',
        'first_name',
        'last_name',
        'timezone',
        'phone',
        'dob',
        'activation_code',
        'external_id',
        'is_activated',
        'paypal_email',
        'created_by',
        'updated_by'
    ];

    protected $with = ['roles:id,name', 'groups:id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email_verified_at', 'pivot'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => UserCreated::class,
        'saving' => UserSaving::class,
    ];

    /**
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \App\Models\User
     */
    public function findForPassport($username)
    {
        return $this->where('username', $username)
                    ->orWhere('email', $username)
                    ->first();
    }

    /**
     * Set the user's dob.
     *
     * @param  string  $value
     * @return void
     */
    public function setDobAttribute($value)
    {
        $this->attributes['dob'] = Carbon::parse($value)->format('Y-m-d');
    }

    public static function findBy($id = null, $email = null, $external_id = null)
    {
        if (isset($id)) {
            return User::find($id);
        }
        if (isset($email)) {
            return User::firstWhere('email', $email);
        }
        if (isset($external_id)) {
            return User::firstWhere('external_id', $external_id);
        }
    }

    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasAnyRole($roles)
    {
        $roles = is_array($roles) ? $roles : func_get_args();

        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function isPasswordExpired()
    {
        if (config('auth.password_expiry') === false) {
            return false;
        }

        $password_updated_at = $this->passwordSecurity->password_updated_at;
        $password_expiry_days = $this->passwordSecurity->password_expiry_days;
        $password_expiry_at = Carbon::parse($password_updated_at)->addDays($password_expiry_days);

        if ($password_expiry_at->lessThan(now())) {
            return true;
        }
        return false;
    }

    /**
     * Retrieves the lottery which is closest to finishing and which
     * the user is either entered into or eligible to enter.
     *
     * @return Competition
     */
    public function nextEndingLottery()
    {
        return Competition::where('is_lottery', true)
            ->where('state', 'started')
            ->where(function ($query) {
                $query->whereHas('entrants', function ($q) {
                    $q->where('user_id', $this->id);
                })
                ->orWhereDoesntHave('groups')
                ->orWhereHas('groups', function ($q) {
                    $q->whereIn('group_id', $this->groups()->pluck('group_id'));
                });
            })
            ->orderBy('end_date', 'asc')
            ->first();
    }

    /**
     * Retrieves all lotteries that haven't started yet and that the user will be
     * eligible to enter.
     *
     * @return \Illuminate\Support\Collection
     */
    public function upcomingLotteries()
    {
        return Competition::where('is_lottery', true)
            ->where('state', 'pending')
            ->where('status', 'live')
            ->where(function ($query) {
                $query->doesntHave('groups')->orWhereHas('groups', function ($q) {
                    $q->whereIn('group_id', $this->groups()->pluck('group_id'));
                });
            })
            ->with('prizes.stock')
            ->orderBy('start_date')
            ->get();
    }

    /**
     * Retrieves all lotteries that are currently running and that the user is
     * eligible to enter.
     *
     * @return \Illuminate\Support\Collection
     */
    public function openLotteries()
    {
        return Competition::where('is_lottery', true)
            ->where('state', 'started')
            ->whereDoesntHave('entrants', function ($query) {
                $query->where('user_id', $this->id);
            })
            ->where(function ($query) {
                $query->doesntHave('groups')->orWhereHas('groups', function ($q) {
                    $q->whereIn('group_id', $this->groups()->pluck('group_id'));
                });
            })
            ->orderBy('end_date')
            ->get();
    }

    /**
     * Retrieves all lotteries that are currently running and that the user is
     * entered in.
     *
     * @return \Illuminate\Support\Collection
     */
    public function runningLotteries()
    {
        return $this->competitions()
            ->where('is_lottery', true)
            ->where('state', 'started')
            ->orderBy('end_date')
            ->get();
    }

    /**
     * Retrieves all lotteries that have ended and that the user was entered in.
     *
     * @return \Illuminate\Support\Collection
     */
    public function closedLotteries()
    {
        return $this->competitions()
            ->where('is_lottery', true)
            ->where('state', 'ended')
            ->orderBy('end_date', 'asc')
            ->get();
    }

    /**
     * Retrieves lotteries which have ended but the result has not yet been revealed.
     *
     * @return \Illuminate\Support\Collection
     */
    public function unrevealedLotteries()
    {
        return $this->competitions()
            ->where('is_lottery', true)
            ->where('state', 'ended')
            ->wherePivot('competition_revealed', false)
            ->orderBy('end_date', 'asc')
            ->get();
    }

    /**
     * Marks the specified competition as having been revealed by this user.
     *
     * @return \App\Models\Winner
     */
    public function revealCompetition(Competition $competition)
    {
        $competition->entrants()->updateExistingPivot(
            $this->id,
            ['competition_revealed' => true]
        );

        return Winner::with('prize.stock')
            ->whereIn('prize_id', $competition->prizes->pluck('id'))
            ->where('user_id', $this->id)
            ->first();
    }

    public function enterCompetition(Competition $competition)
    {
        $competition->entrants()->attach($this->id);
    }

    public function balance()
    {
        return $this->transactions()->sum('amount');
    }

    /**
     * Scope a query to only include active users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_activated', 1);
    }

    /**
     * Scope a query to only include inactive users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_activated', 0);
    }

    /**
     * Scope a query to only include admin users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmins($query)
    {
        return $query->whereRelation('roles', 'name', 'admin');
    }

    /**
     * Scope a query to only include non-admin users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNonAdmins($query)
    {
        return $query->whereRelation('roles', 'name', 'user');
    }

    /**
     * Scope a query to filter by given parameters.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, $params)
    {
        $fields = ['email', 'external_id', 'first_name', 'last_name', 'username'];

        foreach ($fields as $field) {
            if (isset($params[$field])) {
                $query->where($field, $params[$field]);
            }
        }

        if (isset($params['phone'])) {
            $query->where('phone', 'LIKE', '%' . $params['phone'] . '%');
        }

        if (isset($params['dob'])) {
            $query->where('dob', Carbon::parse($params['dob'])->format('Y-m-d'));
        }

        return $query;
    }

    /**
     * Scope a query to only include users with the given role.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithRole($query, $role)
    {
        return $query->whereRelation('roles', 'name', $role);
    }

    /**
     * Scope a query to only include users in the given group.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInGroup($query, $group)
    {
        return $query->whereRelation('groups', 'groups.id', $group);
    }

    /*** Relations ***/
    public function passwordSecurity()
    {
        return $this->hasOne(PasswordSecurity::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    public function verifyUser()
    {
        return $this->hasOne(VerifyUser::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_user')->withTimestamps();
    }

    public function competitions()
    {
        return $this->belongsToMany(Competition::class, 'competition_participants')
            ->withPivot('competition_revealed')
            ->withTimestamps();
    }

    public function achievementWinners()
    {
        return $this->hasMany(AchievementWinner::class);
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }
    /*** Relations ***/
}
