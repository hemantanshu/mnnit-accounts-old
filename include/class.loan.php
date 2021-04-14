<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
require_once 'class.pending.php';
require_once 'class.dateDifference.php';

class loan extends pending {
	protected $dateDifference;
	
    public function  __construct() {
        parent::__construct();
        
        $this->dateDifference = new dateDifference();
    }

	public function getLoanTypeId(){
    	$sqlQuery = "SELECT id FROM loantype ORDER BY name ASC";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	$variable = array();
    	while ($result = mysql_fetch_array($sqlQuery))
    		array_push($variable, $result[0]);
    	return $variable;
    }
    
    public function getLoanTypeIdDetails($id){
    	$sqlQuery = "SELECT * FROM loantype WHERE id = \"$id\" ";
    	$sqlQuery = $this->processArray($sqlQuery);
    		
    	return $sqlQuery;
    }
    
    public function getLoanAccountIdDetails($id){
    	$sqlQuery = "SELECT * FROM loanaccount WHERE id = \"$id\" ";
    	$sqlQuery = $this->processArray($sqlQuery);
    	
    	return $sqlQuery;
    }
    
	public function getInstallmentAmount($id){
    	return $this->getValue('amount', 'installment', 'id', $id);
    }
    
    public function getLoanAccountId(){
    	$sqlQuery = "SELECT id FROM loanaccount WHERE status = \"y\" ORDER BY loanid ASC";    	
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	$variable = array();
    	while ($result = mysql_fetch_array($sqlQuery))
    		array_push($variable, $result[0]);
    	return $variable;
    }    
    
    public function getLoanInstallmentLeft($id, $date){
    	if (func_num_args() > 1)
    		if (func_num_args() > 2)
    			$sqlQuery = "SELECT id FROM loaninstallment WHERE loanid = \"$id\" && month <= \"$date\" && flag = \"r\" && id NOT IN (SELECT id FROM loaninstallment WHERE month = \"$date\" && (flag = \"r\" || flag = \"i\"))";
    		else 
    			$sqlQuery = "SELECT id FROM loaninstallment WHERE loanid = \"$id\" && month <= \"$date\" && flag = \"r\" ";
    	else 
    		$sqlQuery = "SELECT id FROM loaninstallment WHERE loanid = \"$id\" && month <= \"$this->currentMonth\" && flag = \"r\" ";    	
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	$variable = mysql_num_rows($sqlQuery);    	
    	$details = $this->getLoanAccountIdDetails($id);
    	
    	$installmentLeft =  $details[4] - $variable;
    	if($installmentLeft <= 0){
    		if($details[6] == 0) // interest rate is zero
    			return 0;
    			    			    		    	
	    	$installmentLeft =  $details[5] + $details[4] - $variable;
    	}
    	return $installmentLeft;    	
    }
    
    public function getLoanPrincipleAmountLeft($id, $date){
        $sqlQuery = "SELECT SUM(amount) FROM loaninstallment WHERE loanid = \"$id\" && month <= \"$date\" && flag != \"i\"";
    	$sqlQuery = $this->processArray($sqlQuery);
    	
    	return $sqlQuery[0];
    }
    
    public function getLoanAmountLeft($id, $date){
    	if(func_num_args() > 2) //the loan balance amount is required but excluding the installment amount
    		$sqlQuery = "SELECT SUM(amount) FROM loaninstallment WHERE loanid = \"$id\" && month <= \"$date\" && id NOT IN (SELECT id FROM loaninstallment WHERE month = \"$date\" && (flag = \"r\" || flag = \"i\"))";	
    	else
        	$sqlQuery = "SELECT SUM(amount) FROM loaninstallment WHERE loanid = \"$id\" && month <= \"$date\"";
        $sqlQuery = $this->processArray($sqlQuery);
        
        return $sqlQuery[0];
    }
    
    public function getEmployeeLoanInstallmentId($id){
    	$sqlQuery = "SELECT id FROM loaninstallment WHERE loanid = \"$id\" ORDER BY month, length(id), id ASC";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	$variable = array();
    	while ($result = mysql_fetch_array($sqlQuery))
    		array_push($variable, $result[0]);
    	return $variable;
    }
    
    public function getTotalLoanAmountSanctioned($loanId){
    	$sqlQuery = "SELECT SUM(amount) FROM loaninstallment WHERE loanid = \"$loanId\" && (flag = \"n\" || flag = \"e\")";
    	$sqlQuery = $this->processArray($sqlQuery);
    	
    	return $sqlQuery[0];
    }
    
    public function getEmployeeLoanInstallmentIdDetails($id){
    	$sqlQuery = "SELECT * FROM loaninstallment WHERE id = \"$id\"";
    	return $this->processArray($sqlQuery);
    }
    
    public function getLoanInstallmentIdAmount($id){
    	$sqlQuery = "SELECT amount FROM loaninstallment WHERE id = \"$id\" ";
    	$sqlQuery = $this->processArray($sqlQuery);
    	
    	return $sqlQuery[0];
    }


    public function isLoanInstallmentBlocked($id, $month){
    	$sqlQuery = "SELECT id FROM stopinstallment WHERE loanid = \"$id\" && month = \"$month\"";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	if(mysql_num_rows($sqlQuery))
    		return true;
    	return false;
    }
    
    public function getEmployeeActiveLoanId($employeeId){
    	if(func_num_args() > 1)
    		if(!func_get_arg(1))
    			$sqlQuery = "SELECT id FROM loanaccount WHERE employeeid = \"$employeeId\" && status = \"\" ORDER BY id ASC";
    		else 
    			$sqlQuery = "SELECT id FROM loanaccount WHERE employeeid = \"$employeeId\" ORDER BY id ASC";
    	else 
    		$sqlQuery = "SELECT id FROM loanaccount WHERE employeeid = \"$employeeId\" && status = \"y\" ORDER BY id ASC ";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	$variable = array();
    	while ($result = mysql_fetch_array($sqlQuery))
    		array_push($variable, $result[0]);
    	if(sizeof($variable, 0))
    		return $variable;
    	return false;
    }
    
    public function processEmployeeLoanInstallment($employeeId, $flag){
    	$loanIds = $this->getEmployeeActiveLoanId($employeeId);
    	if(!$loanIds)
    		return false;
    	if(!$flag){    		
	    	$data = array();
	    	$i = 0;	    	
	    	foreach ($loanIds as $loanId){
	    		if ($this->isLoanInstallmentBlocked($loanId, $this->currentMonth))
	    			continue;
	    		$details = $this->getLoanAccountIdDetails($loanId);
	    		$details = $this->getLoanTypeIdDetails($details[2]);
	    		    		
	    		$data[$i][0] = $details[1];	    		
	    		$data[$i][1] = $this->getInstallmentAmount($loanId);
                $var = $this->getLoanAmountLeft($loanId, $this->currentMonth);
                $data[$i][1] = $data[$i][1] > $var ? $var : $data[$i][1];
	    		
	    		$counter = $this->getCounter('loanInstallment');
	    		$sqlQuery = "INSERT INTO bakloaninstallment (id, loanid, amount, month, flag) VALUES (\"$counter\", \"$loanId\", \"".(0 - $data[$i][1])."\", \"$this->currentMonth\", \"r\")";
	    		$this->processQuery($sqlQuery);	    		
	    		
	    		++$i;
	    	}
	    	return $data;	
    	} 
    	foreach ($loanIds as $loanId) {
    		$sqlQuery = "INSERT INTO loaninstallment (SELECT * FROM bakloaninstallment WHERE loanid = \"$loanId\")";
    		$this->processQuery($sqlQuery);
    		
    		if($this->isLastInstallment($loanId)){ //closing the loan account
    			$sqlQuery = "UPDATE loanaccount SET status = \"\" WHERE id = \"$loanId\" ";
    			$this->processQuery($sqlQuery);
    		}else{
    			$counter = $this->getCounter("salaryLoanHead");
    			$details = $this->getLoanAccountIdDetails($loanId);
   				$amount = $this->getLoanPrincipleAmountLeft($loanId, $this->currentMonth);
    			if ($amount == 0)
    				$amount = $this->getLoanAmountLeft($loanId, $this->currentMonth);
    			$installment = $this->getLoanInstallmentLeft($loanId, $this->currentMonth);
    			
    			$sqlQuery = "INSERT INTO salaryloanhead (id, employeeid, loanid, amount, installment, flag, month) VALUES ( \"$counter\", \"$employeeId\", \"$details[2]\", \"$amount\", \"$installment\", \"l\", \"$this->currentMonth\")";
    			$this->processQuery($sqlQuery);    			
    		}    		    		
    		$sqlQuery = "DELETE FROM bakloaninstallment WHERE loanid = \"$loanId\" ";
    		$this->processQuery($sqlQuery);
    	}
    	return true;   		    	
    }
    
    public function isLastInstallment($loanId){
        $amount = $this->getLoanPrincipleAmountLeft($loanId, $this->currentMonth);               
    	$details = $this->getLoanAccountIdDetails($loanId);
        $totalInstallmentPaid = $this->getLoanTotalPaidInstallment($loanId);
        if($totalInstallmentPaid > $details[4]){ //total installment paid is greater than the said installment
            if($this->getLoanAmountLeft($loanId, $this->currentMonth) <= 0) //the loan balance amount is zero
                return true;
        }elseif($totalInstallmentPaid == $details[4]){ //total installmnet paid is equal to the installment required
            if($this->getLoanPrincipleAmountLeft($loanId, $this->currentMonth) == 0)
                if($details[6] == "0") //there is no interest on this loan, so the loan account is closed
                    return true;            
            $this->processLastInterestOnLoan($loanId);     //the principal loan amount has been cleared. get interest on the loan and finalise the new installment with amount
        }
        return false;        
    }
    
    public function getLoanTotalPaidInstallment($loanId){
        $sqlQuery = "SELECT id FROM loaninstallment WHERE flag = \"r\" && loanid = \"$loanId\" ";
        $sqlQuery = $this->processArray($sqlQuery);
        
        return $sqlQuery[0];
    }
        
    public function changeInstallmentIntoInterest($loanId){
    	$amount = $this->getLoanPrincipleAmountLeft($loanId, $this->currentMonth);
    	if ($amount == 0){
    		$this->getInterestOnLoan($loanId, true);
    		$details = $this->getLoanAccountIdDetails($loanId);
    		$amountLeft = $this->getLoanPrincipleAmountLeft($loanId, $this->currentMonth, true);
    		
    		$installmentAmount = ceil($amountLeft / $details[5]);
    		
    		$sqlQuery = "UPDATE installment SET amount = \"$installmentAmount\" WHERE id = \"$loanId\" ";
    		$this->processQuery($sqlQuery);
    		
    		return true;    		
    	}
    }
    
    public function revertEmployeeLoanInstallment($employeeId){
    	$loanIds = $this->getEmployeeActiveLoanId($employeeId, 'all');
    	foreach ($loanIds as $loanId){
    		//deleting the loaninstallment entries    		
    		$sqlQuery = "DELETE FROM loaninstallment WHERE loanid = \"$loanId\" && month = \"$this->currentMonth\" && (flag = \"r\" || flag = \"i\" )";
    		$sqlQuery = $this->processQuery($sqlQuery);
    		
    		if(mysql_affected_rows($sqlQuery)){
	    		//correcting the loanaccount entry
	    		$sqlQuery = "UPDATE loanaccount SET status = \"y\" WHERE id = \"$loanId\" ";
	    		$this->processQuery($sqlQuery);
	    		
	    		$sqlQuery = "DELETE FROM interest WHERE employeeid = \"$employeeId\" && month = \"$this->currentMonth\" && type = \"l\" ";
	    		$this->processQuery($sqlQuery);
    		}   		
    	}
    	return;
    }
    
    public function getProcessedInstallmentLoanAccountId($month){
    	$sqlQuery = "SELECT id FROM loaninstallment WHERE month = \"$month\" && flag = \"r\" ORDER BY loanid ASC ";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	$variable = array();
    	while ($result = mysql_fetch_array($sqlQuery))
    		array_push($variable, $result[0]);
    	if(sizeof($variable, 0))
    		return $variable;
    	return false;
    }

    public function getSanctionedLoanId($month){
        $sqlQuery = "SELECT loanid FROM loaninstallment WHERE month = \"$month\" && (flag = \"n\" || flag = \"e\")";
        $sqlQuery = $this->processQuery($sqlQuery);
        $variable = array();

        while ($result = mysql_fetch_array($sqlQuery))
    		array_push($variable, $result[0]);
    	if(sizeof($variable, 0))
    		return $variable;
    	return false;
    }

    public function processLastInterestOnLoan($loanId){                     //  the last time insterest calculation to bring new installment of interest
        $details = $this->getLoanAccountIdDetails($loanId);
                
        $sqlQuery = "SELECT month FROM loaninstallment WHERE loanid = \"$loanId\" && flag = \"i\" ";
        $sqlQuery = $this->processArray($sqlQuery);
        $interestMonth = $sqlQuery[0] == "" ? $details[7] : $sqlQuery[0];   //  checking the last date of interest calculation
        
        $count = $sum = 0;
        while(true){
            $month = date('Ym', mktime(0, 0, 0, substr($interestMonth, 4, 2) + $count, 15, substr($interestMonth, 0 , 4)));
            if($month == $this->currentMonth)
                break;
            $sum += $this->getLoanPrincipleAmountLeft($loanId, $month);                        
            ++$count;
        }        
        $interestAmount = round($sum * $details[6] / (100 * 12));           //  calculating the new interest applicable
        
        $sqlQuery = "SELECT SUM(amount) FROM loaninstallment WHERE flag = \"i\" && loanid = \"$loanId\" ";
        $sqlQuery = $this->processArray($sqlQuery);     
        $interestAmount += $sqlQuery[0];                                    //  adding up the existing interest on the loan total interest amount
        
        $newInstallment = ceil($interestAmount / $details[5]);
        
        $sqlQuery = "UPDATE installment SET amount = \"$newInstallment\" WHERE id = \"$loanId\" ";  //  updating the new installment amount created out of the interests
        $this->processQuery($sqlQuery);
        
        $counter = $this->getCounter("loanInstallment");                    //  inserting the interest amount in the installments
        $sqlQuery = "INSERT INTO loaninstallment (id, loanid, amount, month, flag) VALUES (\"$counter\", \"$loanId\", \"$interestAmount\", \"$this->currentMonth\", \"i\")";
        $this->processQuery($sqlQuery);
        
        return true;        
    }
    
    public function getInterestOnLoan($loanId, $flag){
    	$details = $this->getLoanAccountIdDetails($loanId);
        
        $currentSession = $this->getCurrentSession();
        $sessionDetails = $this->getSessionDetails($currentSession);
        $sessionStartMonth = substr($sessionDetails, 0, 4).substr($sessionDetails, 5, 2);
                
        $sqlQuery = "SELECT month FROM loaninstallment WHERE loanid = \"$loanId\" && flag = \"i\" ";
        $sqlQuery = $this->processArray($sqlQuery);
        $interestMonth = $sqlQuery[0] == "" ? $details[7] : $sqlQuery[0];   //  checking the last date of interest calculation
        
        $count = $sum = 0;
        while(true){
            $month = date('Ym', mktime(0, 0, 0, substr($interestMonth, 4, 2) + $count, 15, substr($interestMonth, 0 , 4)));
            if($month == $sessionStartMonth)
                break;
            $sum += $this->getLoanPrincipleAmountLeft($loanId, $month);                        
            ++$count;
        }        
        $interestAmount = round($sum * $details[6] / (100 * 12));           //  calculating the new interest applicable
        
        
        if($flag){
            $counter = $this->getCounter("loanInstallment");                    //  inserting the interest amount in the installments
            $previousMonth = date('Ym', mktime(0, 0, 0, substr($sessionStartMonth, 4, 2) - 1, 15, substr($sessionStartMonth, 0 , 4)));
            $sqlQuery = "INSERT INTO loaninstallment (id, loanid, amount, month, flag) VALUES (\"$counter\", \"$loanId\", \"$interestAmount\", \"$previousMonth\", \"i\")";
            $this->processQuery($sqlQuery);    
        }       
        return $interestMonth;
    }

    public function updateInstallmentInLoan($employeeId){
        echo $employeeId;
        $loanIds = $this->getEmployeeActiveLoanId($employeeId);
    	if(!$loanIds)
    		return false;
    	foreach ($loanIds as $loanId) {
    			$counter = $this->getCounter("salaryLoanHead");
    			$details = $this->getLoanAccountIdDetails($loanId);
   				$amount = $this->getLoanPrincipleAmountLeft($loanId, $this->currentMonth);
    			if ($amount == 0)
    				$amount = $this->getLoanAmountLeft($loanId, $this->currentMonth);
    			$installment = $this->getLoanInstallmentLeft($loanId, $this->currentMonth);

    			$sqlQuery = "INSERT INTO salaryloanhead (id, employeeid, loanid, amount, installment, flag, month) VALUES ( \"$counter\", \"$employeeId\", \"$details[2]\", \"$amount\", \"$installment\", \"l\", \"$this->currentMonth\")";
    			$this->processQuery($sqlQuery);    			

    	}
    	return true;
    }
    
    public function getEmployeeNewSanctionedLoanInstallmentIds($month){
    	$sqlQuery = "SELECT * FROM loaninstallment WHERE  month = \"$month\" && (flag = \"n\" || flag = \"e\")";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	$variable = array();
        while ($result = mysql_fetch_array($sqlQuery))
    		array_push($variable, $result[0]);
    	if(sizeof($variable, 0))
    		return $variable;
    	return false;
    }   
    
    
}
?>
