<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SurveyDistribution;
use App\Models\User;
use App\Models\SurveyResponse;
use Illuminate\Support\Facades\Log;
use App\Services\SurveyDistributionMailer;
use Carbon\Carbon;

class SendScheduledSurveys extends Command
{
    protected $signature = 'surveys:send-scheduled';
    protected $description = 'Send one email per user containing all active unresponded surveys';

    public function handle()
    {
        $today = Carbon::today();

        // Step 1: Get all active distributions due
        $distributions = SurveyDistribution::whereDate('scheduled_active_date', '<=', $today)
            ->where('is_active', true)
            ->with('survey')
            ->get();

        if ($distributions->isEmpty()) {
            $this->info('No distributions due today.');
            return 0;
        }

        // Step 2: Group active, unresponded surveys per user
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
                $alreadyResponded = SurveyResponse::where('survey_id', $distribution->survey_id)
                    ->where('user_id', $user->id)
                    ->exists();

                if (!$alreadyResponded) {
                    $surveyLink = url('/respond/surveys/' . $distribution->survey_id);
                    $userSurveyMap[$user->email]['name'] = $user->name;
                    $userSurveyMap[$user->email]['surveys'][$distribution->survey->title] = $surveyLink;
                }
            }
        }

        // Step 3: Send combined Brevo email per user
        foreach ($userSurveyMap as $email => $data) {
            $name = $data['name'];
            $surveyLinks = $data['surveys'] ?? [];

            if (count($surveyLinks) > 0) {
                (new SurveyDistributionMailer($email, $name, $surveyLinks))->send();
                Log::info("📧 Sent combined survey email to {$email} (" . count($surveyLinks) . " surveys)");
                $this->info("✅ Sent email to {$email}");
            }
        }

        $this->info('✅ All combined survey emails sent successfully.');
        return 0;
    }
}
