<?php
/*
 * Email Composer
 *
 * Provide interface for composing emails
 *
 * USAGE:
 * 1. Create with the file to use as a template.
 * 2. Update content.
 * 3. Get final text
 *
 * - thats it!
 *
 * All email content stored in files in the templates\email folder.
 *
 */
class EmailCompose
{
	private static $folder = "../includes/templates/emails/";
	private $body = "";
	private $subject = "";

	/**
	 * Create email specifying the template to use.
	 *
	 */
	public function __construct ($emailTemplateName="", $subject="")
	{
		// set the subject
		$this->subject = $subject;
		// Set the template
		if ($emailTemplateName != "")
		{
			// template file exists
			$filename = realpath(self::$folder . $emailTemplateName . ".html");
			if (!file_exists($filename)) return;

			// read the message from the file
			$handle = fopen($filename, "r");
			$this->body = fread($handle, filesize($filename));
			fclose($handle);
		}
		//
		if ($this->body == "") $this->body = "Template $emailTemplateName is missing.";
	}

	/**
	 * Set the template folder - only required if
	 * not using the standard folder
	 *
	 * @param string - email template folder relative to root.
	 */
	public static function setTemplateFolder($folder)
	{
		self::$folder = $folder;
	}

	/**
	 * The subject
	 *
	 */
	public function setSubject($subject)
	{
		$this->subject = $subject;
	}
	public function getSubject()
	{
		return $this->subject;
	}
	/**
	 * The body content
	 *
	 */
	public function setContent($body)
	{
		$this->body = $body;
	}
	public function getContent()
	{
		return $this->body;
	}

	/**
	 * Replace tag in body or subject with the value given
	 *
	 * @param string $tagName
	 * @param string $value
	 */
	public function replaceTag ($tagName, $value)
	{
		// replace tag in body and subject
		$tagName = strtolower("[$tagName]");
		$this->body = str_replace($tagName, $value, $this->body);
		$this->subject = str_replace($tagName, $value, $this->subject);
	}

	/**
	 * Takes any object then tries to replace any string
	 * in the email body/title that matches any of the methods names.
	 * e.g. [getName] would be replace with return value of
	 * $object->getName(), if method exists.
	 *
	 * @param object $object
	 */
	public function updateContent ($object)
	{
		$recflection = new ReflectionClass($object);

		foreach ($recflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
		{
			$methodName = $method->getName();

			if (substr($methodName,0,3) == "get")
			{
				$call = false;
				$tagName = strtolower("[$methodName]");

				if (strpos($this->body, $tagName) > 0)
				{
					$call = true;
				}
				else if (strpos($this->subject, $tagName))
				{
					$call = true;
				}
				if ($call)
				{
					$this->replaceTag($methodName, trim($object->$methodName()));
				}
			}
		}
	}
}
