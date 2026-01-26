<?php
class RightColumn implements iVisualComponent
{
	private static $item = null;
	private $show = true;
	//
	/**
	 * Enter description here...
	 *
	 * @return
	 */
	 
	
	 
	public static function getItem()
	{
		if (self::$item == null)
		{
			self::$item = new RightColumn();
		}
		return self::$item;
	}

	/**
	 * Allow the right column to be hidden
	 */
	public function hide()
	{
		$this->show = false;
	}


	public function init()
	{

	}

	public function renderInner()
	{
		if (!$this->show) return;
		?>
		<h3 class="img"><span>Parcel Delivery<br />Service</span><img src="../images/boy.png" width="293" height="113" alt="" /></h3>

		<div class="inner body">

		<p>
		<span class="important">One World Express</span>
		Is a reliable and affordable logistics supplier, offering express air courier, air freight and sea freight services unparalleled in the market.
		</p>

		<p><a href="../our_services.php" class="more">Read more</a></p>

		</div>
	<?php
	}

	public function render()
	{
		if (!$this->show) return;
		
		$BaseURL= "http://www.oneworldexpress.com";
		?>
			
           
                  <aside class="right-aside">
                    <section>
          <div class="gray-box">
            <h3><a href="<? echo $BaseURL ?>/logistics-news.php">Logistics News Releases</a><span></span></h3>
            <div class="graybox-contents">
             <?php
	  
		
 $query=" SELECT * FROM newsreleases where active = 1 and deletedq = 'N'
 ORDER BY ID DESC LIMIT 3 " ;
 $result = Db::query($query);

$counter = 1 ;
while ($row = @mysqli_fetch_row($result))
{   
?>
            <ul>
            <li class="logic"><a href="<? echo $BaseURL ?>/logistics-news/<? echo $row[0];?>/<? echo str_replace(" ", "-", strtolower($row[4]))?>.html"><? echo $row[4];?></a></li>
            <li class="date"><? echo  date('d M Y', strtotime( $row[3]))?></li>
            <li class="logic-cont"><? echo  $row[1] ?></li>
            <li class="more"><a href="<? echo $BaseURL ?>/logistics-news/<? echo $row[0];?>/<? echo str_replace(" ", "-", strtolower($row[4]))?>.html">More</a></li>
            <!--<li class="logic"><a href="#">Production logistics</a></li>
            <li class="date">17 Nov 2011</li>
            <li class="logic-cont">The term production logistics describes logistic processes within an industry. Production logistics aims to ensure that each machine and workstation receives the right product in the right quantity and quality at the right time.</li>
            <li class="more"><a href="#">More</a></li>
            <li class="logic"><a href="#">Military logistics</a></li>
            <li class="date">17 Nov 2011</li>
            <li class="logic-cont">In military science, maintaining one's supply lines while disrupting those of the enemy is a crucial—some would say the most crucial—element of military strategy, since an armed force without resources and transportation is defenseless.</li>
            <li class="more"><a href="#">More</a></li>-->
            </ul>
              <?php
                  }
	          ?>
            </div>
          </div>
        </section>
        			<section>
          <div class="gray-box">
            <h3>Why Choose OWE<span></span></h3>
            <div class="graybox-contents parcel-deliver">
        
      <p>One world Express Inc. Ltd is a versatile and dynamic logistics company supplying, Courier, Air Freight, Sea Freight and fulfillment services to the world. Our service is second to none in the market with a dedicated team of professionals with over eighty years of combined experience in the forwarding industry.</p>
          
            </div>
          </div>
        </section>
        			<section>
          <div class="gray-box">
            <h3>Parcel Delivery<span></span></h3>
            <div class="graybox-contents parcel-deliver">
              <p>Is a reliable and affordable logistics supplier, offering express air courier, air freight and sea freight services unparalleled in the market. <a href="<? echo $BaseURL ?>/our_services.php">more ></a></p>
              </ul>
            </div>
          </div>
        </section>
      			    <section>   <img src="../../App_Themes/images/add-3.png" alt="add"> </section>
                  </aside>  
       

          
          		
        <!-- <a href="http://www.facebook.com/group.php?gid=176780028875" title="One World Express Facebook"><img style="margin-bottom:10px;" src="/App_Themes/images/button-facebook-gls.gif" border="0" align="absmiddle"  /></a><a  href="http://twitter.com/global_logistic" title="One World Express Twitter"><img src="/App_Themes/images/button-twitter-gls.gif" border="0" align="absmiddle"  /></a>-->
        
    
		<?php
	}

}
?>