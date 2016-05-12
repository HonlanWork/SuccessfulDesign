<?php
	$url = $_SERVER['REQUEST_URI'];
	$url = split('/', $url);
	$param = $url[count($url) - 1];

	// 大赛页面
	if ($param == '582') {
		header('Location:'.'../index.php/Home/Index/contest/id/1');
	}
?>