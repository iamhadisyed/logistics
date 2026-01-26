<?php
// get settings
require_once("../includes/settings/config.inc.php");
class Page extends BasePage
{
	// status colours
	private $colours_map = array("active"  => "state_valid",
								 "inactive" => "state_held"
								 );
	private $user_filter;
	/***
	* Controller logic
	*/
	protected function init()
	{
		//$this->user_filter = new UserFilter();
		$sessionUser = SessionManager::getUser();	
		
		// common initialisation for ths page
		$this->setTitle("User List");
		
	}

	/**
	* Page-specific buttons
	*/
	protected function renderHead()
	{}

	/***
	* Content View
	*/
	protected function renderBody()
	{
		
		$sessionUser = SessionManager::getUser();	
		?>
        <ul class="breadcrumb">
			<li><a href="../main/index.php">Home</a></li>
            <li><a href="#">Error 404 </a></li>
		</ul>

        <h1 class="heading" style="float:left;">Page Not Found</h1>
        <div style="clear:both;"></div>
       <div class="main_grid">
		<div id="user_list">
		
		<div style="text-align:center;">
		<img src="../images/404.jpg"  />
		</div>

		</div>
             
      
       </div>
		<?php
	}
	
	
    /**
     * Override to show the menu
     *
     */
    public function renderMenu()
    {
    	$menu = new Adminmenu(Adminmenu::COURIERS);
    	$menu->render();
    }
}  // class

/*------------------------------------------------------------------------------*/
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();

?>