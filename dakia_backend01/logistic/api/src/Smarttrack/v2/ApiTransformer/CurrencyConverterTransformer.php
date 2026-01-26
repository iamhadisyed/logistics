<?php
namespace SmarttrackTransformer\V2;

use Smarttrack\V2\SmartTransformer;

/**
 *
 */
class CurrencyConverterTransformer  extends SmartTransformer
{
    public function transform($shipRes)
    {
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'ERROR'){
            $message = $shipRes['MESSAGE'];
            return $this->sr->error(400,$message,$shipRes['ERROR']);
        }
        //$data = $this->mappingData( $shipRes );
        if(isset($shipRes['STATUS']) && $shipRes['STATUS'] == 'SUCCESS'){
            $warnings = [];
            if(isset($shipRes['ERROR']) && !empty($shipRes['ERROR']))
                $warnings = $shipRes['ERROR'];
            $message = $shipRes['MESSAGE'];
            $data['amount'] = $shipRes['amount'];
            return $this->sr->success(200,$message,$data,$warnings);
        }

        return $this->sr->error(400, "Please contact system administrator",["Whoops, looks like something went wrong."]);
    }

}
