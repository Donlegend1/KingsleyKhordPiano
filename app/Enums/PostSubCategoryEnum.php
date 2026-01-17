<?php

namespace App\Enums;

enum PostSubCategoryEnum: string
{
    case SAYHELLO = 'say_hello';
    case ASKQUESTION = 'ask_question';
    case POSTPROGRESS = 'post_progress';
    case BEGINNER = 'beginner';
    case INTERMEDIATE = 'intermediate';
    case ADVANCE = 'advance';
    case LESSONS = 'lessons';
    case PROGRESSREPORTS = 'progress_report';
    case ACTIVITYEFEED = 'activity_feed';
}