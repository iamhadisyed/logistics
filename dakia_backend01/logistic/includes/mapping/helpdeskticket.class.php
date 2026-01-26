<?php

class HelpDeskTicket extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {
        $fieldList = array(
            'id' => 'number',
            'ticket_code' => 'string',
            'department_id' => 'number',
            'priority' => 'string',
            'subject' => 'string',
            'status' => 'string',
            'addedby' => 'string',
            'added_date' => 'datetime',
            'updatedby' => 'string',
            'updated_date' => 'datetime'
        );
        //
        parent::__construct("helpdesk_ticket", 'id', $fieldList, $mixedCreator);
    }
    

    /**
     * Get list of HelpDeskMessage objects, using sql given
     *
     * @param string $sql
     */
    public static function getTicketListFromSql($sql) {
        return DbAccess3::getListFromSql(__CLASS__, $sql);
    }
    

    /**
     * Get count of consignment objects, using sql given
     *
     * @param string $sql
     */
    public static function getTicketCountFromSql($sql) {
        return DbAccess3::getSql(__CLASS__, $sql);
    }

}

// class