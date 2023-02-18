<?php

namespace App\Providers;


use Illuminate\Auth\EloquentUserProvider as UserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;


class CustomUserProvider extends UserProvider
{

    public function validateCredentials(UserContract $user, array $credentials)
    {
        //if username is phone . check for sms verification if fail. check for password

        if (isset($credentials['phone'])) {
            $sms = new \SMS();
            if ($sms->verifyActivationSMS($credentials['phone'], $credentials['password'])) {
                $sms->deleteActivationSMS($credentials['phone']);
                return true;
            }
        }
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }

}