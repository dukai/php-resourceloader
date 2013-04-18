<?php
require_once "resource-loader.php";
require_once "resource-register.php";

function getFile($path){
		
	if(function_exists('realpath')){
		$path = realpath($path);
	}
	
	if(!$path || !@is_file($path)){
		return '';
	}

	return @file_get_contents($path);
}

define("ABSPATH", dirname(__FILE__) . '/');
$load = $_GET['load'];
$verify = md5($load);
$filename = ABSPATH . 'cache/' . $verify . '.js';
$out = getFile($filename);
header('Content-Type: application/x-javascript; charset=UTF-8');
if(empty($out)){

	$load = explode(',', $load);

	$scriptLoader = new ResourceLoader();
	$scriptLoader->setAbsPath(ABSPATH);
	resource_default_register($scriptLoader);
	$out = '';
	foreach($load as $key){
		$out .= $scriptLoader->getSrc($key);
	}
	
	@file_put_contents($filename, $out);
	
}else{
	$nowTime = time();
	$expTime = $nowTime + 600;
	$now = gmdate(DATE_RFC822, $nowTime);
	$exp = gmdate(DATE_RFC822, $expTime);
	//header("Last-Modified: Wed, 18 Jun 2011 14:22:27 GMT;Cache-Control: max-age=600;Expires: {$exp};Date: {$now}");
	//print_r($_SERVER);
	if(!empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
		$modifiedSince = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
		$sincetime = strtotime($modifiedSince);
		if($nowTime - $sincetime < 6000){
			//exit('step in');
			header("Last-Modified: " . $modifiedSince);
			header("Cache-Control: max-age=600");
			header("Date: {$now}");
			header('Not Modified', true, 304);
			
			exit;
		}
	}
	header("Last-Modified: " . gmdate(DATE_RFC822));
	header("Cache-Control: max-age=600");
	header("Expires: {$exp}");
	header("Date: {$now}");
	//header('Not Modified',true,304);
}


echo $out;