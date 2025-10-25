<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\SkillAssessmentEmail;
use Illuminate\Console\Command;
use App\Enums\Roles\UserRoles;

class SendSkillAssessmentEmail extends Command
{
    protected $signature = 'email:send-skill-assessment';
    protected $description = 'Send skill assessment emails to users with latest course info';

    public function handle(): int
    {
        $users = User::where('role', UserRoles::MEMBER->value)->get();
        
        $latestCourse =Upload::latest()->take(1)->first();

        foreach ($users as $user) {

            if (!$latestCourse) continue;

            $assessmentLink = "member/lesson/".$latestCourse->id;

            $user->notify(new SkillAssessmentEmail($assessmentLink));
        }

        $this->info("Skill assessment emails sent.");

        return Command::SUCCESS;
    }
}
