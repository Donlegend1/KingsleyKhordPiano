<?php

namespace App\Enums;

enum PostCategoryEnum: string
{
    case GETSTARTED = 'get_started';
    case OTHERS = 'others';
    case FORUM = 'forum';
    case PROGRESSREPORTS = 'progress_report';
    case LESSONS = 'lessons';
    case EXCLUSIVEFEED = 'exclusive_feed';
    case GENERAL = 'general';
}