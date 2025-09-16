<?php

namespace App\Resources\Resources;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use voku\helper\HtmlDomParser;
use GuzzleHttp\Client;

final class PastorJiang{
    public function _invoke($keyword)
    {
        if ($keyword == 888) {
            $who = "@jiangyongliu";
            $baseUrl = "https://pub-3813a5d14cba4eaeb297a0dba302143c.r2.dev/youtube_channels/latest_update";
            
            // 获取videos和streams数据
            $videosData = $this->fetchVideoData($baseUrl, $who, 'videos', $keyword);
            $streamsData = $this->fetchVideoData($baseUrl, $who, 'streams', 887);
            
            // 构建最终结构
            $videosData['addition']['addition'] = $streamsData;
            
            return $videosData;
        }
    }


    private function fetchVideoData($baseUrl, $who, $type, $keywordId) {
        // 获取JSON数据
        $url = "{$baseUrl}/{$who}_{$type}.json";
        $json = Http::get($url)->json();
        $vid = $json['id'];
        
        // 构建视频数据
        $videoData = $this->buildMediaData('link', $who, $vid, $json, $keywordId, 'video');
        
        // 构建音频数据
        $audioData = $this->buildMediaData('music', $who, $vid, $json, $keywordId, 'audio');
        
        // 组合数据结构
        $audioData['addition'] = $videoData;
        
        return $audioData;
    }

    private function buildMediaData($type, $who, $vid, $json, $keywordId, $statisticsType) {
        $isVideo = ($type === 'link');
        $urlEnv = $isVideo ? 'R2_SHARE_VIDEO' : 'R2_SHARE_AUDIO';
        $extension = $isVideo ? 'mp4' : 'm4a';
        
        return [
            'type' => $type,
            'data' => [
                'url' => env($urlEnv) . "/{$who}/{$vid}.{$extension}",
                'title' => $json['title'],
                'description' => "江涌流牧师的频道",
                'vid' => $vid,
                'image' => $json['thumbnails'][3]['url']
            ],
            'statistics' => [
                'metric' => class_basename(__CLASS__),
                'keyword' => $keywordId,
                'type' => $statisticsType
            ]
        ];
    }
}
