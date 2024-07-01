<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
        
        // $storage = Storage::disk('local');
        $storage = Storage::disk('r2-share');

        $type = 'devotional';
        $directory = "/missionpathway/{$type}/{$year}/";
        $storage->makeDirectory($directory);
        
        // download devotional
        $url = "https://missionpathway.net/{$type}-{$year}-{$month}.php";
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
                $link = basename(trim($a->getAttribute("href")));
                if(!Str::startsWith($link, '+')) continue;
                $links[] = $link;
            }
            $item = compact('title','thumbnail','content','links');
            DownloadPathWayThumbQueue::dispatch($item);
            DownloadPathWayMp3Queue::dispatch($item,$type);
            $items[] = $item;
        }
        Log::info(__CLASS__,['downloaded',count($items)]);
        $storage->put("$directory/$month.json", json_encode($items));
        
        // download https://missionpathway.net/prayer-2024-06.php#27
        $type = 'prayer';
        $url = "https://missionpathway.net/{$type}-{$year}-{$month}.php";
        $response = Http::get($url);
        // dd($response->body());
        $htmlTmp = HtmlDomParser::str_get_html($response->body());
        $directory = "/missionpathway/{$type}/{$year}/";
        $storage->makeDirectory($directory);

        $items = [];
        foreach ($htmlTmp->find('.row .span4 .post') as $html) {
            // $day = $html->getAttribute("id");
            $title =  $html->findOne('h2.post-title')->text()." (". $html->findOne('h3.post-title')->text().")";
            $thumbnail =  $html->findOne('.post-thumbnail img')->getAttribute("src");
            $content =  $html->findOne('.post-content')->text();
            
            $contents = explode('代祷文', $content);
            if(!isset($contents[1])){
                $contents = explode('你所在的国家', $content);
                $content = trim($contents[0]);
            }else{
                $content = trim($contents[1]);
            }
            $content = str_replace(["\r", "\t", "\n"], "", $content);

            $links = [];
            foreach ($html->find("a") as $a) {
                $link = basename(trim($a->getAttribute("href")));
                if(!Str::startsWith($link, '+')) continue;
                $links[] = $link;
            }
            $item = compact('title','thumbnail','content','links');
            DownloadPathWayThumbQueue::dispatch($item);
            DownloadPathWayMp3Queue::dispatch($item,$type);
            $items[] = $item;
        }
        Log::info(__CLASS__,['downloaded',count($items)]);
        $storage->put("$directory/$month.json", json_encode($items));
        return Command::SUCCESS;
    }
}