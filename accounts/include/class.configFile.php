<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
error_reporting(0);
class configuration{
    protected $mysqlServer;
    protected $mysqlUsername;
    protected $mysqlPassword;
    protected $mysqlDatabase;

    protected function  __construct() {
        $this->mysqlServer = "localhost";       //the ip of the mysql server

        $this->mysqlUsername = "root";          //the mysql username which will connect to the server
        
        $this->mysqlPassword = "databasestech09";    //the mysql password against the given username
	// $this->mysqlPassword = "patna";

        $this->mysqlDatabase = "accounts";      //the database where the accounts details will be saved
        
    }
}
?>
