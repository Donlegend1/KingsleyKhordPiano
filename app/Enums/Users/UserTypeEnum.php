<?php

namespace App\Enums\Users;

enum UserTypeEnum: string
{
    case PREMIUM = 'premium';
    case STANDARD = 'standard';
    case VISITOR = 'visitor';
}