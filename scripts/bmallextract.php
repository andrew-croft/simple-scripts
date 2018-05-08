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

$companies = 1151; //登録企業数を設定

$successfulcompanies = 0;


$request = array(
        'http' => array(
            'follow_location' => false
        ),
        'ssl' => array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );
	
$context = stream_context_create($request); //sslによるリクエストが必要なため

for ($i = 1; $i <= $companies; $i = $i+20) {
	mb_language('Japanese');
	$source = file_get_contents('https://www.b-mall.ne.jp/companysearch_areaR1_gyoshuA.aspx?b='.$i.'', false, $context);
	$source = mb_convert_encoding($source, 'utf8', 'auto'); //文字化け対策
	
	$html = str_get_html($source);
if ($html !== false) {
	$kensakukigyou = $html->find('table.SearchResult');
		foreach ($kensakukigyou[1]->find('a') as $infopage) { //注意 - 業種によってはプロモート企業欄ないため０となる場合がある
			$pagelink = $infopage->href;
			$pagelink = 'https://www.b-mall.ne.jp'.$pagelink.'';
					mb_language('Japanese');
					$source2 = file_get_contents($pagelink, false, $context);
					$source2 = mb_convert_encoding($source2, 'utf8', 'auto');
					$info = str_get_html($source2);
					$employees = get_string_between($info, '<th class="mojihidari mizuiroTensen">従業員数</th>  	<td class="mizuiroTensen">                      <span id="ctl00_ContentPlaceHolder1__companyTable_Label7">', '</span>');
					if (!empty($employees)) {
					$employees = rtrim($employees,"人");
							if ($employees >= 90) {
								$url = get_string_between($info, '<th class="mojihidari mizuiroTensen">ＵＲＬ</th>                  <td class="mizuiroTensen">','</td>');
								if	(strpos($url, '<a') !== false) {
									$name = get_string_between($info, '企業名 <span id="ctl00_ContentPlaceHolder1__companyTable_Label9">（カナ）</span>                  </th>                  <td class="mizuiroTensen">                      <span id="ctl00_ContentPlaceHolder1__companyTable_Label1">', '</span>');
									$address = get_string_between($info, '住所</th>                  <td class="mizuiroTensen">                      <div class="lay-l">                          ','</div>');
									$cat = get_string_between($info, 'class=\'gyoshu_row\'><span>','</li>');
									$cat2 = get_string_between($info, '業務内容</th>  	<td class="mizuiroTensen">                      <span id="ctl00_ContentPlaceHolder1__companyTable_Label13">','</span>');
									$successfulcompanies++;
									echo ''.$successfulcompanies.'. '.$name.'<br />'.$url.'<br />'.$address.'<br />'.$cat.'<br />'.$cat2.'<br /><br />';
								
							}		
					}
					}
}
}
}
?>
