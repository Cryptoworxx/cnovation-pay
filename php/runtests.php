<?php

require("CoinworxxClient.php");

array_shift($argv);
$token = array_shift($argv);
$url = array_shift($argv);

$cw = new Coinworxx($token);
if( $url )
	$cw->url = $url;

try
{
	$res = $cw->info();
}
catch(CoinworxxException $ex)
{
	$res = ['exception'=>$ex->getMessage()];
}


echo json_encode($res,JSON_PRETTY_PRINT);
