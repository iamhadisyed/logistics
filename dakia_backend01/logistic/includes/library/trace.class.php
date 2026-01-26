<?php
/**
 * Trace functions/class
 *
 * Want to be able to add trace messages for debugging.
 * Needs to be incredibly easy to use (so used all the time).
 * Need to ensure that if trace messages are left in then the do appear on live site!
 *
 * To facilitate have simple trace functons, that use the Trace object.
 * For local tracing the tracking messages can be turned on and off with t_on and t_off.
 * At global level all tracing is disabled unless specifically turned on - done in site specific file.
 *
 * The trace display should be included at bottom of template file, so is shown on
 * pages automatically.
 *
 */

/**
 * Add trace message
 *
 * @param string $msg
 * @param string $class_name - Prefix to trace information.
 * @param string $additional - included in brackets on trace line
 */
function t($msg, $class_name = "", $additional = "")
{
	if (!Trace::isActive()) return;
	Trace::addMsg($msg, $class_name, $additional);
}

/**
 * Local tracing on/off
 *
 */
function t_on()
{
	Trace::setActiveLocal(true);
}
function t_off()
{
	Trace::setActiveLocal(false);
}

/**
 * Multiline message
 *
 * @param string $sql
 * @param string $msg
 */
function t_ml($sql, $msg = "")
{
	if (!Trace::isActive()) return;
	//
	$lines = explode("\n",$sql);

	// Add space either side of message
	if ($msg != "") $msg = " $msg " ;
	//
	t("***$msg*******");
	foreach ($lines as $line)
	{
		t($line);
	}
	t("**********");

}

function t_xml($xml, $msg = "")
{
	if (!Trace::isActive()) return;
	//
	$xml = str_replace("<", "\n<", $xml);
	$xml = str_replace("\n</", "</", $xml);
	$xml = str_replace("></", ">\n</", $xml);

	t_ml ($xml, $msg);
}

/**
 * Trace & Die
 *
 */
function t_die ($msg = "", $class_name = "", $additional = "")
{
	if (!Trace::isActive()) return;
	Trace::addMsg($msg, $class_name, $additional);
	Trace::display();
	die;
}

/**
 * Trace class
 *
 */
class Trace
{
	private static $msg_list = array();
	private static $local_active_flag = false;
	private static $global_active_flag = false;

	/**
	 * Indicate if trace is turned on,
	 * checks at global and local level.
	 *
	 * @return bool
	 */
	static function isActive()
	{
		if (!self::$global_active_flag) return false;
		if (!self::$local_active_flag) return false;
		return true;
	}

	/**
	 * Allows trace functionality to be turned on by passing "dbug" parameter to page.
	 * ?dbug=X
	 * Where x is sum of (day+month+year) * 3
	 * Year - is the last 2 digits.
	 */
	static function dynamicSwitch()
	{
		if (@$_GET["dbug"] != "")
		{
			$now = time();

			$code = intval(date("d", $now)) + intval(date("m", $now)) + intval(date("y", $now));
			$code *= 3;

			if ($_GET["dbug"] == $code)
			{
				Trace::setActiveGlobal(true);
				Trace::setActiveLocal(true);
			}
		}
	}

	/**
	 * Add a trace message
	 *
	 * @param string $msg
	 * @param string $class_name - message prefix
	 * @param string $additional - message suffix.
	 */
	static function addMsg ($msg, $class_name, $additional)
	{
		if (trim($msg) == "") return;
		if ($class_name != "")
		{
			$msg = $class_name . " - " . $msg;
		}
		if (trim($additional) != "") $msg .= "  ($additional)";
		self::$msg_list[] = $msg;
	}

	/**
	 * Turn local debugging on/off
	 *
	 * @param bool $flag
	 */
	static function setActiveLocal ($flag)
	{
		self::$local_active_flag = $flag;
	}

	/**
	 * Turn global debugging on/off
	 *
	 * @param bool $flag
	 */
	static function setActiveGlobal ($flag)
	{
		self::$global_active_flag = $flag;
	}

	/**
	 * Display trace information
	 *
	 */
	static function display()
	{
		// check global flag, the local flag can be turned on/off as required in code.
		if (!self::$global_active_flag) return false;
		//
		if (sizeof(self::$msg_list) == 0) return;

		echo "<div>";
		foreach (self::$msg_list as $msg)
		{
			$msg = str_replace("<", "&lt;", $msg);
			$msg = str_replace(">", "&gt;", $msg);
			echo $msg . "<br />";
		}
		echo "</div>";
	}
}