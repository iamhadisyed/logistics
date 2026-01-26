<?php

////////////////////////////////////////////////////
//
// Class for dealing with products
//
////////////////////////////////////////////////////

/**
 * Product - Product class
 * @package Ecommerce
 */
class Product extends DbAccess
{
	const IMAGE_SIZE_SMALL = "small";
	const IMAGE_SIZE_LARGE = "large";

    protected $name;
    protected $summary_line_1;
    protected $summary_line_2;
    protected $description;
    protected $sku_number;
    protected $price;
    protected $search_strings;
    protected $featuredq;
    protected $special_offerq;
    protected $bestsellerq;
    protected $category_id;

    protected $image_small_width	 = 200;
    protected $image_large_width	 = 448;

    protected $orderq;
    protected $active;
    protected $deletedq;
    protected $added_on;
    protected $added_by;
    protected $changed_on;
    protected $changed_by;

    /**
     * Constructor. Returns the object.
     * @param int $id
     * @return obj
     */
    public function __construct($id = 0)
    {

        $this->tablename = 'products';
        $this->pkey      = 'id';
        $this->fields      =
            array(  'name'                     => 'name',
                    'summary_line_1'           => 'summary_line_1',
                    'summary_line_2'           => 'summary_line_2',
                    'description'              => 'description',
                    'sku_number'               => 'sku_number',
                    'price'               	   => 'price',
                    'search_strings'           => 'search_strings',
                    'featuredq'                => 'featuredq',
                    'special_offerq'           => 'special_offerq',
                    'featuredq'                => 'featuredq',
                    'bestsellerq'              => 'bestsellerq',
                    'category_id'              => 'category_id',

                    'orderq'                   => 'orderq',

                    'active'                   => 'active',
                    'deletedq'                 => 'deletedq',

                    'added_on'                 => 'added_on',
                    'added_by'                 => 'added_by',
                    'changed_on'               => 'changed_on',
                    'changed_by'               => 'changed_by'
                    );

        $this->_construct($id);

        return $this;
    }

    ////////////////////////////////////////////////////
    // Access and update methods
    ////////////////////////////////////////////////////

    /**
     * getAnyProduct. Returns the object array of Products.
     * @param string $where
     * @param string $activeOnly
     * @param string $orderBy
     * @return string
     */
    public function getAnyProduct($where = "", $activeOnly = true, $orderBy = "orderq")
    {
        $ret = array();
        $ids = $this->getAnyProductIds($where, $activeOnly, $orderBy);

        if (count($ids)>0)
        {
            foreach ($ids as $i)
            {
                $ret[] = new Product($i);
            }

            return $ret;
        }
        else return $ret;
    }

    /**
     * getAnyProductIds. Returns the object array of Product ids.
     * @param string $where
     * @param string $activeOnly
     * @param string $orderBy
     * @return string
     */
    public function getAnyProductIds($where = "", $activeOnly = true, $orderBy = "orderq")
    {

        $ret       = array();
        $whereSql  = "WHERE deletedq <> 'Y' ";

        if ($where)      $whereSql .= " and $where";
        if ($activeOnly) $whereSql .= " and active=1";
           $sql = "select {$this->pkey} from {$this->tablename} $whereSql order by $orderBy";

        //$this->sql = $sql;

        $result = Db::query($sql);
        while (list($pId) = mysqli_fetch_row($result))
        {
            $ret[] = $pId;
        }

        return $ret;
    }

    /**
     * save. Updates table.
     * @return void
     */
    public function save()
    {
        $this->changed_on = date("Y-m-d H:i:s");
        $this->changed_by = (isset($_SESSION['admin']['firstname'])    ? $_SESSION['admin']['firstname'] . " " . $_SESSION['admin']['surname'] : 'Website');

        if ($this->id < 1)
        {
            $this->added_on = date("Y-m-d H:i:s");
            $this->added_by = (isset($_SESSION['admin']['firstname'])    ? $_SESSION['admin']['firstname'] . " " . $_SESSION['admin']['surname'] : 'Website');
        }

        // save
        parent::save();

        // get new id
        if ($this->id < 1)
        {
             $this->id = Db::getLastInsertId();
        }

        return;

    }

    /**
     * delete. Updates row as deleted
     * @return void
     */
    public function delete()
    {

        $sql = "UPDATE {$this->tablename}
                   SET {$this->fields['deletedq']} = 'Y'
                 WHERE {$this->pkey} = '{$this->id}'
                 ";

        $result = Db::query($sql);
        return;

    }


    ////////////////////////////////////////////////////
    // Getters
    ////////////////////////////////////////////////////

    // specific getters
    public function getId()                    {     return $this->id; }
    public function getName()                  {     return $this->name; }
    public function getSummaryLine1()          {     return $this->summary_line_1; }
    public function getSummaryLine2()          {     return $this->summary_line_2; }
    public function getDescription()           {     return $this->description; }
    public function getSKUNumber()             {     return $this->sku_number; }
    public function getPrice()             	   {     return $this->price; }
    public function getSearchStrings()         {     return $this->search_strings; }
    public function getIsFeatured()            {     return $this->featuredq; }
    public function getIsSpecialOffer()        {     return $this->special_offerq; }
    public function getIsBestSeller()          {     return $this->bestsellersq; }
    public function getCategoryId()            {     return $this->category_id; }

    public function getImage($prefix = "", $size = IMAGE_SIZE_SMALL)
	{
		if (file_exists($this->getImagePath($prefix, $size) . $this->getId() . ".jpg"))
		{	$file =  $this->getImagePath($prefix, $size) . $this->getId() . '.jpg';
			return '<img src="' . $file . '?' . date("His") . '" width="125" height="100">';
		}
		return false;
	}

    public function getImagePath($prefix = "", $size = "small")
	{
		if ($size == self::IMAGE_SIZE_SMALL) {return $prefix . "../assets/images/products/small/"; }
		if ($size == self::IMAGE_SIZE_LARGE) {return $prefix . "../assets/images/products/large/"; }
	}

    public function getCategory()
	{
		return new Productcategory($this->getCategoryId());
	}

    public function getSummaryHTML()
	{
		$html = "<ul>";
		if ($this->getSummaryLine1() != "")
		{
			$html .= "<li>" . $this->getSummaryLine1() . "</li>";
		}
		if ($this->getSummaryLine2() != "")
		{
			$html .= "<li>" . $this->getSummaryLine2() . "</li>";
		}
		$html .= "</ul>";
		if ($this->getSummaryLine1() == "")
		{
			$html .= "<br/>";
		}
		if ($this->getSummaryLine2() == "")
		{
			$html .= "<br/>";
		}

		return $html;
	}

    public function getImageSmallWidth()        {     return $this->image_small_width; }
    public function getImageLargeWidth()        {     return $this->image_large_width; }

    // general and audit getters
    public function getRowOrder()              {     return $this->orderq; }
    public function getActive()                {     return $this->active; }
    public function getDeleted()               {     return $this->deletedq; }
    public function getAddedOn()               {     return $this->added_on; }
    public function getAddedBy()               {     return $this->added_by; }
    public function getChangedOn()             {     return $this->changed_on; }
    public function getChangedBy()             {     return $this->changed_by; }


    ////////////////////////////////////////////////////
    // Setters
    ////////////////////////////////////////////////////

    // specific setters
    public function setName($name)                          {     $this->name = $name; }
    public function setSummaryLine1($summary_line_1)        {     $this->summary_line_1 = $summary_line_1; }
    public function setSummaryLine2($summary_line_2)        {     $this->summary_line_2 = $summary_line_2; }
    public function setDescription($description)            {     $this->description = $description; }
    public function setSKUNumber($sku_number)               {     $this->sku_number = $sku_number; }
    public function setPrice($price)               			{     $this->price = $price; }
    public function setSearchStrings($search_strings)       {     $this->search_strings = $search_strings; }
    public function setFeatured($featuredq)                 {     $this->featuredq = $featuredq; }
    public function setSpecialOffer($special_offerq)        {     $this->special_offerq = $special_offerq; }
    public function setBestSeller($bestsellerq)             {     $this->bestsellerq = $bestsellerq; }
    public function setCategoryId($category_id)             {     $this->category_id = $category_id; }

    // general and audit setters
    public function setRowOrder($order)                     {     $this->orderq = $order; }
    public function setActive($active)                      {     $this->active = $active; }
    public function setDeleted($deleted)                    {     $this->deletedq = $deleted; }
    public function setAddedOn($added_on)                   {     $this->added_on = added_on; }
    public function setAddedBy($added_by)                   {     $this->added_by = added_by; }
    public function setChangedOn($changed_on)               {     $this->changed_on = changed_on; }
    public function setChangedBy($changed_by)               {     $this->changed_by = changed_by; }

    // make image directories
    public function makeDirectories()
	{
		@mkdir("../assets/images", 0644);
		@mkdir("../assets/images/products", 0644);
		@mkdir("../assets/images/products/large", 0775);
		@mkdir("../assets/images/products/small", 0775);
	}



}
?>
