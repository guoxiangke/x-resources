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
            $date = now()->tz(config('app.timezone_display'))->format('ymd');
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
	}
}
