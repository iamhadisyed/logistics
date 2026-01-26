<?php

class SalesPotComission extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {




        $fieldList = array(
            'id' => 'number',
            'date_added' => 'string',
            'company_comission' => 'number',
            'sales_comission' => 'number',
            'no_of_shipments' => 'number',
            'comission' => 'number',
            'is_paid' => 'number',
            'paid_by' => 'number',
            'paid_date' => 'datetime',
            'salepot_table_data' => 'string',
            'company_sale_percentage' => 'number',
            'sale_pot_percentage' => 'number',
            'sale_user_id' => 'number',
            'invoice_id' => 'number',
            'added_by' => 'number',
            'date_created' => 'undefined',
            'invoice_no' => 'undefined',
            'invoice_date_created' => 'undefined',
            'net_amount' => 'undefined',
            'user_account' => 'undefined',
            );
        //
        parent::__construct("sales_pot_comission", 'id', $fieldList, $mixedCreator);
    }

    
    /**
     * Get list of Sample objects, using sql given
     *
     * @param string $sql
     */
    public static function getSalesPotComissionListFromSql($sql) {

        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    

    /**
     * Get count of Sample objects, using sql given
     *
     * @param string $sql
     */
    public static function getSalesPotComissionCountFromSql($sql) {
        return DbAccess3::getSql(__CLASS__, $sql);
    }
    public static function getTotalNumberOfSalePotComissionFromSql($sql)
    {
        $rs = DbAccess3::runQuery($sql);
        $data=mysqli_fetch_assoc($rs);
        return $data['total'];
    }

}

// class