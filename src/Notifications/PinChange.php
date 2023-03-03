<?php

namespace Ikechukwukalu\Requirepin\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PinChange  extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(trans('requirepin::pin.notify.subject'))
            ->line(trans('requirepin::pin.notify.introduction'))
            ->line(trans('requirepin::pin.notify.message'))
            ->action(trans('requirepin::pin.notify.action'), url(config('requirepin.change_pin_route')))
            ->line(trans('requirepin::pin.notify.complimentary_close'));
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
