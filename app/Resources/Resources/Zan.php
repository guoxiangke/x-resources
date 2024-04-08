<?php

namespace App\Resources\Resources;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use voku\helper\HtmlDomParser;

final class Zan{
	public function _invoke($keyword)
	{
        // https://play.zanmei.co/song/p/47372.mp3

        $triggerKeywords = ["赞美诗网","赞美诗歌","赞美诗", "赞美", "赞" ];
        if(Str::startsWith($keyword, $triggerKeywords)){
            $name = str_replace(
                $triggerKeywords,
                ['', '', '', '', ''],
                $keyword
            );
            $name = trim($name);
            $cacheKey = "xbot.keyword.zmsg.{$name}";
            $data = Cache::get($cacheKey, false);
            if(!$data){
            // if(1){
                $url = "https://www.zanmei.ai/search/song/{$name}";
                $response = Http::get($url);
                $html = $response->body();
                $htmlTmp = HtmlDomParser::str_get_html($html);
                $notFound = $htmlTmp->findOneOrFalse('.empty');

                if($notFound){
                    $notFound = $notFound->text();
                    return [
                            "type"=>"text",
                            "data"=> [
                                "content" => $notFound
                            ]
                        ];
                }

                $songs = [];
                $max = 0;
                $description = '';
                foreach ($htmlTmp->find('.songs table tr') as $tr) {
                    // $counts = str_replace('&nbsp;','', $tr->findOne('.length span')->text());
                    $style = $tr->findOne('.length span')->getAllAttributes()['style'];
                    $style = str_replace('width:','',$style);
                    $style = str_replace('%;','',$style);
                    $style *= 100;
                    $counts = $style;
                    foreach ($tr->find('.name a') as $key => $value) {
                        switch ($key) {
                            case 0:
                                $link = $value->getAttribute('href');
                                $title = $value->text();
                                break;
                            case 1:
                                $album = $value->text();
                                break;
                            case 2:
                                $singer = $value->text();
                                break;

                            default:
                                // code...
                                break;
                        }
                    }

                    $songs[$counts] = $link ;
                    if($counts>$max){
                        $max = $counts;
                        $description = $album . ' ' . $singer;
                        $name = $title;
                    }
                }
                preg_match('/\d+/',$songs[$max],$matchs);
                $id = $matchs[0];
                // $mp3 = "https://play.readbible.cn/song/p/{$id}.mp3";
                $mp3 = "https://play.zanmei.co/song/p/{$id}.mp3";
                $data =[
                    "url" => $mp3,
                    'title' => $name,
                    'description' => $description,
                ];
                Cache::put($cacheKey, $data);
            }
            return [
                "type"=>"music",
                "data"=> $data
            ];
        }
        return null;
	}
}
