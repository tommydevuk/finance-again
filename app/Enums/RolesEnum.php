<?php

namespace App\Enums;

enum RolesEnum: string
{
    case SUPER_ADMIN = 'Super Admin';
    case ADMIN = 'Admin';
    case USER = 'User';
}
