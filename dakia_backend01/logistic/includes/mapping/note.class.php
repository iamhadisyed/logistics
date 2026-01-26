<?php
/**
 * Parcel Group Note
 *
 */
class Note extends DbAccess3
{
 	public function __construct($mixedCreator = null)
	{
		$fieldList =
			array(  'update_time' => 'datetime',
					'updated_by' => 'string',
					'note' => 'string'
					);
		//
		parent::__construct("notes", 'id', $fieldList, $mixedCreator);
	}

	/**
	 * Get list of notes
	 *
	 * @param array $filterArray
	 * @return array
	 */
	public static function getList($filterArray = null)
	{
		return parent::getObjectList(__CLASS__, $filterArray, "id");
	}

	/**
	 * Get list of notes for given order
	 *
	 * @param int $order_id
	 * @return array of notes
	 */
	public static function getListForOrderId($order_id)
	{
		$sql = "SELECT n.*
				FROM notes n
				LEFT JOIN order_notes o on n.id=o.note_id
				WHERE o.order_id=$order_id";
		//
		return parent::getListFromSql(__class__, $sql);
	}

	/**
	 * Get all notes associated with a parcel group
	 *
	 * @param int $parcel_group_id
	 * @return array of notes
	 */
	public static function getListForParcelGroupId($parcel_group_id)
	{
		$subSql = "SELECT o.note_id
					FROM order_notes o
					LEFT JOIN baskets b on o.order_id=b.order_id
					LEFT JOIN basket_items i on b.id=i.basket_id
					WHERE parcel_group_id=$parcel_group_id";

		$sql = "SELECT n.*
				FROM notes n
				LEFT JOIN parcel_group_notes p on n.id=p.note_id
				WHERE p.parcel_group_id=$parcel_group_id
				  OR n.id in ($subSql)
				ORDER BY n.update_time DESC
				"
				;
		//echo "<p>$sql</p>";
		//
		return parent::getListFromSql(__class__, $sql);
	}


	/*
	 * Get id
	 */
	public function getId() { return $this->valArray["id"]; }

	/**
	 * Save a note associated with a parcel group
	 *
	 * @param int $parcel_group_id
	 */
	public function saveParcelGroupNote($parcel_group_id)
	{
		$this->save();
		$note_id = $this->getId();
		//
		$sql = "INSERT INTO parcel_group_notes (parcel_group_id, note_id)
				SELECT $parcel_group_id as parcel_group_id, $note_id as note_id FROM parcel_group_notes
				WHERE parcel_group_id=$parcel_group_id AND note_id=$note_id
				HAVING count(id)=0";
		//echo $sql;
		DB::query($sql);
	}

	public function saveOrderNote($order_id)
	{
		$this->save();
		$note_id = $this->getId();
		//
		$sql = "INSERT INTO order_notes (order_id, note_id)
				SELECT $order_id as order_id, $note_id as note_id FROM order_notes
				WHERE order_id=$order_id AND note_id=$note_id
				HAVING count(id)=0";
		DB::query($sql);
	}

	/**
	 * Save parcel group notes
	 *
	 */
	public function save()
	{
		// override the default save,
		// - always set the last updated value when saved
		$this->setUpdateTime(time());
		//
		parent::save();
	}
}