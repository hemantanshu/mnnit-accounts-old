<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */

require_once 'class.loggedInfo.php';

class loginInfo extends loggedIn {  //all the operation done in here is based on the logged user info

    private $user;

    public function  __construct() {
        parent::__construct();

        $this->user = $this->checkLogged();
        if($this->user == "")
        	$this->user = $this->checkLoanOfficerLogged();
    }

    public function checkPassword($password, $type){        //this function is used to chk the password provided
    	if(func_num_args() > 1){
    		if($type == 'employee'){
    			$employeeId = $this->checkEmployeeLogged();    			
    			if($this->getValue("password", "employeelogin", "id", $employeeId) == md5($password))
                	return true;
    		}
    	}
        if($this->getValue("password", "officer", "username", $this->user) == md5($password))
                return true;
        return false;
    }

    public function getOfficerName(){                   //this function is used to get the logged officer name
        return $this->getValue("name", "officer", "username", $this->user);
    }

    public function getOfficerNameNotLogged($id){       //this function is used to get the information of officer name who is not logged based on their id
        return $this->getValue("name", "officer", "id", $id);
    }

    protected function isSupretendent(){                //this function chks whether the logged user is a supretendent
        $variable = $this->getValue("type", "officer", "username", $this->user);
        if($variable == "s")
            return true;

        return false;
    }

    public function isAdmin(){       //this function chks whether the logged user is an admin

        $variable = $this->getValue("type", "officer", "username", $this->user);
        if($variable == "a")
            return true;

        return false;
    }

    protected function insertProcess($id, $process){            //this function inserts the process log in the process. once any job gets confirmed by the admin its not should go to the process log
		$query = "INSERT INTO processlog(pid, log, datetime) VALUES (\"$id\", \"$process\",  \"$this->datetime\") ";
        if($this->processQuery($query))
                return true;
        return false;
    }

    protected function dropProcessLog($id){
        $sqlQuery = "DELETE FROM processlog WHERE pid = \"$id\" ";
        $this->processQuery($sqlQuery);
        return true;
    }

    protected function getOfficerId(){          //this function retrives the officer id of the logged officer
        return $this->getValue("id", "officer", "username", $this->user);
    }

    public function setPassword($password, $type){      //this function sets the password of the logged officer
        $password = md5($password);

        if(func_num_args() > 1){
        	if($type == 'employee'){
	        	$query = "UPDATE employeelogin SET password = \"$password\" WHERE id = \"".$this->checkEmployeeLogged()."\" ";
		        if($this->processQuery($query)){
		            $this->insertProcess($this->checkEmployeeLogged(), "Password Changed");
		                return true;
		        }	
        	}	        
        }else{
	        $query = "UPDATE officer SET password = \"$password\" WHERE username = \"$this->user\" ";
	        if($this->processQuery($query)){
	            $this->insertProcess($this->getOfficerId(), "Password Changed");
	                return true;
	        }	
        }
        
        return false;
    }

    public function getCurrentSession(){
        $currentMonth = date('Y-m-d', mktime(0, 0, 0, date('m') -1 , 15 , date('Y')));
        $sqlQuery = "SELECT id FROM fiscalyear WHERE sdate <= \"$currentMonth\" && edate > \"$currentMonth\"";
        
        $sqlQuery = $this->processArray($sqlQuery);

        if($sqlQuery[0] == "")
            $this->palert ("The previous financial year has expired. Please Ask The Administrator to Start New Financial Year To Continue", './');

        return $sqlQuery[0];
    }

    public function getSessionIds(){

        $sqlQuery = "SELECT id FROM fiscalyear ORDER BY sdate DESC";
        $sqlQuery = $this->processQuery($sqlQuery);

        $variable = array();
        while($result = mysql_fetch_array($sqlQuery))
            array_push($variable, $result[0]);

        if(sizeof($variable))
            return $variable;
        return false;
    }

    public function getSessionDetails($id){

        $sqlQuery = "SELECT * FROM fiscalyear WHERE id = \"$id\" ";
        $sqlQuery = $this->processArray($sqlQuery);

        return $sqlQuery;
    }
    
    public function setNewFinancialYear($name, $date){        
        $day = substr($date, 6, 2) - 1;
        $month = substr($date, 4, 2);
        $year = substr($date, 0, 4);

        $previousDay = mktime(0, 0, 0, $month, $day , $year);
    	$previousDay = date("Y-m-d", $previousDay);

    	$sessionIds = $this->getSessionIds();
    	
    	$sqlQuery = "UPDATE fiscalyear SET edate = \"$previousDay\" WHERE id = \"$sessionIds[0]\"";
    	$this->processQuery($sqlQuery);
    	
    	$counter = $this->getCounter('fiscalYear');
    	$sqlQuery = "INSERT INTO fiscalyear (id, name, sdate, edate) VALUES (\"$counter\", \"$name\", \"$date\", \"\")";
    	$this->processQuery($sqlQuery);

    	$this->insertProcess($counter, "NEW FINANCIAL YEAR CREATED");
    }

    public function getChangedDateFormat($date){

        $variable = explode("-", $date);
        return $variable[2]."-".$variable[1]."-".$variable[0];
    }

    public function getMonthSession($date){
        
        $pMonth = date('Y-m-d', mktime(0, 0, 0, substr($date, 4, 2) + 1, 15, substr($date, 0, 4)));        
        $sqlQuery = "SELECT id FROM fiscalyear WHERE sdate <= \"$pMonth\" && edate >= \"$pMonth\" ORDER BY sdate DESC";
        
        $sqlQuery = $this->processArray($sqlQuery);        
        return $sqlQuery[0];
    }

 }
?>
