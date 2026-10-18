<?php

namespace App\Exceptions\Sms;

class InsufficientCentralCreditsException extends SmsSendingException
{
    public function __construct()
    {
        parent::__construct('The central SMS balance is exhausted. SMS sending is temporarily unavailable until more credits are purchased from the provider.');
    }
}
