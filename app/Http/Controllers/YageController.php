<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use voku\helper\HtmlDomParser;
use GuzzleHttp\Client;

class YageController extends Controller
{
    public function json($keyword)
    {
        //302 http://napi.yageapp.com/api/t.php?k=6b42e8370481f752
        // http://napi.yageapp.com/api/web/share/album.php?mid=ba1d4d91ebf17ea4&bundleid=&base_uid=-1
        $date = now()->format('ymd');
        $cacheKey = "resources.keyword.yage.".$keyword;
        $data = Cache::get($cacheKey, false);
        if(!$data){
            $client = new Client();
            $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36';
            $headers = [
                'User-Agent'=> $userAgent,
            ];
            // $url = "http://napi.yageapp.com/api/t.php?k={$keyword}";
            $url = "http://napi.yageapp.com/api/web/share/album.php?mid={$keyword}";
            
            $response = $client->get($url, [
                'headers'  => $headers,
                'debug' => false,
            ]);
            $html = (string)$response->getBody();

            $htmlTmp = HtmlDomParser::str_get_html($html);

            preg_match('/var patharray = ({.*?});/', $htmlTmp, $matches);
            // 解析匹配到的JSON字符串
            $json_string = $matches[1];
            // 转换为PHP数组
            $patharray = json_decode(str_replace('\/', '/', $json_string), true);
            
            // album__cover

            $mp3s = [];
            foreach ($htmlTmp->getElementByClass('qui_list__item') as $e) {
                $songid = $e->getAttribute('data-songid');
                $title = $e->findOne('span.qui_list__txt')->text();
                $path = $patharray[$songid];
                $mp3s[$songid] = compact('title','path');
            }
            Cache::put($cacheKey, $data);
            return $mp3s;
        }
    }
}