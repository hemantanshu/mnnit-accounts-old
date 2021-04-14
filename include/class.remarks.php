<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */

require_once 'class.pending.php';

class remarks extends pending {
    
    public function  __construct() {
        parent::__construct();
    }
    
    public function insertSalaryRemarks($employeeId, $remarks, $date){
    	$counter = $this->getCounter('remarks');	
    	
    	if($this->isAdmin()){
    		$sqlQuery = "INSERT INTO remarks (id, employeeid, month, remarks, type) VALUES (\"$counter\", \"$employeeId\", \"$date\", \"$remarks\", \"s\")";
    		$this->processQuery($sqlQuery);
    		
    		$pendingId = $this->setPendingWork($counter);
    		$this->insertProcess($pendingId, "The Remark For The Employee Has Been Successfully Inserted");    		
    	}else{
    		$sqlQuery = "INSERT INTO bakremarks (id, employeeid, month, remarks, type) VALUES (\"$counter\", \"$employeeId\", \"$date\", \"$remarks\", \"s\")";
    		$this->processQuery($sqlQuery);
    		
    		$this->setPendingWork($counter);
    	}
    	return;
    }
    
    public function getPendingSalaryRemarksId(){
    	$sqlQuery = "DELETE FROM bakremarks WHERE month < \"$this->currentMonth\"";
    	$this->processQuery($sqlQuery);
    	
    	$sqlQuery = "SELECT id FROM bakremarks WHERE month >= \"$this->currentMonth\" ";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	$remarksId = array();
    	while($result = mysql_fetch_array($sqlQuery))
    		array_push($remarksId, $result[0]);
    	if(sizeof($remarksId))
    		return $remarksId;
    		
    	return false;    	
    }
    
    public function getRemarkIdDetails($id, $flag){
    	if($flag)
    		$sqlQuery = "SELECT * FROM remarks WHERE id = \"$id\" ";
    	else 
    		$sqlQuery = "SELECT * FROM bakremarks WHERE id = \"$id\" ";
    	$sqlQuery = $this->processArray($sqlQuery);
    	
    	return $sqlQuery;
    }
    
    public function updatePendingRemarks($id, $remarks){
    	$sqlQuery = "UPDATE bakremarks SET remarks = \"$remarks\" WHERE id = \"$id\" ";
    	$this->processQuery($sqlQuery);
    	
    	if($this->isAdmin()){
    		$sqlQuery = "INSERT INTO remarks (SELECT * FROM bakremarks WHERE id = \"$id\")";
    		$this->processQuery($sqlQuery);
    		
    		$sqlQuery = "DELETE FROM bakremarks WHERE id = \"$id\" ";
    		$this->processQuery($sqlQuery);
    		
    		$pendingId = $this->setPendingWork($id);
    		$this->insertProcess($pendingId, "The Remark For The Employee Has Been Successfully Updated");
    	}else{
    		$this->setPendingWork($id);
    	}
    	return;
    }
    
    public function isEmployeeRemarkAvailiable($employeeId, $month){
    	$sqlQuery = "SELECT id FROM remarks WHERE employeeid = \"$employeeId\" && month = \"$month\" ";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	$remarksId = array();
    	while($result = mysql_fetch_array($sqlQuery))
    		array_push($remarksId, $result[0]);
    	if(sizeof($remarksId))
    		return $remarksId;
    	return false;
    }
    
	public function getEmployeeLoginDetails($employeeId){
		$sqlQuery = "SELECT password FROM employees WHERE id = \"$employeeId\" ";
		$sqlQuery = $this->processArray($sqlQuery);
		
		return $sqlQuery[0];
	}
}
?>
