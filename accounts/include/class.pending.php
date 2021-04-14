<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
require_once 'class.loginInfo.php';

class pending extends loginInfo {
	
	
    public function  __construct() {
        parent::__construct();       
    }   

    protected function setPendingWork($id){         //this sets the status log into the pending table about the work flow for a given job. if the job is not in pending set a new pending else edit the existing pending job
        $loggedOfficerId = $this->getOfficerId();
        if($loggedOfficerId == '')
        	$loggedOfficerId = $this->checkEmployeeLogged();

        $query = "SELECT pid FROM pending WHERE id=\"$id\" && idAdmin = \"\" ";
        $query = $this->processQuery($query);

        if(!mysql_num_rows($query)){
            $counter = $this->getCounter("pending");

            if($this->isAdmin() || $this->checkLoanOfficerLogged())
                    $query = "INSERT INTO pending (pid, id, admin, idAdmin) VALUES (\"$counter\", \"$id\", \"$this->datetime\", \"$loggedOfficerId\" )";    //this is the admin who is entering the value;
            elseif($this->isSupretendent())
                    $query = "INSERT INTO pending (pid, id, supervisor, idSupervisor) VALUES (\"$counter\", \"$id\", \"$this->datetime\", \"$loggedOfficerId\" )";        //this is the supervisor who is entering the value;
            else
                    $query = "INSERT INTO pending (pid, id, operator, idOperator) VALUES (\"$counter\", \"$id\", \"$this->datetime\", \"$loggedOfficerId\")";        //this is the operator who is entering the value


            if($this->processQuery($query))
                    return $counter;
            return false;

        }
        $query = mysql_fetch_array($query);
        $counter = $query[0];

        if($this->isAdmin())
                $query = "UPDATE pending SET admin = \"$this->datetime\", idAdmin=\"$loggedOfficerId\" WHERE pid = \"$counter\"";        //this is the Admin who is entering the value
        elseif($this->isSupretendent())
                $query = "UPDATE pending SET supervisor = \"$this->datetime\", idSupervisor=\"$loggedOfficerId\" WHERE pid = \"$counter\"";         //this is the Supervisor who is entering the value
        else
                $query = "UPDATE pending SET operator = \"$this->datetime\", idOperator=\"$loggedOfficerId\" WHERE pid = \"$counter\"";         //this is the operator who is entering the value

        if($this->processQuery($query))
                return $counter;
        return false;
    }

    public function pendingStatus($id){         //this function returns the process pending times if avaialiable neither returns false
        
        $query = "SELECT * FROM pending WHERE id = \"$id\" && idAdmin = \"\" ";
        if($query = $this->processArray($query)){
            $variable = array();
            array_push($variable, $query['operator']);
            array_push($variable, $query['supervisor']);
            return $variable;
        }
        return false;
    }

    public function isPendingEditable($id){         //this function chks if the logged user is capable of edit the given pending job
        if($this->isAdmin()){
            $query = "SELECT idSupervisor FROM pending WHERE id = \"$id\" && idAdmin =\"\" ORDER BY operator DESC LIMIT 1";
            if($query = $this->processArray($query)){
                if($query[0] != "")
                    return true;
                return false;
            }
            return false;
        }elseif($this->isSupretendent()){
            return true;
        }else{
            $query = "SELECT idSupervisor FROM pending WHERE id = \"$id\" && idAdmin =\"\" ORDER BY operator DESC LIMIT 1";
            if($query = $this->processArray($query)){
                if($query[0] == "")
                    return true;
                return false;
            }
            return false;
        }
    }

    public function isWorkInPendingStatus($id){              //this function is used to chk if the given job is in the pending status
		$sqlQuery = "SELECT admin FROM pending WHERE id =\"$id\" && idAdmin = \"\" ";
        $sqlQuery = $this->processQuery($sqlQuery);

        if(mysql_num_rows($sqlQuery))
            return true;
        return false;
    }

    public function getPendingLogIds($id){                  //this function is used to get the process ids of the jobs corresponding to a particular event. it is used in showing the history of any event.
        $sqlQuery = "SELECT pid FROM pending WHERE id LIKE \"$id\" && idAdmin != \"\" ORDER BY admin DESC ";
        $sqlQuery = $this->processQuery($sqlQuery);

        $ids = array();
        while ($result = mysql_fetch_array($sqlQuery))
            array_push($ids, $result[0]);
        return $ids;
    }

    public function getPendingLogIdInfo($id){           //this function is used to get the complete info on the logs of a given process id
		$variable = array();

        $sqlQuery = "SELECT * FROM pending WHERE pid = \"$id\" ";
        $sqlQuery = $this->processArray($sqlQuery);

        array_push($variable, $sqlQuery['operator']);
        array_push($variable, $sqlQuery['supervisor']);
        array_push($variable, $sqlQuery['admin']);
        array_push($variable, $sqlQuery['idOperator']);
        array_push($variable, $sqlQuery['idSupervisor']);
        array_push($variable, $sqlQuery['idAdmin']);

        $sqlQuery = "SELECT log FROM processlog WHERE pid = \"$id\" ";
        $sqlQuery = $this->processArray($sqlQuery);
        array_push($variable, $sqlQuery[0]);

        return $variable;

    }

    public function getPendingNumber($id){                      //function to get the process id for a given pending id
		$sqlQuery = "SELECT pid FROM pending WHERE id = \"$id\" && idAdmin = \"\" ";
        if($sqlQuery = $this->processArray($sqlQuery))
                return $sqlQuery[0];

        return false;
    }

    public function dropPendingJob($id){                //function to drop a given peding id
        
        $starter = substr($id, 0, 3);
        $starter = $this->getValue("tablep", "counter", "starter", $starter);
        $starter = "bak".$starter;

        $sqlQuery = "DELETE FROM $starter WHERE id = \"$id\" ";
        $sqlQuery = $this->processQuery($sqlQuery);
        if(!mysql_affected_rows($sqlQuery)){
            $sqlQuery = "DELETE FROM $starter WHERE did = \"$id\" ";
            $sqlQuery = $this->processQuery($sqlQuery);
        }

        $starter = $this->getPendingNumber($id);

        $sqlQuery = "DELETE FROM pending WHERE pid = \"$starter\" ";
        if(!$this->processQuery($sqlQuery))
                return false;

        return true;
    }

    public function getRedirectUrl($code, $flag){
		if($flag)
            $sqlQuery = "SELECT aurl FROM redirect WHERE code = \"$code\" ";
        else
            $sqlQuery = "SELECT purl FROM redirect WHERE code = \"$code\" ";

        $sqlQuery = $this->processQuery($sqlQuery);
        if(mysql_num_rows($sqlQuery)){
            $sqlQuery = mysql_fetch_array($sqlQuery);
            return $sqlQuery[0];
        }
            return './';
    }

    public function getUrlOfRedirect($id){
        $code = substr($id, 0, 3);

        $sqlQuery = "SELECT id FROM pending WHERE idAdmin = \"\" && id LIKE \"$code%\" ";
        $sqlQuery = $this->processQuery($sqlQuery);

        while ($result = mysql_fetch_array($sqlQuery)) {
            if($this->isPendingEditable($result[0]))
                return $this->getRedirectUrl($code, false);
            else
                return $this->getRedirectUrl($code, true);
        }
        return $this->getRedirectUrl($code, true);
    }

    public function getNumber2Month($number){
        switch ($number) {
            case 1:
                $month = 'JANUARY';
                break;
            case 2:
                $month = 'FEBRUARY';
                break;
            case 3:
                $month = 'MARCH';
                break;
            case 4:
                $month = 'APRIL';
                break;
            case 5:
                $month = 'MAY';
                break;
            case 6:
                $month = 'JUNE';
                break;
            case 7:
                $month = 'JULY';
                break;
            case 8:
                $month = 'AUGUST';
                break;
            case 9:
                $month = 'SEPTEMBER';
                break;
            case 10:
                $month = 'OCTOBER';
                break;
            case 11:
                $month = 'NOVEMBER';
                break;
            case 12:
                $month = 'DECEMBER';
                break;
            default:
                break;
        }
        return $month;
    }
	public function nameMonth($month){
    	return ($this->getNumber2Month(substr($month, 4, 2)).", ".substr($month, 0, 4));
    }
    
    public function getFlagIds(){
    	$sqlQuery = "SELECT flag FROM flag WHERE updateable = \"y\"  ORDER BY value ASC";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	$data = array();
    	while ($result = mysql_fetch_array($sqlQuery))
    		array_push($data, $result[0]);
    	return $data;
    }
}
?>
