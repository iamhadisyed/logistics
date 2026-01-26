<?php
$curl_post_data = array(
		"method" => 'register',//'106007',
		"jobid" => '106007',
		"country" => 'DE',
		"zipcode" => '3910',
		"city" => 'Nürnberg Bayern',
		"street" => 'Alter Markt',
		"number" => '10 c',
		"district" => 'Altstadt',
		"weight" => 13,
		"reference" => 'sdfag '
		
		);
		
		foreach($curl_post_data as $key=>$value)
		{
		// $curl_post_data[$key] = utf8_encode ($value);	
		}
echo 		$request_json = json_encode($curl_post_data , JSON_UNESCAPED_UNICODE );
		die;

function  cleanupBrokenUmlauts($input)
	{
	 $result = $input;
	 $result = str_replace("ÃŸ", "ß", $result); 
	 $result = str_replace("Ã¤", "ä", $result); 
	 $result = str_replace("Ã¼", "ü", $result); 
	 $result = str_replace("Ã¶", "ö", $result); 
	 $result = str_replace("Ã„", "Ä", $result); 
	 $result = str_replace("Ãœ", "Ü", $result); 
	 $result = str_replace("Ã–", "Ö", $result); 
	 $result = str_replace("â‚¬", "€", $result);
	 return $result;
	}
	
	echo cleanupBrokenUmlauts('NÃ¼rnberg Bayern');
	die;
	
	
	
////////////////////////////////////////////////////
//
// List of accounts for business customers
//
////////////////////////////////////////////////////

// get settings
require_once("../includes/settings/config.inc.php");

// set up local page class
class Page extends BasePage
{
	private $id = 0;

    /***
     * Set the page header
     * @return void
     */
    public function getTitle ()
    {
        echo "Accounts";
    }

    /***
     * This page's content
     * @return void
     */
    public function renderBody()
    {
        ?>
        <div>
			<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="list-table">
				<tr>
					<td colspan="6">Search by Account name: <input name="keyword" type="text" value="<?php echo $this->keyword;?>"> <input name="btn_go" type="submit" value="Go"></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
				</tr>
			    <tr>
				    <th width="120">Name</th>
				    <th width="60" align="left">&nbsp;</th>
				    <th width="60" align="left">&nbsp;</th>
					<th width="40" align="center">&nbsp;</th>
					<th width="40" align="center">&nbsp;</th>
				</tr>
				<tbody>
              <?php
              if (count($this->accounts) > 0)
			  {
			      foreach ($this->accounts as $account)
				  {
				  ?>
				  <tr>
                    <td><?php echo $account->getCompany();?></td>
                    <td align="left">&nbsp;</td>
                    <td align="left">&nbsp;</td>
					<td align="center">&nbsp;</td>
					<td align="center">&nbsp;</td>
				  </tr>
				  <?php
			      }
		      }
			  else
			  {
			  ?>
			  <tr>
			  	<td colspan="6">No Accounts were found for this search term</td>
			  </tr>
			  <?php
			  }
			  ?>
			  </tbody>
			</table>
		</div>
        <?php
    }

    /**
     * Override to show the menu
     *
     */
    public function renderMenu()
    {
    	$menu = new Adminmenu(Adminmenu::ACCOUNTS);
    	$menu->render();
    }


    /***
     * Controller logic goes here
     */
    public function init()
    {
        // check admin user is authenticated
		if (!isset($_SESSION['admin']['id']) OR  is_null($_SESSION['admin']['id'])) {util_redirect("login.php");}

        /*------------------------------------------------------------------------------*/
        // get vars
        $this->keyword                       = (isset($_POST['keyword'])                   ? strip_tags($_POST['keyword']) : '');

		/*------------------------------------------------------------------------------*/
		// get accounts
		$AccObj		     = new Account;
		$where			 = "";
		if ($this->keyword != "")
		{
			$where		     = "(name LIKE '%" . DbAccess3::escape( $this->keyword ). "%')";
		}
		//echo $where;
		$this->accounts  = $AccObj->getAnyAccount($where);

    }
}

/*------------------------------------------------------------------------------*/
// create and render page
$PageObj = new Page(CONFIG_TEMPLATE_ADMIN);
$PageObj->show();

?>
