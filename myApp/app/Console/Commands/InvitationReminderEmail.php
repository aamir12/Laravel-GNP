<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Classes\EmailConfigManager;
use App\Mail\InvitedToEarnieReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class InvitationReminderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invitation-reminder-email:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It will send reminder emails to those users who didn\'t respond of invitation email';

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
     * @return mixed
     */
    public function handle()
    {
        if (EmailConfigManager::isEmailEnabled('invitation_reminder')) {
            foreach (User::inactive()->get() as $user) {
                Mail::to($user)->send(new InvitedToEarnieReminder($user));
            }
        }
    }
}
