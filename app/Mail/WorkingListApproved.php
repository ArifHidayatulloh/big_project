<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkingListApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $workingList;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($workingList)
    {
        $this->workingList = $workingList;
    }

    public function build()
    {
        return $this->from('kkioperational@gmail.com')
            ->subject('Working List Approved')
            ->view('working_list.email_approved')
            ->with([
                'workingList' => $this->workingList,
            ]);
    }
}
