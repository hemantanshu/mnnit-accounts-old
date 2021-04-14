<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
require_once 'class.pending.php';
class gpfTotal extends pending {

    public function  __construct() {
        parent::__construct();
    }
    
    public function getGpfIdDetails($id, $flag){
    	if($flag)
    		$sqlQuery = "SELECT * FROM gpftotal WHERE id = \"$id\"";
    	else
    		$sqlQuery = "SELECT * FROM bakgpftotal WHERE id = \"$id\"";
    	return $this->processArray($sqlQuery);    		
    }
    
    public function getGpfLoanIds($flag){
    	if($flag)
    		$sqlQuery = "SELECT id FROM gpfloanaccount WHERE status = \"y\" && refundable = \"y\" ORDER BY employeeid ASC";
    	else 
    		$sqlQuery = "SELECT id FROM gpfloanaccount ORDER BY employeeid ASC";	
    	$completeIds = array();
    	$sqlQuery = $this->processQuery($sqlQuery);
    	while($result = mysql_fetch_array($sqlQuery))
    		array_push($completeIds, $result[0]);
    	if(sizeof($completeIds))
    		return $completeIds;
    	return false; 
    }
    
    public function getEmployeeGpfIds($employeeId, $flag, $month){
    	if(func_num_args() > 2){
    		if($flag)
    			$sqlQuery = "SELECT id FROM gpftotal WHERE employeeid = \"$employeeId\" && month = \"$month\" ";
    		else 
    			$sqlQuery = "SELECT id FROM bakgpftotal WHERE employeeid = \"$employeeId\" && month = \"$month\" ";
    	}else{
    		if($flag)
    			$sqlQuery = "SELECT id FROM gpftotal WHERE employeeid = \"$employeeId\" ORDER BY month ASC ";
    		else 
    			$sqlQuery = "SELECT id FROM bakgpftotal WHERE employeeid = \"$employeeId\" ORDER BY month ASC";
    	}
    	$completeIds = array();
    	$sqlQuery = $this->processQuery($sqlQuery);
    	while($result = mysql_fetch_array($sqlQuery))
    		array_push($completeIds, $result[0]);
    	if(sizeof($completeIds))
    		return $completeIds;
    	return false;
    }
    
	public function getGpfLoanInstallmentIds($id){    	
		$sqlQuery = "SELECT id FROM gpfloaninstallment WHERE loanid = \"$id\" ORDER BY month ASC";    	
    	$completeIds = array();
    	$sqlQuery = $this->processQuery($sqlQuery);
    	while($result = mysql_fetch_array($sqlQuery))
    		array_push($completeIds, $result[0]);
    	if(sizeof($completeIds))
    		return $completeIds;
    		
    	return false;
    }
    
	public function getGpfLoanInstallmentIdDetails($id, $flag){
    	if($flag)
    		$sqlQuery = "SELECT * FROM gpfloaninstallment WHERE id = \"$id\"";
    	else
    		$sqlQuery = "SELECT * FROM bakgpfloaninstallment WHERE id = \"$id\"";
    	return $this->processArray($sqlQuery);    		
    }
    
    public function getEmployeeGpfTotalSum($employeeId, $month){
    	if(func_num_args() > 1)
    		$sqlQuery = "SELECT SUM(amount) FROM gpftotal WHERE employeeid = \"$employeeId\" && month <= \"$month\"";
    	else 
    		$sqlQuery = "SELECT SUM(amount) FROM gpftotal WHERE employeeid = \"$employeeId\" ";
    	
    	$sqlQuery = $this->processArray($sqlQuery);
    	
    	return $sqlQuery[0];
    }

	public function getEmployeeCpfTotalSum($employeeId, $month){
    	if(func_num_args() > 1)
    		$sqlQuery = "SELECT SUM(amount) FROM cpftotal WHERE employeeid = \"$employeeId\" && month <= \"$month\"";
    	else 
    		$sqlQuery = "SELECT SUM(amount) FROM cpftotal WHERE employeeid = \"$employeeId\" ";
    	
    	$sqlQuery = $this->processArray($sqlQuery);
    	
    	return $sqlQuery[0];
    }
    
    public function getEmployeeNpsTotalSum($employeeId, $month){
    	if(func_num_args() > 1)
    		$sqlQuery = "SELECT SUM(amount) FROM npstotal WHERE employeeid = \"$employeeId\" && month <= \"$month\"";
    	else 
    		$sqlQuery = "SELECT SUM(amount) FROM npstotal WHERE employeeid = \"$employeeId\" ";
    	
    	$sqlQuery = $this->processArray($sqlQuery);
    	
    	return $sqlQuery[0];
    }
    public function getEmployeeGpfLoanAccountId($employeeId){ //is capable of taking two arguments  
    	if(func_num_args() > 1){ //getting the inactive loan
    		if(func_num_args() > 2)
    			$sqlQuery = "SELECT id FROM gpfloanaccount WHERE employeeid = \"$employeeId\" && refundable = \"y\""; //total refundable loan records;
    		else{ 
	    		if(func_get_arg(1))
	    			$sqlQuery = "SELECT id FROM gpfloanaccount WHERE employeeid = \"$employeeId\" && refundable = \"\""; //non refundable loan
	    		else 
	    			$sqlQuery = "SELECT id FROM gpfloanaccount WHERE employeeid = \"$employeeId\" && status = \"\" && refundable = \"y\""; //refundable loan
    		}	
    	}else    	
    		$sqlQuery = "SELECT id FROM gpfloanaccount WHERE employeeid = \"$employeeId\" && status = \"y\" && refundable = \"y\" && id not in (SELECT loanid FROM stopinstallment WHERE month=\"$this->currentMonth\")";
    	
    	$sqlQuery = $this->processQuery($sqlQuery);
    	$variable = array();
    	while ($result = mysql_fetch_array($sqlQuery))
    		array_push($variable, $result[0]);
    		
    	if (sizeof($variable, 0))
    		return $variable;    	
    	
    	return false;    	
    }
    
    public function getEmployeeGpfLoanAccountIdDetails($id){
    	$sqlQuery = "SELECT * FROM gpfloanaccount WHERE id = \"$id\"";
    	$sqlQuery = $this->processArray($sqlQuery);
    	if($sqlQuery[0] == "")
    		return false;
    	return $sqlQuery;
    }
    
    public function getEmployeeGpfLoanInstallmentAmount($employeeId){
    	$sqlQuery = "SELECT amount FROM installment WHERE id = \"$employeeId\" ";
    	$sqlQuery = $this->processArray($sqlQuery);
    	if($sqlQuery[0] == "")
    		return false;
    	return $sqlQuery[0];
    }
    
    public function getEmployeeGpfLoanAmountLeft($employeeId){ //to check the amount left in the loan 
    	$loanAccountId = $this->getEmployeeGpfLoanAccountId($employeeId);
    	if(func_num_args() > 2)
    		$sqlQuery = "SELECT sum(amount) FROM gpfloaninstallment WHERE loanid = \"".$loanAccountId[0]."\" && month <= \"".func_get_arg(1)."\" && id NOT IN (SELECT id FROM gpfloaninstallment WHERE amount < \"0\" && month = \"".func_get_arg(1)."\" && loanid = \"$loanAccountId[0]\")";
    	elseif (func_num_args() > 1)
    		$sqlQuery = "SELECT sum(amount) FROM gpfloaninstallment WHERE loanid = \"".$loanAccountId[0]."\" && month <= \"".func_get_arg(1)."\"";
    	else 
    		$sqlQuery = "SELECT sum(amount) FROM gpfloaninstallment WHERE loanid = \"".$loanAccountId[0]."\"";    		
   		
    	$sqlQuery = $this->processArray($sqlQuery);
    	
    	return abs($sqlQuery[0]);
    	    	
    }
    
    
    public function getEmployeeGpfLoanInstallmentLeft($employeeId){ //to check the no of installments left
    	$loanAccountId = $this->getEmployeeGpfLoanAccountId($employeeId);    	    	    	
    	if (func_num_args() > 2)
    		$sqlQuery = "SELECT count(id) FROM gpfloaninstallment WHERE loanid = \"".$loanAccountId[0]."\" && amount < \"0\" && month < \"".func_get_arg(1)."\"";
    	elseif (func_num_args() > 1)
    		$sqlQuery = "SELECT count(id) FROM gpfloaninstallment WHERE loanid = \"".$loanAccountId[0]."\" && amount < \"0\" && month <= \"".func_get_arg(1)."\"";
    	else 
    		$sqlQuery = "SELECT count(id) FROM gpfloaninstallment WHERE loanid = \"".$loanAccountId[0]."\" && flag = \"r\"";	
    	    	
    	$sqlQuery = $this->processArray($sqlQuery);
    	$totalInstallmentPaid = $sqlQuery[0];
    	
    	$details = $this->getEmployeeGpfLoanAccountIdDetails($loanAccountId[0]);    	
    	return ($details[3] - $totalInstallmentPaid);
    }
    
    public function sanctionNewLoan($employeeId, $amount, $installment, $type){ //to sanction new loan 
    	$loanAccountId = $this->getEmployeeGpfLoanAccountId($employeeId);
    	if($loanAccountId && $type == 'r'){//there is already an active loan account  && the new loan type is refundable

    		$details = $this->getEmployeeGpfLoanAccountIdDetails($loanAccountId[0]);
    		$totalAmount = $amount + $this->getEmployeeGpfLoanAmountLeft($employeeId);    		
    		$installmentLeft = $this->getEmployeeGpfLoanInstallmentLeft($employeeId);
    		$installmentPaid = $details[3] - $installmentLeft; 
    		
    		$totalInstallment = $installment + $this->getEmployeeGpfLoanInstallmentLeft($employeeId);
    		$totalInstallment = $totalInstallment > 36 ? 36 : $totalInstallment;
    		$installmentAmount = ceil($totalAmount / $totalInstallment);
            $totalInstallment += $installmentPaid;                       		  		
    		
    		//updating the gpf loan account
    		$sqlQuery = "UPDATE gpfloanaccount SET amount = \"".($details[2] + $amount)."\", installment = \"$totalInstallment\" WHERE id = \"$loanAccountId[0]\" ";
    		$this->processQuery($sqlQuery);
    		
    		//updating the installment entry
    		$sqlQuery = "UPDATE installment SET amount = \"$installmentAmount\" WHERE id = \"$employeeId\"";
    		$this->processQuery($sqlQuery);
    		
    		//inserting a new entry into gpf loaninstallment 
    		$counter = $this->getCounter('gpfLoanInstallment');
    		$sqlQuery = "INSERT INTO gpfloaninstallment (id, loanid, amount, month, flag) VALUES (\"$counter\", \"$loanAccountId[0]\", \"$amount\", \"$this->currentMonth\", \"n\")";
    		$this->processQuery($sqlQuery);
    		
    		//inserting a new entry into employee gpftotal
    		$counter = $this->getCounter('gpfTotal');
    		$sqlQuery = "INSERT INTO gpftotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"".(0 - $amount)."\", \"$this->currentMonth\", \"n\")";
    		$this->processQuery($sqlQuery);
    		
    		$counter = $this->setPendingWork($loanAccountId);
    		$this->insertProcess($counter, "NEW GPF LOAN ACCOUNT TAKEN. AMOUNT = $amount");
    		
    	}else{//no existing gpf loan account exists
    		
    		$counter = $this->getCounter('gpfLoanAccount');    		
            $totalInstallment = $installment > 36 ? 36 : $installment;
    		$installmentAmount = ceil($amount / $totalInstallment);
    		$option = $type == 'r' ? 'y' : '';    		
    		
	    	//creating new gpf loan account
    		$sqlQuery = "INSERT INTO gpfloanaccount (id, employeeid, amount, installment, refundable, month, status) VALUES (\"$counter\", \"$employeeId\", \"$amount\", \"$totalInstallment\", \"$option\", \"$this->currentMonth\", \"y\")";
    		$this->processQuery($sqlQuery);
    		
    		if($type == 'r'){ //the loan sanctioned is non refundable
    			//creating new installment entry
    			$sqlQuery = "UPDATE installment SET amount = \"$installmentAmount\" WHERE id = \"$employeeId\" ";
    			$sqlQuery = $this->processQuery($sqlQuery);
    			if(!mysql_affected_rows($sqlQuery)){
		    		$sqlQuery = "INSERT INTO installment (id, amount) VALUES (\"$employeeId\", \"$installmentAmount\")";
		    		$this->processQuery($sqlQuery);
    			}
    				    		
	    		//inserting a new entry into gpf loaninstallment 
	    		$counter1 = $this->getCounter('gpfLoanInstallment');
	    		$sqlQuery = "INSERT INTO gpfloaninstallment (id, loanid, amount, month, flag) VALUES (\"$counter1\", \"$counter\", \"$amount\", \"$this->currentMonth\", \"n\")";
	    		$this->processQuery($sqlQuery);	
    		}   		
    		
    		///inserting a new entry into employee gpftotal
    		$counter1 = $this->getCounter('gpfTotal');
    		$sqlQuery = "INSERT INTO gpftotal (id, employeeid, amount, month, flag) VALUES (\"$counter1\", \"$employeeId\", \"".(0 - $amount)."\", \"$this->currentMonth\", \"n\")";
    		$this->processQuery($sqlQuery);
    		
    		$counter1 = $this->setPendingWork($counter);
    		$this->insertProcess($counter1, "NEW GPF LOAN ACCOUNT TAKEN. AMOUNT = $amount");
    	}
    }
    
    public function processEmployeGPFLoanInstallment($employeeId, $flag){ //to process the gpf advance recovery amount in the salary slip during salary processing
 		$variable = $this->getEmployeeGpfLoanAccountId($employeeId);   //getting the gpf loan account id details which is refundable and not blocked
 		if($variable){
 			if(!$flag){
 				$amount['installment'] = $this->getEmployeeGpfLoanInstallmentAmount($employeeId);    //getting the installment amount
 				$amount['left'] = $this->getEmployeeGpfLoanAmountLeft($employeeId);
 				
 				$amount = $amount['left'] < $amount['installment'] ? $amount['left'] : $amount['installment']; 				
 				$counter = $this->getCounter('gpfLoanInstallment'); 				
 				
 				$sqlQuery = "INSERT INTO bakgpfloaninstallment (id, loanid, amount, month, flag) VALUES (\"$counter\", \"$variable[0]\", \"".(0 - $amount)."\", \"$this->currentMonth\", \"r\")";
 				$this->processQuery($sqlQuery);
 				
 				return $amount;
 			}else{ 				
 				$sqlQuery = "INSERT INTO gpfloaninstallment (SELECT * FROM bakgpfloaninstallment WHERE month = \"$this->currentMonth\" && loanid = \"$variable[0]\")";
 				$this->processQuery($sqlQuery);
 				 				
 				$leftAmount = $this->getEmployeeGpfLoanAmountLeft($employeeId);
 				if($leftAmount != 0){
 					$counter = $this->getCounter("salaryLoanHead");
 					$installment = $this->getEmployeeGpfLoanInstallmentLeft($employeeId);

 					$sqlQuery = "INSERT INTO salaryloanhead (id, employeeid, loanid, amount, installment, flag, month) VALUES (\"$counter\", \"$employeeId\", \"$variable[0]\", \"$leftAmount\", \"$installment\", \"g\", \"$this->currentMonth\")";
 					$this->processQuery($sqlQuery);
 				}else{ //the gpf loan account has been completed. making the account inactive now
                    $sqlQuery = "UPDATE gpfloanaccount SET status = \"\" WHERE id = \"$variable[0]\""; //changing gpfloanaccount table status flag
                     $this->processQuery($sqlQuery);                    
                } 				
 				$sqlQuery = "DELETE FROM bakgpfloaninstallment WHERE loanid = \"$variable[0]\" ";
 				$this->processQuery($sqlQuery);
 			}
 			return true;
 		}	
 		return false;
    }
    
    public function processEmployeeGpfTotal($employeeId){    	//to process the gpf and advance recovery amount in the gpf total table while processing salary during admin operation
		//inserting the gpf amount in their respective account
		$sqlQuery = "SELECT * FROM salary WHERE employeeid = \"$employeeId\" && accounthead = \"ACH14\" && month = \"$this->currentMonth\""; 
		$details = $this->processArray($sqlQuery);
		if($details){
			$counter = $this->getCounter('gpfTotal');
			$amount = $details[6] == 'd' ? $details[5] : (0 - $details[5]);
			$sqlQuery = "INSERT INTO gpftotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$amount\", \"$this->currentMonth\", \"m\")";
			$this->processQuery($sqlQuery);
		}
		
		//gpf advance recovery
		$sqlQuery = "SELECT * FROM salary WHERE employeeid = \"$employeeId\" && accounthead = \"ACH15\" && month = \"$this->currentMonth\""; 
		$details = $this->processArray($sqlQuery);
		if($details){
			$counter = $this->getCounter('gpfTotal');
			$amount = $details[6] == 'd' ? $details[5] : (0 - $details[5]);
			$sqlQuery = "INSERT INTO gpftotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$amount\", \"$this->currentMonth\", \"r\")";
			$this->processQuery($sqlQuery);
		}
		return true;           	   	
    }   
    
    public function revertEmployeeGpfLoanInstallment($employeeId){
    	$variable = $this->getEmployeeGpfLoanAccountId($employeeId, "all", "all"); //getting all the refundable loan records
    	foreach ($variable as $loanId){
    		$sqlQuery = "DELETE FROM gpfloaninstallment WHERE loanid = \"$loanId\" && month = \"$this->currentMonth\" && flag = \"r\" ";
    		$sqlQuery = $this->processQuery($sqlQuery);
    		if(mysql_affected_rows($sqlQuery)){
    			$sqlQuery = "UPDATE gpfloanaccount SET status = \"y\" WHERE id = \"$loanId\" ";
    			$this->processQuery($sqlQuery);
    		}
    	}
    	return;
    }
    
    
    public function getProcessedGPFInstallmentId($month){
    	$sqlQuery = "SELECT id FROM gpfloaninstallment WHERE month = \"$month\" && flag = \"r\" ";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	$variable = array();
    	while ($result = mysql_fetch_array($sqlQuery))
    		array_push($variable, $result[0]);
    	if (sizeof($variable, 0))
    		return $variable;
    	return false;    	
    }
    
    public function getMonthlyNewGpfLoanAccountInstallmentId($month , $flag){
    	if ($flag)
    		$sqlQuery = "SELECT id FROM gpfloaninstallment WHERE amount > \"0\" && month = \"$month\" && (flag = \"n\" || flag = \"e\")";
    	else 
    		$sqlQuery = "SELECT id FROM gpfloanaccount WHERE month = \"$month\" && refundable = \"\" ";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	$variable = array();
    	while ($result = mysql_fetch_array($sqlQuery)) {
    		array_push($variable, $result[0]);
    	}
    	if (sizeof($variable, 0))
    		return $variable;
    	return false;
    }

    public function directAdditionOfAmount($employeeId, $month, $amount, $fund_type, $type){
        if($type == "GPF"){
            $counter = $this->getCounter('gpfTotal');
            $sqlQuery = "INSERT INTO gpftotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$amount\", \"$month\", \"$fund_type\")";
            $this->processQuery($sqlQuery);
            return true;
        }elseif($type == "CPF"){
            $counter = $this->getCounter('cpfTotal');
            $sqlQuery = "INSERT INTO cpftotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$amount\", \"$month\", \"$fund_type\")";
            $this->processQuery($sqlQuery);
            return true;
        }elseif($type == "NPS"){
            $counter = $this->getCounter('npsTotal');
            $sqlQuery = "INSERT INTO npstotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"$amount\", \"$month\", \"$fund_type\")";
            $this->processQuery($sqlQuery);
            return true;
        }
        return false;
    }
    
    public function getEmployeeTotalFundBalance($employeeId, $fundType){                         
        $table = $fundType == "gpf" ? "gpftotal" : ($fundType == "cpf" ? "cpftotal" : "npstotal");
        $sqlQuery = "SELECT SUM(amount) FROM $table WHERE employeeid = \"$employeeId\" ";                                                                                  
        $sqlQuery = $this->processArray($sqlQuery);
        
        return $sqlQuery[0];
    }
    
    public function getEmployeeFundIds($employeeId, $fundType){
        $table = $fundType == "gpf" ? "gpftotal" : ($fundType == "cpf" ? "cpftotal" : "npstotal");
        $sqlQuery = "SELECT id FROM $table WHERE employeeid = \"$employeeId\" ORDER BY month, flag ASC";        
        $completeIds = array();
        $sqlQuery = $this->processQuery($sqlQuery);
        while($result = mysql_fetch_array($sqlQuery))
            array_push($completeIds, $result[0]);
        if(sizeof($completeIds))
            return $completeIds;
        return false;
    }

    public function getFundIdDetails($id, $fundType){
        $table = $fundType == "gpf" ? "gpftotal" : ($fundType == "cpf" ? "cpftotal" : "npstotal");
        $sqlQuery = "SELECT * FROM $table WHERE id = \"$id\"";
        return $this->processArray($sqlQuery);            
    }
    
    public function checkInstallment(){
    	$completeIds = $this->getGpfLoanIds(true);    	
    	foreach ($completeIds as $loanId){
    		$sqlQuery = "SELECT * FROM gpfloanaccount where id = \"$loanId\" ";
    		$details = $this->processArray($sqlQuery);
    		
    		$amount = $this->getEmployeeGpfLoanAmountLeft($details[1]);
    		$left = $this->getEmployeeGpfLoanInstallmentLeft($details[1]);
    		
    		$installment = $this->getEmployeeGpfLoanInstallmentAmount($details[1]);
    		echo $amount."====".$left."====".$installment."=====";
    		echo $details[1]."====".(ceil($amount / $left) - $installment)."<br />";
    	}
    }
    
}
?>
