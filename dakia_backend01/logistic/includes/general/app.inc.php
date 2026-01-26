<?php

function app_imageFolder ($file = "")
{
	return "../images/";
}

function app_imageSrc ($file = "")
{
	return "../images/$file";
}

/**
 * Add post values as properties of the object passed.
 *
 * @param object $object
 */
function app_setPostValues ($object)
{
	foreach ($_POST as $key=>$val)
	{
		//echo "<br>Got $key = $val";
		$object->$key = strip_tags($val);
	}
}

/**
 * Creates a html user list for the list for the array passed
 *
 * @param unknown_type $arrayList
 */
function app_ul ($arrayList)
{
	$html = "<ul>";
	foreach ($arrayList as $arrayItem)
	{
		$html .= "<li>$arrayItem</li>";
	}
	$html .= "</ul>";
	return $html;
}