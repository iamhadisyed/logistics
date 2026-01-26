<?php
// get settings
require_once("../includes/settings/config.inc.php");


$SETTING_FTP_USER_YODEL = "oneworld";// $ftpConstants['YODEL_FTP_USER'];
$SETTING_FTP_PASSWORD_YODEL = "serv1ce";//$ftpConstants['YODEL_FTP_PASSWORD'];
$SETTING_FTP_SITE_YODEL = "cs.yodel.co.uk";// $ftpConstants['YODEL_FTP_SITE'];

echo "<pre>";


// connect and login to FTP server
$ftp_server = $SETTING_FTP_SITE_YODEL;
$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");

$login = ftp_login($ftp_conn, $SETTING_FTP_USER_YODEL, $SETTING_FTP_PASSWORD_YODEL);

ftp_pasv($ftp_conn, true) or die("Unable switch to passive mode");

//print_r($login);

// get the file list for /
$filelist = ftp_rawlist($ftp_conn, "/");

// upload file
if (ftp_put($ftp_conn, "developer.txt.csv", "developer.txt.csv", FTP_ASCII))
  {
  echo "Successfully uploaded $file.";
  }
else
  {
  echo "Error uploading $file.";
  }
  
  
// close connection
ftp_close($ftp_conn);

// output $filelist
var_dump($filelist);


die;

// FTP file to Yodel
// - default port#
$ftp_object = new FTPfile($SETTING_FTP_USER_YODEL, $SETTING_FTP_PASSWORD_YODEL, $SETTING_FTP_SITE_YODEL);
$ftp_object->passive();
print_r($ftp_object);
echo "me here";
print_r($this->is_login);
echo "me here 1";
print_r($ftp_object->nlist());//$this->is_login
echo "me here 2";
die;

$filename = $linkFile->getFileName();
$remote_file_path = "./stdcol/" . $linkFile->getFileName();
$isYodelUpload = $ftp_object->put($remote_file_path, $local_file, FTP_BINARY);
$this->link_file = NULL;
$this->record_array = NULL;
                
                
