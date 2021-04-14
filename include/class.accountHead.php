<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
require_once 'class.pending.php';
class accountHead extends pending {

    public function  __construct() {
        parent::__construct();
    }

    public function getAccountHeadIds($flag){        //all the operations are done on the deparment
        if($flag)
            $query = "SELECT id FROM options WHERE field=\"accountHead\" && status = \"y\" ORDER BY name ASC ";
        else
            $query = "SELECT id FROM bakoptions WHERE field=\"accountHead\" && status = \"y\" ";
        
        $query = $this->processQuery($query);

        if(mysql_num_rows($query)){
            $ids = array();
            while($result = mysql_fetch_array($query))
                array_push($ids, $result[0]);


            return $ids;
        }
        return false;
    }

    public function getAccountHeadName($id){     //the functoin to get the deparment name for a given accountHead name
        $variable = $this->getValue("name", "options", "id", $id);

        if($variable == ""){
            $variable = $this->getValue("name", "bakoptions", "id", $id);
        }

        return $variable;
    }
    
    public function getReservedAccountHeadName($accountHead, $month){
    	$sqlQuery = "SELECT name FROM salaryaccounthead WHERE accounthead = \"$accountHead\" && month = \"$month\" ";
    	$sqlQuery = $this->processArray($sqlQuery);
    	
    	return $sqlQuery[0];
    }

    public function setAccountHeadName($name){   //the function to set the name of a accountHead
        $counter = $this->getCounter("accountHead");
        if($this->isAdmin()){
            $query = "INSERT INTO options (field, id, name, status) VALUES (\"accountHead\", \"$counter\", \"$name\", \"y\") ";
            if($this->processQuery($query)){
                $pendingId = $this->setPendingWork($counter);
                $this->insertProcess($pendingId, "New AccountHead <i>".$name."</i> Created");

                return true;
            }
            return false;
        }
        $query = "INSERT INTO bakoptions (field, id, name, status) VALUES (\"accountHead\", \"$counter\", \"$name\", \"y\") ";
        if($this->processQuery($query)){
            $this->setPendingWork($counter);
            return true;
        }
        return false;
    }

    public function getAccountHeadEmployeeCount($id){        //the function that gives the total count of the employees working in a given accountHead
        $query = "SELECT distinct(employeeid) FROM mastersalary WHERE active = \"y\" && allowanceid in (SELECT id FROM subheads WHERE accountHead = \"$id\") ";
        //$query = $this->processQuery($query);

        //return mysql_num_rows($query);
    }

    public function checkAccountHeadName($name){             //the function to chk for a accountHead name if it exists already or is in the pending list
        $query = "SELECT id FROM options WHERE field=\"accountHead\" && name=\"$name\" ";
        $query = $this->processQuery($query);

        if(mysql_num_rows($query))
            return false;

        $query = "SELECT id FROM bakoptions WHERE field=\"accountHead\" && name=\"$name\" ";
        $query = $this->processQuery($query);

        if(mysql_num_rows($query))
            return false;


        return true;
    }

    public function deActivateAccountHeadName($id){ //@todo this will check first in whole database where the field is used and if there is still persistant which is still under use then it should give a fatal error showing the list of employees whose details has to be edited
        static $query;
    }

    public function getAccountHeadPending(){     //function to get the list of pending accountHeads jobs
        $query = "SELECT id FROM bakoptions WHERE field=\"accountHead\" ";
        $query = $this->processQuery($query);

        if(mysql_num_rows($query)){
            $ids = array();
            while($result = mysql_fetch_array($query))
                array_push($ids, $result[0]);

            return $ids;
        }
        return false;

    }

    public function getAccountHeadIdNature($accountHead){
        $sqlQuery = "SELECT nature FROM accounthead WHERE accounthead = \"$accountHead\" LIMIT 1";
        $sqlQuery = $this->processArray($sqlQuery);

        return $sqlQuery[0];
    }
    
    public function getAllowanceAccountHead($allowanceId){
        $sqlQuery = "SELECT accounthead FROM accounthead WHERE allowanceid = \"$allowanceId\" ";
        $sqlQuery = $this->processArray($sqlQuery);
        return $sqlQuery[0];
    }

}
?>
