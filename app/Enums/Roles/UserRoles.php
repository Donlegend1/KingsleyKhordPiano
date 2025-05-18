<?php

namespace App\Enums\Roles;

enum UserRoles: string
{
    case ADMIN = 'admin';
    case MEMBER = 'member';
    case SUPERADMIN = 'superadmin';
}