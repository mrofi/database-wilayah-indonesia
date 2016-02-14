<?php

require 'vendor/autoload.php';
require 'phpquery-master/phpQuery/phpQuery.php';


/**
 * Convert a string to snake case.
 *
 * @param  string  $value
 * @param  string  $delimiter
 * @return string
 */
function snake($value, $delimiter = '_')
{
    return implode('_', explode(' ', strtolower(str_replace('/', ' ', $value))));
}

function service($nik) {
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

	 	$key = snake(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', rtrim(trim(pq($content)->find('.label')->eq(0)->html()), ':')));
	 	$value = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', rtrim(trim(pq($content)->find('.field')->eq(0)->html()), ':'));

	 	if (empty($key)) {
	 		continue;
	 	}

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