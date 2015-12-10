<?php

require 'vendor/autoload.php';
require 'phpquery-master/phpQuery/phpQuery.php';

function service($params = [])
{
	$default = ['kata_kunci' => '', 'pilihcari' => '', 'submit' => 'Cari'];

	$params = array_merge($default, $params);

	$client = new GuzzleHttp\Client();
	$res = $client->request('POST', 'http://mfdonline.bps.go.id/index.php?link=hasil_pencarian', [
	    'form_params' => $params
	]);
	
	$html = $res->getBody();
	
	$dom = phpQuery::newDocumentHTML($html);

	$result = [];
	
	foreach ($dom['tr.table_content'] as $content) 
	{
	 	$data = pq($content)->find('td');
	 	$propinsi_id = (int) trim(pq($data)->eq(1)->html());
	 	$propinsi_name = trim(pq($data)->eq(2)->html());
	 	$kabupaten_id = (int) trim(pq($data)->eq(3)->html());
	 	$kabupaten_name = trim(pq($data)->eq(4)->html());
	 	$kecamatan_id = (int) trim(pq($data)->eq(5)->html());
	 	$kecamatan_name = trim(pq($data)->eq(6)->html());
	 	$desa_id = (int) trim(pq($data)->eq(7)->html());
	 	$desa_name = trim(pq($data)->eq(8)->html());
	 	$result[] = compact(['propinsi_id', 'propinsi_name', 'kabupaten_id', 'kabupaten_name', 'kecamatan_id', 'kecamatan_name', 'desa_id', 'desa_name']);
	}
	
 	return $result;
}

function getPropinsi($keyword = '')
{	
	$params = ['kata_kunci' => $keyword, 'pilihcari' => 'prop'];
	return service($params);
}

function getKabupaten($keyword = '')
{	
	$params = ['kata_kunci' => $keyword, 'pilihcari' => 'kab'];
	return service($params);
}

function getKecamatan($keyword = '')
{	
	$params = ['kata_kunci' => $keyword, 'pilihcari' => 'kec'];
	return service($params);
}

function getDesa($keyword = '')
{	
	$params = ['kata_kunci' => $keyword, 'pilihcari' => 'desa'];
	return service($params);
}


?>
<h3>Example : Search data for 'Jawa' and 'Pekalongan'</h3>
<pre>

<?php
var_dump(getPropinsi('jawa'));
var_dump(getKabupaten('pekalongan'));
var_dump(getKecamatan('pekalongan'));
var_dump(getDesa('pekalongan'));
?>
</pre>