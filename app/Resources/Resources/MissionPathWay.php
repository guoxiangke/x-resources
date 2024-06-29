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
        // 808 国语
        // 809 粤语
        if($keyword == '807'){
          $now = now();
          $year = $now->format('Y');
          $month = $now->format('m');
          $url = "https://pub-3813a5d14cba4eaeb297a0dba302143c.r2.dev/missionpathway/devotional/{$year}/{$month}.json";
          $title = "#宣教日引每日灵修" . $now->format('md');
          $index = (int)$now->format('d') - 1;//1-30 0-29
          
          $jsons = Http::get($url)->json();
          $item = $jsons[$index];

          $description = $item['title'];

          $image = env('R2_SHARE_VIDEO')."/missionpathway/{$item['thumbnail']}";

          $addition = [
            'type' => 'text',
            "data" => [
                'content' => $title . "\n" . $description . "\n" .$item['content'],
            ],
          ];
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

        if($keyword == '808'){
          $now = now();
          $year = $now->format('Y');
          $month = $now->format('m');
          $url = "https://pub-3813a5d14cba4eaeb297a0dba302143c.r2.dev/missionpathway/devotional/{$year}/{$month}.json";
          $title = "宣教日引" . $now->format('md');
          $index = (int)$now->format('d') - 1;//1-30 0-29
          
          $jsons = Http::get($url)->json();
          $item = $jsons[$index];
          // 0 ca 国语
          // 1 ma 粤语
          $link = str_replace('+', '%2B', $item['links'][0]);
          $url = env('R2_SHARE_VIDEO')."/missionpathway/devotional/{$year}/ca/{$link}.mp3";
          $description = $item['title'];
          $data = [
            'type' => 'music',
            "data"=> compact("url",'title','description'),
          ];
          $data['statistics'] = [
              'metric' => class_basename(__CLASS__),
              "keyword" => $keyword
          ];
          return $data;
        }

        if($keyword == '809'){
          $now = now();
          $year = $now->format('Y');
          $month = $now->format('m');
          $url = "https://pub-3813a5d14cba4eaeb297a0dba302143c.r2.dev/missionpathway/devotional/{$year}/{$month}.json";
          $title = "(粤)宣教日引" . $now->format('md');
          $index = (int)$now->format('d') - 1;//1-30 0-29
          
          $jsons = Http::get($url)->json();
          $item = $jsons[$index];
          // 0 ca 国语
          // 1 ma 粤语
          $link = str_replace('+', '%2B', $item['links'][1]);
          $url = env('R2_SHARE_VIDEO')."/missionpathway/devotional/{$year}/ma/{$link}.mp3";
          $description = $item['title'];
          $data = [
            'type' => 'music',
            "data"=> compact("url",'title','description'),
          ];
          $data['statistics'] = [
              'metric' => class_basename(__CLASS__),
              "keyword" => $keyword
          ];
          return $data;
        }


        if($keyword == '810'){
          $now = now();
          $year = $now->format('Y');
          $month = $now->format('m');
          $url = "https://pub-3813a5d14cba4eaeb297a0dba302143c.r2.dev/missionpathway/prayer/{$year}/{$month}.json";
          $title = "#认识未得之民" . $now->format('md'). "\nby 宣教日引";
          $index = (int)$now->format('d') - 1;//1-30 0-29
          
          $jsons = Http::get($url)->json();
          $item = $jsons[$index];

          $description = $item['title'];

          $image = env('R2_SHARE_VIDEO')."/missionpathway/{$item['thumbnail']}";

          $addition = [
            'type' => 'text',
            "data" => [
                'content' => $title . "\n" . $description . "\n" .$item['content'],
            ],
          ];
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

        if($keyword == '811'){
          $now = now();
          $year = $now->format('Y');
          $month = $now->format('m');
          $url = "https://pub-3813a5d14cba4eaeb297a0dba302143c.r2.dev/missionpathway/prayer/{$year}/{$month}.json";
          $title = "宣教日引" . $now->format('md');
          $index = (int)$now->format('d') - 1;//1-30 0-29
          
          $jsons = Http::get($url)->json();
          $item = $jsons[$index];
          // 0 ca 国语
          // 1 ma 粤语
          $link = str_replace('+', '%2B', $item['links'][0]);
          $url = env('R2_SHARE_VIDEO')."/missionpathway/prayer/{$year}/ca/{$link}.mp3";
          $description = $item['title'];
          $data = [
            'type' => 'music',
            "data"=> compact("url",'title','description'),
          ];
          $data['statistics'] = [
              'metric' => class_basename(__CLASS__),
              "keyword" => $keyword
          ];
          return $data;
        }

        if($keyword == '812'){
          $now = now();
          $year = $now->format('Y');
          $month = $now->format('m');
          $url = "https://pub-3813a5d14cba4eaeb297a0dba302143c.r2.dev/missionpathway/prayer/{$year}/{$month}.json";
          $title = "(粤)宣教日引" . $now->format('md');
          $index = (int)$now->format('d') - 1;//1-30 0-29
          
          $jsons = Http::get($url)->json();
          $item = $jsons[$index];
          // 0 ca 国语
          // 1 ma 粤语
          $link = str_replace('+', '%2B', $item['links'][1]);
          $url = env('R2_SHARE_VIDEO')."/missionpathway/prayer/{$year}/ma/{$link}.mp3";
          $description = $item['title'];
          $data = [
            'type' => 'music',
            "data"=> compact("url",'title','description'),
          ];
          $data['statistics'] = [
              'metric' => class_basename(__CLASS__),
              "keyword" => $keyword
          ];
          return $data;
        }
        return null;
	}
}
