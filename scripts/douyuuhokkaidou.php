<?php
include_once('simple/simple_html_dom.php');

ini_set('user_agent','Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
header("Content-Type: text/html; charset=UTF-8");


error_reporting(0);
ini_set('display_errors', 0);
set_time_limit(0);


$companies = 642;


$successfulcompanies = 0; 


for ($i = 0; $i <= $companies; $i = $i+30) {

$request = array(  //POSTによるリクエストが必要のため内容を設定
'http' => array(
    'method' => 'POST',
    'content' => http_build_query(array(
        'dummy' => '%CD%AD%CA%FE%BC%AB%B1%F3%CA%FD%CD%E8',
		'pref[]' => '01',
		'cate1' => '4',
		'offset' => ''.$i.''
    )),
),
        'ssl' => array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        )
);

$context = stream_context_create($request);

$html = file_get_html('https://www.sys.doyu.jp/member/search.php', false, $context);
if ($html !== false) {
$tables = $html->find('table');
$table = $tables[4];

foreach ($table->find('a') as $companyinfolink) {
$infopage = $companyinfolink->href;
$infopage = 'https://www.sys.doyu.jp/member/'.$infopage.'';
$infopagehtml = file_get_html($infopage, false, $context);
$infotds = $infopagehtml->find('td.text14');
$address = $infotds[4];
//if(preg_match('(札幌|千歳|北広島|小樽|石狩)', $address) === 1) {
	$urls = $infopagehtml->find('a');
	$siteurl = $urls[2]->href;
		if (strlen($siteurl) >= 1) { //URLが存在する場合
			//$sitehtml = file_get_html($siteurl); //企業の公式サイトを抽出して
		//if(preg_match('(外国|海外|輸入|輸出|英語|English|簡体字|繁体字|中文|世界各国)', $sitehtml) === 1) { 
			$successfulcompanies++;
			$cat = $infotds[1];
			$namearray = $infopagehtml->find('td.text24');
			$name = $namearray[0];
			
			echo ''.$successfulcompanies.'. '.$name.'<br />'.$siteurl.'<br />'.$address.'<br />'.$cat.'<br /><br />';
		//}
		//}
	
}
}
}
}

?>