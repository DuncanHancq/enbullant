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
	$fusion = array_merge($maj,$min,$num);
	shuffle($fusion);
	foreach($fusion as $valeurs)
	{
			$token .= $valeurs;
	}
	return $token;
}

function srcJS(array $src){
	$scripts = "";
	foreach ($src as $value) {
		$scripts .= '<script src="'. $value .'"></script>'."\n\t\t";
	}
	return $scripts;
}
function srcCSS(array $src){
	$cssSheets = "";
	foreach ($src as $value) {
		$cssSheets .= '<link rel="stylesheet" href="'.$value.'">'."\n\t\t";
	}
	return $cssSheets;
}

?>
