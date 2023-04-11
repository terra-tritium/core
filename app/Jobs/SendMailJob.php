<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Notifications\Message;
use App\Notifications\NotificationMail;

class SendMailJob implements ShouldQueue 
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public Message $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Message $message )
    {
        $this->message = $message;
    }
    
    public function  handle()
    {
        Mail::to($this->message->to)->send(new NotificationMail($this->message));
    }
 

}
