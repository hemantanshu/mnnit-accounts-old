<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
require_once 'class.pending.php';

class allowance extends pending {

    public function  __construct() {
        parent::__construct();
    }

    public function setAllowanceNameDetails($name, $head, $update, $round, $contribution){
        $allowanceId = $this->getCounter('allowance');
        $id = $this->getCounter('accountHeadDependency');

        if($this->isAdmin()){
            $sqlQuery = "INSERT INTO accounthead (id, allowanceid, name, accounthead, allowupdate, roundoff, contribution, status) VALUES (\"$id\", \"$allowanceId\", \"$name\", \"$head\", \"$update\", \"$round\", \"$contribution\", \"y\")";
            $this->processQuery($sqlQuery);

            $processingId = $this->setPendingWork($allowanceId);
            $this->insertProcess($processingId, 'New Allowance Type Defined');
        }else{
            $sqlQuery = "INSERT INTO bakaccounthead (id, allowanceid, name, accounthead, allowupdate, roundoff, contribution, status) VALUES (\"$id\", \"$allowanceId\", \"$name\", \"$head\", \"$update\", \"$round\", \"$contribution\", \"y\")";
            $this->processQuery($sqlQuery);

            $this->setPendingWork($allowanceId);
        }
        return $allowanceId;
    }

    public function getAllowanceAccountHead($id){
        $sqlQuery = "SELECT accounthead FROM accounthead WHERE allowanceid = \"$id\" ";
        $sqlQuery = $this->processQuery($sqlQuery);

        if(mysql_num_rows($sqlQuery)){
            $sqlQuery = mysql_fetch_array($sqlQuery);
            return $sqlQuery[0];
        }
        $sqlQuery = "SELECT accounthead FROM bakaccounthead WHERE allowanceid = \"$id\" ";
        $sqlQuery = $this->processArray($sqlQuery);

        return $sqlQuery[0];
    }
    
    public function setAllowanceDetails($id, $value, $dependent, $type){   //the function to set the name of a allowance
        $dependentId = $this->getCounter('dependence');
        if($this->isAdmin()){
            $sqlQuery = "INSERT INTO subheads (id, allowanceid, value, dependent, type, status) VALUES (\"$dependentId\", \"$id\", \"$value\", \"$dependent\", \"$type\", \"y\")";
            $this->processQuery($sqlQuery);
        }else{
            $sqlQuery = "INSERT INTO baksubheads (id, allowanceid, value, dependent, type, status) VALUES (\"$dependentId\", \"$id\", \"$value\", \"$dependent\", \"$type\", \"y\",)";
            $this->processQuery($sqlQuery);
        }
        return;
    }

    public function getAllowanceIds($flag){        //all the operations are done on the deparment
        if($flag)
            $query = "SELECT allowanceid FROM accounthead WHERE status = \"y\" && id != \"ACT1\" ORDER BY name ASC";
        else
            $query = "SELECT allowanceid FROM bakaccounthead WHERE status = \"y\" && id != \"ACT1\" ORDER BY name ASC";
        
        $query = $this->processQuery($query);

        if(mysql_num_rows($query)){
            $ids = array();
            while($result = mysql_fetch_array($query))
                array_push($ids, $result[0]);

            return $ids;
        }
        return false;
    }

    public function getAllowanceDependentCount($id){

        $sqlQuery = "SELECT type FROM subheads WHERE status = \"y\" && allowanceid = \"$id\" ";
        $sqlQuery = $this->processQuery($sqlQuery);

        if(mysql_num_rows($sqlQuery))
            return mysql_num_rows($sqlQuery);

        $sqlQuery = "SELECT type FROM baksubheads WHERE status = \"y\" && allowanceid = \"$id\" ";
        $sqlQuery = $this->processQuery($sqlQuery);

        if(mysql_num_rows($sqlQuery))
            return mysql_num_rows($sqlQuery);

        return false;

    }

    public function getAllowanceDependentIds($id, $flag){
        if($flag)
            $sqlQuery = "SELECT id FROM subheads WHERE allowanceid = \"$id\" ";
        else
            $sqlQuery = "SELECT id FROM baksubheads WHERE allowanceid = \"$id\" ";
        $sqlQuery = $this->processQuery($sqlQuery);
        $variable = array();
        
        while($result = mysql_fetch_array($sqlQuery))
            array_push($variable, $result[0]);

        return $variable;
    }

    public function getAllowanceDetails($id){
        $variable = array();
        
        $count = $this->getAllowanceDependentCount($id);       

        $sqlQuery = "SELECT * FROM subheads WHERE allowanceid = \"$id\" ";
        $sqlQuery = $this->processQuery($sqlQuery);

        if(!mysql_num_rows($sqlQuery)){
            $sqlQuery = "SELECT * FROM baksubheads WHERE allowanceid = \"$id\" ";
            $sqlQuery = $this->processQuery($sqlQuery);
        }
        $i = 0;
        while($result = mysql_fetch_array($sqlQuery)){
            $variable[$i] = array();
            array_push($variable[$i], $result['id']);
            array_push($variable[$i], $result['allowanceid']);
            array_push($variable[$i], $result['value']);
            array_push($variable[$i], $result['dependent']);            
            array_push($variable[$i], $result['type']);
            array_push($variable[$i], $result['status']);
            ++$i;
        }
 
        if(sizeof($variable))
                return $variable;
        return false;
    }
    
    public function getCollegeContributionAllowanceIds(){
    	$sqlQuery = "SELECT allowanceid FROM accounthead WHERE contribution = \"y\" ";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	$variable = array();
    	while ($result = mysql_fetch_array($sqlQuery)) {
    		array_push($variable, $result[0]);
    	}
    	return $variable;
    }

    public function getAllowanceHeadDetails($id){       

        $sqlQuery = "SELECT * FROM accounthead WHERE allowanceid=\"$id\" ";
        $sqlQuery = $this->processQuery($sqlQuery);
        if(!mysql_num_rows($sqlQuery)){
            $sqlQuery = "SELECT * FROM bakaccounthead WHERE allowanceid=\"$id\" ";
            $sqlQuery = $this->processQuery($sqlQuery);
        }
        $result = mysql_fetch_array($sqlQuery);
        return $result;
    }
    
    public function isAllowanceRoundable($id){
    	$sqlQuery = "SELECT roundoff FROM accounthead WHERE allowanceid = \"$id\" ";
    	$sqlQuery = $this->processArray($sqlQuery);
    	
    	return $sqlQuery[0];
    }
	
    public function isAllowanceUpdateable($id){
    	$value = $this->getValue('allowupdate', 'accounthead', 'allowanceid', $id);
    	if($value == 'y')
    		return true;
    	return false;
    }
    
    public function getAllowanceDependentDetails($did, $flag){
        $variable = array();       

        if($flag)
            $sqlQuery = "SELECT * FROM subheads WHERE id = \"$did\" ";
        else
            $sqlQuery = "SELECT * FROM baksubheads WHERE id = \"$did\"";

        $sqlQuery = $this->processQuery($sqlQuery);
        
        while($result = mysql_fetch_array($sqlQuery)){        
            array_push($variable, $result['id']);
            array_push($variable, $result['allowanceid']);
            array_push($variable, $result['value']);
            array_push($variable, $result['dependent']);
            array_push($variable, $result['type']);
            array_push($variable, $result['status']);            
        }
        if(sizeof($variable))
                return $variable;
        return false;
    }

    public function getAllowanceOptions(){

        $variable = array();

        $sqlQuery = "SELECT allowanceid FROM accounthead ORDER BY name ASC ";
        $sqlQuery = $this->processQuery($sqlQuery);

        while($result = mysql_fetch_array($sqlQuery))
            array_push($variable, $result[0]);

        $sqlQuery = "SELECT DISTINCT(field) FROM options WHERE value != \"\" ORDER BY field ASC ";
        $sqlQuery = $this->processQuery($sqlQuery);

        while($result = mysql_fetch_array($sqlQuery))
            array_push($variable, $result[0]);


        if(sizeof($variable))
            return $variable;

        return false;
    }

    public function getAllowanceTypeName($id){     //the functoin to get the deparment name for a given department name

        $sqlQuery = "SELECT name FROM accounthead WHERE allowanceid = \"$id\" ";
        $sqlQuery = $this->processArray($sqlQuery);

        if($sqlQuery[0] == ""){
            $sqlQuery = "SELECT name FROM bakaccounthead WHERE allowanceid = \"$id\" ";
            $sqlQuery = $this->processArray($sqlQuery);

            if($sqlQuery[0] == ""){
                $variable = $this->getValue("field", "options", "field", $id);
            }else
                return $sqlQuery[0];
        }else
            return $sqlQuery[0];

        return $variable;
    }  

    

    public function getHousingEmployeeCount($id){        //the function that gives the total count of the employees working in a given department
        static $query;

        $query = "SELECT employeeid FROM personal_info WHERE housing = \"$id\" && active = \"y\" ";
        $query = $this->processQuery($query);

        return mysql_num_rows($query);
    }    

    public function getSalaryAllowanceEmployeeInfo($allowanceId, $type){
        if($type == "yes" || $type == "no"){
            if($type == "yes")
                $overridden = 'y';
            else
                $overridden = '';
            $sqlQuery = "SELECT employeeid, amount, type FROM mastersalary WHERE allowanceid = \"$allowanceId\" && active = \"y\" && overridden = \"$overridden\" ORDER BY amount DESC";            
        }
        else{
           $sqlQuery = "SELECT employeeid, amount, type FROM mastersalary WHERE allowanceid = \"$allowanceId\" && active = \"y\" ORDER BY amount DESC";
        }
        $sqlQuery = $this->processQuery($sqlQuery);
        $details = array();
        $serial = 0;
        while ($result = mysql_fetch_array($sqlQuery)){
            $details[$serial][0] = $result['employeeid'];
            $details[$serial][1] = $result['amount'];
            $details[$serial][2] = $result['type'] == 'c'?'Credit' : 'Debit';

            ++$serial;
        }
        if(sizeof($details))
            return $details;
        else
            return false;
    }
    public function deActivateAllowanceType($id){ //@todo this will check first in whole database where the field is used and if there is still persistant which is still under use then it should give a fatal error showing the list of employees whose details has to be edited
        $this->palert("sorry it cannot be deactivated as of now", "./allowance.php");
    }
    

}
?>
