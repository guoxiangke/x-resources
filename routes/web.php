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