<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SurveyDistribution;
use App\Models\User;
use App\Models\SurveyResponse;
use App\Mail\SurveyDistributionMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendScheduledSurveys extends Command
{
    protected $signature = 'surveys:send-scheduled';
    protected $description = 'Send one email per user containing all active unresponded surveys';

    public function handle()
    {
        $today = Carbon::today();

        // Step 1: Get all active distributions for today or earlier
        $distributions = SurveyDistribution::whereDate('scheduled_active_date', '<=', $today)
                                            ->where('is_active', true)
                                            ->with('survey')
                                            ->get();

        if ($distributions->isEmpty()) {
            $this->info('No distributions due today.');
            return 0;
        }

        // Step 2: Group surveys by user
        $userSurveyMap = [];

        foreach ($distributions as $distribution) {
            $query = User::where('role', $distribution->target_role);

            if ($distribution->date_field === 'enroll_date') {
                $query->whereBetween('enroll_date', [$distribution->start_date, $distribution->end_date]);
            } else {
                $query->whereBetween('expected_graduate_date', [$distribution->start_date, $distribution->end_date]);
            }

            $users = $query->get();

            foreach ($users as $user) {
                // Check if this user already responded to this survey
                $alreadyResponded = SurveyResponse::where('survey_id', $distribution->survey_id)
                    ->where('user_id', $user->id)
                    ->exists();

                if (!$alreadyResponded) {
                    $userSurveyMap[$user->email][] = url('/login' . $distribution->survey_id);
                }
            }
        }

        // Step 3: Send one email per user
        foreach ($userSurveyMap as $email => $surveyLinks) {
            if (count($surveyLinks) > 0) {
                Mail::to($email)->send(new SurveyDistributionMail($surveyLinks));
                Log::info("Sent combined survey email to {$email} for " . count($surveyLinks) . " surveys.");
                $this->info("Sent combined survey email to {$email}");
            }
        }

        $this->info('✅ Survey emails sent successfully.');
    }
}
