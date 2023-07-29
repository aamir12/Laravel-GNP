<?php

namespace App\Models;

use App\Mail\AccountChanges;
use App\Mail\CompetitionEnded;
use App\Mail\CompetitionStarted;
use App\Mail\InvitedToEarnie;
use App\Mail\InvitedToEarnieReminder;
use App\Mail\InvitedToLeague;
use App\Mail\PasswordReset;
use App\Mail\VerifyEmail;
use App\Mail\Welcome;
use App\Models\Traits\HasDates;
use App\Models\Traits\HasMetadata;
use App\Models\Traits\RecordsUserstamps;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class EmailConfig extends Model implements Auditable
{
    use AuditableTrait;
    use HasDates;
    use HasMetadata;
    use RecordsUserstamps;

    protected $fillable = ['email_type', 'is_enabled', 'subject', 'body', 'resend_interval'];

    public static function findByType($emailType)
    {
        return EmailConfig::firstWhere('email_type', $emailType);
    }

    public function makeMailable($user)
    {
        switch ($this->email_type) {
            case 'email_verification':
                return new VerifyEmail($user);
                break;
            case 'invitation':
                return new InvitedToEarnie($user);
                break;
            case 'invitation_reminder':
                return new InvitedToEarnieReminder($user);
                break;
            case 'account_changes':
                return new AccountChanges($user);
                break;
            case 'password_reset':
                return new PasswordReset($user, null);
                break;
            case 'competition_started':
                return new CompetitionStarted($user, null);
                break;
            case 'competition_ended':
                return new CompetitionEnded($user, null);
                break;
            case 'competition_win':
                break;
            case 'competition_loss':
                break;
            case 'league_invite':
                return new InvitedToLeague($user, Auth::user());
                break;
            case 'welcome':
                return new Welcome($user);
                break;
        }
    }

    public function getEmailAsHtml($user)
    {
        $mailable = $this->makeMailable($user);

        if (isset($mailable)) {
            return str_replace(["\r", "\n"], '', $mailable->getHtml());
        }
    }
}
