<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;


use Illuminate\Support\Arr;
use Madcoda\Youtube\Facades\Youtube;
use App\Helper;
use YouTube\YouTubeDownloader;
use App\Http\Controllers\YageController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/resources/{keyword}', function (Request $request, $keyword){
    $resource = app("App\Resources\Resources");
    return $request->query()?$resource->_invoke($keyword . '?' . http_build_query($request->query())):$resource->_invoke($keyword);
})->where('keyword', '.*');


Route::get('/yage/{keyword}', [YageController::class, 'json']);


// 百度茶室
Route::get('/set/baidutea/sendIsOn', function (){
    $cacheKey = '805';
    $data = date('Y-m-d H:i:s',strtotime('tomorrow'));
    Cache::put($cacheKey, $data, strtotime('tomorrow') - time());
    $data = Cache::get($cacheKey, false);
    return [$data];
});
// 主日讲道
Route::get('/set/fwdlist/sendIsOn', function (){
    $cacheKey = '806';
    $data = date('Y-m-d H:i:s',strtotime('tomorrow'));
    Cache::put($cacheKey, $data, strtotime('tomorrow') - time());
    $data = Cache::get($cacheKey, false);
    return [$data];
});

Route::get('/youtube/search-last-by-channel/{channelId}/{keyword}', function ($channelId,$keyword){
    $all = Youtube::searchChannelVideos($keyword, $channelId, $limit=1, $order='date');
    return collect($all)->first();
});

Route::get('/youtube/{vid}', function ($vid){
   return  $video = Youtube::getVideoInfo($vid);
});

// TODO test
Route::get('/youtube/get-last-by-playlist/{playlistId}', function ($playListId){
    $all = Helper::get_all_items_by_youtube_playlist_id($playListId);
    return collect($all)->last();
});

Route::get('/cache/clear/all', function (){
    Cache::flush();
    return ['All Cache cleared!'=>'success'];
});

Route::get('/cache/clear/{key}', function ($key){
    $cacheKey = "xbot.700.$key";
    $before = Cache::get($cacheKey);
    Cache::flush();
    $after = Cache::get($cacheKey);
    return ['before'=>$before,'after'=>$after];
});

Route::get('/mi-music', function (){
  $now = date('Y-m-d');
  $query = <<<GQL
    {
      ly_items(play_at: "$now 00:00:00") {
        data {
          id
          link: path
          program: ly_meta {
            name
          }
        }
      }
    }
  GQL;
    $res = Http::withHeaders([
        'Content-Type' => 'application/json',
    ])->post('https://pms.lyadmin.net/graphql',[
            'query' => $query
    ]);
    $name = "今日节目";
    $musics = [];
    foreach ($res['data']['ly_items']['data'] as $key => $item) {
        $musics[] = ['name'=> $item['program']['name'],'url'=>$item['link']];
    }
    $playlist[] = compact('name','musics');


    $name = "广播电台";
    $musics = [

    ];
    $playlist[] = [
        "name" => "广播电台",
        "musics" => [
            [
                "name" => "圣经广播网",
                "type" => "radio",
                "url" => "https://audio-edge-2a8hd.sfo.he.radiomast.io/ce298b32-8776-4192-9900-092f44b63e7f"
            ],
            [
                "name" => "同行频道",
                "type" => "radio",
                "url" => "https://ly729.out.airtime.pro/ly729_a"
            ],
            [
                "name" => "北京音乐广播",
                "type" => "radio",
                "url" => "http://ls.qingting.fm/live/332.m3u8"
            ],
            [
                "name" => "CRI轻松调频",
                "type" => "radio",
                "url" => "http://sk.cri.cn/915.m3u8"
            ],
            [
                "name" => "CRI华语环球",
                "type" => "radio",
                "url" => "http://sk.cri.cn/hyhq.m3u8"
            ],
            [
                "name" => "CRI环球资讯",
                "type" => "radio",
                "url" => "http://sk.cri.cn/905.m3u8"
            ],
            [
                "name" => "CRI英语资讯",
                "type" => "radio",
                "url" => "http://sk.cri.cn/am846.m3u8"
            ],
            [
                "name" => "CRI世界华声",
                "type" => "radio",
                "url" => "http://sk.cri.cn/hxfh.m3u8"
            ],
            [
                "name" => "CNR经济之声",
                "type" => "radio",
                "url" => "http://ngcdn002.cnr.cn/live/jjzs/index.m3u8"
            ],
            [
                "name" => "CNR经典音乐",
                "type" => "radio",
                "url" => "http://ngcdn004.cnr.cn/live/dszs/index.m3u8"
            ],
            [
                "name" => "CNR阅读之声",
                "type" => "radio",
                "url" => "http://ngcdn014.cnr.cn/live/ylgb/index.m3u8"
            ],
            [
                "name" => "CNR音乐之声",
                "type" => "radio",
                "url" => "http://ngcdn003.cnr.cn/live/yyzs/index.m3u8"
            ],
            [
                "name" => "天籁古典",
                "type" => "radio",
                "url" => "http://stream3.hndt.com/now/MdOpB4zP/playlist.m3u8"
            ],
            [
                "name" => "天籁国风",
                "type" => "radio",
                "url" => "http://play-radio-stream3.hndt.com/now/UzPxbRaT/playlist.m3u8"
            ],
            [
                "name" => "天籁之音",
                "type" => "radio",
                "url" => "http://play-radio-stream3.hndt.com/now/JxkyN5mR/playlist.m3u8"
            ],
            [
                "name" => "有声文摘",
                "type" => "radio",
                "url" => "http://stream3.hndt.com/now/WNoVfBcQ/playlist.m3u8"
            ],
            [
                "name" => "80后音悦台",
                "type" => "radio",
                "url" => "http://stream3.hndt.com/now/SFZeH2cb/playlist.m3u8"
            ],
            [
                "name" => "经典FM",
                "type" => "radio",
                "url" => "http://stream3.hndt.com/now/C5NvUpwy/playlist.m3u8"
            ],
            [
                "name" => "美国之音",
                "type" => "radio",
                "url" => "http://voa-ingest.akamaized.net/hls/live/2035206/151_124L/playlist.m3u8"
            ],
            [
                "name" => "NPR News",
                "type" => "radio",
                "url" => "https://nprdmcoitunes.akamaized.net/hls/live/2034276/itls/playlist.m3u8"
            ],
            [
                "name" => "VOA环球英语",
                "type" => "radio",
                "url" => "http://voa-ingest.akamaized.net/hls/live/2035200/161_352R/playlist.m3u8"
            ],
            [
                "name" => "BBC News",
                "type" => "radio",
                "url" => "http://as-hls-ww-live.akamaized.net/pool_904/live/ww/bbc_world_service/bbc_world_service.isml/bbc_world_service-audio=320000.m3u8"
            ],
            [
                "name" => "RFI",
                "type" => "radio",
                "url" => "https://rfienchinois64k.ice.infomaniak.ch/rfienchinois-64.mp3"
            ],
            [
                "name" => "News Radio",
                "type" => "radio",
                "url" => "http://playoutonestreaming.com:8008/stream"
            ],
            [
                "name" => "BBC Radio",
                "type" => "radio",
                "url" => "http://as-hls-ww-live.akamaized.net/pool_904/live/ww/bbc_radio_one/bbc_radio_one.isml/bbc_radio_one-audio=320000.m3u8"
            ]
        ]
    ];

    // 定义数字到中文的映射
    $chineseNumbers = [
        '1' => '一', '2' => '二', '3' => '三', '4' => '四', '5' => '五', 
        '6' => '六', '7' => '七', '8' => '八', '9' => '九', '10' => '十', 
        '11' => '十一', '12' => '十二', '13' => '十三', '14' => '十四',
        '15' => '十五', '16' => '十六', '17' => '十七', '18' => '十八',
        '19' => '十九', '20' => '二十', '21' => '二十一', '22' => '二十二',
        '23' => '二十三', '24' => '二十四', '25' => '二十五', '26' => '二十六',
        '27' => '二十七', '28' => '二十八', '29' => '二十九', '30' => '三十',
        '31' => '三十一'
    ];

    foreach (['cc','mw','mn','bc','pc','it','ttb','cw','gg','ws'] as $code) {
        $hasManyType = "ly_items";
        $programType = "ly_meta";
        $query = <<<GQL
            {
              data:ly_meta_by_code(code: "$code") {
                id
                name
                code
                cover
                description
                begin_at
                end_at
                remark
                category
                ly_items: $hasManyType {
                  data {
                    id
                    alias
                    description
                    play_at
                    path: novaMp3Path
                    link: path
                    program: $programType {
                      id
                      name
                      code
                    }
                  }
                  paginatorInfo {
                    total
                    currentPage
                    hasMorePages
                  }
                }
              }
            }
        GQL;
        $res = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://pms.lyadmin.net/graphql',[
                'query' => $query
        ]);
        // return $res;
        $name = $res['data']['data']['name'];
        $musics = [];

        foreach ($res['data']['data']['ly_items']['data'] as $key => $item) {

            $dataStr = $item['play_at'];
            $formattedDate = date('n月j号的', strtotime($dataStr)); // 生成 "9月13日"

                        // 使用正则表达式来匹配月份和日期部分，并进行替换
            $formattedDate = preg_replace_callback('/(\d+)/', function ($matches) use ($chineseNumbers) {
                return $chineseNumbers[$matches[0]];
            }, $formattedDate);

            // echo $formattedDate; // 输出: 九月十三日
            switch ($key) {
                case '0':
                    $formattedDate = "今天的";
                    break;
                case '1':
                    $formattedDate = "昨天的";
                    break;
                case '2':
                    $formattedDate = "前天的";
                    break;
                
                default:
                    break;
            }

            $musics[] = ['name'=> $formattedDate . $item['program']['name'],'url'=>$item['link']];
        }
        $playlist[] = compact('name','musics');
    }

    return response()->json($playlist, 200, [], JSON_UNESCAPED_UNICODE);

});
