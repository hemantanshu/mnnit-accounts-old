<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
////error_reporting(0)

require_once 'class.pending.php';
require_once 'class.loan.php';
require_once 'class.gpftotal.php';

class salaryRollBack extends pending {
	private $month;
	private $loan;
	private $gpfTotal;
	
    public function  __construct() {
        parent::__construct();

        $this->month = $this->currentMonth;
        $this->loan = new loan();
        $this->gpfTotal = new gpfTotal();
    }
	
    public function rollBackProcessedSalary($employeeId){    		
    	if($this->isAdmin()){
    		if($this->isEmployeeSalaryInRollBack($employeeId))
    			$sqlQuery = "UPDATE rollbacksalary SET status = \"y\" WHERE id = \"".$this->isEmployeeSalaryInRollBack($employeeId)."\" ";
    		else {
    			$id = $this->getCounter('salaryRollBack');
    			$sqlQuery = "INSERT INTO rollbacksalary (id, employeeid, month, status) VALUES (\"$id\", \"$employeeId\", \"$this->month\", \"y\")";   
    		}    			    		
    		$this->processQuery($sqlQuery);
    		
    		//delete entry from the salary table
    		$sqlQuery = "DELETE FROM salary WHERE employeeid = \"$employeeId\" && month = \"$this->month\" ";
	    	$this->processQuery($sqlQuery);
	    	
	    	//delete entry from the salaryemployeehead	    	
	    	$sqlQuery = "DELETE FROM salaryemployeehead WHERE employeeid = \"$employeeId\" && month = \"$this->month\" ";
	    	$this->processQuery($sqlQuery);

	    	//delete entry from the gpftotal
	    	$sqlQuery = "DELETE FROM gpftotal WHERE employeeid=\"$employeeId\" && month = \"$this->currentMonth\" && (flag = \"m\" || flag = \"r\" || flag = \"c\") ";
	    	$this->processQuery($sqlQuery);
	    	
	    	//delete entry from the cpftotal
	    	$sqlQuery = "DELETE FROM cpftotal WHERE employeeid=\"$employeeId\" && month = \"$this->currentMonth\" && (flag = \"m\" || flag = \"r\" || flag = \"c\") ";
	    	$this->processQuery($sqlQuery);
	    	
	    	//delete entry from the npstotal
	    	$sqlQuery = "DELETE FROM npstotal WHERE employeeid=\"$employeeId\" && month = \"$this->currentMonth\" && (flag = \"m\" || flag = \"r\" || flag = \"c\") ";
	    	$this->processQuery($sqlQuery);
	    	    	
	    	//deleting from the college contribution
            $sqlQuery = "DELETE FROM collegecontribution WHERE employeeid=\"$employeeId\" && month = \"$this->currentMonth\" ";
            $this->processQuery($sqlQuery);

            //deleting entry from the salaryloanhead
            $sqlQuery = "DELETE FROM salaryloanhead WHERE employeeid = \"$employeeId\" && month = \"$this->month\" ";
	    	$this->processQuery($sqlQuery);
	    	
          	$this->loan->revertEmployeeLoanInstallment($employeeId);
	    	$this->gpfTotal->revertEmployeeGpfLoanInstallment($employeeId);
                
	    	$processId = $this->setPendingWork($id);
	    	$this->insertProcess($processId, 'Employee Salary Roll Backed Successfully');
    	}else{
    		if(!$this->isEmployeeSalaryInRollBack($employeeId)){
    			$id = $this->getCounter('salaryRollBack');
    			$sqlQuery = "INSERT INTO rollbacksalary (id, employeeid, month, status) VALUES (\"$id\", \"$employeeId\", \"$this->month\", \"n\")";
    			$this->processQuery($sqlQuery);
    		}   		    		
    		$this->setPendingWork($id);
    	}   	
    	return true;
    }

    public function getRollBackSalaryEmployeeIds($flag, $date){
    	if(func_num_args() > 1)
    		$month = $date;
    	else
    		$month = $this->month;
		
    	if($flag)
    		$sqlQuery = "SELECT employeeid FROM rollbacksalary WHERE month = \"$month\" && status = \"y\" ";
    	else 
    		$sqlQuery = "SELECT employeeid FROM rollbacksalary WHERE month = \"$month\" && status = \"n\" ";

    	$variable = array();
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	while($result = mysql_fetch_array($sqlQuery))
    		array_push($variable, $result[0]);
    	
    	if(sizeof($variable))
    		return $variable;
    	
    	return false;
    }
    
    public function isEmployeeSalaryInRollBack($employeeId){
    	$sqlQuery = "SELECT id FROM rollbacksalary WHERE employeeid = \"$employeeId\" && status = \"n\" && month = \"$this->month\" ";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	if(mysql_num_rows($sqlQuery)){
    		$sqlQuery = mysql_fetch_array($sqlQuery);
    		return $sqlQuery[0];
    	}
    	return false;    	
    }
    
    public function dropEmployeeSalaryRollback($id){
    	if(!$this->isPendingEditable($id))
    		return false;
    		
    	$sqlQuery = "UPDATE rollbacksalary SET status = \"f\" WHERE id = \"$id\" ";
    	$this->processQuery($sqlQuery);
    	
    	$pendingId = $this->setPendingWork($id);
    	$this->insertProcess("The Salary Roll Back Service Has Been Denied", $pendingId);
    	
    	return;    	
    }
}
?>
