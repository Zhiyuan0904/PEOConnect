<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SurveyDistribution;
use App\Models\User;
use App\Mail\SurveyDistributionMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendScheduledSurveys extends Command
{
    protected $signature = 'surveys:send-scheduled';
    protected $description = 'Send survey emails based on scheduled active date';

    public function handle()
    {
        $today = Carbon::today();

        $distributions = SurveyDistribution::whereDate('scheduled_active_date', '<=', $today)
                                            ->where('is_active', true)
                                            ->get();

        foreach ($distributions as $distribution) {
            $query = User::where('role', $distribution->target_role);

            if ($distribution->date_field == 'enroll_date') {
                $query->whereDate('enroll_date', '>=', $distribution->start_date)
                      ->whereDate('enroll_date', '<=', $distribution->end_date);
            } else {
                $query->whereDate('expected_graduate_date', '>=', $distribution->start_date)
                      ->whereDate('expected_graduate_date', '<=', $distribution->end_date);
            }

            $users = $query->get();

            foreach ($users as $user) {
                // Example survey link (you can customize!)
                $surveyLink = url('http://127.0.0.1:8000/home');

                Mail::to($user->email)->send(new SurveyDistributionMail($surveyLink));
                
                Log::info('Survey email sent to: ' . $user->email);
                $this->info('Survey email sent to: ' . $user->email);
            }
        }

        $this->info('Scheduled survey emails sent successfully!');
    }
}
