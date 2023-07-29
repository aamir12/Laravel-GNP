<?php

namespace App\Classes;

use App\Models\VerifyUser;

class VerifyUserManager
{
    public static function verifyToken($token)
    {
        $verifyUser = VerifyUser::getDataForToken($token);
        if( isset($verifyUser) ){
            $user = $verifyUser->user;
            //dd($verifyUser->verified);
            if(!$user->verified) {
                $verifyUser->verified = 1;
                $verifyUser->save();
                $status = __('verify_email')['success'];
            }else{
                $status = __('verify_email')['already_exist'];
            }
        }else{
            $status = __('verify_email')['link_expire'];
        }
        return $status;
    }
}





?>