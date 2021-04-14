<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
////error_reporting(0)

require_once 'class.pending.php';

class blockUnblockSalary extends pending {
	
    public function  __construct() {
        parent::__construct();
    }
	
    public function blockEmployeeSalary($employeeId, $sMonth, $eMonth, $type){
    	if($this->isEmployeeSalaryBlocked($employeeId))
    		return;
    	if($sMonth == 0)
    		$sDate = $this->currentMonth;
    	else
    		$sDate = $sMonth;
    	if($this->isEmployeeSalaryBlockageInPendingStatus($employeeId)){    		
    		$sqlQuery = "UPDATE bakblocksalary SET smonth = \"$sDate\", emonth = \"$eMonth\", status=\"$type\" WHERE employeeid = \"$employeeId\" ";
    		$this->processQuery($sqlQuery);
    		$id = $this->isEmployeeSalaryBlockageInPendingStatus($employeeId);
    	}else{
    		$id = $this->getCounter('blockSalary');
    		$sqlQuery = "INSERT INTO bakblocksalary (id, employeeid, smonth, emonth, status) VALUES (\"$id\", \"$employeeId\", \"$sDate\", \"$eMonth\", \"$type\")";
    		$this->processQuery($sqlQuery);    		
    	}
    	
    	if($this->isAdmin()){
    		$sqlQuery = "INSERT INTO blocksalary (SELECT * FROM bakblocksalary WHERE employeeid = \"$employeeId\")";
    		$this->processQuery($sqlQuery);
    		
    		$sqlQuery = "DELETE FROM bakblocksalary WHERE employeeid = \"$employeeId\" ";
    		$this->processQuery($sqlQuery);
    		
    		$processId = $this->setPendingWork($id);
    		$this->insertProcess($processId, 'The Salary Of The Employee Has Been Blocked');
    	}else{
    		$this->setPendingWork($id);
    	}
    	return $id;
    }
    
    public function unblockEmployeeSalary($employeeId){
    	$blockingId = $this->getEmployeeId2BlockedId($employeeId, true);
    	
    	if(!$this->isEmployeeSalaryBlockageInPendingStatus($employeeId)){
    		$sqlQuery = "INSERT INTO bakblocksalary (SELECT * FROM blocksalary WHERE id = \"$blockingId\" )";
    		$this->processQuery($sqlQuery);    		
    	}    	
    	$sqlQuery = "UPDATE bakblocksalary SET emonth = \"".($this->currentMonth - 1)."\" WHERE id = \"$blockingId\" ";
    	$this->processQuery($sqlQuery);
    	
    	if($this->isAdmin()){
    		$sqlQuery = "DELETE FROM blocksalary WHERE id = \"$blockingId\" ";
    		$this->processQuery($sqlQuery);
    		
    		$sqlQuery = "INSERT INTO blocksalary (SELECT * FROM bakblocksalary WHERE id = \"$blockingId\" )";
    		$this->processQuery($sqlQuery);
    		
    		$sqlQuery = "DELETE FROM bakblocksalary WHERE id = \"$blockingId\" ";
    		$this->processQuery($sqlQuery);
    		    		
    		$pendingId = $this->setPendingWork($blockingId);
    		$this->insertProcess($pendingId, "The Employee Salary Has Been Successfully Unblocked");
    	}else{
    		$this->setPendingWork($blockingId);
    	}
    	return;    	
    }
    
    public function isEmployeeSalaryBlocked($employeeId){
    	$currentMonth = $this->currentMonth;
    	
    	$sqlQuery = "SELECT id FROM blocksalary WHERE employeeid = \"$employeeId\" && (emonth = \"\" || emonth >= \"$currentMonth\") ";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	if(mysql_num_rows($sqlQuery))
    		return true;
    	return false;
    }
    
    public function isEmployeeSalaryBlockageInPendingStatus($employeeId){
    	$sqlQuery = "DELETE FROM bakblocksalary WHERE emonth = \"".($this->currentMonth - 2)."\" ";
    	$this->processQuery($sqlQuery);
    	
    	$sqlQuery = "SELECT id FROM bakblocksalary WHERE employeeid = \"$employeeId\" ";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	if(mysql_num_rows($sqlQuery)){
    		$sqlQuery = mysql_fetch_array($sqlQuery);
    		return $sqlQuery[0];
    	}    		
    	return false;    	
    }

    public function dropPendingEmployeeSalaryBlockage($id){ 
    	$this->dropPendingJob($id);    	
    	return true;
    }
    
    public function getPendingBlockedEmployeeSalaryEmployeeId($flag){
    	if($flag)
    		$sqlQuery = "SELECT id FROM bakblocksalary WHERE emonth = \"\" || emonth >= \"$this->currentMonth\"";
    	else 
    		$sqlQuery = "SELECT id FROM bakblocksalary WHERE emonth != \"\" && emonth = \"".($this->currentMonth - 1)."\"";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	$variable = array();    	
    	while($result = mysql_fetch_array($sqlQuery))
    		array_push($variable, $result[0]);
    	
    	if(sizeof($variable))
    		return $variable;
    	return false;
    }
    
    public function getBlockedEmployeeIdDetails($id, $flag){
    	if($flag)
    		$sqlQuery = "SELECT * FROM blocksalary WHERE id = \"$id\" ";
    	else
    		$sqlQuery = "SELECT * FROM bakblocksalary WHERE id = \"$id\" ";
    	$sqlQuery = $this->processArray($sqlQuery);
    	
    	return $sqlQuery;
    }
    
    public function getEmployeeId2BlockedId($employeeId, $flag){
    	if($flag)
    		$sqlQuery = "SELECT id FROM blocksalary WHERE employeeid = \"$employeeId\" && (emonth >= \"$this->currentMonth\" || emonth = \"\") ";
    	else
    		$sqlQuery = "SELECT id FROM bakblocksalary WHERE employeeid = \"$employeeId\" && (emonth >= \"$this->currentMonth\" || emonth = \"\") ";
    		
    	$sqlQuery = $this->processArray($sqlQuery);
    	return $sqlQuery[0];
    }
}
?>
