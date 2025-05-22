<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SurveyDistributionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $surveyLink; // Public so the Blade template can access it

    /**
     * Create a new message instance.
     */
    public function __construct($surveyLink)
    {
        $this->surveyLink = $surveyLink;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('New Survey Available - Please Participate')
                    ->view('emails.survey-distribution')
                    ->with([
                        'surveyLink' => $this->surveyLink, // Pass it properly here
                    ]);
    }
}
