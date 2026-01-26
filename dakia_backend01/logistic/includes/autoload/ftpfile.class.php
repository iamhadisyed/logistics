<?php

////////////////////////////////////////////////////
//
// Class for dealing with FTP
//
////////////////////////////////////////////////////

class FTPfile
{
	protected $host 		= "localhost";
	protected $port 		= "21";
	protected $user 		= "Anonymous";
	protected $pass 		= "Email";
	public $link_id 		= "";
	public $is_login 	= "";
	protected $debug 		= 1;
	protected $local_dir 	= "";
	protected $rootdir 		= "";
	protected $dir 			= "/";

	/**
	* constructor.
	* @return void
	*/
    public function __construct($user = "Anonymous", $pass = "Email", $host = "localhost", $port = "21")
	{
		if($host) $this->host = $host;
		if($port) $this->port = $port;
		if($user) $this->user = $user;
		if($pass) $this->pass = $pass;
		$this->login();
	$this->dir = $this->rootdir;
	}

	/**
	* halt.
	* @return void
	*/
	public function halt($msg, $line = __LINE__)
	{
		echo "FTP Error in line: $line<br/>\n";
		echo "FTP Error message: $msg<br/>\n";
		exit();
	}

	/**
	* login.
	* @return void
	*/
	public function login()
	{
		if(!$this->link_id)
		{
			$this->link_id = ftp_connect($this->host,$this->port) or $this->halt("can not connect to host:$this->host:$this->port", __LINE__);
			//$this->link_id = ftp_connect($this->host) or $this->halt("can not connect to host:$this->host:$this->port", __LINE__);
		}
		if(!$this->is_login)
		{
			$this->is_login = ftp_login($this->link_id, $this->user, $this->pass);
			
			if(!$this->is_login)
				mail("ITSupport@oneworldexpress.com", "FTP LOGIN FAILED", "FTP LOGIN FAILED Host : " . $this->host . " 
					 User name : " . $this->user . " Password : " . $this->pass);
			
			/*$this->is_login = ftp_login($this->link_id, $this->user, $this->pass) or

            $this->halt("ftp login faild.invalid user or password", __LINE__);*/
			
			
		}
	}

	/**
	* system type.
	* @return string (system type)
	*/
	public function systype()
	{
		return ftp_systype($this->link_id);
	}

	/**
	* working directory.
	* @return string
	*/
	public function pwd()
	{
		$this->login();
		$dir = ftp_pwd($this->link_id);
		$this->dir = $dir;
		return $dir;
	}



/**
	* passive mode.
	* @return string
	*/
	public function passive()
	{
		$this->login();

   	    ftp_pasv($this->link_id, true);


	}


	/**
	* up one level of directory.
	* @return bool
	*/
	public function cdup()
	{
		$this->login();
		$isok =  ftp_cdup($this->link_id);
		if($isok) $this->dir = $this->pwd();
		return $isok;
	}

	/**
	* change directory.
	* @return bool
	*/
	public function cd($dir)
	{
		$this->login();
		$isok = ftp_chdir($this->link_id,$dir);
		if($isok) $this->dir = $dir;
		return $isok;
	}

	/**
	* get files in directory.
	* @return array
	*/
	public function nlist($dir = "")
	{
		$this->login();
		if(!$dir) $dir = ".";
		$arr_dir = ftp_nlist($this->link_id,$dir);
		return $arr_dir;
	}

	/**
	* get raw list.
	* @return array
	*/
	public function rawlist($dir = "/")
	{
		$this->login();
		$arr_dir = ftp_rawlist($this->link_id,$dir);
		return $arr_dir;
	}
	/**
	* change directory.
	* @return bool
	*/
	public function ftp_is_dir($dir)
	{
		$this->login();
	
		if (@ftp_chdir($this->link_id, $dir))
		{
			return true;
		}
		else
		{
			@ftp_mkdir($this->link_id,$dir);
			@ftp_chdir($this->link_id, $dir);
			return true;
		}
		return false;
	}
	/**
	* make a directory.
	* @return string (new directory name)
	*/
	public function mkdir($dir)
	{
		$this->login();
		return @ftp_mkdir($this->link_id,$dir);
	}

	/**
	* get the file size.
	* @return int (file size)
	*/
	public function file_size($file)
	{
		$this->login();
		$size = ftp_size($this->link_id,$file);
		return $size;
	}

	/**
	* chmod.
	* @return int (new chmod value)
	*/
	public function chmod($file, $mode = 0666)
	{
		$this->login();
		return ftp_chmod($this->link_id,$mode,$file);
	}

	/**
	* delete a remote file.
	* @return bool
	*/
	public function delete($remote_file)
	{
		$this->login();
		return ftp_delete($this->link_id,$remote_file);
	}

	/**
	* get a file.
	* @return bool
	*/
	public function get($local_file, $remote_file, $mode = FTP_BINARY)
	{
		$this->login();
		return ftp_get($this->link_id,$local_file,$remote_file,$mode);
	}

	/**
	* put a file.
	* @return bool
	*/
	public function put($remote_file, $local_file, $mode = FTP_BINARY)
	{
		$this->login();
		return ftp_put($this->link_id, $remote_file, $local_file, $mode);
	}

	/**
	* put a string.
	* @return bool
	*/
	public function put_string($remote_file,$data,$mode=FTP_BINARY)
	{
		$this->login();
		$tmp 		= "/tmp";//ini_get("session.save_path");
		$tmpfile 	= tempnam($tmp,"tmp_");
		$fp 		= @fopen($tmpfile,"w+");

		if($fp)
		{
			fwrite($fp,$data);
			fclose($fp);
		}
		else
		{
			return 0;
		}

		$isok = $this->put($remote_file, $tmpfile, FTP_BINARY);
		@unlink($tmpfile);
		return $isok;
	}

	/**
	* output message to browser.
	* @return void
	*/
	public function p($msg)
	{
		echo "<pre>";
		print_r($msg);
		echo "</pre>";
	}

	/**
	* close FTP session.
	* @return void
	*/
	public function close()
	{
		@ftp_quit($this->link_id);
	}

}

?>