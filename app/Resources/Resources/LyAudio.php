<?php

namespace App\Resources\Resources;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use voku\helper\HtmlDomParser;

final class LyAudio{
    public function _invoke($keyword) {
    //3位数关键字xxx
    // $offset = substr($oriKeyword, 3) ?: 0;
    $keyword = substr($keyword, 0, 3);
    
    if($keyword == 600){     
        $content = <<<EOD
=====生活智慧=====
【612】书香园地
【610】天路男行客
【678】肋骨咏叹调
【604】真爱世界
【675】不孤单地球
【674】深度泛桌派
【668】岁月正好
【638】来点播FUN清单
【613】i-Radio爱广播
【614】今夜心未眠
【657】天使夜未眠
【611】零点凡星
=====少儿家庭=====
【605】一起成长吧！
【659】爆米花
【602】欢乐下课趣
【607】将将！百宝书开箱
【664】小羊圣经故事
【606】亲情不断电
【660】我们的时间
=====诗歌音乐=====
【623】齐来颂扬
【616】长夜的牵引
【680】午的空间
【608】一起弹唱吧！
=====生命成长=====
【601】无限飞行号
【603】空中辅导
【620】旷野吗哪
【618】献上今天
【627】故事‧心天空
【619】拥抱每一天
【698】馒头的对话
【639】青春良伴
【646】晨曦讲座
【624】成主学堂
【630】主啊！995！
【640】这一刻，清心
【628】空中崇拜
【672】燃亮的一生
【609】颜明放羊班
【626】微声盼望
=====圣经讲解=====
【621】真道分解
【622】圣言盛宴
【676】穿越圣经
【654】与神同行
【681】卢文心底话
【679】经典讲台
【629】善牧良言
【615】泛桌茶经班
【617】相约香草山
【625】真理之光
【648】天路导向
=====课程训练=====
【641】良友圣经学院（启航课程）
【642】良院普及本科第一套
【643】良院普及本科第二套
【644】良院普及进深第一套
【645】良院普及进深第二套
【671】良院讲台
=====其他语言=====
【650】恩典与真理
【651】爱在人间（云南话）
【677】穿越圣经（粤）
【649】天路导向（粤、英）
【637】旷野吗哪（客家语）
【647】旷野吗哪（闽南语）
【688】窗内窗外（粤语）
【689】初职强检（粤语）
【690】让爱跑动（粤语）
【691】情心树窿（粤语）
【692】跑赢障碍赛（粤语）
【693】树叶在唱歌（粤语）
【694】恋爱不能Sooo Stupid（粤语）
EOD;
        return [
            'type' => 'text',
            'data' => ['content' => $content]
        ];
    }
    if($keyword>600 && $keyword<700){
        $map = array_flip([601 => "iba",602 => 'fa',603 => "cc",604 => "tv",605 => "gg",606 => "up",607 => 'bx',608 => 'pp',609 => 'ym',610 => "pm",611 => "sa",612 => "bc",613 => "ir",614 => "rt",615 => 'fd',616 => "ws",617 => 'hr',618 => "dy",619 => "ee",620 => "mw",621 => "be",622 => "bs",623 => "cw",624 => "dr",625 => "th",626 => "wr",627 => "yy",628 => "aw",629 => "yp",630 => "mg",
            631 => 'cfbwh',
            632 => "cedna",
            633 => 'cfcbp',
            634 => "cfbls",
            635 => 'cfbsg',
            636 => 'cgaal',
            637 => 'tmw',
            638 => 'fp',
            639 => 'yb',
            640 => "mpa",641 => "ltsnp",642 => "ltsdp1",643 => "ltsdp2",644 => "ltshdp1",645 => "ltshdp2",646 => "ds",647 => "hmw",648 => "wa",649 => "cwa",650 => "gt",651 => "ynf",652 => "",653 => "",654 => "it",655 => '',656 => '',657 => "ka",658 => '',659 => "pc",660 => "ut",661 => '',662 => '',663 => '',664 => "cs",665 => '',666 => '',667 => '',668 => "ec",669 => '',670 => '',671 => "vp",672 => "ls",673 => '',674 => "pt",675 => "wc",676 => "ttb",677 => "cttb",678 => "sz",679 => "sc",680 => "gf",681 => "fh",
            682=>'caabg',
            683=>"caawm",
            684=>"caccp",
            685=>"caatp",
            686=>"caaco",
            687=>"cacac",
            688=>"cfabb",
            689=>"cedfj",
            690=>"cgdlr",
            691=>"chaet",
            692=>"cadoh",
            693=>"caasl",
            694=>"cadls",

            698 => "mn",699 => "", ]);

        if($code = array_search($keyword, $map)){
            $data = Cache::get($code, false);//cc
            $isNoCache = in_array($code, ['cc','dy','gf']);
            if($isNoCache || !$data){
                $json = Http::get('https://open.729ly.net/api/program/'.$code)->json();
                if(empty($json['data'])) return;
                $item = $json['data'][0];
                $data =[
                    'type' => 'music',
                    'data' => [
                        "url" => $item['link'],
                        'title' => "【{$keyword}】".str_replace('圣经','SJ',$item['program_name']).'-'.$item['play_at'],
                        'description' => str_replace('教会','JH',$item['description']),
                        'image' => "https://txly2.net/images/program_banners/{$code}_prog_banner_sq.png",
                    ],
                ];
                $data['statistics'] = [
                    'metric' => class_basename(__CLASS__),
                    "keyword" => $code,
                ];
                // Carbon::tomorrow()->diffInSeconds(Carbon::now());
                if(!$isNoCache) Cache::put($code, $data, strtotime('tomorrow') - time());
            }
            return $data;
        }
    }
  }
}