<?php

class AuditLogs{

    /*
    *  DDL for select box with SQL query
    *  Params
    *  $sql        =   complete sql query 
    *  $dd_name    =   name of html entity 
    *  $option_name_field   =   that display in the option
    *  $option_value_field   =   that display in the value in option if blank then $option_name_field will display 
    *  $selected_valude       = default selected value 
    *  $attr   =   it will placed in the select box such as class="form-contral " disable="disable"
    *  $default_select = ''
    *  $default_select_value = ''
    *  $dd_id = ''
    *  $title=''
    * 
    */ 
    
    
    public function __construct() {
        
    }
    
    public static function getAuditDetails($tableName,$recordId,$ajaxPage) {
        $outputHtml         = "";
        $recordId           = (int)$recordId;
        $className          = str_replace(' ','',ucwords(str_replace('_', ' ', $tableName))).'LogFilter';
        
        $tableLogFilter = new $className();
        $tableLog = $tableLogFilter->getAuditLog($recordId);
        if (trim($tableName) == 'user' && $recordId < '1343') {
            $outputHtml .= '<tr>';
            $outputHtml .= '<td>User Imported from CMS</td>';
            $outputHtml .= '<td></td>';
            $outputHtml .= '<td></td>';
            $outputHtml .= '<td></td>';
            $outputHtml .= '</tr>';
        }
        if (count($tableLog) > 0) {
            foreach ($tableLog as $clog) {
                $outputHtml .= '<tr>';
                $outputHtml .= '<td>' . $clog->getMessage() . '</td>';
                $outputHtml .= '<td>' . strtoupper($clog->getIpaddress()) . '</td>';
                $outputHtml .= '<td>' . formatDateTime(date("Y-m-d H:i:s", $clog->getLogDate())) . '</td>';
                $outputHtml .= '<td>';
                if (trim($clog->getPreviousData()) != '') {
                    $outputHtml .= '<a href="#" class="log_detail_link"  data-table="'.$tableName.'"  data-toggle="modal" data-target="#detail-log-popup" role="dialog" tabindex="-1" data-cid="' . $clog->getId() . '" data-ajax_url="'.$ajaxPage.'">Details</a>';
                }
                $outputHtml .= '</td>';
                $outputHtml .= '</tr>';
            }
        } else {
            $outputHtml .= '<tr>';
            $outputHtml .= '<td colspan="4">No History Data Found.</td>';
            $outputHtml .= '</tr>';
        }
        return $outputHtml;
    }
    
    
    public static function getAuditItemDetails($tableName,$logId)
    {
        $outputHtml         = "";
        $logId           = (int)$logId;
        $className          = str_replace(' ','',ucwords(str_replace('_', ' ', $tableName))).'Log';
        
        
        $LogRecord = new $className($logId);
        $oldData = unserialize($LogRecord->getPreviousData());
        $newData = unserialize($LogRecord->getCurrentData());
        $output .= '<table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Column Name</th>
                <th>Previous value</th>
                <th></th>
                <th>New Value</th>
            </tr>
        </thead>';
        if(method_exists($className,'logArray'))
        {
            $output .= self::makeHtml($oldData, $newData,$className::logArray());
        }
        else
        {
            $output .= '<tr>
            <th colspan="4">You have not selected fields to display logs </th>
        </tr>';
        }
        
        $output .= '</table>';
        echo $output;
    }
    
    
    public static function makeHtml($oldData, $newData,$displayFieldsArray=array()) {
        $output = '';
        if(count($displayFieldsArray)>0)
        {
            foreach($displayFieldsArray as $key=>$value){
                $output .= self::extractData($oldData, $newData, 'get'.$key, $value);
            }
        }
        return $output;
    }
    
    public static function extractData($oldData, $newData, $fieldName, $fieldLabel) {
        try {
            $oldVaues = $oldData->$fieldName();
            $newValue = $newData->$fieldName();
            if (
                        strtolower(trim($oldVaues)) != strtolower(trim($newValue)) 
                    &&  (strtolower(trim($oldVaues)) != '' && strtolower(trim($newValue)) != 'none') 
                    &&  (strtolower(trim($oldVaues)) != '||' && strtolower(trim($newValue)) != '')
                )
                $output = '<tr><td>' . $fieldLabel . '</td><td>' . $oldVaues . '</td><td> to </td><td>' . $newValue . '</td></tr>';
        } catch (Exception $ex) {
            //$output = '<tr><td>' . $fieldLabel . '</td><td></td><td> to </td><td></td></tr>';
        }
        return $output;
    }
    
    
    
    
    
    
      
    
}
