<?php

namespace App\Console\Commands;

use App\Models\AchievementWinner;
use App\Models\CompetitionParticipant;
use App\Models\GroupUser;
use App\Models\LeagueEntrant;
use App\Models\RoleUser;
use App\Models\Score;
use App\Models\ScoreArchive;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\VerifyUser;
use App\Models\Winner;
use Illuminate\Console\Command;
use Mahfuz\LoginActivity\Models\LoginActivity;

class DeleteUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:delete {ids* : A list of user ids}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes users matching the provided ids. WARNING: This command is destructive and could result in loss of database integrity.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ids = $this->argument('ids');

        AchievementWinner::whereIn('user_id', $ids)->delete();
        CompetitionParticipant::whereIn('user_id', $ids)->delete();
        GroupUser::whereIn('user_id', $ids)->delete();
        LeagueEntrant::whereIn('user_id', $ids)->delete();
        LoginActivity::whereIn('user_id', $ids)->delete();
        RoleUser::whereIn('user_id', $ids)->delete();
        Score::whereIn('user_id', $ids)->delete();
        ScoreArchive::whereIn('user_id', $ids)->delete();
        Transaction::whereIn('user_id', $ids)->delete();
        UserAddress::whereIn('user_id', $ids)->delete();
        VerifyUser::whereIn('user_id', $ids)->delete();
        Winner::whereIn('user_id', $ids)->delete();

        $numDeleted = User::whereIn('id', $ids)->delete();

        $this->info($numDeleted . ' users were deleted.');
    }
}
