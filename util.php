<?php

function getByIndexes($assArr, $indexes)
{
	$result = [];
	foreach($indexes as $index)
		$result[$index] = @$assArr[$index];
	
	return $result;
}

function __autoload($cn){
	$filename = LOGIC_DIR . $cn . '.php';
    include_once($filename);
}