<?php

/*
 * Version 3 of DbAccess - Database access class
 *
 * Use in place of earlier versions, class name unversioned - selection controlled by include list.
 *
 * Notes.
 * The primary key is always assumed to be numerical
 * This is not backwards compatible with version 1.
 *
 * Usage:
 *  The table name, primary key and field list are passed to the base constructor.
 *  The primary key field should NOT be included within the field list.
 *  Assumes a auto increment field is being used for Id.
 *  The field types are declared in the field list; types are: string, number, datetime
 *  Database row values are stored in array $valArray, in the base object.
 *  A getList method is provided to get list of objects in a single database hit.  The
 *  name of the object type is passed as first parameter of the getList method.
 *  There are magic methods for basic get/set operation.  This should be overridden
 *  as required (e.g. non number/string data types).
 *  Date Time fields are set/got as unix time stamps
 * e.g.

  class SampleClass extends DbAccess3
  {
  public function __construct($mixedCreator = null)
  {
  $fieldList =
  array(  'customerid'    => 'number',
  'name'          => 'string',
  'start_date'    => 'datetime'
  );
  //
  parent::__construct("parcel_groups", 'id', $fieldList, $mixedCreator);
  }

  public static function getList($filterArray = null)
  {
  return parent::getList(__CLASS__, $filterArray);
  }

  // The following magic methods weould be available:
  // - getId, setId, getName, setName, getStartDate, setStartDate.
  }

 *
 *
 * Changes
 * -------
 * Version 3 removed the dependency on the DB class.
 * This extra layer provided no gains, so this class was updated to use the mysql methods directly.
 *
 * ***************************************************************************************** */

class DbAccess3
{

    private static $connection;
    //
    protected $valArray;
    //
    private $primaryKey = "";
    private $tableName = "";
    private $fieldList = null;
    protected $modifyArray = array();
    public static $dbError = array();
    public $logArray = array();
    public $logMoreDataNew = array();
    public $logMoreDataOld = array();
    public $eventKey = '';

    public $eventOldArray = array();
    public $eventNewArray = array();

    public $auditLogDataOld = array();
    public $auditLogDataNew = array();
    public $saveLog = true;
    public $tableKey = 0;
    public $auditTableName = '';
    public $custom_message = '';

    /**
     * Connect to database
     * @param string $server
     * @param string $user
     * @param string $password
     * @param string $database
     * @return void
     */
    public static function closeConnection()
    {
        mysqli_close(self::$connection);
    }

    public static function connect($server, $user, $password, $database)
    {

        self::$connection = mysqli_connect($server, $user, $password)
        or die("Could not connect to database server.");

        mysqli_select_db(self::$connection, $database)
        or die("Could not select database");
        mysqli_set_charset ( self::$connection , "utf8");
        return self::$connection;
    }

    /**
     * Creation of DB mappping object.
     * The first item in the field list has to be the primary key field.
     * If no creator value is passed an empty object is created.
     * If an integer is passed, tries to look up corrosponding record in datbase.
     * If an array is passed, then this is used as value array for this objeect,
     * i.e. sets a pre populated record.
     *
     * @param string $tableName
     * @param string $primaryKey
     * @param array $fieldList
     * @param mixed $mixedCreator
     */
    public function __construct($tableName, $primaryKey, $fieldList, $mixedCreator = null, $debug = false)
    {
        $this->tableName = $tableName;
        $this->primaryKey = $primaryKey;
        $this->fieldList = $fieldList;
        //
        $successFlag = false;
        //
        if ($mixedCreator != null) {
            //t("Populate on contruction", __METHOD__);
            // Look up for given id
            if (is_numeric($mixedCreator)) {
                // Get values from database
                $successFlag = $this->fillFromDatabase($mixedCreator, $debug);
            } else if (is_array($mixedCreator)) {
                $successFlag = $this->fillFromArray($mixedCreator, $debug);
            }
        }
        // Create blank array if not created
        if (!$successFlag)
            $this->fillFromArray(array(), $debug);
    }

    /**
     * Fill object from existing array
     *
     * @param array of values
     * @return bool
     */
    public function fillFromArray($aValueArray, $debug = false)
    {
        // ensure a primary field is set
        if (!isset($aValueArray[$this->primaryKey]))
            $aValueArray[$this->primaryKey] = 0;
        //
        foreach ($this->fieldList as $field => $type) {
            //if the field array type is external then it will not save into database
            if ($type == "external" || $type == "undefined") continue;

            // default values
            $val = (($type == "number") ? 0 : "");

            // Add field if not in the value array passed
            if (!isset($aValueArray[$field]))
                $aValueArray[$field] = $val;
        }
        $this->valArray = $aValueArray;

        return true;
    }

    /**
     * Fill
     *
     * @param unknown_type $id
     * @return unknown
     */
    public function fillFromDatabase($id, $debug = false)
    {
        if ($id > 0) {
            $filter = array();
            $filter[$this->primaryKey] = $id;
            //
            $sql = $this->getSql($filter);
            if ($debug)
                echo $sql;
            //	t($sql, __METHOD__);
            //
            $rs = mysqli_query(self::$connection, $sql);
            if($rs !== false)
            {
                // if successfully got values,
                if ($row = mysqli_fetch_assoc($rs)) {
                    $this->valArray = $row; // only overwrite if get successful
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Magic setter and getter method
     *
     * @param string $method
     * @param any $params
     */
    function __call($method, $params)
    {

        $ok = false;
        $setter = substr($method, 0, 3);
        $field_key = "";

        // Check if setter or getter
        if (($setter == 'get') || ($setter == 'set')) {
            $name = strToLower(substr($method, 3));
            //
            foreach (array_keys($this->fieldList) as $key) {
                $cleanKey = str_replace("_", "", $key);
                if ($cleanKey == $name) {
                    $field_key = $key;
                    // coded by tahir
                    if ($setter == 'set') {
                        $this->modifyArray[$field_key] = $field_key;

                        if ($this->valArray[$field_key] != $params[0])
                            $this->logArray[$field_key] = $field_key;
                    }
                    break;
                }
            }
        }

        // If method name not found then throw error.
        if ($field_key == "") {
            throw new Exception('table "' . $this->tableName . '" does not have a field for method "' . $method . '".');
        } else {
            // Get the required value
            if ($setter == "get") {
                // Check for datetime type
                if ($this->fieldList[$field_key] == "datetime") {
                    return strtotime($this->valArray[$field_key]);
                }
                return $this->valArray[$field_key];
            } else { // Set the value
                if (sizeof($params) > 0) {
                    // Check for datetime type.
                    if ($this->fieldList[$field_key] == "datetime") {
                        $this->valArray[$field_key] = date("Y-m-d H:i:s", $params[0]);
                    } else {
                        $this->valArray[$field_key] = $params[0];
                    }
                }
            }
        }
    }

    /**
     * Basic save method.  Updates or Inserts record depending
     * on whether the primary key is set.
     * if $setterParam is false then the MODIFYARRAY will be checked and update accordingly otherwise
     *    only class default array will be considered and update the whole record
     */
    public function save($setterParam = false, $debug = false)
    {
     //   echo $this->eventKey; die;
        $fieldSql = "";
        $logFields = [];
        // Ignore primary key
        foreach ($this->fieldList as $field => $_type) {

            $type = "";
            $posibleValues = [];
            $enumDefault = "";
            if (is_array($_type)) {
                foreach ($_type as $t => $v) {
                    if ($t == 'default') {
                        $enumDefault = $v;
                    } else {
                        $type = $t;
                        $posibleValues = $v;
                    }
                }
            } else {
                $type = $_type;
            }
            $encloseChar = ($type == "number" || $type == "enum" || $type == "bit") ? "" : "'";
            //if the field array type is external then it will not save into database
            if ($type == "external" || $type == "undefined") continue;

            //
            if (($type == "number") && !is_numeric($this->valArray[$field])) {
                $this->valArray[$field] = "NULL";
            }
            if (($type == "bit") && !is_numeric($this->valArray[$field])) {
                $this->valArray[$field] = 0;
            }

            if (($type == "enum") && !empty($this->valArray[$field]) && !in_array($this->valArray[$field], $posibleValues)) {
                $this->valArray[$field] = $enumDefault != "" ? $enumDefault : "NULL";
            } else if ($type == "enum" && empty($this->valArray[$field])) {
                $this->valArray[$field] = $enumDefault != "" ? $enumDefault : "NULL";
            }

            if (($type == "number" || $type == "enum") && !empty($this->valArray[$field]) && $this->valArray[$field] != "NULL") {
                $encloseChar = "'";
            }
            // If section is based on update query
            if ($this->valArray[$this->primaryKey] > 0) {
                if ($setterParam === false) {
                    if (isset($this->valArray[$field]) && isset($this->modifyArray[$field])) {
                        $logFields[] = $field;
                        $fieldSql .= ", " . $field . "=" . $encloseChar . addslashes($this->valArray[$field]) . $encloseChar;
                    }
                } else {
                    if (isset($this->valArray[$field])) {
                        $logFields[] = $field;
                        $fieldSql .= ", " . $field . "=" . $encloseChar . addslashes($this->valArray[$field]) . $encloseChar;
                    }
                }
            } else {
                // An unset datetime field - set to null
                if ($type == "datetime") {
                    if ($this->valArray[$field] == "") {
                        $logFields[] = $field;
                        $fieldSql .= ", $field=NULL ";
                        continue;
                    }
                }
                if ($type == "date") {
                    if ($this->valArray[$field] == "") {
                        $logFields[] = $field;
                        $fieldSql .= ", $field=NULL ";
                        continue;
                    }
                }
                //
                $logFields[] = $field;
                $fieldSql .= ", " . $field . "=" . $encloseChar . addslashes($this->valArray[$field]) . $encloseChar;
            }
        }
        // If primary key value set, then update else insert
        $userData = SessionManager::getUser();
        $userAudit = new UserAudit();

        if ($this->valArray[$this->primaryKey] > 0) {
            $logSql = "SELECT " . implode(",", $logFields) . " FROM " . $this->tableName . " WHERE " . $this->primaryKey . "=" . $this->valArray[$this->primaryKey];
            $rs = self::runQuery($logSql);

            if(!empty($this->eventNewArray))
            {
                $new_data = json_encode($this->eventNewArray);
            }
            else
            {
                $new_data = json_encode(array_merge($this->valArray, $this->logMoreDataNew));
            }

            if(empty($this->tableKey)) {
                $this->tableKey = $this->valArray[$this->primaryKey];
            }
            if($this->eventKey != '')
                $eventKey = $this->eventKey;
            else
                $eventKey = 'update';

            if(empty($this->auditTableName)) {
                $this->auditTableName = $this->tableName;
            }
            if($this->saveLog == true) {
                $old_data = json_encode(array_merge(mysqli_fetch_assoc($rs), $this->logMoreDataOld));
                // old data from data base mysqli_fetch_assoc($rs)
                $userAudit->insertAuditData($this->auditTableName, $eventKey, $userData->getFirstName() . ' ' . $userData->getLastName(), $userData->getId(), $new_data, $this->tableKey, $old_data, $this->custom_message);
            } else {
                $this->auditLogDataOld = mysqli_fetch_assoc($rs);
                $this->auditLogDataNew = $this->valArray;
            }

            //$userAudit->insertAuditData($this->tableName, $eventKey, $userData->getFirstName() . ' ' . $userData->getLastName(), $userData->getId(), $new_data, $this->tableKey, $old_data);
            $sql = "UPDATE " . $this->tableName . " SET " . substr($fieldSql, 2) .
                " WHERE " . $this->primaryKey . "=" . $this->valArray[$this->primaryKey];
            /*if($_SERVER['REMOTE_ADDR'] == '188.66.86.88')
              {
              echo $sql;
              exit;
              } */
        } else {
            $insertFlag = true;
            //echo $this->tableName;
            //exit;
            $sql = "INSERT INTO " . $this->tableName . " SET " .
                substr($fieldSql, 2);

        }
        if ($debug) {
            echo '<pre>';
            print_r($sql);
            echo '</pre>';
            die;
        }
//        echo '<pre>';
//        print_r($sql);
//        echo '</pre>';
//        die;
       //	t($sql, __METHOD__);
        mysqli_set_charset ( self::$connection , "utf8");
        $result = mysqli_query(self::$connection, $sql);
        if ($result === FALSE) {
            $error = mysqli_error(self::$connection);
            self::$dbError[] = $error;
        }
        
        if ($this->valArray[$this->primaryKey] <= 0) {
            $this->valArray[$this->primaryKey] = @mysqli_insert_id(self::$connection);
            if($this->saveLog == true) {
                if(empty($this->auditTableName)) {
                    $this->auditTableName = $this->tableName;
                }
                if(empty($this->tableKey)) {
                    $this->tableKey = $this->valArray[$this->primaryKey];
                }
                $new_data = json_encode(array_merge($this->valArray, $this->logMoreDataNew));
                $userAudit->insertAuditData($this->auditTableName, 'insert', $userData->getFirstName() . ' ' . $userData->getLastName(), $userData->getId(), $new_data, $this->tableKey, '', $this->custom_message);
            } else {
                $this->auditLogDataNew = $this->valArray;
            }
        }
        
        return $result;
    }

    /*
     * 	Update helping class
     */

    public function update()
    {
        $userData = SessionManager::getUser();
        $userAudit = new UserAudit();
        $sql = "Select * FROM " . $this->tableName . " WHERE " . $this->primaryKey . "=" . $this->valArray[$this->primaryKey];

        t($sql, __METHOD__);
        $logSql = "SELECT " . implode(",", array_keys($this->valArray)) . " FROM " . $this->tableName . " WHERE " . $this->primaryKey . "=" . $this->valArray[$this->primaryKey];
        $rs = self::runQuery($logSql);
        $old_data = json_encode(mysqli_fetch_assoc($rs));
        $new_data = json_encode($this->valArray);
        $table_key = $this->valArray[$this->primaryKey];
        $userAudit->insertAuditData($this->tableName, 'update', $userData->getFirstName() . ' ' . $userData->getLastName(), $userData->getId(), $new_data, $table_key, $old_data);
        $rs = mysqli_query(self::$connection, $sql);
        $valArray = mysqli_fetch_assoc($rs);
        return $valArray;
    }

    /**
     * Deletes record from database.
     *
     */
    public function delete()
    {
        // first item is primary key
        if ($this->valArray[$this->primaryKey] > 0) {
            $userData = SessionManager::getUser();
            $userAudit = new UserAudit();
            $logSql = "SELECT * FROM " . $this->tableName . " WHERE " . $this->primaryKey . "=" . $this->valArray[$this->primaryKey];
            $rs = self::runQuery($logSql);
            $old_data = json_encode(mysqli_fetch_assoc($rs));
            $this->tableKey = $this->valArray[$this->primaryKey];
            $userAudit->insertAuditData($this->tableName, 'delete', $userData->getFirstName() . ' ' . $userData->getLastName(), $userData->getId(), '', $this->tableKey, $old_data);
            $sql = "DELETE FROM " . $this->tableName . " WHERE " .
                $this->primaryKey . "=" . $this->valArray[$this->primaryKey];
            //
            mysqli_query(self::$connection, $sql);
        }
    }

    /**
     * Gets list of objects.
     * The class name for type of object to be created
     * along with the filter conditions are passed.
     *
     * @param string $className
     * @param array $filterArray
     * @return array of objects
     */
    protected static function getList($className, $filterArray = null, $theOrderByFieldList = "", $limit = null, $offset = null)
    {
        $objectArray = array();

        if ($filterArray == null)
            $filterArray = array();

        $filterObj = new $className;
        $sql = $filterObj->getSql($filterArray, $theOrderByFieldList, $limit, $offset);

        t($sql, __METHOD__);
        //
        return self::getListFromSql($className, $sql);
    }

    protected static function getCount($className, $filterArray = null)
    {
        //
        $filterObj = new $className;
        $sql = "SELECT COUNT(" . $filterObj->primaryKey . ") as total FROM "
            . $filterObj->tableName
            . $filterObj->getFilter($filterArray);
        //
        t($sql, __METHOD__);
        $rs = mysqli_query(self::$connection, $sql);

        $count = 0;
        if ($row = mysqli_fetch_assoc($rs))
            $count = $row["total"];
        //
        return $count;
    }

    protected static function getObject($res)
    {
        return mysqli_fetch_object($res);
    }

    protected static function getArray($res)
    {
        return mysqli_fetch_assoc($res);
    }

    protected static function getNumRows($res)
    {
        return mysqli_num_rows($res);
    }

    /**
     * Gets list of objects.
     * The class name for type of objects to be created
     * is passed.
     * The sql that returns data for object list is passed.
     *
     * @param string $className
     * @param array $filterArray
     * @return array of objects
     */
    protected static function getListFromSql($className, $sql, $debug = false)
    {
        //t($sql, __METHOD__);

//		if (!mysqli_ping())
        //		self::connect(SETTING_DB_SERVER,SETTING_DB_USER, SETTING_DB_PASSWORD, SETTING_DB_DATABASE);

        //mysqli_ping(self::$connection);
        $rs = mysqli_query(self::$connection, $sql);
        if ($debug) {
            echo "<pre>";
            echo $sql;
            print_r($rs);
            echo '<br/>';
        }

        //
        $objectArray = array();
        while ($valArray = mysqli_fetch_assoc($rs)) {
            $objectArray[] = new $className($valArray);
            if ($debug){
                print_r($valArray);
                echo '<br/>';
            }
        }
        if ($debug) {
            print_r(mysqli_error(self::$connection));
            echo '<br/>';
            echo "DBACCESS3 CLASS HERE " . $sql . "DBACCESS3 CLASS HERE <br> " . $className . "<br>";
            print_r($objectArray);
        }
        return $objectArray;
    }

    /**
     * Get result set based on filter array passed.
     *
     * @param array $theFilterArray
     * @return Mysql resultset
     */
    private function getSql($theFilterArray, $theOrderByFieldList = "", $limit = null, $offset = null)
    {
        
        $fieldList = $this->primaryKey;
        foreach ($this->fieldList as $key => $val) {
            if ($val == "external" || $val == "undefined") continue;
            $fieldList .= ", " . $key;
        }

        // determine filter conditions
        $filter = $this->getFilter($theFilterArray);

        // Get the data rows
        $sql = "SELECT $fieldList FROM " . $this->tableName . $filter;
        if ($theOrderByFieldList != "") {
            $sql .= " ORDER BY $theOrderByFieldList";
        }
        if (!is_null($limit)) {
            $sql .= ' LIMIT ' . $limit;
            if (!is_null($offset)) {
                $sql .= ' OFFSET ' . $offset;
            }
        }

        return $sql;
    }

    private function getFilter($theFilterArray)
    {
        if ($theFilterArray == null)
            $theFilterArray = array();
        //
        $filter = "";
        // Add filter items
        foreach ($theFilterArray as $field => $val) {
            // Check that this is a valid field name!
            if (isset($this->fieldList[$field]) || ($field == $this->primaryKey)) {
                // if primary key, then type is a number otherwise get from field list definition
                $type = ($field == $this->primaryKey) ? "number" : $this->fieldList[$field];
                //
                $encloseChar = ($type == "number") ? "" : "'";
                //
                $filter .= "AND $field=" . $encloseChar . $val . $encloseChar . " ";
            }
        }
        if ($filter != "")
            $filter = " WHERE " . substr($filter, 4);
        //
        return $filter;
    }

    /**
     * Serialise this object
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->valArray);
    }

    /**
     * Create
     *
     * @return string
     */
    protected static function unserialize($class, $serialStr)
    {
        $obj = new $class();
        $obj->valArray = unserialize($serialStr);

        return $obj;
    }

    /*     * *
     * Run a SQL string
     */

    public static function runQuery($sql)
    {
        return mysqli_query(self::$connection, $sql);
    }

    /*     * *
     * Run a SQL string
     */

    public static function runQueryWithError($sql,$debug = false)
    {
        $result = mysqli_query(self::$connection, $sql);
        if ($result === FALSE) {
            $error = mysqli_error(self::$connection);
            self::$dbError[] = $error;
        }
        if($debug)
        {
            print($error );
        }
        return $result;
    }

    function callme($stream, &$buffer, $buflen, &$errmsg)
    {
        $buffer = fgets($stream);
        //echo $buffer;
        // convert to upper case and replace "," delimiter with [TAB]
        //	$buffer = strtoupper(str_replace(",", "\t", $buffer));
        return strlen($buffer);
    }

    /*     * *
     * Run a SQL string
     */

    public static function runQueryFileNew($sql)
    {
        mysqli_set_local_infile_handler(self::$connection, "callme");
        mysqli_query(self::$connection, $sql);
        mysqli_set_local_infile_default(self::$connection);

        //return mysqli_query(self::$connection, $sql) or die( mysqli_error());
    }

    /*     * *
     * Run a SQL string
     */

    public static function runQueryNew($sql)
    {
        return mysqli_query(self::$connection, $sql) or die(mysqli_error());
    }

    /*
     * 	Update helping class
     */

    public static function escape($string)
    {
        return mysqli_real_escape_string(self::$connection, $string);
    }

    public static function getAffectedRows()
    {
        return mysqli_affected_rows(self::$connection);
    }

    /*     * *
     * Run a SQL string
     */

    public static function runQueryReturnId($sql)
    {
        mysqli_query(self::$connection, $sql);
        return mysqli_insert_id(self::$connection);
    }

    public static function getConnection()
    {
        return self::$connection;
    }

    public function saveAuditData() {
        $old_data = json_encode(array_merge($this->auditLogDataOld, $this->logMoreDataOld));
        $new_data = json_encode(array_merge($this->auditLogDataNew, $this->logMoreDataNew));
        $userData = SessionManager::getUser();
        $userAudit = new UserAudit();
        $message = '';
        if(!empty($this->custom_message)) {
            $message = $this->custom_message;
        }
        $userAudit->insertAuditData($this->tableName, 'update', $userData->getFirstName() . ' ' . $userData->getLastName(), $userData->getId(), $new_data, $this->tableKey, $old_data, $message);
    }
}
