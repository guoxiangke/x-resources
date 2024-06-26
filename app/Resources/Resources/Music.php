<?php

namespace App\Resources\Resources;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Metowolf\Meting;

final class Music{
	public function _invoke($keyword)
	{
        $triggerKeywords = ["点歌", "我想听", "想听", "来一首", "来首"];
        if(Str::startsWith($keyword, $triggerKeywords)){
            $name = str_replace(
                $triggerKeywords,
                ['', '', '', '', ''],
                $keyword
            );
            $name = trim($name);
            $cacheKey = "xbot.keyword.163.{$name}";
            $data = Cache::get($cacheKey, false);
            if(!$data) {
                $api = new Meting('netease');
                // $api->cookie("");
                $data = json_decode($api->format(true)->search($name), 1);
                $data = json_decode($api->format(true)->url($data[0]['id']), 1);
                
                // $mp3 = "http://music.163.com/song/media/outer/url?id={$data[0]['id']}.mp3";

                $data =[
                    "url" => $data['url'],
                    'title' => $name,
                    'description' => "来自网易云音乐",
                ];
                Cache::put($cacheKey, $data);
            }
            return [
                "type" => "music",
                "data" => $data,
            ];
        }
        return null;
  }
}
