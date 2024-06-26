<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use voku\helper\HtmlDomParser;

use App\Jobs\DownloadPathWayThumbQueue;
use App\Jobs\DownloadPathWayMp3Queue;

class DownloadPathWay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:download-pathway {month?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $month = $this->argument('month');
        // 如果未提供 month 参数，可以设置一个默认值，例如当前月份
        if (!$month) {
            $month = now()->format('m'); // 获取当前月份
        }
        $year = now()->format('Y');
        $url = "https://missionpathway.net/devotional-{$year}-{$month}.php";
        
        $storage = Storage::disk('r2-share');
        // $storage = Storage::disk('local');
        $directory = "/missionpathway/{$year}/";
        $storage->makeDirectory($directory);
        
        $response = Http::get($url);
        $htmlTmp = HtmlDomParser::str_get_html($response->body());

        $items = [];
        foreach ($htmlTmp->find('.row .span4') as $html) {
            // $day = $html->getAttribute("id");
            $title =  "(".$html->findOne('h5')->text().") " . $html->findOne('h3')->text();
            $thumbnail =  $html->findOne('.post-thumbnail img')->getAttribute("src");
            $content =  $html->findOne('.post-content')->text();
            $content = str_replace(["\r", "\t", "\n"], "", $content);

            $links = [];
            foreach ($html->find(".post-header a") as $a) {
                $links[] = basename(trim($a->getAttribute("href")));
            }
            $item = compact('title','thumbnail','content','links');
            DownloadPathWayThumbQueue::dispatch($item);
            DownloadPathWayMp3Queue::dispatch($item);
            $items[] = $item;
        }
        $storage->put("$directory/$month.json", json_encode($items));
        return Command::SUCCESS;
    }
}