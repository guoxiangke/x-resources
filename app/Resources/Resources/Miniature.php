<?php

namespace App\Resources\Resources;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use voku\helper\HtmlDomParser;

final class Miniature{
	public function _invoke($keyword)
	{
        if($keyword == "miniature"){
            $date = now()->format('ymd');
            $cacheKey = "xbot.keyword.miniature";
            $data = Cache::get($cacheKey, false);
            if(!$data){
                // https://miniature-calendar.com/221003
                $response = Http::get("https://miniature-calendar.com/{$date}");
                $html =$response->body();
                $htmlTmp = HtmlDomParser::str_get_html($html);

                $src =  $htmlTmp->findOne('img.size-full')->getAttribute('src');

                $data =[
                    "url" => $src,
                    'title' => "【miniature】{$date}",
                    'description' => '每日一图',
                ];
                Cache::put($cacheKey, $data, strtotime('tomorrow') - time());
            }
            return [
                'type' => 'image',
                "data"=> $data,
            ];
        }

        // 784 scripture https://znsj.wxsorg.com/scripture.html
        if($keyword == 784){
            $cacheKey = "xbot.keyword.{$keyword}";
            $data = Cache::get($cacheKey, false);
            if(!$data){
                $response = Http::get("https://v1api.bible.hi.cn/dailyScriptures/find/short?typeId=0&link=&rowSize=16&start=0");
                $json =$response->json();
                $src =  $json['data']['data'][0]['nsimg'];
                $content = $json['data']['data'][0]['content'] . PHP_EOL . "「{$json['data']['data'][0]['title']}」";

                $addition = [
                    'type' => 'text',
                    "data" => [
                        'content' => "今日经文".PHP_EOL.$content,
                    ],
                ];
                $data = [
                    'type' => 'imageUrl',
                    "data"=> [
                        "url" => $src,
                    ],
                    'statistics' => [
                        "keyword" => $keyword,
                        "type" => 'image',
                    ],
                    'addition' => $addition,
                ];
                Cache::put($cacheKey, $data, strtotime('tomorrow') - time());
            }

            return $data;
        }
	}
}
