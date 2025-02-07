<?php

namespace App\Traits;

trait ConstantsTrait
{
    public const MESSAGES = [
        'validation_error' => 'Validation Error',
        'register' => 'registered successfully.',
        'login' => 'logged in successfully.',
        'logout' => 'logged out successfully.',
        'system_error' => 'Something went wrong. Please try again later.',
    ];
}
