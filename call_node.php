<?php
	$ch = curl_init();
	$url = "http://localhost:8888/callnode?url=".strval($_GET["params"]);
	echo $url."<br/>";
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$output = curl_exec($ch);
	curl_close($ch);
	echo $output;
?>