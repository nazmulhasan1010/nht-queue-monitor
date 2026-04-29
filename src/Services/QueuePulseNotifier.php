<?php

namespace NHT\QueueMonitor\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class QueueMonitorNotifier
{
    public function notify(string $title, string $message, array $meta = []): void
    {
        if (! config('queue-monitor.notifications.enabled', false)) {
            return;
        }

        if (config('queue-monitor.notifications.channels.slack') && config('queue-monitor.notifications.slack_webhook_url')) {
            $this->sendSlack($title, $message, $meta);
        }

        if (config('queue-monitor.notifications.channels.mail') && config('queue-monitor.notifications.mail_to')) {
            $this->sendMail($title, $message, $meta);
        }
    }

    protected function sendSlack(string $title, string $message, array $meta = []): void
    {
        Http::post(config('queue-monitor.notifications.slack_webhook_url'), [
            'text' => "*{$title}*\n{$message}\n```" . json_encode($meta, JSON_PRETTY_PRINT) . "```",
        ]);
    }

    protected function sendMail(string $title, string $message, array $meta = []): void
    {
        Mail::raw($message . "\n\n" . json_encode($meta, JSON_PRETTY_PRINT), function ($mail) use ($title) {
            $mail->to(config('queue-monitor.notifications.mail_to'))
                ->subject($title);
        });
    }
}
