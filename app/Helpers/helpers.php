<?php
function display_format($number,$digit=8,$format=NULL) {
        if($format ==""){
            $twocoin = sprintf('%.'.$digit.'f',$number);
        }elseif($format==0){
            $twocoin = number_format($number,$digit);
        }else{
            $twocoin = number_format($number,$digit,",",".");
        }
        return $twocoin;
}
function ncAdd($value1,$value2,$digit=8) {
	$value = bcadd(sprintf('%.10f',$value1), sprintf('%.10f',$value2), $digit);
	return $value;
}
function ncSub($value1,$value2,$digit=8) {
	$value = bcsub(sprintf('%.10f',$value1), sprintf('%.10f',$value2), $digit);
	return $value;
}
function ncMul($value1,$value2,$digit=8) {
	$value = bcmul(sprintf('%.10f',$value1), sprintf('%.10f',$value2), $digit);
	return $value;
}
function ncDiv($value1,$value2,$digit=8) {
	$value = bcdiv(sprintf('%.10f',$value1), sprintf('%.10f',$value2), $digit);
	return $value;
}
 function TransactionString($length = 60) {
	$str = substr(hash('sha256', mt_rand() . microtime()), 0, $length);
	return $str;
}
function seoUrl($string) {
    //Lower case everything
    $string = strtolower($string);
    //Make alphanumeric (removes all other characters)
    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
    //Clean up multiple dashes or whitespaces
    $string = preg_replace("/[\s-]+/", " ", $string);
    //Convert whitespaces and underscore to dash
    $string = preg_replace("/[\s_]/", "-", $string);
    return $string;
}
function round_down($number, $precision = 2) {
    $precision=$precision+1;
    $fig = (int) str_pad('1', $precision, '0');
    return (floor($number * $fig) / $fig);
}
