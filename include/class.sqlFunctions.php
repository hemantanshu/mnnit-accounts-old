<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
////error_reporting(0);
require_once 'class.configFile.php';

class sqlFunction extends configuration {
    private $connection;
    protected $currentMonth;
    protected $nextMonth;
    
    protected function  __construct() {
        parent::__construct();
        $this->connection = mysql_connect($this->mysqlServer, $this->mysqlUsername, $this->mysqlPassword);        
        $this->currentMonth = date('Ym');
        $this->nextMonth = date('Ym', mktime(0, 0, 0, date('m')+1, 24, date('Y')));
    }

    protected function processQuery($sqlQuery){
    	
        mysql_select_db($this->mysqlDatabase, $this->connection);
        $query = mysql_query($sqlQuery) or die(mysql_error());
	
        if(!$query)
            return false;

        return $query;
    }

    protected function processArray($sqlQuery){
        mysql_select_db($this->mysqlDatabase, $this->connection);
        if($query = $this->processQuery($sqlQuery)){
            if(mysql_num_rows($query)){
                $query = mysql_fetch_array($query);
                return $query;
            }
            return false;
        }
        return false;
    }

    protected function getValue($column, $table, $condition, $value){     

        $query = "SELECT $column FROM $table WHERE $condition = \"$value\" LIMIT 1";
        if($query = $this->processArray($query))
                return $query[0];

        return false;

    }

    protected function getCounter($field){
        $query = "SELECT starter, value FROM counter WHERE field = \"$field\" ";

        if($query = $this->processQuery($query)){
            if(mysql_num_rows($query) == 1){
                $query = mysql_fetch_array($query);
                
                $counter = $query['starter'].($query['value'] + 1);
                $this->updateCounter($counter);
                return $counter;
            }
            return false;
        }
        return false;
    }

    protected function updateCounter($counter){
        $starter = substr($counter, 0, 3);      //the first three characters of the counter
        $length = strlen($counter) - 3;

        $counter = substr($counter, 3, $length);

        $query = "UPDATE counter SET value = \"$counter\" WHERE starter = \"$starter\" ";
        mysql_select_db($this->database, $this->connection);
        if(mysql_query($query))
            return true;
        else
            return false;

    }
    
    public function getFlagComment($flag){
        return $this->getValue("value", "flag", "flag", $flag);
    }
    
    public function getCurrentMonth(){
    	return $this->currentMonth;
    }
    
    public function getPreviousMonth($date){
    	$variable = date("Y-m-d", mktime(0, 0, 0, substr($date, 4, 2)) - 1, 12, substr($date, 0, 4));
    	$data = explode('-', $variable);
    	return $data[0].$data[1];
    }

    public function  __destruct() {
        mysql_close($this->connection);
    }

    
}
?>
