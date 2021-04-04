<?php 
/*
**LOADERS FILE
*/
function load_config($value)
{
	include_once ROOT.CONFIG.$value.'.conf';
}

function load_lib($value)
{
	include_once ROOT.LIB.$value.'.php';
}

function load($value)
{
	include_once ROOT.'./'.$value.'.conf';
}
/*
**REDIRECT
*/

?>