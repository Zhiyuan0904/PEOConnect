<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SurveyDistribution;
use App\Models\User;
use App\Models\SurveyResponse;
use Illuminate\Support\Facades\Mail;
use App\Mail\SurveyDistributionMail;
use Carbon\Carbon;

class DistributeSurveysCommand extends Command
{
    protected $signature = 'distribute:surveys';
    protected $description = 'Automatically distribute surveys to students/alumni based on schedule';

    public function handle()
    {
        $today = Carbon::today();

        // ✅ 1. Fetch all active distributions where date is due
        $distributions = SurveyDistribution::where('scheduled_active_date', '<=', $today)
            ->where('is_active', true)
            ->with('survey')
            ->get();

        if ($distributions->isEmpty()) {
            $this->info('No surveys to distribute today.');
            return 0;
        }

        foreach ($distributions as $distribution) {
            $this->info('📤 Distributing: ' . $distribution->survey->title);

            // ✅ 2. Find users matching the role and date range
            $query = User::where('role', $distribution->target_role);

            if ($distribution->target_role === 'student') {
                $query->whereBetween('enroll_date', [$distribution->start_date, $distribution->end_date]);
                $dateField = 'enroll_date';
            } elseif ($distribution->target_role === 'alumni') {
                $query->whereBetween('expected_graduate_date', [$distribution->start_date, $distribution->end_date]);
                $dateField = 'expected_graduate_date';
            }

            $users = $query->get();

            $this->info("✅ Found {$users->count()} users for date field: {$dateField}");

            foreach ($users as $u) {
                $this->info("➡️  Match: {$u->email} ({$dateField} = " . $u[$dateField] . ")");
            }

            // ✅ 3. Send emails only to users who haven't responded
            $sentCount = 0;
            foreach ($users as $user) {
                $hasResponded = SurveyResponse::where('survey_id', $distribution->survey_id)
                    ->where('user_id', $user->id)
                    ->exists();

                if ($hasResponded) {
                    $this->info("⏩ Skipped: {$user->email} (already responded)");
                    continue;
                }

                // Send only to users who haven't responded
                Mail::to($user->email)->send(new SurveyDistributionMail($distribution->survey));
                $this->info('📧 Email sent to: ' . $user->email);
                $sentCount++;
            }

            $this->info("✅ Distribution complete. Emails sent: {$sentCount}");

            // (Optional) mark as sent
            // $distribution->update(['sent_at' => Carbon::now()]);
        }

        return 0;
    }
}
