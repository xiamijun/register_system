<?php
//验证码

function random_text($count,$rm_similar=false){
    $chars=array_flip(array_merge(range(0,9),range('A','Z')));

    //去除相似字符
    if ($rm_similar){
        unset($chars[0],$chars[1],$chars[2],$chars[5],$chars[8],$chars['B'],$chars['I'],$chars['O'],$chars['Q'],$chars['S'],$chars['U'],$chars['V'],$chars['Z']);
    }

    //产生随机字符
    for ($i=0,$text='';$i<$count;$i++){
        $text.=array_rand($chars);
    }

    return $text;
}
