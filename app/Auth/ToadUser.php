<?php

namespace App\Auth;

use Illuminate\Auth\GenericUser;

class ToadUser extends GenericUser
{
    public function getRememberToken(): string
    {
        return '';
    }

    public function setRememberToken($value): void {}

    public function getRememberTokenName(): string
    {
        return '';
    }
}