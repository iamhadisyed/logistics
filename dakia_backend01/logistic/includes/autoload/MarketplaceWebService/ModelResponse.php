<?php

/**
 * 
 */
abstract class MarketplaceWebService_ModelResponse
extends MarketplaceWebService_Model
{
    protected $_actionNamespace = null;
    
    public function __construct($data)
    {
        // TODO test feature
        if ( !isset($this->_actionNamespace) ) {
            // use last part of class name as default action name.
            $this->_actionNamespace = preg_replace('@^.+(_[a-z0-9]+)$@Si', '\\1', get_class($this));
        }
        return parent::__construct($data);    
    }
       
    // TODO might use 'static' keyword (PHP 5.3+) for fromXML($xml) method..
    public static function fromXML($xml, $ns = null)
    {
        $class = "MarketplaceWebService_Model_$ns";
        if ( !class_exists($class) ) {
            throw new InvalidArgumentException("No response class found for namespace $ns.");
        }
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('a', 'http://mws.amazonaws.com/doc/2009-01-01/');
	    $response = $xpath->query('//a:'.$ns);
        
        if ($response->length == 1) {
            return new $class($response->item(0)); 
        }
        
        throw new Exception ("Unable to construct MarketplaceWebService_Model_$ns from provided XML. 
                              Make sure that $ns is a root element");
    }
    
    /**
     * XML Representation for this object
     * 
     * @return string XML for this object
     */
    public function toXML() 
    {
        $action = $this->_actionNamespace;
        $xml = "";
        $xml .= "<$action xmlns=\"http://mws.amazonaws.com/doc/2009-01-01/\">";
        $xml .= $this->_toXMLFragment();
        $xml .= "</$action>";
        return $xml;
    }
}