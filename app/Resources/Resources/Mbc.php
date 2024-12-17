<?php
// Mbc

namespace App\Resources\Resources;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use voku\helper\HtmlDomParser;
use GuzzleHttp\Client;

final class Mbc{
    public function _invoke($keyword){
        if($keyword == "mbc"){
            $day = now()->format('md');
            $data = [
                'type' => 'link',
                'data' => [
                    "url" => "https://mbcotc.david777.net/devotional/CN/{$day}.html",
                    'title' => "慕安德烈每日靈修CN-{$day}" ,
                    'description' => '来源：大光傳宣教福音中心',
                    'image' => 'image',
                ]
            ];

            $data['statistics'] = [
                'metric' => 'MBC',
                "keyword" => 'MBC-CN',
                'type' => 'link',
            ];


            $data2 = [
                'type' => 'link',
                'data' => [
                    "url" => "https://mbcotc.david777.net/devotional/EN/{$day}.html",
                    'title' => "慕安德烈每日靈修EN-{$day}" ,
                    'description' => '来源：大光傳宣教福音中心',
                    'image' => 'image',
                ]
            ];

            $data2['statistics'] = [
                'metric' => 'MBC',
                "keyword" => 'MBC-EN',
                'type' => 'link',
            ];


            $data['addition'] = $data2;

            return $data;
        }

        return;
        // // https://www.glorypress.com/devotional/FaithAndLifeOneYearBook.asp?bid=2&rdate=2/29
        // $date = Carbon::now()
        // $day = $date->format('n/j');
        // // $day = '2/29';
        // $url = "https://www.glorypress.com/devotional/FaithAndLifeOneYearBook.asp?bid=2&rdate={$day}";
        // $response = Http::get($url);
        // $content = $response->body();
        // $html = mb_convert_encoding($content, 'UTF-8', 'Big5');
        // $html = str_replace(
        //     ['上一篇</a>', 'Privious</a>','<b>Scripture:</b>','<b>經文:</b>'],
        //     ['上一篇</a></a></a></a>', 'Privious</a></a></a></a>','<br/>Scripture:','<br/>經文:'], 
        //     $html
        // );

        // $converter = new HtmlConverter();

        // $pattern = '/<a name="#CN">([\s\S]*?)<\/a><\/a>/';
        // // 正则匹配
        // preg_match_all($pattern, $html, $matches);
        // $htmlLines = explode("\n", $matches[1][0]);
        // // 删除最后一行
        // array_pop($htmlLines);
        // // 拼接回字符串
        // $contentCN = implode("\n", $htmlLines);

        // $contentCN = $converter->convert($contentCN);
        // $contentCN = strip_tags($contentCN);

        // $htmlLines = explode("\n", $contentCN);
        // array_shift($htmlLines);
        // array_shift($htmlLines);
        // $contentCN = implode("\n", $htmlLines);
        // $contentCN = str_replace('**', ' ## ', $contentCN);

        // $htmlLines = explode("\n", $contentCN);
        // foreach ($htmlLines as $key => $htmlLine) {
        //     $htmlLines[$key] = trim($htmlLine);
        // }
        // $contentCN = implode("\n\n", $htmlLines);

        

        // $pattern = '/<a name="#EN">([\s\S]*?)<\/a><\/a>/';
        // // 正则匹配
        // preg_match_all($pattern, $html, $matches);
        // $htmlLines = explode("\n", $matches[1][0]);
        // // 删除最后一行
        // array_pop($htmlLines);
        // // 拼接回字符串
        // $contentEN = implode("\n", $htmlLines);
        // $contentEN = $converter->convert($contentEN);
        // $contentEN = strip_tags($contentEN);
        // $contentEN = str_replace('**', ' ## ', $contentEN);

        // $htmlLines = explode("\n", $contentEN);
        // foreach ($htmlLines as $key => $htmlLine) {
        //     $htmlLines[$key] = trim($htmlLine);
        // }
        // $contentEN = implode("\n\n", $htmlLines);


        // $date = $date->format('md');
        // // $date = '0229';
        // // if (!Storage::exists($directory)) {
        // //    Storage::makeDirectory($directory);
        // // }
        // $filePath = "public/MBC/CN/{$date}.md";
        // Storage::put($filePath, $contentCN);
        // $filePath = "public/MBC/EN/{$date}.md";
        // Storage::put($filePath, $contentEN);
        // // return;
        

    }
}