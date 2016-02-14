<?php

require 'vendor/autoload.php';
require 'phpquery-master/phpQuery/phpQuery.php';

function service($nik)
{
	$default = ['wilayah_id' => '0', 'g-recaptcha-response' => '000', 'cmd' => 'Cari.', 'page' => '',  'nik_global' => $nik];

	$params = $default;

	$client = new GuzzleHttp\Client();
	$res = $client->request('POST', 'http://data.kpu.go.id/ss8.php', [
	    'form_params' => $params
	]);
	

	$html = $res->getBody();
	$startsAt = strpos($html, '<body onload="loadPage()">') + strlen('<body onload="loadPage()">');
	$endsAt = strpos($html, '</body>', $startsAt);
	$result = substr($html, $startsAt, $endsAt - $startsAt);

	// return $html;
	
	$dom = phpQuery::newDocumentHTML($result);

	// return $dom;
	$result = [];

	foreach (pq('div.form') as $content) 
	{

	 	$key = trim(pq($content)->find('.label')->eq(0)->html());
	 	$value = trim(pq($content)->find('.field')->eq(0)->html());

	 	$result[$key] = $value;
	}
	

 	if (!empty($result)) {
 		echo json_encode(['success' => true, 'message' => 'Success', 'data' => $result]);
 	} else {
 		http_response_code(400);
 		echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
 	} 
}

service($_GET['nik']);