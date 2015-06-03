<?php
$string = file_get_contents("gestures.json");
$result = json_decode($string, true);

$string = file_get_contents("gestures.json");
		
		$result = json_decode($string, true);
		$return = array();
		foreach($result as $r)
			print_r($r);		
