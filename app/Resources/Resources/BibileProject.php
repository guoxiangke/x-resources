<?php
namespace App\Resources\Resources;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use voku\helper\HtmlDomParser;

final class BibileProject{
	public function _invoke($keyword)
	{
        if($keyword == "bibleproject"){
            $date = now()->format('ymd');
            $cacheKey = "xbot.keyword.bibileproject";
            $data = Cache::get($cacheKey, false);
            if(!$data){
                // https://miniature-calendar.com/221003
                $response = Http::get("https://bibleproject.com/locale/downloads/zhs/");
                $html =$response->body();
                // $htmlTmp = HtmlDomParser::str_get_html($html);
				$re = '/<div class="intl-downloads-item-title">(.*?)<\/div>[\s\S]*?<a\s+href="(https:\/\/[^"]+\.mp4)"/m';
				// $re = '/<div class="intl-downloads-item-title">([^<]+)<\/div>.*?<a href="([^"]+\.mp4)".*?(?:<a href="([^"]+\.png)")?/s';
				$pattern = '/<div class="intl-downloads-item-title">([^<]+)<\/div>.*?<a\s+href="([^"]+\.mp4)"[^>]*>.*?(?:<a\s+href="([^"]+\.png)"[^>]*>[^<]*<\/a>|<span class="intl-downloads-item-empty">无<\/span>)/s';

				preg_match_all($pattern, $html, $data);
				unset($data[0]);
                Cache::put($cacheKey, $data, strtotime('+1 week') - time());
            }
            $total = count($data[1]);
			$offset = (now()->format('z')+10)%$total+1;
            
            $titles = $data[1];
			$mp4links = $data[2];
			$pnglinks = $data[3];

            return [
            	'type' => 'link',
                "data"=> [
                    "url" => $mp4links[$offset],
                    'title' => "{$offset}/{$total} 【bibileproject】". $titles[$offset],
                    'description' => "来自 Bibile Project",
                    'image' => $pnglinks[$offset]??'',
                ],
                'statistics' => [
                    'metric' => 'BibileProject',
                    "keyword" => $offset,
                    "type" => 'video',
                ]
            ];
        }
	}
}
