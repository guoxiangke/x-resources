<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use voku\helper\HtmlDomParser;

class DownloadPathWayThumbQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $item;
    /**
     * Create a new job instance.
     */
    public function __construct($item)
    {
        $this->item = $item;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $item = $this->item;

        $storage = Storage::disk('r2-share');
        $year = now()->format('Y');
        $directory = "/missionpathway/images/devotional/";
        $storage->makeDirectory($directory);
        // $storage = Storage::disk('r2-tingdao');
        $url = "https://missionpathway.net/".$item['thumbnail'];
        $name = basename($url);//uRgxyOV3XzoA.mp3
        $filename = "$directory/".basename($url);
        if(Storage::exists($filename)){
            Log::error('Exists',[$url]);
            return;
        }

        // 放到下载队列里
        Log::info("Dowloading start",[$url]);
        $storage->put($filename, file_get_contents($url));

    }
}
