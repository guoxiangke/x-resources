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
        $content = "=====生活智慧=====\n【601】无限飞行号\n【604】真爱世界\n【609】颜明放羊班\n【610】天路男行客\n【615】泛桌茶经班\n【617】相约香草山\n【678】肋骨咏叹调\n【612】书香园地\n【675】不孤单地球\n【674】深度泛桌派\n【668】岁月正好\n【613】i-Radio爱广播\n【614】今夜心未眠\n【657】天使夜未眠\n【611】零点凡星\n【638】来点播FUN清单\n=====少儿家庭=====\n【605】一起成长吧\n【659】爆米花\n【602】欢乐下课趣\n【607】将将！百宝书开箱\n【664】小羊圣经故事\n【606】亲情不断电\n【660】我们的时间\n=====诗歌音乐=====\n【623】齐来颂扬\n【616】长夜的牵引\n【680】午的空间\n【608】一起弹唱吧！ \n=====生命成长=====\n【603】空中辅导\n【620】旷野吗哪\n【618】献上今天\n【627】故事‧心天空\n【619】拥抱每一天\n【698】馒头的对话\n【646】晨曦讲座\n【624】成主学堂 \n【630】主啊！995！ \n【640】这一刻，清心\n【628】空中崇拜\n【672】燃亮的一生\n【626】微声盼望\n【639】青春良伴\n=====圣经讲解=====\n【621】真道分解\n【622】圣言盛宴\n【676】穿越圣经\n【654】与神同行\n【681】卢文心底话\n【679】经典讲台\n【629】善牧良言\n【625】真理之光\n【648】天路导向\n=====课程训练=====\n【641】良友圣经学院（启航课程）\n【642】良院普及本科1\n【643】良院普及本科2\n【644】良院普及进深1\n【645】良院普及进深2\n【671】良院讲台\n=====其他语言=====\n【650】恩典与真理\n【651】爱在人间（云南话）\n【677】穿越圣经（粤）\n【649】天路导向（粤、英）\n【682】呢铺你点拣（粤）\n【683】方形西瓜（粤）\n【684】冷行（粤）\n【685】王籽，谢谢你！（粤）\n【686】陪你自由行（粤）\n【687】清唱清谈（粤）\n【631】好想健康（粤）\n【632】动漫查经员（粤）\n【633】美丽见证‧生命大画家（粤）\n【634】抬头望四季（粤）\n【635】Song Song声3（粤）\n【636】爱情几岁（粤）";
        // \n【637】音乐人……新（粤）（2024年1月1日起，逢星期一播出）
        // 【688】好好恋爱学堂（粤）\n
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
            637 => '',
            638 => 'fp',
            639 => 'yb',
            640 => "mpa",641 => "ltsnp",642 => "ltsdp1",643 => "ltsdp2",644 => "ltshdp1",645 => "ltshdp2",646 => "ds",647 => "",648 => "wa",649 => "cwa",650 => "gt",651 => "ynf",652 => "",653 => "",654 => "it",655 => '',656 => '',657 => "ka",658 => '',659 => "pc",660 => "ut",661 => '',662 => '',663 => '',664 => "cs",665 => '',666 => '',667 => '',668 => "ec",669 => '',670 => '',671 => "vp",672 => "ls",673 => '',674 => "pt",675 => "wc",676 => "ttb",677 => "cttb",678 => "sz",679 => "sc",680 => "gf",681 => "fh",
            682=>'caabg',
            683=>"caawm",
            684=>"caccp",
            685=>"caatp",
            686=>"caaco",
            687=>"cacac",
            688=>"",
            698 => "mn",699 => "", ]);

        if($code = array_search($keyword, $map)){
            $data = Cache::get($code, false);//cc
            $isNoCache = in_array($code, ['cc','dy','gf']);
            if($isNoCache || !$data){
                $json = Http::get('https://open.729ly.net/api/program/'.$code)->json();
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