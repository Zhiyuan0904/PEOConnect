<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SurveyDistributionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $surveyLinks;

    public function __construct($surveyLinks)
    {
        $this->surveyLinks = $surveyLinks;
    }

    public function build()
    {
        return $this->view('emails.survey-distribution')
                    ->with([
                        'surveyLinks' => $this->surveyLinks,
                    ]);
    }
}
