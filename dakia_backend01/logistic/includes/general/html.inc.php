<?php
/*
 * HTML Class
 * 
 * Provides a set of functions to assist in generation of html tags.
 * 
 **************************************************************************/

function html_span ($theText, $theClass = "") {
	$class = "";
	if ($theClass != "") $class = "class=\"$theClass\" ";
	
	return "<span $class>$theText</span>";
}

function html_div ($theText, $theClass = "") {
	$class = "";
	if ($theClass != "") $class = "class=\"$theClass\" ";
	//
	return "\n<div $class>$theText</div>";
}

function html_ul ($theText, $theClass = "") {
	$class = "";
	if ($theClass != "") $class = "class=\"$theClass\" ";

	return "<ul $class>$theText</ul>";
}

function html_li ($theText) {
	return "<li>$theText</li>";
}

function html_h1 ($theText) {
	return "<h1>$theText</h1>";
}

function html_h2 ($theText) {
	return "<h2>$theText</h2>";
}

function html_p ($theText, $theClass = "") {
	$class = "";
	if ($theClass != "") $class = "class=\"$theClass\" ";

	return "<p $class>$theText</p>";
}

function html_pre ($theText) {
	return "<pre>$theText</pre>";
}

function html_a ($theText, $theHref, $theClass = "", $theAttr = "") {
	$class = "";
	if ($theClass != "") $class = "class=\"$theClass\" ";

	return "<a href=\"$theHref\" $class $theAttr>$theText</a>";
}

function html_img ($theSrc) {
    return '<img src="' . $theSrc . '" border="0" />';
}

function html_option ($theText, $theValue, $theSelectFlag) {
    $select = "";
    if ($theSelectFlag) $select = "selected";
    return "<option value=\"$theValue\" $select>$theText</option>";
}
?>
