<?php
class Ddl extends DbAccess3 {

    public function __construct() {
        
    }
    
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

    public static function generateDDLFromSql($sql, $dd_name, $option_name_field, $option_value_field = 'id', $selected_valude = '', $attr = '', $default_select = '', $default_select_value = '', $dd_id = '',$title='',$dataAttr='') {
        $dd_id = $dd_id != "" ? $dd_id : $dd_name;
        $ddl = '<select name="' . $dd_name . '" id="' . $dd_id . '"' . $attr . '  title = "' . $title . '" placeholder = "' . $title . '"  >';
        if (!empty($default_select)) {
            $ddl .= '<option value="' . $default_select_value . '">' . $default_select . '</option>';
        }
        $reslt = DbAccess3::runQueryWithError($sql);
        if (DbAccess3::getNumRows($reslt) > 0) {
            while ($obj = DbAccess3::getObject($reslt)) {
                $data_attribute = '';
                if (is_array($dataAttr)){
                    foreach($dataAttr as $datakey=>$dataColName)
                        $data_attribute .= ' data-'.$datakey.'="'.$obj->$dataColName.'"';
                } else {
                    $data_attribute = $dataAttr;
                }
                $option_name = '';
                if (is_array($option_name_field)) {
                    $name_arr = array();
                    foreach ($option_name_field as $col) {                        
                        $name_arr[] = $obj->$col;
                    }
                    $option_name = implode(" ", $name_arr);
                } else {
                    $option_name = $obj->$option_name_field;
                }
                /* selected if array pass */
                $selected = "";
                if(is_array($selected_valude)){
                    $selected = (in_array($obj->$option_value_field, $selected_valude) ? ' selected="selected"' : '');
                } else {
                    $selected = ($obj->$option_value_field == $selected_valude ? ' selected="selected"' : '');
                }

                $ddl .= '<option '.$data_attribute.' value="' . $obj->$option_value_field . '"'. $selected .'>' . ucfirst($option_name) . '</option>';
            }
        }
        $ddl .= '</select>';
        return $ddl;
    }
    
    
    public static function generateDDL($dd_name, $className, $filters, $option_name_field, $option_value_field = 'id', $selected_value = '', $attr = '', $default_select = '', $default_select_value = '', $dd_id = '',$title='',$image_column = '',$folder_path='',$imagePreFix="",$debug = false) {
        $imagePath = "";
        $dd_id = $dd_id != "" ? $dd_id : $dd_name;
        $ddl = '<select name="' . $dd_name . '" id="' . $dd_id . '"' . $attr . '  title = "' . $title . '" data-container="body" placeholder = "' . $title . '"  >';
        if (!empty($default_select)) {
            $ddl .= '<option value="' . $default_select_value . '">' . $default_select . '</option>';
        }
  
        $classObj = new $className();
        if (is_array($filters) && count($filters) > 0) {
            foreach ($filters as $field => $val) {
                $classObj->addFilter($field . "='" . DbAccess3::escape($val) . "'");
            }
        } else {
            $classObj->addFilter($filters);
        }
//        if($className == "ServiceFilter")
//            $classObjList = $classObj->getList(true);
//        else
            if($debug)
                $classObjList = $classObj->getList(TRUE);
            else
                $classObjList = $classObj->getList();

        if (count($classObjList) > 0) {
            foreach ($classObjList as $obj) {
                $option_name = "";
                if (is_array($option_name_field)) {
                    $name_arr = array();
                    foreach ($option_name_field as $col) {
                        $textfunc = 'get' . self::getClassColName($col);
                        $name_arr[] = $obj->$textfunc();
                    }
                    $option_name = implode(" ", $name_arr);
                } else {
                    $textfunc = 'get' . self::getClassColName($option_name_field);
                    $option_name = $obj->$textfunc();
                }
                $valfunc = 'get' . self::getClassColName($option_value_field);
                $selected = "";
                if(is_array($selected_value)){
                    $selected = (in_array($obj->$valfunc(), $selected_value) ? ' selected="selected"' : '');
                } else {
                    $selected = ($obj->$valfunc() == $selected_value ? ' selected="selected"' : '');
                }
                if(!empty($image_column)){
                    $imageColumn = "";
                    $imageColumn = "get".$image_column;
                    if($imagePreFix.$obj->$imageColumn() !="" && file_exists($folder_path.$imagePreFix.$obj->$imageColumn())){
                        $imagePath = 'data-content="<img src='.$folder_path.$imagePreFix.$obj->$imageColumn().' /> ' . ucfirst($option_name).'"';
                    }else{
                        $imagePath = 'data-content="<img src=../images/no_image_found_14_16.png  /> ' . ucfirst($option_name).'"';
                    }
                }
                $ddl .= '<option '.$imagePath.' value="' . $obj->$valfunc() . '"' . $selected . '>' . ucfirst($option_name) . '</option>';
            }
        }
        $ddl .= '</select>';
        return $ddl;
    }

    public static function generateArrayDDL($dd_name, $value_array, $selected = "", $default_select = "", $attr = "", $default_select_value = "", $dd_id = '' ,$title='',$use_key_value = '') {
        $output = "";
        $select_str = "";
        $dd_id = ($dd_id == "") ? $dd_name : $dd_id;
        $output .= '<select id="' . $dd_id . '" name="' . $dd_name . '"' . $attr . '  title = "' . $title . '" placeholder = "' . $title . '"  >';
        if ($default_select != "")
            $output .= '<option value="' . $default_select_value . '">' . $default_select . '</option>';

        if (is_array($value_array)) {
            foreach ($value_array as $key => $value) {
                $select_str = "";
                if ($key == $selected && $selected != "") {
                    $select_str = 'selected="selected"';
                }
                if($use_key_value != '')
                    $value = $key;
                $output .= '<option ' . $select_str . ' value="' . $key . '">' . ucfirst($value) . '</option>';
            }
        }

        $output .= '</select>';
        return $output;
    }
    
    

    public static function generateCountryDDL($dd_name='country', $country = "", $value = 'id', $attr = ' class="form-control rounded-pill" required="" data-live-search="true" data-container="body" data-size="8"',$dd_id='',$title='',$showSelect = true) {
        $countryObj = new CountryFilter();
        $countryObj->addFieldFilter('active', '1');
        $rs = $countryObj->getColumnList('id,iso, name, region, german_name');
        $dd_id = ($dd_id == "") ? $dd_name : $dd_id;
        $option = "";
        $option .= '<select name="' . $dd_name . '"  id="' . $dd_id. '" ' . $attr . ' title = "' . $title . '" placeholder = "' . $title . '"  >';
        if($showSelect){
            $option .= "<option value=''>Select Country</option>";
        }
        if (count($rs) > 0) {
            foreach ($rs as $dataItem) {
                $iso = $dataItem->getIso();
                $name = $dataItem->getName();
                $id = $dataItem->getId();
                $german_name = html_entity_decode($dataItem->getGermanName());

                if (isset($_SESSION['lang']) && $_SESSION['lang'] == "de-DE")
                    $display_name = $german_name;
                else
                    $display_name = $name;

                if ($name == "")
                    continue;
                if(strtolower($value) == 'iso'){
                    $selected = ($iso == $country) ? " selected" : "";
                }
                else{
                    $selected = ($id == $country) ? " selected" : "";
                }
                $flag = "../assets/global/img/flags/".strtolower($iso).".png";
                if(!file_exists($flag))
                    $flag = "../images/no_image_found_14_16.png";
                $option .= '<option ' . $selected . ' value="' . ($value == 'iso' ? $iso : $id  ). '" data-countryiso = "'.$iso.'" data-content="<img src=\''.$flag.'\' /> ' . ucfirst($display_name) . ' ">' . $display_name . '</option>';
            }
        }
        $option .= '</select>';
        return $option;
    }
    
    public static function generateSKUDDL($dd_name='sku', $sku = "", $value = 'sku', $attr = ' class="form-filter bs-select form-control" required="" data-live-search="true" data-container="body" data-size="8"',$dd_id='',$title='',$showSelect = true) {
        $skuObj = new SkuFilter();
        $skuObj->addFieldFilter('active', '1');
        $skuObj->addFieldFilter('customer_id', $this->user->getUserId());
        $rs = $skuObj->getColumnList('id,sku');
        $dd_id = ($dd_id == "") ? $dd_name : $dd_id;
        $option = "";
        $option .= '<select name="' . $dd_name . '"  id="' . $dd_id. '" ' . $attr . ' title = "' . $title . '" placeholder = "' . $title . '"  >';
        if($showSelect){
            $option .= "<option value=''>Select Sku</option>";
        }
        if (count($rs) > 0) {
            foreach ($rs as $dataItem) {
                $iso = $dataItem->getSku();
                $id = $dataItem->getId();
                $selected = ($id == $sku) ? " selected" : "";
                $option .= '<option ' . $selected . ' value="' . $id . '" data-content="' . ucfirst($sku) . ' ">' . $sku . '</option>';
            }
        }
        $option .= '</select>';
        return $option;
    }

    public static function generateCarrierDDL($dd_name = 'carrier', $selectedCarrier = 0, $attr = ' class="form-control select2" rel="tooltip"' , $dd_id = '') {
        $dd_id = ($dd_id == "") ? $dd_name : $dd_id;
        $carrierCorp = new CarrierFilter();
        $carrierCorp->addFilter(" status = '1'");
        $carrierCorp->AddOrderBy("carrier", true);
        $carrierCorpList = $carrierCorp->getList();
        if (count($carrierCorpList) > 0) {
            foreach ($carrierCorpList as $result) {
                $carrier['carrier'][$result->getId()] = $result;
                $carrier['parent_carrier'][$result->getCarrierId()][] = $result->getId();
            }
        }

        $html = '';
        $html .= '<select name = "' . $dd_name . '" id= "' . $dd_id . '" ' . $attr . '  title = "' . $dropdwonName . '" placeholder = "' . $dropdwonName . '"  >';
        $html .= "<option value=''>Select Carrier</option>";
        $html .= self::getSelectBoxCategories(0, $carrier, $selectedCarrier);
        $html .= '</select>';
        return $html;
    }

    protected static function getSelectBoxCategories($parent, $carrier, $selectedCarrier) {
        $html = "";
        if (isset($carrier['parent_carrier'][$parent])) {
            foreach ($carrier['parent_carrier'][$parent] as $cat_id) {
                if (!isset($carrier['parent_carrier'][$cat_id])) {
                    if ($selectedCarrier == $cat_id)
                        $selected = ' selected="selected"';
                    else
                        $selected = '';
                    $html .= "<option value='" . $cat_id . "' " . $selected . ">" . ucfirst($carrier['carrier'][$cat_id]->getCarrier()) . "</option>";
                }
                if (isset($carrier['parent_carrier'][$cat_id])) {
                    $html .= "<optgroup label='" . $carrier['carrier'][$cat_id]->getCarrier() . "'>";
                    $html .= self::getSelectBoxCategories($cat_id, $carrier, $selectedCarrier);
                    $html .= "</optgroup>";
                }
            }
        }
        return $html;
    }
    protected static function getClassColName($param) {
        return str_replace(" ","",ucwords(strtolower(str_replace("_"," ",$param))));
    }
    
    
    public static function generateCarrierDDLWithImage($dd_name='carrier', $carrier_id = "", $value = 'id', $attr = ' class="bs-select form-control" required="" data-show-subtext="true" data-container="body"', $dd_id='', $title='', $filter='') {
        $carrierCorp = new CarrierFilter();
        $carrierCorp->addFilter(" status = '1'");
        
        $carrierCorp->AddOrderBy("carrier", true);
        if(trim($filter)!='')
        {
            $carrierCorp->addFilter($filter);
        }
        else
        {
            $carrierCorp->addFilter(" id not in ( select distinct carrier_id from carrier WHERE carrier_id > 0)");
        }
        $sessionUser = SessionManager::getUser();
        if($sessionUser->getUserType() == User::USER_TYPE_CORPORATE) {
            $carrierFilter = new CarrierFilter();
            $carrierFilter->addFilter("usr.user_account_id = '" . $sessionUser->getUserAccountId() . "'");
            $allowedCarriersObj = $carrierFilter->getCarrierSetupList('DISTINCT cl.id');
            $allowedCarriers = [];
            if (!empty($allowedCarriersObj)) {
                foreach ($allowedCarriersObj as $allowedCarrier) {
                    $allowedCarriers[] = $allowedCarrier->getId();
                }
                $carrierCorp->addFilter(' id IN ('.implode(",",$allowedCarriers).')');

            }else{
                $carrierCorp->addFilter(" id =  '-50000'");
            }
        }
        $rs =  $carrierCorp->getList();
        $dd_id = ($dd_id == "") ? $dd_name : $dd_id;
        $option = "";
        $option .= '<select name="' . $dd_name . '"  id="' . $dd_id. '" ' . $attr . ' title = "' . $title . '" placeholder = "' . $title . '"  >';
        $option .= "<option value=''>Select Carrier</option>";
        if (count($rs) > 0) {
            foreach ($rs as $dataItem) {
                $id         = $dataItem->getId();
                $name       = $dataItem->getCarrier();
                $logo       = $dataItem->getLogo();
                $zoneBase   = $dataItem->getZoneBase();
                $zoneType   = $dataItem->getZoneType();
                $logoPath = '../images/carrierlogo/thumbnail/owe_16_' . $logo;
                if(!file_exists($logoPath)){
                    $logoPath = '../images/carrierlogo/thumbnail/owe_16_no_image.png';
                }
                $display_name = $name;

                if ($name == "")
                    continue;
                $selected = ($id == $carrier_id) ? " selected" : "";
                $option .= '<option ' . $selected . ' value="' . $id . '" data-zone_base="'.$zoneBase.'" data-zone_type="'.$zoneType.'" data-content="<img src=\'' . $logoPath . '\' /> ' . ucfirst($display_name) . ' ">' . $display_name . '</option>';
            }
        }
        $option .= '</select>';
        return $option;
    }
    
    
    public static function generateMarketPlaceDDLWithImage($dd_name='marketPlace', $marketPlace_id = "", $value = 'id', $attr = ' class="bs-select form-control" required="" data-show-subtext="true" data-container="body"', $dd_id='', $title='', $filter='', $mkid='') 
    {
        $user =  SessionManager::getUser();        
        
        $userMarketPlaceMapping = new UserMarketPlacesMappingFilter();
        $userMarketPlaceMapping->addFieldFilter("user_account_id", $user->getUserAccountId());
         $userMarketPlaceMapping->addFieldFilter("active", '1'); /// commented only for demo purpose
        //$userMarketPlaceMapping->addFieldFilter("market_places_id", $mkid); // added only for demo purpose
        $userMarketPlaceMapping->addJoin("market_places mp", "ump.market_places_id = mp.id");
        $rs = $userMarketPlaceMapping->getList();
        

        $dd_id = ($dd_id == "") ? $dd_name : $dd_id;
        $option = "";
        $option .= '<select name="' . $dd_name . '"  id="' . $dd_id. '" ' . $attr . ' title = "' . $title . '" placeholder = "' . $title . '"  >';
        $option .= "<option value=''>Marketplace</option>";
        if (count($rs) > 0) {
            foreach ($rs as $dataItem) {
                $id         = $dataItem->getMarketPlacesId();
                $name       = $dataItem->getTitle();
                $logo       = $dataItem->getIntegrationLogo();
                if(empty($logo)) {
                    $logo = 'No-image-found.jpg';
                }
                $logoPath = '../images/thirdparty/' . $logo;
               
                if(!file_exists($logoPath)){
                    $logoPath = '../images/No-image-found.jpg';
                }
                $display_name = $name;

                if ($name == "")
                    continue;
                $selected = ($id == $marketPlace_id) ? " selected" : "";
            //    echo $display_name;
                $option .= '<option ' . $selected . ' value="' . $id . '"  data-content="<img src=\'' . $logoPath . '\' / width=16> ' . ucfirst($display_name) . ' ">' . $display_name . '</option>';
            }
        }
        $option .= '</select>';
        return $option;
    }

    public static function generateWarehouseDDLWithImage($dd_name='warehouse_id', $warehouseID = "", $value = 'id', $attr = ' class="bs-select form-control" required="" data-show-subtext="true" data-container="body"', $dd_id='warehouse_id', $title='', $filter='', $mkid='')
    {
        $user =  SessionManager::getUser();

        $warehouseObj = new WarehouseFilter();
        $warehouseObj->addFieldFilter("is_active", '1'); /// commented only for demo purpose
        $warehouseObj->addFieldFilter("is_deleted", '0'); /// commented only for demo purpose
        //$userMarketPlaceMapping->addFieldFilter("market_places_id", $mkid); // added only for demo purpose
        $rs = $warehouseObj->getList();


        $dd_id = ($dd_id == "") ? $dd_name : $dd_id;
        $option = "";
        $option .= '<select name="' . $dd_name . '"  id="' . $dd_id. '" ' . $attr . ' title = "' . $title . '" placeholder = "' . $title . '"  >';
        $option .= "<option value=''>Select Hub</option>";
        if (count($rs) > 0) {
            foreach ($rs as $dataItem) {
                $id         = $dataItem->getId();
                $name       = $dataItem->getWarehouseName();
                $selected = ($id == $warehouseID) ? " selected" : "";
                //    echo $display_name;
                $option .= '<option ' . $selected . ' value="' . $id . '" >' . $name . '</option>';
            }
        }
        $option .= '</select>';
        return $option;
    }
    
    public static function generateServiceDDLWithImage($dd_name='carrier', $selectedServiceId = "", $value = 'id', $attr = ' class="bs-select form-control" required="" data-show-subtext="true" data-container="body"',$dd_id='',$title='',$displayValue='code',$selectTitle='Select Services', $carrierId = 0, $onlyServices = false, $serviceIds = array()) {
       include_classes([
    'services.class',
    'servicesfilter.class',
    'carrier.class',
    'carrierfilter.class',
           ]);
        $serviceFilter      = new ServiceFilter();
        $serviceFilter->addFilter("AND active = '1' AND deletedq = 0");
        $sessionUser = SessionManager::getUser();
        if($sessionUser->getUserType() == User::USER_TYPE_CORPORATE) {
            $carrierFilter = new CarrierFilter();
            $carrierFilter->addFilter("usr.user_account_id = '" . $sessionUser->getUserAccountId() . "'");
            $allowedServicesObj = $carrierFilter->getCarrierSetupList('DISTINCT s.id');
            $allowedServices = [];
            if (!empty($allowedServicesObj)) {
                foreach ($allowedServicesObj as $allowedService) {
                    $allowedServices[] = $allowedService->getId();
                }
                $serviceFilter->addFilter(' ser.id IN ('.implode(",",$allowedServices).')');

            }else{
                $serviceFilter->addFilter(" ser.id =  '-50000'");
            }
        }
        if(!empty($carrierId)) {
            $serviceFilter->addFilter(" ser.carrier_id =  $carrierId");
        }
		if($onlyServices) {
            $serviceFilter->addFilter(" ser.is_customized =  0");
        }
		if(!empty($serviceIds)) {
            $serviceFilter->addFilter(" ser.id IN ('". implode("','", $serviceIds)."')");
        }        
		$rs =  $serviceFilter->getCarrierServicesList("ser.id, ser.name, ser.code, ca.logo 'carrier_logo' ");
        $dd_id = ($dd_id == "") ? $dd_name : $dd_id;
        $option = "";
        $option .= '<select name="' . $dd_name . '"  id="' . $dd_id. '" ' . $attr . ' title = "' . $title . '" placeholder = "' . $title . '"  >';
        if(!empty($selectTitle))
            $option .= "<option value=''>".$selectTitle."</option>";
        if (count($rs) > 0) {
            foreach ($rs as $dataItem) {
                $id     = $dataItem->getId();
                if($displayValue=='code')
                    $name   = strtoupper($dataItem->getCode());
                else if($displayValue=='name_code')
                    $name   = ucfirst(strtolower($dataItem->getName()))." [".strtoupper($dataItem->getCode())."]";
                else                     
                    $name   = ucfirst(strtolower($dataItem->getName()));
                $logo   = $dataItem->getCarrierLogo();
                $display_name = $name;

                $logoPath = '../images/carrierlogo/thumbnail/owe_16_' . $logo;
                if(!file_exists($logoPath)){
                    $logoPath = '../images/no_image_found_14_16.png';
                }

                if ($name == "")
                    continue;
                $selected = ($id == $selectedServiceId) ? " selected" : "";
                $option .= '<option ' . $selected . ' value="' . $id . '" data-content="<img src=\'' . $logoPath . '\' /> ' . $display_name . ' ">' . $display_name . '</option>';
            }
        }
        $option .= '</select>';
        return $option;
    }
    public static function showTreeDropdown($dd_name, $table_name, $select_col, $id_col="id", $parent_id = 0, $where = array(), $selected = "", $default_select = "", $attr = "", $dd_id = "", $sort_order = "",$image_column = '',$folder_path='',$image_prefix="", $include_parent = true, $allowed_level = 0, $parentCol = "parentid") {
        $output = "";
        $level = 1;
        $output .= '<select id="' . ($dd_id != '' ? $dd_id : $dd_name) . '" name="' . $dd_name . '" ' . $attr . ' data-container="body">';

        if ($default_select != "") {
            $output .= '<option value="">' . $default_select . '</option>';
        }
        if ($parent_id > 0) {
            $select_col_str = "";
            if(is_array($select_col)) {
                $select_col_str = implode(",", $select_col);
            }else if(is_string($select_col) && $select_col != "") {
                $select_col_str = $select_col;
            }
            $select_col_str .= ",".$id_col.($image_column != "" ? ",".$image_column:"");
            $whereStr = "";
            if (is_array($where) && count($where) > 0){
                $whereStr .= " AND ".implode(" AND ",$where);
            }else if(is_string($where) && $where != "") {
                $whereStr .= $where;
            }
            $sql = "SELECT ".$select_col_str." FROM ".$table_name." WHERE ".$id_col." = '".$parent_id."' ".$whereStr;
            if ($sort_order != "")
            $sql .= " ORDER BY ".$sort_order;
            $reslt = DbAccess3::runQueryWithError($sql);
            if (DbAccess3::getNumRows($reslt) > 0) {
                while ($obj = DbAccess3::getObject($reslt)) {
                    $indent = str_repeat('&nbsp;', ($level -1) * 3);
                    $data_attribute = '';
                    $option_name = '';
                    if (is_array($select_col)) {
                        $name_arr = array();
                        foreach ($select_col as $col) {
                            $name_arr[] = $obj->$col;
                        }
                        $option_name = implode(" ", $name_arr);
                    } else {
                        $option_name = $obj->$select_col;
                    }
                    $imagePath = "";
                    if(!empty($image_column)){
                        if($image_prefix.$obj->$image_column !="" && file_exists($folder_path.$image_prefix.$obj->$image_column)){
                            $imagePath = 'data-content="<img src='.$folder_path.$image_prefix.$obj->$image_column.' /> ' . ucfirst($option_name).'"';
                        }else{
                            $imagePath = 'data-content="<img src=../images/no_image_found_14_16.png  /> ' . ucfirst($option_name).'"';
                        }
                    }
                    if($include_parent) {
                        $_selected = "";
                        if(is_array($selected)){
                            $_selected = (in_array($obj->$id_col, $selected) ? ' selected="selected"' : '');
                        } else {
                            $_selected = ($obj->$id_col == $selected ? ' selected="selected"' : '');
                        }
                        $output .= '<option '.$imagePath.' data-parentid="'.$parent_id.'" '.$data_attribute.' value="' . $obj->$id_col . '"'. $_selected .'>' . $indent.' '.ucfirst($option_name).'</option>';
                    }
                    $level++;
                }
            }
        }
        $output .= self::_treeDropdown($table_name, $select_col, $id_col, $selected, $where, $sort_order, $parent_id, $level,$image_column,$folder_path,$image_prefix,$allowed_level, $parentCol);
        $output .= '</select>';
        return $output;
    }

    public static function _treeDropdown($table_name, $select_col, $id_col, $selected_valude, $where, $sort_order, $parent_id, $level = 0,$image_column = '',$folder_path='',$image_prefix="",$allowed_level,$parentCol) {
        $output = "";
        $whereStr = "";
        $select_col_str = "";
        if(is_array($select_col)) {
            $select_col_str = implode(",", $select_col);
        }else if(is_string($select_col) && $select_col != "") {
            $select_col_str = $select_col;
        }
        $select_col_str .= ",".$id_col.($image_column != "" ? ",".$image_column:"");
        $parentWhere = $parentCol." = '".$parent_id."'";
        if($parent_id == 0 ){
            $parentWhere .= " OR ".$parentCol." IS NULL";
        }
        $sql = "SELECT ".$select_col_str." FROM ".$table_name." WHERE ".$parentWhere;
        if (is_array($where) && count($where) > 0){
            $whereStr .= implode(" AND ",$where);
        }else if(is_string($where) && $where != "") {
            $whereStr .= $where;
        }
        $sql .= " AND ".$whereStr;
        if ($sort_order != "")
            $sql .= " ORDER BY ".$sort_order;

        $reslt = DbAccess3::runQueryWithError($sql);
        if (DbAccess3::getNumRows($reslt) > 0) {
            while ($obj = DbAccess3::getObject($reslt)) {
                $indent = "";
                if ($level > 0) {
                    for ($i = 1; $i < $level; $i++) {
                        $indent .=  str_repeat('&nbsp;', 4);
                    }
                }
                $data_attribute = '';
                $option_name = '';
                if (is_array($select_col)) {
                    $name_arr = array();
                    foreach ($select_col as $col) {
                        $name_arr[] = $obj->$col;
                    }
                    $option_name = implode(" ", $name_arr);
                } else {
                    $option_name = $obj->$select_col;
                }
                $imagePath = "";
                if(!empty($image_column)){
                    if($image_prefix.$obj->$image_column !="" && file_exists($folder_path.$image_prefix.$obj->$image_column)){
                        $imagePath = 'data-content="<img src='.$folder_path.$image_prefix.$obj->$image_column.' /> ' . $indent. ucfirst($option_name).'"';
                    }else{
                        $imagePath = 'data-content="<img src=../images/no_image_found_14_16.png  /> ' . $indent. ucfirst($option_name).'"';
                    }
                }

                $selected = "";
                if(is_array($selected_valude)){
                    $selected = (in_array($obj->$id_col, $selected_valude) ? ' selected="selected"' : '');
                } else {
                    $selected = ($obj->$id_col == $selected_valude ? ' selected="selected"' : '');
                }

                $output .= '<option '.$imagePath.' data-parentid="'.$parent_id.'" '.$data_attribute.' value="' . $obj->$id_col . '"' . $selected . '>'. $indent. ucfirst($option_name).'</option>\n';
                if($level <= $allowed_level || $allowed_level <= 0)
                    $output .= self::_treeDropdown($table_name, $select_col, $id_col, $selected_valude, $where, $sort_order, $obj->$id_col, $level+1,$image_column,$folder_path,$image_prefix,$allowed_level,$parentCol);
            }
        }
        return $output;
    }

    public static function showAccountTreeDropdown($parent_id = 0, $selected = '', $attr=[],$default_select = "",$default_select_val = "", $include_self= true){
        $default_attributes = [
                                'name'=>'user_account_id',
                                'id'=>'user_account_id',
                                'class'=>'form-filter bs-select form-control',
                                'data-live-search' => 'true',
                                'data-container' => 'body',
                                'data-original-title'=>'',
                                'title'=>''
                              ];
        $attribitesArr = array_merge($default_attributes, $attr);
        $attribites = "";
        foreach($attribitesArr as $attribute => $attributeVal){
            $attribites .= ' '.$attribute .'="'.$attributeVal.'"';
        }
        $option = "";
        $option .= '<select'.$attribites.'>';
        $default_select = trim($default_select);
        if($default_select != ""){
            $option .= '<option value="'.$default_select_val.'">'.$default_select.'</option>';
        }
        $level = 0;
        if($include_self && $parent_id > 0){
            $level = 1;
            $userAccount = new CustomerAccount($parent_id);
            if(!empty($userAccount)){
                $selectedStr = ($userAccount->getId() == $selected ? ' selected="selected"' : '');
                $logoPath = '../images/userlogo/thumbnail/owe_16_' . $userAccount->getLogo();
                if(!file_exists($logoPath)){
                    $logoPath = '../images/no_image_found_14_16.png';
                }
                $option .= '<option ' . $selectedStr . ' value="' . $userAccount->getId() . '" data-content="<img src=\'' . $logoPath . '\' /> ' . ucfirst($userAccount->getUserAccount()) . ' ">' . $userAccount->getUserAccount() . '</option>';
            }
        }
        $sql = "WITH RECURSIVE obs_tree AS (
                   SELECT id, user_account, logo, ".$level." AS level, parentid, CAST(id AS CHAR) AS tree
                   FROM `user_account`
                   WHERE parentid = '".DbAccess3::escape($parent_id)."'
                   UNION ALL 
                   SELECT t.id, t.user_account, t.logo, p.level + 1, t.parentid, CONCAT(p.tree,'/',CAST(t.id AS CHAR))
                   FROM `user_account` t JOIN obs_tree p ON t.parentid = p.id
                )
                SELECT * FROM obs_tree ORDER BY tree";


        $result = mysqli_query(DbAccess3::getConnection(),$sql);
        $totalAccounts = DbAccess3::getNumRows($result);
        if($totalAccounts > 0){
            while ($valArray = mysqli_fetch_assoc($result)) {
                $level = $valArray['level'];
                $indent = "";
                if ($level > 0) {
                    for ($i = 1; $i <= $level; $i++) {
                        $indent .=  str_repeat('&nbsp;', 4);
                    }
                }
                $selectedStr = $valArray['id'] == $selected ? ' selected="selected"' : '';
                $logoPath = '../images/userlogo/thumbnail/owe_16_' . $valArray['logo'];
                if(!file_exists($logoPath)){
                    $logoPath = '../images/no_image_found_14_16.png';
                }
                $option .= '<option ' . $selectedStr . ' value="' . $valArray['id'] . '" data-content="<img src=\'' . $logoPath . '\' /> ' . $indent .ucfirst($valArray['user_account']) . ' ">' . $indent .$valArray['user_account'] . '</option>';
            }
        }
        $option .= '</select>';
        return $option;
    }
    public static function showTreeDropdownuser($dd_name, $table_name, $select_col, $id_col="id", $parent_id = 0, $where = array(), $selected = "", $default_select = "", $attr = "", $dd_id = "", $sort_order = "",$image_column = '',$folder_path='',$image_prefix="", $include_parent = true) {
       
        $output = "";
        $level = 0;
        $output .= '<select id="' . ($dd_id != '' ? $dd_id : $dd_name) . '" name="' . $dd_name . '"' . $attr . ' data-container="body">';

        if ($default_select != "") {
            $output .= '<option value="">' . $default_select . '</option>';
        }
        if ($parent_id > 0) {
            $select_col_str = "";
            if(is_array($select_col)) {
                $select_col_str = implode(",", $select_col);
            }else if(is_string($select_col) && $select_col != "") {
                $select_col_str = $select_col;
            }
            $select_col_str .= ",".$id_col.($image_column != "" ? ",".$image_column:"");
            $whereStr = "";
            if (is_array($where) && count($where) > 0){
                $whereStr .= " AND ".implode(" AND ",$where);
            }else if(is_string($where) && $where != "") {
                $whereStr .= $where;
            }
            
            $sql = "SELECT ".$select_col_str." FROM ".$table_name." WHERE ".$id_col." = '".$parent_id."' ".$whereStr;
            
            $reslt = DbAccess3::runQueryWithError($sql);
            if (DbAccess3::getNumRows($reslt) > 0) {
                while ($obj = DbAccess3::getObject($reslt)) {
                    $indent = str_repeat('&nbsp;', $level * 3);
                    $data_attribute = '';
                    $option_name = '';
                    if (is_array($select_col)) {
                        $name_arr = array();
                        foreach ($select_col as $col) {
                            $name_arr[] = $obj->$col;
                        }
                        $option_name = implode(" ", $name_arr);
                    } else {
                        $option_name = $obj->$select_col;
                    }
                    $imagePath = "";
                    if(!empty($image_column)){
                        if($image_prefix.$obj->$image_column !="" && file_exists($folder_path.$image_prefix.$obj->$image_column)){
                            $imagePath = 'data-content="<img src='.$folder_path.$image_prefix.$obj->$image_column.' /> ' . ucfirst($option_name).'"';
                        }else{
                            $imagePath = 'data-content="<img src=../images/no_image_found_14_16.png  /> ' . ucfirst($option_name).'"';
                        }
                    }
                    if($include_parent){
                        
                        $output .= '<optgroup '.$imagePath.' label="'.$indent.ucfirst($option_name).'" >';
                        $sqluser = "SELECT id, user_name FROM user WHERE user_account_id = '".$obj->id."' and active_flag = 1 ";
                        $resltuser = DbAccess3::runQueryWithError($sqluser);
                        if (DbAccess3::getNumRows($resltuser) > 0) {
                            while ($objuser = DbAccess3::getObject($resltuser)) {
                                $output .= '<option value="' . $objuser->id . '"'.($objuser->id == $selected ? ' selected="selected"' : '').'  > '.$indent.ucfirst($objuser->user_name).'</option>';
                            }
                        }
                        $output .= '</optgroup>'; 
                    }
                    //$output .= '<option '.$imagePath.' data-parentid="'.$parent_id.'" '.$data_attribute.' value="' . $obj->$id_col . '"'.($obj->$id_col == $selected ? ' selected="selected"' : '').'>' . $indent.' '.ucfirst($option_name).'</option>';
                    $level++;
                }
            }
        }
        $output .= self::_treeDropdownuser($table_name, $select_col, $id_col, $selected, $where, $sort_order, $parent_id, $level,$image_column,$folder_path,$image_prefix);
        $output .= '</select>';
        return $output;
    }

    public static function _treeDropdownuser($table_name, $select_col, $id_col, $selected_valude, $where, $sort_order, $parent_id, $level = 0,$image_column = '',$folder_path='',$image_prefix="") {
        $output = "";
        $whereStr = "";
        $select_col_str = "";
        if(is_array($select_col)) {
            $select_col_str = implode(",", $select_col);
        }else if(is_string($select_col) && $select_col != "") {
            $select_col_str = $select_col;
        }
        $select_col_str .= ",".$id_col.($image_column != "" ? ",".$image_column:"");
        $parentWhere = "parentid = '".$parent_id."'";
        if($parent_id == 0 ){
            $parentWhere .= " OR parentid IS NULL";
        }
        $sql = "SELECT ".$select_col_str." FROM ".$table_name." WHERE ".$parentWhere;        
        if (is_array($where) && count($where) > 0){
            $whereStr .= implode(" AND ",$where);
        }else if(is_string($where) && $where != "") {
            $whereStr .= $where;
        }
        $sql .= " AND ".$whereStr;
        if ($sort_order != "")
            $sql .= " ORDER BY ".$sort_order;        
        $reslt = DbAccess3::runQueryWithError($sql);
        if (DbAccess3::getNumRows($reslt) > 0) {
            while ($obj = DbAccess3::getObject($reslt)) {
                $indent = "";
                if ($level > 0) {
                    for ($i = 1; $i <= $level; $i++) {
                        $indent .=  str_repeat('&nbsp;', 4);
                    }
                }
                $data_attribute = '';
                $option_name = '';
                if (is_array($select_col)) {
                    $name_arr = array();
                    foreach ($select_col as $col) {
                        $name_arr[] = $obj->$col;
                    }
                    $option_name = implode(" ", $name_arr);
                } else {
                    $option_name = $obj->$select_col;
                }
                $imagePath = "";
                if(!empty($image_column)){
                    if($image_prefix.$obj->$image_column !="" && file_exists($folder_path.$image_prefix.$obj->$image_column)){
                        $imagePath = 'data-content="<img src='.$folder_path.$image_prefix.$obj->$image_column.' /> ' . $indent. ucfirst($option_name).'"';
                    }else{
                        $imagePath = 'data-content="<img src=../images/no_image_found_14_16.png  /> ' . $indent. ucfirst($option_name).'"';
                    }
                }
                
                        $output .= '<optgroup '.$imagePath.' label="'.$indent.ucfirst($option_name).'" >';
                        $sqluser = "SELECT id, user_name FROM user WHERE user_account_id = '".$obj->id."' and active_flag = 1 ";
                        $resltuser = DbAccess3::runQueryWithError($sqluser);
                        if (DbAccess3::getNumRows($resltuser) > 0) {
                            while ($objuser = DbAccess3::getObject($resltuser)) {
                                $output .= '<option value="' . $objuser->id . '"'.($objuser->id == $selected_valude ? ' selected="selected"' : '').'  > '.$indent.ucfirst($objuser->user_name).'</option>';
                            }
                        }
                        $output .= '</optgroup>'; 
                
                    
                //$output .= '<option '.$imagePath.' data-parentid="'.$parent_id.'" '.$data_attribute.' value="' . $obj->$id_col . '"'.($obj->$id_col == $selected_valude ? ' selected="selected"' : '').'>'. $indent. ucfirst($option_name).'</option>\n';
                $output .= self::_treeDropdownuser($table_name, $select_col, $id_col, $selected_valude, $where, $sort_order, $obj->$id_col, $level+1,$image_column,$folder_path,$image_prefix);
            }
        }
        return $output;
    }
}
