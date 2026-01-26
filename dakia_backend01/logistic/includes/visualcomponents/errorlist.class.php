<?php
/**
 * Error List Visual Component
 *
 * See iVisualComponent interface for more details.
 */
class ErrorList implements iVisualComponent
{
	private static $item = null;
	//
	private $error_array = array();
	private $msg = "Sorry, unable to save changes:";

	/**
	 * Factory method to get instance
	 *
	 *
	 * @return ErrorList
	 */
	public static function getItem()
	{
		if (self::$item == null)
		{
			self::$item = new ErrorList();
		}
		return self::$item;
	}

	/**
	 * Initialise
	 *
	 */
	public function init()
	{
		// nothing required.
	}

	/**
	 * Show list of errors.
	 *
	 */
	public function render()
	{
		if (sizeof($this->error_array) == 0) return;
		?>	
			<div class="warring" style='padding-left:60px'>
        	<p>
			 <strong><?php echo $this->msg; ?> </strong>
			
			<?php
			foreach ($this->error_array as $error)
			{
				?>
				 <span><?php echo $error ?></span>
				<?php
			}
			?>
			</ul>
                        </p>
		</div>
		<?php
	}

	/***
	 * Add list of errors from an array
	 */
	public function addErrorList($error_array)
	{
		t("List in " . sizeof($error_array), __METHOD__);
		//
		if (sizeof($this->error_array) == 0)
		{
			$this->error_array = $error_array;
		}
		else
		{
			foreach ($error_array as $error)
			{
				$this->error_array[] = $error;
			}
		}
		t("Error list " . sizeof($this->error_array), __METHOD__);
	}

	/**
	 * Add an error to the list
	 *
	 * @param string - error description
	 */
	public function addError($error)
	{
		$this->error_array[] = $error;
	}

	/**
	 * Set message displayed before error list
	 *
	 * @param string $msg
	 */
	public function setPrelistMessage($msg)
	{
		$this->msg = $msg;
	}

	/**
	 * Number of errors that have been added
	 *
	 * @return int
	 */
	public function getErrorCount()
	{
		return sizeof($this->error_array);
	}
}
?>