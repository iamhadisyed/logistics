<?php

class HelpDeskTicketMessage extends DbAccess3 {

    /**
     * Construct
     *
     * @param id/array
     */
    public function __construct($mixedCreator = null) {

        $fieldList = array(
            'id' => 'number',
            'ticketid' => 'number',
            'message' => 'string',
            'attachment' => 'string',
            'addedby' => 'string',
            'added_date' => 'datetime',
            'updatedby' => 'string',
            'updated_date' => 'datetime');
        //
        parent::__construct("helpdesk_ticket_message", 'id', $fieldList, $mixedCreator);
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