<?php
include_once('simple/simple_html_dom.php');

error_reporting(0);
ini_set('display_errors', 0);
set_time_limit(0);

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

$companies = 36; //登録企業数を設定

$successfulcompanies = 0;

for ($i = 0; $i <= $companies; $i = $i+4) { //ページごとの表示件数は4件となっているため、合計企業数を超えない限り、各繰り返しの開始時にiに４を足して、次のページを表示させる
$html = file_get_html('http://kaishaseikatsu.biz/result/?asf=i4&categories1=15&todoufuken=j01&employee=17&aso=0,'.$i.'');
if ($html !== false) {  //ページが存在する場合
foreach ($html->find('div.corpBox') as $company) { //企業情報を示す「corpBox」というクラスの付いたすべてのdivを配列として抽出し、ページに記載された各企業ごとに  
$names = $company->find('h3'); //企業名等を含むh3タグを選択
$name = get_string_between($names[0], 'g05 corp-name">', '<div class'); //関数get_string_betweenを使って企業名だけを抽出

$links = $company->find('a');  //corpBox内のリンクだけを配列として抽出
$link = $links[1]->href; //企業のサイトに当たる配列の2番目の要素のリンクを$link変数に設定


$entries = $company->find('td'); //corpBoxの中の表の行を配列として抽出
$address = $entries[3]->plaintext; //住所に当たる4番目の要素を出力
$cat1 = $entries[1]->plaintext;
$cat2 = $entries[2]->plaintext;

//if	(strpos($address, '宇都宮') !== false) { //本社が札幌か近い街のいずれかにある企業に絞って     if(preg_match('(札幌|千歳|北広島|小樽|石狩)', $address) === 1) {
//$sitehtml = file_get_html($link); //企業の公式サイトを検索して
		//if(preg_match('(外国|海外|輸入|輸出|英語|English|簡体字|繁体字|中文|世界各国)', $sitehtml) === 1) { 
		$successfulcompanies++;
		echo ''.$successfulcompanies.'. '.$name.'<br />'.$link.'<br />'.$address.'<br />'.$cat1.'<br />'.$cat2.'<br /><br />';
			
		//}
	//}
}


}
}
?>