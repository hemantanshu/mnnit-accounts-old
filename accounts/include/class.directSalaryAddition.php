<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
require_once 'class.pending.php';
require_once 'class.accountInfo.php';
require_once 'class.editPersonalInfo.php';


class directSalaryAddition extends pending {
    private $accounts;
    private $editPersonalInfo;
    
    public function  __construct() {
        parent::__construct();
        
        $this->accounts = new accounts();        
        $this->editPersonalInfo = new editPersonalInfo();
    }     
    
    public function getEmployeeAdditionalSalaryIds($employeeId){    
        $details = array();
        $sqlQuery = "SELECT id FROM salaryadditions WHERE employeeid = \"$employeeId\" && month = \"$this->currentMonth\" ";
        $sqlQuery = $this->processQuery($sqlQuery);

        while ($result = mysql_fetch_row($sqlQuery)) {
            array_push($details, $result[0]);
        }
        if(sizeof($details))
            return $details;

        return false;
    }
    
    public function getAdditionalSalaryIdDetails($id){
    	$sqlQuery = "SELECT * FROM salaryadditions WHERE id = \"$id\" ";
    	$sqlQuery = $this->processArray($sqlQuery);
    	
    	return $sqlQuery;
    }
    
    public function getDirectAllowanceIdDetails($id){
    	$sqlQuery = "SELECT * FROM directallowance WHERE id = \"$id\" ";
    	$sqlQuery = $this->processArray($sqlQuery);
    	
    	return $sqlQuery;
    }
    
    public function getPendingDirectAllowanceIds(){
    	$sqlQuery = "SELECT id FROM directallowance ORDER BY allowanceid ASC";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	$completeIds = array();
    	
    	while($result = mysql_fetch_array($sqlQuery))
    		array_push($completeIds, $result[0]);
    	if(sizeof($completeIds, 0))
    		return $completeIds;
    	return false;
    }
    
    public function getPendingSalaryAdditionsDropIds(){
    	$sqlQuery = "SELECT id FROM baksalaryadditions ORDER BY allowanceid ASC";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	$completeIds = array();
    	
    	while($result = mysql_fetch_array($sqlQuery))
    		array_push($completeIds, $result[0]);
    	if(sizeof($completeIds, 0))
    		return $completeIds;
    	return false;
    }
    
    public function dropAdditionalSalary($id){
    	if($this->isAdmin()){
    		$sqlQuery = "DELETE FROM salaryadditions WHERE id = \"$id\"";
    		$this->processQuery($sqlQuery);
    		
    		$sqlQuery = "DELETE FROM baksalaryadditions WHERE id = \"$id\"";
    		$this->processQuery($sqlQuery);    		
    		
    		$pendingId = $this->setPendingWork($id);
    		$this->insertProcess($id, "The Additional Salary Component Has Been Successfully Dropped");
    	}else{
    		if(!$this->isWorkInPendingStatus($id)){
    			$sqlQuery = "INSERT INTO baksalaryadditions (SELECT * FROM salaryadditions WHERE id = \"$id\")";
    			$this->processQuery($sqlQuery);	
    		}   		
    		$this->setPendingWork($id);
    	}
    	return;
    }
    
    public function dropPendingAdditionalSalaryRequest($id){
    	if (substr($id, 0, 3) == "DAA"){
    		$sqlQuery = "DELETE FROM directallowance WHERE id = \"$id\" ";
    		$this->processQuery($sqlQuery);
    		
    		$sqlQuery = "DELETE FROM pending WHERE id = \"$id\"";
    		$this->processQuery($sqlQuery);    		
    	}else{
    		$this->dropPendingJob($id);
    	}    		
    }
    
    public function insertDirectSalary($employeeId, $allowanceId, $amount, $direct){
    	$counter = $this->getCounter('directAllowance');
    	if($amount > 0)
    		$type = 'c';
    	else
    		$type = 'd';
    	
    		
    	$sqlQuery = "INSERT INTO directallowance (id, employeeid, allowanceid, amount, type, direct) VALUES (\"$counter\", \"$employeeId\", \"$allowanceId\", \"".abs($amount)."\", \"$type\", \"$direct\")";
    	$this->processQuery($sqlQuery);
        
    	$this->setPendingWork($counter);
    	if($this->isAdmin())
    		$this->insertPendingDirectSalary($counter);
    	return;
    }
    
    public function insertPendingDirectSalary($id){
    	$this->setPendingWork($id);
    	if(!$this->isAdmin())
    		return;
    		
    	$sqlQuery = "SELECT * FROM directallowance WHERE id = \"$id\" ";
    	$details  = $this->processArray($sqlQuery);

    	if($details[5] == 'y'){ //the salary has to be updated on the salary addition table
                $sqlQuery = "DELETE FROM salaryadditions WHERE employeeid = \"$details[1]\" && allowanceid = \"$details[2]\" && month = \"$this->currentMonth\"";
                $this->processQuery($sqlQuery);

                if($details[3] != 0){
                    $counter = $this->getCounter('salaryAddition');

                    $sqlQuery = "INSERT INTO salaryadditions (id, employeeid, allowanceid, amount, type, month) VALUES (\"$counter\", \"$details[1]\", \"$details[2]\", \"$details[3]\", \"$details[4]\", \"$this->currentMonth\")";
                    $this->processQuery($sqlQuery);
                }                
    		$pendingId = $this->setPendingWork($details[0]);
    		$this->insertProcess($pendingId, "The Allowance Has Been Successfully Added In The Directs Salary");
    		
    	}elseif($details[5] == 'n'){ //the salary has to be updated in the mastersalary table
                if($details[2] == 'ACT1'){
                    $this->editPersonalInfo->updateEmployeeBasicSalary($details[1], $details[3]);
                }elseif($details[3] == 0){
                    $sqlQuery = "DELETE FROM mastersalary WHERE allowanceid = \"$details[2]\" && employeeid = \"$details[1]\" ";
                    $this->processQuery($sqlQuery);
                }else{
                    $salaryAdditionAmount = $this->accounts->getAccountSum($details[1], $details[2]);
                    if($salaryAdditionAmount != ($details[4] == 'c' ? $details[3] : (0 - $details[3]))) //checking for the overridden settings
                            $flag = 'y';

                    $sqlQuery = "SELECT * FROM mastersalary WHERE active = \"y\" && allowanceid = \"$details[2]\" && employeeid = \"$details[1]\" ";
                    $sqlQuery = $this->processQuery($sqlQuery);

                    if(mysql_num_rows($sqlQuery)){
                            $additionalSalary = mysql_fetch_array($sqlQuery);

                            $sqlQuery = "UPDATE mastersalary SET amount = \"$details[3]\", type = \"$details[4]\", overridden = \"$flag\" WHERE id = \"$additionalSalary[0]\" ";
                            $this->processQuery($sqlQuery);

                    }else{
                            $counter = $this->getCounter('masterSalary');

                            $sqlQuery = "SELECT did FROM mastersalary WHERE employeeid = \"$details[1]\" && active = \"y\" ";
                            $sqlQuery = $this->processArray($sqlQuery);
                            if($sqlQuery[0] != ''){ //there is entry for the employee in the mastersalary table
                                    $did = $sqlQuery[0];
                            }else{ //there is no entry for the employee in the mastersalary table
                                    $did = $this->getCounter('masterSalaryDependency');
                            }
                            $sqlQuery = "INSERT INTO mastersalary (id, did, employeeid, sessionid, allowanceid, amount, type, overridden, active) VALUES (\"$counter\", \"$did\", \"$details[1]\", \"".$this->getCurrentSession()."\", \"$details[2]\", \"$details[3]\", \"$details[4]\", \"$flag\", \"y\")";
                            $this->processQuery($sqlQuery);
                    }                    
                }
                $pendingId = $this->setPendingWork($details[0]);
                $this->insertProcess($pendingId, "The Allowance Has Been Successfully Updated In The Master Salary Table");
    		
    	}
    	$sqlQuery = "DELETE FROM directallowance WHERE id = \"$id\"";
        $this->processQuery($sqlQuery);
		    	
    	return;
    }

    public function getAdditionalSalaryId($month){
    	$sqlQuery = "SELECT id FROM salaryadditions WHERE month = \"$month\" ORDER BY allowanceid ASC";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	$completeIds = array();
    	while($result = mysql_fetch_array($sqlQuery))
    		array_push($completeIds, $result[0]);
    	if(sizeof($completeIds))
    		return $completeIds;
    	return false;    	    	
    }
}
?>
