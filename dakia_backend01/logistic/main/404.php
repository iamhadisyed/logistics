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
       <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-gift"></i>Page Not Found
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse">
                    </a>
                    <a href="" class="fullscreen">
                    </a>
                    <a href="#portlet-config" data-toggle="modal" class="config">
                    </a>
                </div>
                
            </div>
            <div class="portlet-body">
                
                <img src="../images/404.jpg"  />
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
    	if (!isset($_SESSION['admin']['id']) OR is_null($_SESSION['admin']['id'])) 
		{}
		else
		{
			$menu = new Adminmenu(Adminmenu::COURIERS);
    		$menu->render();
		}
    }
}  // class

/*------------------------------------------------------------------------------*/
// create and render page
$page = new Page(CONFIG_TEMPLATE_ADMIN);
$page->show();

?>