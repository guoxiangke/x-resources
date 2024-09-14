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

Route::get('/mi-music/ly/today', function (){
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
    return [compact('name','musics')];

});
