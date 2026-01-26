<?php
/**
 * FTP with Implicit SSL/TLS Class
 *
 * Simple wrapper for cURL functions to transfer an ASCII file over FTP with implicit SSL/TLS
 *
 * @category    Class
 * @author      Max Rice
 * @since       1.0
 */
class FTPImplicitSSL {
	/** @var resource cURL resource handle */
	private $curl_handle;
	/** @var string cURL URL for upload */
	private $url;
	/**
	 * Connect to FTP server over Implicit SSL/TLS
	 *
	 *
	 * @access public
	 * @since 1.0
	 * @param string $username
	 * @param string $password
	 * @param string $server
	 * @param int $port
	 * @param string $initial_path
	 * @param bool $passive_mode
	 * @throws Exception - blank username / password / port
	 * @return \FTP_Implicit_SSL
	 */
	public function __construct($username, $password, $server, $port = 990, $initial_path = '', $passive_mode = true ) {
		// check for blank username
            $this->server = $server;
            $this->username = $username; 
            $this->password = $password; 
		if ( ! $username )
			throw new Exception( 'FTP Username is blank.' );
		// don't check for blank password (highly-questionable use case, but still)
		// check for blank server
		if ( ! $server )
			throw new Exception( 'FTP Server is blank.' );
		// check for blank port
		if ( ! $port )
			throw new Exception ( 'FTP Port is blank.', WC_XML_Suite::$text_domain );
		// set host/initial path
		$this->url = "ftps://".$server;
		// setup connection
		$this->curl_handle = curl_init();
		// check for successful connection
		if ( ! $this->curl_handle )
			throw new Exception( 'Could not initialize cURL.' );
		// connection options
		$options = array(
			CURLOPT_USERPWD        => $username . ':' . $password,
			CURLOPT_SSL_VERIFYPEER => false, // don't verify SSL
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_FTP_SSL        => CURLFTPSSL_ALL, // require SSL For both control and data connections
			CURLOPT_FTPSSLAUTH     => CURLFTPAUTH_DEFAULT, // let cURL choose the FTP authentication method (either SSL or TLS)
			CURLOPT_UPLOAD         => true,
			CURLOPT_PORT           => $port,
			CURLOPT_TIMEOUT        => 30
		);
		// cURL FTP enables passive mode by default, so disable it by enabling the PORT command and allowing cURL to select the IP address for the data connection
		if ( ! $passive_mode )
			$options[ CURLOPT_FTPPORT ] = '-';
		// set connection options, use foreach so useful errors can be caught instead of a generic "cannot set options" error with curl_setopt_array()
		foreach ( $options as $option_name => $option_value ) {
			if ( ! curl_setopt( $this->curl_handle, $option_name, $option_value ) )
				throw new Exception( sprintf( 'Could not set cURL option: %s', $option_name ) );
		}
	}
	/**
	 * Write file into temporary memory and upload stream to remote file
	 * @access public
	 * @since 1.0
	 * @param string $file_name - remote file name to create
	 * @param string $file - file content to upload
	 * @throws Exception - Open remote file failure or write data failure
	 */
	public function upload( $file_name, $file ) {
          //  echo 'sdasd'; die;
		// set file name
		if ( ! curl_setopt( $this->curl_handle, CURLOPT_URL, $this->url .'/Inbox/'. $file_name ))
			throw new Exception ( "Could not set cURL file name: $file_name" );
		// open memory stream for writing
		$stream = fopen( 'php://temp', 'w+' );
		// check for valid stream handle
		if ( ! $stream )
			throw new Exception( 'Could not open php://temp for writing.' );
		// write file into the temporary stream
		if(fwrite( $stream, $file ))
                        echo "write";
		// rewind the stream pointer
		rewind( $stream );
		// set the file to be uploaded
		if ( ! curl_setopt( $this->curl_handle, CURLOPT_INFILE, $stream ) )
			throw new Exception( "Could not load file $file_name" );
		// upload file
		if ( ! curl_exec( $this->curl_handle ) )
			throw new Exception( sprintf( 'Could not upload file. cURL Error: [%s] - %s', curl_errno( $this->curl_handle ), curl_error( $this->curl_handle ) ) );
		// close the stream handle
		fclose( $stream );
	}
        
        
        
   

public function download($remote, $local = null) {
//echo $local; 
            $source_file = $this->url.$remote;
           // $destination_file = $local;
           // $file = fopen($local, 'w');
          //  echo $source_file;  
            curl_setopt($this->curl_handle, CURLOPT_URL, $source_file);
            curl_setopt($this->curl_handle, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($this->curl_handle, CURLOPT_UPLOAD, 0);
            curl_setopt($this->curl_handle, CURLOPT_FILE, $local);

curl_exec($this->curl_handle);
$error_no = curl_errno($this->curl_handle);
$error_msg = curl_error($this->curl_handle);
//curl_close ($this->curl_handle);
if ($error_no == 0) {
    $msg = 'File downloaded succesfully.';
} else {
    $msg = 'File download error:' . $error_msg . ' | ' . $error_no;
}
fclose($local);
//echo $msg;
//die;
            }
    
public function rename($remoteold, $newLocation) {
 
$this->curl_handle = curl_init();
curl_setopt($this->curl_handle, CURLOPT_URL, $this->url);
curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER,1);
curl_setopt($this->curl_handle, CURLOPT_VERBOSE, 1);
curl_setopt($this->curl_handle, CURLOPT_USERPWD, $this->username . ":" . $this->password);
curl_setopt($this->curl_handle, CURLOPT_FTP_USE_EPSV, 0);
curl_setopt($this->curl_handle, CURLOPT_FTP_USE_EPRT, 0);
curl_setopt($this->curl_handle, CURLOPT_FTP_SSL, CURLFTPSSL_ALL);
curl_setopt($this->curl_handle, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($this->curl_handle, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($this->curl_handle, CURLOPT_POSTQUOTE, array("RNFR $remoteold",
"RNTO $newLocation"));


  
  $result = curl_exec($this->curl_handle);
  
  /*if(curl_exec($this->curl_handle) === false) {
        throw new Exception(curl_error($this->curl_handle));
*/
         //   }
}          
            
public function ftplist(){
    
        curl_setopt($this->curl_handle, CURLOPT_URL, $this->url. '/Outbox/');
        curl_setopt($this->curl_handle, CURLOPT_UPLOAD, 0);
        curl_setopt($this->curl_handle, CURLOPT_FTPLISTONLY, 1);
        curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, 1);

      
        $result = curl_exec($this->curl_handle);
      // print_r($result); die;
        if (curl_error($this->curl_handle)) {
            return false;
        } else {
            $files = explode("\n", trim($result));
          //  print_r($files); die;
            return $files;
     //       return $local;
        }
    
}

public function delete($file_name, $url){

    $this->url = $url;
if ( ! curl_setopt( $this->curl_handle, CURLOPT_URL, $this->url))
throw new Exception ( "Could not set cURL directory: $this->url" );

if( !in_array($file_name, $this->ftplist()) ){
throw new Exception ( $file_name . "not found" );
}

curl_setopt( $this->curl_handle,CURLOPT_QUOTE,array('DELE' . $this->url . $file_name ));
curl_setopt( $this->curl_handle, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($this->curl_handle);
//print_r( curl_getinfo($this->curl_handle) ) ;
$files = explode("\n",trim($result));

if( ! in_array( $file_name, $files) ){
return 'SUCCESS';
} else {
return 'FAILED';
}

}

	/**
	 * Attempt to close cURL handle
	 * Note - errors suppressed here as they are not useful
	 *
	 * @access public
	 * @since 1.0
	 */
	public function __destruct() {
		@curl_close( $this->curl_handle );
	}
}