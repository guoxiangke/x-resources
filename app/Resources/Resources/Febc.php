<?php

namespace App\Resources\Resources;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class Febc {
	public function _invoke($keyword) {

    if($keyword == 700){
        //\n如遇节目无法播放，请稍后再试
        $content = "【701】灵程真言\n【702】喜乐灵程\n【703】认识你真好 \n【704】真爱驻我家\n【705】尔道自建\n【706】旷野吗哪\n【707】真道分解\n【708】馒头的对话(周1-5)\n【709】拥抱每一天\n【710】天路男行客\n【711】肋骨咏叹调\n【712】颜明放羊班\n【713】真爱世界";
        return [
            'type' => 'text',
            'data' => ['content' => $content]
        ];
    }
    $res = [
        // https://d3pc7cwodb2h3p.cloudfront.net/all_tllczy_songs.json
        // https://depk9mke9ym92.cloudfront.net/tllczy/tllczy221203.mp3
        '701' =>[ // 1 - 7
            'title' => '灵程真言',
            'code' => "tllczy",
        ],
        '702' =>[ // 1-7
            'title' => '喜乐灵程',
            'code' => "tljd",
        ],
        '703' =>[//1-7
            'title' => '认识你真好',
            'code' => "vof",
        ],
        '704' =>[ // 1 - 7
            'title' => '真爱驻我家',
            'code' => "tltl",
        ],
        '705' =>[//1-7
            'title' => '尔道自建',
            'code' => "edzj",
        ],
        '706' =>[//ly
            'title' => '旷野吗哪',
            'code' => "mw",
        ],
        '707' =>[//ly
            'title' => '真道分解',
            'code' => "be",
        ],
        '708' =>[//1-5
            'title' => '馒头的对话',
            'code' => "mn",
        ],
        '709' =>[//6，7
            'title' => '豪放乐龄',
            'code' => "hfln",
        ],
        '710' =>[//1-5
            'title' => '天路男行客',
            'code' => "pm",
        ],
        '711' =>[//1-5
            'title' => '肋骨咏叹调',
            'code' => "sz",
        ],
        '712' =>[//6,7
            'title' => '颜明放羊班',
            'code' => "ym",
        ],
        '713' =>[//1-2
            'title' => '真爱世界',
            'code' => "tv",
        ],
    ];

    if(in_array($keyword, array_keys($res))){
        $res = $res[$keyword];
        // https://febc.blob.core.windows.net/jso/ single_vof_songs.json
        $response = Http::get(config('services.febc.all_json_domain')."single_{$res['code']}_songs.json");
        $json =$response->json();
        $jdata = last($json);
        $dateStr = $jdata['time'];

        $title = "【{$keyword}】{$res['title']}-" . $dateStr;
        $code = $res['code'];
        $data = [
            'type' => 'music',
            "data"=> [
                "url" => $jdata['path'],
                'title' => $title,
                'description' => $jdata['title'],
                'image' => $jdata['image'],
            ],
        ];
        // 节目文本
        if(false){
            $codeStr = "/{$mp3Code}/$mp3Code" . $dateStr;
            $addition = [
                'type' => 'link',
                'data' => [
                    'image' => $jdata['image'],
                    "url" => "https://dxd6tocqg9xyb.cloudfront.net/html{$codeStr}.html",
                    'title' => $title,
                    'description' => '节目文本-'. $jdata['title'],
                ],
            ];
        }

        $data['statistics'] = [
            'metric' => class_basename(__CLASS__),
            "keyword" => $code,
        ];
        return $data;
    }
    return null;
	}
}
