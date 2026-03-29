<?php

namespace App\Enums\Notification;

enum NotificationSectionEnum: string
{
    case MEMBER_AREA = 'member_area';
    case COMMUNITY = 'community';
    case GENERAL = 'general';
}