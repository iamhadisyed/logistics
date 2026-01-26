<?php

/**
 * 
 */
 //require_once('../includes/autoload/MarketplaceWebService/ModelRequest.php');
 require_once('../includes/autoload/MarketplaceWebService/Model.php');
abstract class MarketplaceWebService_ModelRequest
extends MarketplaceWebService_Model
{
    public function __construct($data = null)
    {
        $this->fields['Marketplace'] = array('FieldValue' => null, 'FieldType' => 'string');
        $this->fields['Merchant'] = array('FieldValue' => null, 'FieldType' => 'string');
        // TODO might not be available for all request classes
        $this->fields['MarketplaceIdList'] = array('FieldValue' => null, 'FieldType' => 'MarketplaceWebService_Model_IdList');
        parent::__construct($data);
    }
    
    /**
     * Convert object to array.
     * 
     * Added to move logic from Client to request classes.
     * 
     * @param array $params
     * 
     * @return array
     */
    public function convert(array $params = array())
    {
        // add any set property from fields
        foreach ($this->fields as $key => $v) {
            if ( $this->__isset($key) ) {
                $params[$key] = $this->__get($key);
            }
        }
        return $params;
    }
    
    public function getDataHandle()
    {
        return null;
    }
    
    public function getContentMd5()
    {
      
	    return $this->__isset('ContentMd5')?$this->__get('ContentMd5'):null;
    }

    /**
     * Sets the value of the MarketplaceIdList.
     * 
     * @param IdList MarketplaceIdList
     * @return void
     */
    public function setMarketplaceIdList($value) 
    {
        if ( array_key_exists('MarketplaceIdList', $this->fields) ) {
            $marketplaceIdList = new MarketplaceWebService_Model_IdList();
            $marketplaceIdList->setId($value['Id']);
            $this->fields['MarketplaceIdList']['FieldValue'] = $marketplaceIdList;
        }
        return $this;
    }
}