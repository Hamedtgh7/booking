<?php

namespace App\Jobs;

use App\Models\UserActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogActivityJob implements ShouldQueue
{
    use Queueable,Dispatchable,InteractsWithQueue,SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $user_id,
                                public string $url,
                                public string $method,
                                public string $action,
                                public array $requestData,
                                public int $responseStatus,
                                public string $description
                                )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        UserActivity::query()->create([
            'user_id'=>$this->user_id,
            'url'=>$this->url,
            'method'=>$this->method,
            'action'=>$this->action,
            'requestData'=>$this->requestData,
            'responseStatus'=>$this->responseStatus,
            'description'=>$this->description
        ]);
    }
}
