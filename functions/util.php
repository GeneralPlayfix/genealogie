<?php
function mbUcfirst($str, $encode = 'UTF-8')
{
    $start = mb_strtoupper(mb_substr($str, 0, 1, $encode), $encode);
    $end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encode), $encode), $encode);
    return $str = $start . $end;
}

function Random()
{
    $array = [true, false];
    $arrayIndex = array_rand($array, 1);
    return $array[$arrayIndex];
}

function float_rand($Min, $Max, $round = 0)
{
    $array = [true, true, true,true, true, true, true, true, true, false, false,true,true, true, true, true];
    $arrayIndex = array_rand($array, 1);
    if ($array[$arrayIndex]) {
        //validate input
        if ($Min > $Max) {
            $min = $Max;
            $max = $Min;
        } else {
            $min = $Min;
            $max = $Max;
        }
        $randomfloat = $min + mt_rand() / mt_getrandmax() * ($max - $min);
        if ($round > 0)
            $randomfloat = round($randomfloat, $round);

        return $randomfloat;
    }else{
        $array = [0.01,0.02, 0.01, 0.01];
        $arrayIndex = array_rand($array, 1);
        return $array[$arrayIndex];
    }
}
