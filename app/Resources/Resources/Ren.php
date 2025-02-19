<?php

namespace App\Resources\Resources;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use voku\helper\HtmlDomParser;
use GuzzleHttp\Client;
use App\Helper;

use Symfony\Component\HttpClient\Psr18Client;
final class Ren{
    public function _invoke($keyword)
    {
        $who = 'LFC';
        //3位数关键字xxx
        $offset = substr($keyword, 3) ?: 0;
        $keyword = substr($keyword, 0, 3);
        # 任牧师短视频(每日/持续更新 video only)
        $playlistTitles = [
            '813'=>  [
                'shorts' => true,
                'title'=>"每日經文",
                'alias' => 'mrjw',
                'id'=>"PL942JJGZpDIehJuBSaLIe_-irOegGih9M"
            ],
            '815'=>  [
                'shorts' => true,
                'title'=>"每日詩歌",
                'alias' => 'mrsg',
                'id'=>"PL942JJGZpDIeF0f51w_ZxoYOAmfXOlEiR"
            ],
            '816'=>  [
                'shorts' => true,
                'title'=>"每日禱告",
                'alias' => 'mrdg',
                'id'=>"PL942JJGZpDIfIollTxvXVxnR-b8HG0k6n"
            ],
            '817'=>  [
                'title'=>"喻道故事",
                'id'=>"PL942JJGZpDIeu1QnggkoXQ8lymaMbA3gT"
            ],
            '818'=>  [
                'shorts' => true,//both
                'title'=>"美國政治小知識",
                'id'=>"PL942JJGZpDIdSluGl2wRHwoiZ43ucHYXw"
            ],
            # (持续更新)
            # $playlistTitles=[
            #     "節日特別節目":"PL942JJGZpDIeHgKvbi21itY15k3MTyeWL",
            #     "聖經故事":"PL942JJGZpDIfNcwnO1aKqdU8VoXQqBZoN",
            #     "我們看世界談話節目":"PL942JJGZpDIddB74WZ7X3CZQUJSpUealR",
            # ];
            '819'=>  [
                'shorts' => false,
                'title'=>"節日特別節目",
                'id'=>"PL942JJGZpDIeHgKvbi21itY15k3MTyeWL"
            ],
            '820'=>  [
                'shorts' => false,
                'title'=>"聖經故事",
                'id'=>"PL942JJGZpDIfNcwnO1aKqdU8VoXQqBZoN"
            ],
            '821'=>  [
                'shorts' => false,
                'title'=>"我們看世界談話節目",
                'id'=>"PL942JJGZpDIddB74WZ7X3CZQUJSpUealR",
                'order' => 'asc'
            ],
            '822'=>  [
                'shorts' => false,
                'title'=>"傳奇基督徒的二三事",
                'id'=>"PL942JJGZpDIdm1vCiIjrj6pF5ZcwVVcw-"
            ],
            '823'=>  [
                'shorts' => false,
                'title'=>"活力見證",
                'id'=>"PL942JJGZpDIcGHU4XTiO3cLoUibfC05gf"
            ],
        ];
        if($keyword >= '813' && $keyword <= '829' && isset($playlistTitles[$keyword])){
            $playListId = $playlistTitles[$keyword]['id'];
            $playlistTitle = $playlistTitles[$keyword]['title'];
            $isShorts = $playlistTitles[$keyword]['shorts'];
            $order = $playlistTitles[$keyword]['order']??false;
            $all = Helper::get_all_items_by_youtube_playlist_id($playListId);

            $item = $all->first();
            if($order) $item = $all->last();
            $title = $item->snippet->title;
            $description = $item->snippet->description;
            
            // $title = str_replace($description,'',$title);
            // $title = explode('-',$title)[1];
            // $title = str_replace(' | ','',$title);

            $vid = $item->snippet->resourceId->videoId;
            $playlistTitleUrl = urlencode($playlistTitle);
            $url = env('R2_SHARE_AUDIO') . "/@{$who}/{$vid}.mp4";

            $image = "https://i.ytimg.com/vi/{$vid}/sddefault.jpg";
            // $image = "https://images.simai.life/images/2024/09/8d1078f5f65110e2379ae6ad42397728.JPG";
            // https://wsrv.nl/?url=

            if($keyword == '813'){
                $parts = explode('|', $title);
                $title = trim(implode('|', array_slice($parts, 0, -1)));
                // $description =  trim(last($parts));
            }
            if(Str::startsWith($playlistTitle, '每日')){
                $title = "$title";
                $description = '@LFC活力生命 每日更新';
            }else{
                $title = "【{$keyword}】$playlistTitle $title";
            }
            $addition = [];
            // $keyword >= '813' && $keyword <= '816'
            if(isset($playlistTitles[$keyword]['alias'])){
                $oriTitle = $title;
                $parts = explode('|', $title);
                $title = trim($parts[0]);
                // $txt = trim($parts[1]);
                $addition = [
                    'type' => 'text',
                    "data" => [
                        'content' => "【{$keyword}】$oriTitle",
                    ],
                ];

                $alias = $playlistTitles[$keyword]['alias'];
                $fileName = $alias.date('ymd').".mp4";
                $url = env('R2_SHARE_AUDIO') . "/Ren/{$alias}/{$fileName}";

                $title = "【{$keyword}】";
                $description = "@LFC活力生命";
            }

            $data = [
                'type' => 'link',
                'data' => [
                    "url" => $url,
                    'title' => $title,
                    'description' => $description,
                    'image' => $image,
                    'vid' => $vid,
                ],
                'addition'=>$addition,
            ];
            $data['statistics'] = [
                'metric' => class_basename(__CLASS__),
                "keyword" => $keyword,
                "type" => 'video',
            ];
            // return $data;
            if(!$isShorts){
                // Add audio
                $m4a = str_replace('.mp4','.m4a', $url);
                $addition = $data;
                $addition['type'] = 'music';
                $addition['data']['url']= $m4a;
                $addition['statistics'] = [
                    'metric' => class_basename(__CLASS__),
                    "keyword" => $keyword,
                    "type" => 'audio',
                ];
                $data['addition'] = $addition;
            }

            return $data;
        }

      

        # $playlistDoneTitles=[
        #     "约翰福音的密码读书会":"PL942JJGZpDIeQKLByNTHavZ-M2gMtMN8s",
        #     "禱告睡眠音樂":"PL942JJGZpDIc2If_FgYxragz02cJHwm-y",
        # ];
        $playlistTitles = [
            '830'=>  [
                'title'=>"约翰福音的密码读书会",
                'id'=>"PL942JJGZpDIeQKLByNTHavZ-M2gMtMN8s"
            ],
            '831'=>  [
                'title'=>"禱告睡眠音樂",
                'id'=>"PL942JJGZpDIc2If_FgYxragz02cJHwm-y"
            ],
        ];
        if($keyword >= '830' && $keyword <= '839'){
            $playListId = $playlistTitles[$keyword]['id'];
            $playlistTitle = $playlistTitles[$keyword]['title'];
            $all = Helper::get_all_items_by_youtube_playlist_id($playListId);
            
            $total = $all->count();
            $index = date('z')%$total;
            $item = $all[$index];
            
            if($offset){
                $index=(int)$offset % $total;//0-8
                if($index==0) $index=$total;
                $item = $all[--$index];
                $index++;
            }
            
            $vid = $item->snippet->resourceId->videoId;
            $title = $item->snippet->title;
            $description = $item->snippet->description;

            $playlistTitleUrl = urlencode($playlistTitle);
            $url = env('R2_SHARE_AUDIO') . "/@{$who}/{$vid}.mp4";
            $image = "https://i.ytimg.com/vi/{$vid}/sddefault.jpg";

            $data = [
                'type' => 'link',
                'data' => [
                    "url" => $url,
                    'title' => "【{$keyword}】$playlistTitle $title ",
                    'description' => "($index/$total) $description",
                    'image' => $image,
                    'vid' => $vid,
                ]
            ];
            $data['statistics'] = [
                'metric' => class_basename(__CLASS__),
                "keyword" => $keyword,
                "type" => 'video',
            ];

            // Add audio
            $m4a = str_replace('.mp4','.m4a', $url);
            $addition = $data;
            $addition['type'] = 'music';
            $addition['data']['url']= $m4a;
            $addition['statistics'] = [
                'metric' => class_basename(__CLASS__),
                "keyword" => $keyword,
                "type" => 'audio',
            ];
            $data['addition'] = $addition;
            return $data;
        }
        return null;
    }
}
