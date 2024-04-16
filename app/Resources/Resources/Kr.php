<?php

namespace App\Resources\Resources;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use voku\helper\HtmlDomParser;
use GuzzleHttp\Client;

final class Kr{
    public function _invoke($keyword)
    {
        // 互联网人的资讯早餐（音频版）周1-5
        if($keyword == "8点1氪"){
            $date = now()->format('ymd');
            $cacheKey = "xbot.keyword.kr";
            $data = Cache::get($cacheKey, false);
            if(!$data){
                
                $client = new Client();
                $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.182 Safari/537.36';
                $headers = [
                    'User-Agent'=> $userAgent,
                ];
                $url = 'http://36kr.com/column/491522785281';
                $response = $client->get($url, [
                    'headers'  => $headers,
                    'debug' => false,
                ]);
                $html = (string)$response->getBody();

                    // cURL error 23: Failed reading the chunked-encoded stream
                // $response = Http::get("http://36kr.com/column/491522785281");
                // $html = $response->body();

                $htmlTmp = HtmlDomParser::str_get_html($html);
                $mp3 =  $htmlTmp->getElementByTagName('audio')->getAttribute('src');
                $title =  $htmlTmp->findOne('.audio-title')->text();


                $data =[
                    "url" => $mp3,
                    'title' => "【8点1氪】{$date}",
                    'description' => $title,
                ];
                Cache::put($cacheKey, $data, strtotime('tomorrow') - time());
            }
            return [
                'type' => 'music',
                "data"=> $data,
            ];
        }

        if($keyword == '虎嗅'){
            $result = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:123.0) Gecko/20100101 Firefox/123.0',
            ])->asForm()->post('https://moment-api.huxiu.com/web-v3/moment/feed',[
                'type'=>'',
                'last_id'=>'',
                'platform'=>'www'
            ])->json();
            $text = '';
            foreach ($result['data']['moment_list']['datalist'] as $arr) {
                $textArray = explode('<br><br>', $arr['content']);
                $text .=  $textArray[0] . "by {$arr['user_info']['username']} {$arr['format_time']} \n";
            }
            
            return [
                'type' => 'text',
                "data"=> ['content'=>$text . "详情：https://www.huxiu.com/moment/"],
            ];
        }
        


        if($keyword == 'rfi'){
            $data = [
                "url" => "https://rfienchinois64k.ice.infomaniak.ch/rfienchinois-64.mp3",
                'title' => "【fri】News广播",
                'description' => '广播News',
            ];
            return [
                'type' => 'music',
                "data"=> $data,
            ];
        }
        if($keyword == 'cnn1'){
            $data = [
                "url" => "https://tunein.cdnstream1.com/3519_96.mp3",
                'title' => "【cnn1】News广播",
                'description' => '广播News',
            ];
            return [
                'type' => 'music',
                "data"=> $data,
            ];
        }

        if($keyword == 'cnn2'){
            $data = [
                "url" => "https://tunein.cdnstream1.com/2868_96.mp3",
                'title' => "【cnn2】News广播",
                'description' => '广播News',
            ];
            return [
                'type' => 'music',
                "data"=> $data,
            ];
        }

        if($keyword == 'npr'){
            $data = [
                "url" => "https://prod-52-201-196-36.amperwave.net/southerncalipr-kpccfmmp3-imc",
                'title' => "【npr】LAist 89.3",
                'description' => 'LAist 89.3 - Southern California Public Radio',
            ];
            return [
                'type' => 'music',
                "data"=> $data,
            ];
        }
        

        
        
    }
}
