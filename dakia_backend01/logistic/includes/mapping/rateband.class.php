<?php

////////////////////////////////////////////////////
//
// Class for dealing with courier ratebands
//
////////////////////////////////////////////////////

/**
 * Rateband - Rateband class
 * @package Courier
 */
class Rateband extends DbAccess3
{

    protected $courier_service_id;
    protected $name;

    protected $orderq;
    protected $active;
    protected $deletedq;
    protected $added_on;
    protected $added_by;
    protected $changed_on;
    protected $changed_by;

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
	public function __construct($mixedCreator = null)
    {
        $fieldList 		=	array(  
					'id'         => 'number',
					'courier_service_id'         => 'number',
                    'name'                 		 => 'string',
                    'orderq'                     => 'string',
                    'active'                     => 'string',
                    'deletedq'                   => 'string',
                    'added_on'                   => 'string',
                    'added_by'                   => 'string',
                    'changed_on'                 => 'string',
                    'changed_by'                 => 'string',
                    'country'                 => 'undefined',
                    'iso'                 => 'undefined'
                    );

		parent::__construct("ratebands", 'id', $fieldList, $mixedCreator);
    }
	


/**
	 * Get list of user objects, using sql given
	 *
	 * @param string $sql
	 */
	 
	public static function getRatebandListFromSql($sql)
	{
//		echo $sql;
		return DbAccess3::getListFromSql(__CLASS__, $sql);
	}
	
	
 

    ////////////////////////////////////////////////////
    // Getters
    ////////////////////////////////////////////////////

    
    public static function buildRatebandDropDown($name, $courier_service_id, $rateband_id)
    {
	//	$where  = "courier_service_id = " . $courier_service_id;
		$rateband 			=	new  RatebandFilter();
        $rateband->addFieldFilter("courier_service_id", DbAccess3::escape($courier_service_id));
        $fromratebandlist 	= 	$rateband->getColumnList("name");
    ?>
        <select name="<?php echo $name;?>"  class="form-control" id="<?php echo $name;?>">
          <option value="">- Please select - </option>
        <?php
        foreach($fromratebandlist as $rateband)
        {
        ?>
            <option value="<?php echo $rateband->getId();?>" <?php if($rateband->getId() == $rateband_id) {echo "selected";}?>><?php echo $rateband->getName();?></option>
        <?php
        }
        ?>
        </select>
        <?php
	}




}
?>
