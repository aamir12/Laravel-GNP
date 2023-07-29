<?php

namespace App\Classes;

use App\Models\EmailConfig;
use Auth;

class EmailConfigManager
{
    public static function updateEmailConfig($data)
    {
        $emailConfig = isset($data['id'])
            ? EmailConfig::find($data['id'])
            : EmailConfig::findByType($data['email_type']);

        $emailConfig->fill($data);
        $emailConfig->save();
        return $emailConfig;
    }

    public static function isEmailEnabled($emailType)
    {
        $email = EmailConfig::findByType($emailType);
        return isset($email->id) && $email->is_enabled == 1;
    }

    public static function getEmailConfig($req)
    {
        $emailConfig = $req->id
            ? EmailConfig::find($req->id)
            : EmailConfig::findByType($req->email_type);

        $emailConfig['html'] = $emailConfig->getEmailAsHtml(Auth::user());
        return $emailConfig;
    }
}
