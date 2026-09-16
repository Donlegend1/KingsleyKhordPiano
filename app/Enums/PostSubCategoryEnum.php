<?php

namespace App\Enums;

enum PostSubCategoryEnum: string
{
    case SAYHELLO = 'say_hello';
    case ASKQUESTION = 'ask_question';
    case POSTPROGRESS = 'post_progress';
    case LESSONS = 'lessons';
    case PROGRESSREPORTS = 'progress_report';
    case ACTIVITYEFEED = 'activity_feed';
    case EXCLUSIVEFEED = 'exclusive_feed';
    case TIPSANDTRICKS = 'tips_and_tricks';
    case ANNOUNCEMENT = 'announcement';
    case SUGGESTIONS = 'suggestions';
    case BEGINNERGUIDEDPRACTICE = 'beginner_guided_practice';
    case INTERMEDIATEGUIDEDPRACTICE = 'intermediate_guided_practice';
    case ADVANCEDGUIDEDPRACTICE = 'advanced_guided_practice';
    case STUDENTCHALLENGES = 'student_challenges';
    case COMMUNITYSHOWCASE = 'community_showcase';
    case PUBLICPLEDGES = 'public_pledges';
    case WORKSPACESHOWCASE = 'workspace_showcase';
}