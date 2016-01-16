<?php
function getLocation($str) {
	$array_size = intval(substr($str, 0, 1)); // 拆成的行数
	$code = substr($str, 1);	// 加密后的串
	$len = strlen($code);
	$subline_size = ($len % $array_size);	// 满字符的行数
	$result = array();
	$deurl = "";
	for ($i = 0; $i < $array_size; $i += 1) {
		if ($i < $subline_size) {
			array_push($result, substr($code, 0, ceil($len / $array_size)));
			$code = substr($code, ceil($len / $array_size));
		} else {
			array_push($result, substr($code, 0, ceil($len / $array_size) - 1));
			$code = substr($code, ceil($len / $array_size) - 1);
		}
	}
	for ($i = 0; $i < ceil($len / $array_size); $i += 1) {
		for ($j = 0; $j < count($result); $j += 1) {
			$deurl = $deurl."".substr($result[$j], $i, 1);
		}
	}	
	return str_replace("^", "0", urldecode($deurl));
}
function getInfo($xml_url) {
	echo $xml_url."</br>";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $xml_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$output = curl_exec($ch);
	//echo $output."</br>";
	curl_close($ch);
	$xml = simplexml_load_string($output);
	return $xml->track;
}
function getCode($info) {
	return $info->location;
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script src="http://code.jquery.com/jquery-latest.js"></script>
<script>
$(function(){
	$("#search").click(function(){
		document.location.href = "xmu.php?k=" + $("#k").val();
	});
});
</script>
</head>
<body> 
<?php
function catch_url($k) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://www.xiami.com/search?key=".$k);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$output = curl_exec($ch);
	curl_close($ch);
	$regs = array();
	//<a target="_blank" href="http://www.xiami.com/song/1773101670?spm=a1z1s.3521865.23309997.1.cT77HE" title="Palpitation!" data-spm-anchor-id="a1z1s.3521865.23309997.1">Palpitation!</a>
	preg_match_all("/href=\"http:\/\/www.xiami.com\/song\/(\d+)/is", $output, $regs);
	$ids = $regs[1];
	$index = 0;
	foreach ($ids as $item) {
		$xml_url = "http://www.xiami.com/widget/xml-single/uid/0/sid/".$item;
		$info = getInfo($xml_url);
		$location = getLocation($info->location);
?>

<audio controls="controls">
  <source src="<?php echo $location;?>" type="audio/mpeg" />
Your browser does not support the audio element.
</audio>
<span><?php echo $info->song_name;?></span><img src="<?php echo $info->album_cover;?>"/></br>
<input type="hidden" value="<?php echo $location;?>"/>
<span style="display:none;">src:<?php echo $location;?></span><br/>
<?php
	echo '<span><input type="button" onclick="mpg('.$index.')" value="mpg it"/></span><br/>';
	$index += 1;
	}
}
//echo getLocation("8h2fmF13189mtD59E9EE-tFii884785ph9e84d8%nt%l.6626_73_2dd7b15up2ec999818%k4726-4El%F.o%%59%83e82aa17-l3mxm22%55_Fy1b2442%A5i%FF28Ela%9c%9%%5%.a263F61.u3d55855E");
if (isset($_GET["k"])) {
	catch_url(strval($_GET["k"]));
}
?>
	<input type="button" value="stop mpg" id="stop_mpg"/><br/>
	<input type="text" id="k" value='<?php echo strval($_GET["k"]) ?>'/><input type="button" id="search" value="search"/>
	
	<script type="text/javascript">
		function mpg(index) {
			var src = $("input[type='hidden']:eq(" + index + ")").val();
			var url = "call_node.php?params=" + encodeURIComponent(src);
			console.log("mpg " + url);
			$.get(url, function(data) {
				console.log(data);
			});
		}
		$("#stop_mpg").click(function() {
			var src = "";
			var url = "call_node.php?params=" + encodeURIComponent(src);
			console.log("mpg " + url);
			$.get(url, function(data) {
				console.log(data);
			});
		});
	</script>
</body> 
</html>











