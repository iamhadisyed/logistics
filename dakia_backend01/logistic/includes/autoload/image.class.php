<?php

////////////////////////////////////////////////////
//
// Class for dealing with Images
//
////////////////////////////////////////////////////

/**
 * Image - Image class
 * @package Image
 */

class Image
{

	protected $id				= NULL;
	protected $data				= array();
	protected $path				= NULL;
	protected $actual			= NULL;
	protected $resize_width		= NULL;
	protected $resize_height	= NULL;

	public function upload()
	{

		if (isset($this->data))
		{
			 $FilObj = new Fileupload($this->data);

			 if ($FilObj->uploaded)
			 {
				 if ($this->actual)
				 {
					$FilObj->file_new_name_body = $this->actual;
				 }
				 else
				 {
					$FilObj->file_new_name_body = $this->id;
				 }

				 $FilObj->file_overwrite 		= true;
				 $FilObj->file_auto_rename 		= false;

				// if resizing is required
				if ($this->resize_width)
				{
					$FilObj->image_convert      = 'jpg';
					$FilObj->image_resize       = true;
					$FilObj->image_ratio_y      = true;
					$FilObj->image_x            = $this->resize_width;
					$FilObj->jpeg_quality       = 100;
				}

				 $FilObj->process($this->path);

				 // use for debugging
				 #echo $FilObj->log;
				 #echo $FilObj->error;

				 if ($FilObj->processed)
				 {
					  #$FilObj->clean();
				 }

			 }
		 }
	}

    ////////////////////////////////////////////////////
    // Getters
    ////////////////////////////////////////////////////

    // specific getters
    public function getId()                     {     return $this->id; }
    public function getData()              		{     return $this->data; }
    public function getPath()              		{     return $this->path; }
    public function getActual()              	{     return $this->actual; }
    public function getResizeWidth()            {     return $this->resize_width; }
    public function getResizeHeight()           {     return $this->resize_height; }

    ////////////////////////////////////////////////////
    // Setters
    ////////////////////////////////////////////////////

    // specific setters
    public function setId($id)                  	{     $this->id = $id; }
    public function setData($data)             		{     $this->data = $data; }
    public function setPath($path)             		{     $this->path = $path; }
    public function setActual($actual)      		{     $this->actual = $actual; }
    public function setResizeWidth($resize_width) 	{     $this->resize_width = $resize_width; }
    public function setResizeHeight($resize_height)	{     $this->resize_height = $resize_height; }

}
?>
