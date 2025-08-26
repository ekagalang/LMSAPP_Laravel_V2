<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        // Check if custom template exists, otherwise use default Laravel template
        if (view()->exists('emails.reset-password')) {
            return (new MailMessage)
                ->subject('Reset Password - BASS Training Center')
                ->view('emails.reset-password', [
                    'url' => $resetUrl,
                    'user' => $notifiable
                ]);
        }

        // Fallback to default Laravel email template
        return (new MailMessage)
            ->subject('Reset Password - BASS Training Center')
            ->greeting('Halo!')
            ->line('Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.')
            ->action('Reset Password', $resetUrl)
            ->line('Link reset password ini akan kedaluwarsa dalam :count menit.', [
                'count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')
            ])
            ->line('Jika Anda tidak meminta reset password, tidak ada tindakan lebih lanjut yang diperlukan.')
            ->salutation('Salam, Tim BASS Training Center');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}