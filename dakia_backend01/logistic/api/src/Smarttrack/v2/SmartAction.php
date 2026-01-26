<?php
namespace Smarttrack\V2;

use Monolog\Logger;
use RKA\ContentTypeRenderer\HalRenderer;


class SmartAction {
var $st;
protected $logger;
protected $renderer;

    public function __construct( Logger $logger, HalRenderer $renderer) {
    $this->logger = $logger;
    $this->renderer = $renderer;
    }
    public  function respond( $transformer ) {
        return $transformer->transform();
    }
}