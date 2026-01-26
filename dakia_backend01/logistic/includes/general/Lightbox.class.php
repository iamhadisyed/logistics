<?php
////////////////////////////////////////////////////
//
// Base class for lightboxes
//
////////////////////////////////////////////////////

/**
 * Standardbookingpage - Standard booking page template class
 * @package Templates
 * @author Exigen
 */
class Lightbox
{

	////////////////////////////////////////////////////
	// Methods
	////////////////////////////////////////////////////

  /**
   * Constructor - sets the page up
   * @return void
   */
	public function __construct()
	{
		$this->template = "../templates/pages/lightbox.html";
	}

  /**
   * Initialise page before rendering
   * @return void
   */
	public function init()
	{
	}

  /**
   * Render the page
   * @return void
   */
    public function show()
	{
		// controller logic set here
		$this->init();

		// output HTML
		require_once($this->template);
    }


	////////////////////////////////////////////////////
	// Render methods
	////////////////////////////////////////////////////

	public function renderBody()
	{
	}

}
?>