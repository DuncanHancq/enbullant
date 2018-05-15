<?php

function debug($param,$mode = 1)
{
	if($mode == 1)
	{
		echo '<pre>';
			print_r($param);
		echo '</pre>';
	}
	else
	{
		echo '<pre>';
			var_dump($param);
		echo '</pre>';
	}
}

//fonction token() :
function token(){
	global $content;
	$token = "";

	$maj = range('A','Z');
	$min = range('a','z');
	$num = range(0,50);
	$fusion = array_merge($maj,$min,$num);  //fusion cf. DBZ
	shuffle($fusion);
	foreach($fusion as $valeurs)
	{
			$token .= $valeurs;
	}
	return $token;
}

?>
