<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkingListMail extends Mailable
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
            ->subject('New Working List Assigned')
            ->view('working_list.email')
            ->with([
                'workingList' => $this->workingList,
            ]);
    }
}
