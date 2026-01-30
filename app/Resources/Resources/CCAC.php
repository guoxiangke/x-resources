<?php

namespace App\Resources\Resources;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use voku\helper\HtmlDomParser;
use GuzzleHttp\Client;

final class CCAC{
	public function _invoke($keyword)
	{
        //3位数关键字xxx
        $offset = substr($keyword, 3) ?: 0;
        $keyword = substr($keyword, 0, 3);
        if($keyword == '796'){
            $response = Http::get("https://www.youtube.com/@cantoneseccac1966/streams");
            $html =$response->body();

            $re = '/"text":"psyuanccac([^"]+).*?"videoId":"([^"]+)"/';
            preg_match_all($re, $html, $matches);

            $vid = $matches[2][$offset];
            $channelDomain = env('R2_SHARE_AUDIO')."/@cantoneseccac1966/";
            $url = $channelDomain.$vid.".mp4";
            $image = 'https://r2.savefamily.net/uPic/2023/IeDDmx.jpg';

            $data = [
                'type' => 'link',
                'data' => [
                    "url" => $url,
                    'title' => "【CCAC】聖谷華人宣道會粤語堂",
                    'description' => $matches[1][$offset]."粵語堂主日直播回放",
                    'image' => $image,
                    'vid' => $vid,
                ]
            ];
            $data['statistics'] = [
                'metric' => class_basename(__CLASS__),
                "keyword" => $keyword,
                "type" => 'video',
            ];

            // Add audio
            $m4a = $channelDomain.$vid.".m4a";
            $addition = $data;
            $addition['type'] = 'music';
            $addition['data']['url']= $m4a;
            $addition['statistics'] = [
                'metric' => class_basename(__CLASS__),
                "keyword" => $keyword,
                "type" => 'audio',
            ];
            $data['addition'] = $addition;
            return $data;
        }

        // 796 CCAC 袁惠鈞牧師
        // 7910 - 7912
        if(Str::startsWith($keyword,'791') && strlen($keyword) >= 3){
            $playLists = [
                ["id"=>"PLtrnB_vqQmqQObaA8ZCYc2Lig0NtuS5hH",'title'=>'聖經真的可信嗎'],
                ["id"=>"PLtrnB_vqQmqRBSltXAKvONWuSJp23hlye",'title'=>'箴言系列 - 智慧的話語'],
                ["id"=>"PLtrnB_vqQmqSsBabU-DuUUNnbhqCc4qiF",'title'=>'天地揭秘系列'],
            ];
            $oriKeyword = substr($keyword,0,3);
            $index = (int)substr($keyword, 3)%count($playLists);

            $playList = $playLists[$index];
            $playListId = $playList['id'];
            $playListTitle = $playList['title'];
            $cacheKey = "resources." . $keyword;
            $items = Cache::get($cacheKey, false);
            if(!$items){
                $url = "https://pub-3813a5d14cba4eaeb297a0dba302143c.r2.dev/playlist/{$playListId}/{$playListId}.txt";
                $response = Http::get($url);
                $ids = explode(PHP_EOL, $response->body());
                $items = [];
                
                foreach ($ids as $key => $yid) {
                    if(!$yid) continue;
                    $url = "https://pub-3813a5d14cba4eaeb297a0dba302143c.r2.dev/playlist/{$playListId}/{$yid}.info.json";
                    $json = Http::get($url)->json();
                    $tempItem['title'] = $json['title'];
                    $tempItem['thumbnail'] = $json['thumbnail'];
                    $tempItem['url'] = "{$yid}.mp4";
                    // "https://i.ytimg.com/vi/$vid/maxresdefault.jpg"
                    $tempItem['id'] = $json['id'];
                    $items[] = $tempItem;
                }
                Cache::put($cacheKey, $items);
            }
            $total = count($items);
            $index =  now()->format('z') % ($total);
            $item = $items[$index++];
            $data = [
                'type' => 'link',
                "data"=> [
                    "url" => env('R2_SHARE_AUDIO')."/playlist/{$playListId}/{$item['url']}",
                    'title' => "($index/$total)".$playListTitle,
                    'description' => "{$item['title']}",
                    'image' => $item['thumbnail'],
                    'vid' => $item['id'],
                ],
            ];
            $data['statistics'] = [
                'metric' => class_basename(__CLASS__),
                "keyword" => $keyword,
                "type" => 'video',
            ];

            // Add audio
            $addition = $data;
            $addition['type'] = 'music';
            $addition['data']['url'] = str_replace('.mp4', '.m4a', $addition['data']['url']);
            $addition['statistics'] = [
                'metric' => class_basename(__CLASS__),
                "keyword" => $keyword,
                "type" => 'audio',
            ];
            $data['addition'] = $addition;
            return $data;
        }

        return null;
	}
}
