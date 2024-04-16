<?php

namespace App\Resources\Resources;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use voku\helper\HtmlDomParser;

final class OurDailyBread {
	public function _invoke($keyword) {
        $data = [];
        if($keyword == 'odb'){
          $now = now();
          $s1 = $now->format('Y/m');
          $s2 = $now->format('m') . "-". $now->format('d') . "-".$now->format('y');
        	$url = "https://dzxuyknqkmi1e.cloudfront.net/odb/{$s1}/odb-{$s2}.mp3";
          $title = "Our Daily Bread" . $s2;
          $data = [
            'type' => 'music',
            "data"=> [
                  "url" => $url,
                  'title' => $title,
                  'description' => "来自Our Daily Bread",
              ],
          ];
          $data['statistics'] = [
              'metric' => class_basename(__CLASS__),
              "keyword" => $keyword
          ];
          return $data;
        }
        return null;
	}
}
