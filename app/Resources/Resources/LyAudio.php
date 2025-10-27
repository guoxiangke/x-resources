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
    
// 【637】
// 【627】
// 【617】
// 【679】
// 【638】
// 【647】
    if($keyword == 600){     
        $content = <<<EOD
=====生活智慧=====
【612】书香园地
【610】天路男行客
【619】星之导航
【678】肋骨咏叹调
【604】真爱世界
【675】不孤单地球
【674】深度泛桌派
【668】岁月正好
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
【680】午的空间
【608】一起弹唱吧！
【616】与你有乐
=====生命成长=====
【601】无限飞行号
【603】空中辅导
【620】旷野吗哪
【617】牧者抱抱团
【618】献上今天
【698】馒头的对话
【639】青春良伴
【646】晨曦讲座
【624】成主学堂
【630】主啊！995！
【640】这一刻，清心
【628】空中崇拜
【652】教会年历‧家庭崇拜
【672】燃亮的一生
【609】颜明放羊班
【626】微声盼望
=====圣经讲解=====
【621】真道分解
【622】圣言盛宴
【676】穿越圣经
【654】与神同行
【681】卢文心底话
【629】善牧良言
【615】泛桌茶经班
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
【649】天路导向（粤、英）
【637】旷野吗哪（客家语）
【653】旷野吗哪（粤语）
EOD;
        return [
            'type' => 'text',
            'data' => ['content' => $content]
        ];
    }
    if($keyword>600 && $keyword<700){
        $map = array_flip([601 => "iba",602 => 'fa',603 => "cc",604 => "tv",605 => "gg",606 => "up",607 => 'bx',608 => 'pp',609 => 'ym',610 => "pm",611 => "sa",612 => "bc",613 => "ir",614 => "rt",615 => 'fd',616 => "jr",617 => 'pk',618 => "dy",619 => "rn",620 => "mw",621 => "be",622 => "bs",623 => "",624 => "dr",625 => "th",626 => "wr",627 => "",628 => "aw",629 => "yp",630 => "mg",
            631 => 'cfbwh',
            632 => "cedna",
            633 => 'cfcbp',
            634 => "cfbls",
            635 => 'cfbsg',
            636 => 'cgaal',
            637 => 'hmw',
            638 => '',
            639 => 'yb',
            640 => "mpa",641 => "ltsnp",642 => "ltstpa1",643 => "ltstpa2",644 => "ltstpb1",645 => "ltstpb2",646 => "ds",647 => "",648 => "wa",649 => "cwa",650 => "gt",651 => "ynf",652 => "gw",653 => "cmw",654 => "it",655 => '',656 => '',657 => "ka",658 => '',659 => "pc",660 => "ut",661 => '',662 => '',663 => '',664 => "cs",665 => '',666 => '',667 => '',668 => "ec",669 => '',670 => '',671 => "vp",672 => "ls",673 => '',674 => "pt",675 => "wc",676 => "ttb",677 => "",678 => "sz",679 => "",680 => "gf",681 => "fh",
            682=>'caabg',
            683=>"caawm",
            684=>"caccp",
            685=>"caatp",
            686=>"caaco",
            687=>"cacac",
            688=>"",
            689=>"",
            690=>"",
            691=>"",
            692=>"",
            693=>"",
            694=>"",

            698 => "mn",699 => "", ]);

        if($code = array_search($keyword, $map)){
            $data = Cache::get($code, false);//cc
            $isNoCache = in_array($code, ['cc','dy','gf']);
            if($isNoCache || !$data){
                $json = Http::get('https://x.lydt.work/api/program/'.$code)->json();
                if(empty($json['data'])) return;
                $item = $json['data'][0];
                $url = $item['link'];

                $url = str_replace('https://x.lydt.work/storage/','https://d3ml8yyp1h3hy5.cloudfront.net/',$item['link']);
                if(in_array($keyword,[641,642,643,644,645]))
                    $url = str_replace('/ly/audio/','/lts/', $url);
                $data =[
                    'type' => 'music',
                    'data' => [
                        "url" => $url,
                        'title' => "【{$keyword}】".str_replace('圣经','SJ',$item['program']['name']).' '.substr($item['play_at'],5,5),
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