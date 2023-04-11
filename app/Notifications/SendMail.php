<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Support\Facades\Mail;
use App\Notifications\Message;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Jobs\SendMailJob;

class SendMail extends Notification implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $template_default = 'email.default';

    public function  notificationDefault(Message $message)
    {
        $message->template =  $this->template_default;
        $message->subject = 'Tritium ';
        dispatch(new SendMailJob($message));
    }

}
