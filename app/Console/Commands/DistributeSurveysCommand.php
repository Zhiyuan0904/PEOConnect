<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SurveyDistribution;
use App\Models\User;
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

        // 1. Fetch all active distributions scheduled for today
        $distributions = SurveyDistribution::where('scheduled_active_date', $today)
            ->where('is_active', true)
            ->with('survey')
            ->get();

        if ($distributions->isEmpty()) {
            $this->info('No surveys to distribute today.');
            return 0;
        }

        foreach ($distributions as $distribution) {
            $this->info('Distributing: ' . $distribution->survey->title);

            // 2. Find users matching the distribution target
            $query = User::where('role', $distribution->target_role);

            if ($distribution->date_field === 'enrol_date') {
                $query->whereBetween('enrol_date', [$distribution->start_date, $distribution->end_date]);
            } elseif ($distribution->date_field === 'graduate_date') {
                $query->whereBetween('graduate_date', [$distribution->start_date, $distribution->end_date]);
            }

            $users = $query->get();

            foreach ($users as $user) {
                // 3. Send email (later we prepare the email content)
                Mail::to($user->email)->send(new SurveyDistributionMail($distribution->survey));

                $this->info('Email sent to: ' . $user->email);
            }
        }

        return 0;
    }
}
