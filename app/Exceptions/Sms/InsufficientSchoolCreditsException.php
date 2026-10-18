<?php

namespace App\Exceptions\Sms;

class InsufficientSchoolCreditsException extends SmsSendingException
{
    public function __construct()
    {
        parent::__construct('Your school does not have enough SMS credits to send this message. Please purchase more credits.');
    }
}
