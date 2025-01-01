<?php

namespace App\Resources\Resources;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use voku\helper\HtmlDomParser;

final class MissionPathWay {
	public function _invoke($keyword) {
        $data = [];
        // 807 文本 + image
        // 808 国语 Delete!
        // 809 粤语 Delete!
        // 810
        // 811 国语 Delete!
        // 812 粤语 Delete!

        if($keyword == '807'){
          $type = 'devotional';
          $now = now();
          $year = $now->format('Y');
          $month = $now->format('m');
          $url = "https://pub-3813a5d14cba4eaeb297a0dba302143c.r2.dev/missionpathway/devotional/{$year}/{$month}.json";
          $title = "#宣教日引每日灵修" . $now->format('md');
          $index = (int)$now->format('d') - 1;//1-30 0-29
          
          $jsons = Http::get($url)->json();
          $item = $jsons[$index];

          $description = $item['title'];

          // $image = env('R2_SHARE_VIDEO')."/missionpathway/{$item['thumbnail']}";
          $image = "https://missionpathway.net/{$item['thumbnail']}";

          $addition = [
            'type' => 'text',
            "data" => [
                'content' => $title . "\n" . $description . "\n" .$item['content'],
            ],
          ];
          $audio = $this->get_audio($type);
          $addition['addition'] = $audio;
          $data = [
            'type' => 'imageUrl',
            "data" => ['url'=> $image],
            'addition' => $addition,
          ];
          $data['statistics'] = [
              'metric' => class_basename(__CLASS__),
              "keyword" => $keyword
          ];
          return $data;
        }


        if($keyword == '810'){
          $type = 'prayer';
          $now = now();
          $year = $now->format('Y');
          $month = $now->format('m');
          $url = "https://pub-3813a5d14cba4eaeb297a0dba302143c.r2.dev/missionpathway/prayer/{$year}/{$month}.json";
          $title = "#认识未得之民" . $now->format('md'). "\nby 宣教日引";
          $index = (int)$now->format('d') - 1;//1-30 0-29
          
          $jsons = Http::get($url)->json();
          $item = $jsons[$index];

          $description = $item['title'];

          // $image = env('R2_SHARE_VIDEO')."/missionpathway/{$item['thumbnail']}";
          $image = "https://missionpathway.net/{$item['thumbnail']}";

          $addition = [
            'type' => 'text',
            "data" => [
                'content' => $title . "\n" . $description . "\n" .$item['content'],
            ],
          ];
          $audio = $this->get_audio($type);
          $addition['addition'] = $audio;
          $data = [
            'type' => 'imageUrl',
            "data" => ['url'=> $image],
            'addition' => $addition,
          ];
          $data['statistics'] = [
              'metric' => class_basename(__CLASS__),
              "keyword" => $keyword
          ];
          return $data;
        }

        return null;
	}
  private function get_audio($type='devotional'){
          $now = now();
          $year = $now->format('Y');
          $month = $now->format('m');
          $url = "https://pub-3813a5d14cba4eaeb297a0dba302143c.r2.dev/missionpathway/{$type}/{$year}/{$month}.json";
          $title = "宣教日引" . $now->format('md');
          $index = (int)$now->format('d') - 1;//1-30 0-29
          
          $jsons = Http::get($url)->json();
          $item = $jsons[$index];
          $link = $item['links'][0];
          $url = "https://missionpathway.net/{$link}";
          $description = $item['title'];
          $data = [
            'type' => 'music',
            "data"=> compact("url",'title','description'),
          ];
          $data['statistics'] = [
              'metric' => class_basename(__CLASS__),
              "keyword" => 'audio'
          ];
          return $data;
  }
}
