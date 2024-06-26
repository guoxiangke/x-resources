<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use voku\helper\HtmlDomParser;

class DownloadPathWayMp3Queue implements ShouldQueue
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

        foreach ($item['links'] as $key => $link) {
            $lang = $key==0?'ca':'ma';
            $directory = "/missionpathway/{$year}/{$lang}/";
            $storage->makeDirectory($directory);
            $link = "https://subsplash.com/crossroadspublications/mp3/mi/{$link}";
            $filename = "$directory/" . basename($link). '.mp3';//+hyzrpm6.mp3

            $response = Http::get($link);
            $html = $response->body();

            preg_match('/<source\s+src="([^"]+\.mp3)"/', $html, $match);
            $url = $match[1];

            if(Storage::exists($filename)){
                Log::error('Exists',[$url]);
                return;
            }
            $done = $storage->put($filename, file_get_contents($url));
            if($done){
                Log::info("Dowloading done!",[$url]);
            }else{
                Log::error("Dowloading failed!",[$url]);
            }
        }

    }
}
