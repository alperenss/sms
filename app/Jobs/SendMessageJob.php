<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Traits\SMS;
use SebastianBergmann\Environment\Console;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, SMS;

    private $user;
    private $number;
    private $message;
    private $ip;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $number, $message, $ip)
    {
        $this->user = $user;
        $this->number = $number;
        $this->message = $message;
        $this->ip = $ip;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Console::log("Geldi");
        $sendSMS = $this->sendSMS($this->user, $this->number, $this->message, $this->ip);
    }
}
