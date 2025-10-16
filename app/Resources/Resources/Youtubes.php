<?php

namespace App\Resources\Resources;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Madcoda\Youtube\Facades\Youtube;
final class Youtubes{
    // https://youtu.be/Y8X8JXNbBbI
    // https://www.youtube.com/watch?v=Y8X8JXNbBbI&list=RDwwpK3p4heEM&index=2
    // https://www.youtube.com/embed/JHdB1dYAteA?si=b2G7vdIdVbNQzH4X
    // https://youtu.be/Y8X8JXNbBbI?si=iglGPQxxloV8MpWI
    // https://www.youtube.com/watch?v=Y8X8JXNbBbI&list=RDwwpK3p4heEM&index=2
    // https://m.youtube.com/shorts/6vsRGl4IsMs?si=QBA11T3v1Sl6ijxo
    // https://www.youtube.com/shorts/6vsRGl4IsMs?app=desktop
    // https://www.youtube.com/watch?app=desktop&v=wk2JytIsAz8&si=3CXuY_JFQsMkj_ku
    // https://studio.youtube.com/video/zG0eTyLMxlM/edit
    // https://www.youtube.com/live/AarqK67LPfE?si=ORsJMYYsxyQYTMB8

	public function _invoke($keyword)
	{
        if(preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|video\/|shorts\/|live\/))([\w\-]{11,})/', $keyword, $matches)){
            $vid = $matches[1];
            $videoInfo = Youtube::getVideoInfo($vid);
            $title = $videoInfo->snippet->title;
            $description = $videoInfo->snippet->description;
            $image = "https://i.ytimg.com/vi/{$vid}/maxresdefault.jpg";
            $thumbnails = $videoInfo->snippet->thumbnails->medium->url ?? $image;
            $mp4 = env('R2_SHARE_VIDEO')."/tmpshare/{$vid}.mp4";
            $mp3 = env('R2_SHARE_AUDIO')."/tmpshare/{$vid}.m4a";
            $addition = [
                'type' => 'link',
                "data"=> [
                    "url" => $mp4,
                    'title' => $title,
                    'description' => $description,
                    'image' => $thumbnails,
                ],
                'statistics' => [
                    'metric' => 'youtube',
                    "keyword" => $vid,
                    "type" => 'video',
                ],
            ];
            $data = [
                'type' => 'music',
                "data"=> [
                    "url" => $mp3,
                    'title' => $title,
                    'description' => $description,
                    'image' => $thumbnails,
                ],
                'statistics' => [
                    'metric' => 'youtube',
                    "keyword" => $vid,
                    "type" => 'audio',
                ],
                'addition'=>$addition,
            ];
            $key = 'youtube-vids-need-download';
            $value = Cache::get($key,[]);
            array_unshift($value, $vid);
            Cache::put($key, array_unique($value));
            return $data;
        }
        return null;
	}
}