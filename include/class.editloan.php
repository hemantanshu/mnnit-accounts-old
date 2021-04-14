<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
//error_reporting(0);    
require_once 'class.loan.php';

class editLoan extends loan {
	
    public function  __construct() {
        parent::__construct();
    }
    
    public function checkLoanName($name){
    	$variable = $this->getValue('id', 'loantype', 'name', $name);
    	if($variable == "")
    		return false;
    	return true;
    }
    
    public function checkLoanSanction($employeeId, $loanType){
    	$sqlQuery = "SELECT id FROM loanaccount WHERE employeeid = \"$employeeId\" && loanid = \"$loanType\" && status = \"y\" ";
    	$sqlQuery = $this->processQuery($sqlQuery);
    	
    	if(mysql_num_rows($sqlQuery))
    		return false;
    	return true;
    }
    
    public function sanctionLoan($employeeId, $loanType, $amount, $installment, $installmenti, $interest){
    	$counter = $this->getCounter('loanAccount');
    	$sqlQuery = "INSERT INTO loanaccount (id, employeeid, loanid, amount, installment, installmenti, interest, month, status) VALUES (\"$counter\", \"$employeeId\", \"$loanType\", \"$amount\", \"$installment\", \"$installmenti\", \"$interest\", \"".$this->currentMonth."\", \"y\") ";
    	$this->processQuery($sqlQuery);
    	
    	$variable = (int) $amount / $installment;   	
    	
    	$sqlQuery = "INSERT INTO installment (id, amount) VALUES (\"$counter\", \"$variable\") ";
    	$this->processQuery($sqlQuery);
    	
    	$variable = $this->getCounter('loanInstallment');    	
    	$sqlQuery = "INSERT INTO loaninstallment (id, loanid, amount, month, flag) VALUES (\"$variable\", \"$counter\", \"$amount\", \"$this->currentMonth\", \"n\" )";    	
    	$this->processQuery($sqlQuery);
    	
    	$pendingId = $this->setPendingWork($counter);
    	$this->insertProcess($pendingId, "New Loan Sanctioned");
    	    	
    	return $counter;
    }
    
    public function deleteLastTransaction($loanId){
    	$completeInstallmentId = $this->getEmployeeLoanInstallmentId($loanId);
    	foreach ($completeInstallmentId as $installmentId)
    		$lastInstallment = $installmentId;
    	$details = $this->getEmployeeLoanInstallmentIdDetails($lastInstallment);
    	
    	if ($details[5] != 'i' && $details[5] != 'r'){
    		$sqlQuery = "DELETE FROM loaninstallment WHERE id = \"$details[0]\"";
    		$this->processQuery($sqlQuery);
    		
    		$completeInstallmentId = $this->getEmployeeLoanInstallmentId($loanId);
    		if (sizeof($completeInstallmentId, 0))
    			return true;
    		else{
    			$sqlQuery = "DELETE FROM loanaccount WHERE id = \"$loanId\"";
    			$this->processQuery($sqlQuery);
    			
    			$sqlQuery = "DELETE FROM installment WHERE id = \"$loanId\" ";
    			$this->processQuery($sqlQuery);
    		}
    	}else
    		$this->palert("This last transaction cannot be deleted", "./loan_account.php?id=$loanId");
    }
    
    public function extendLoan($loanId, $amount, $type, $remarks){
    	$flagCounter = $this->getCounter('loanFlag');
        $sqlQuery = "INSERT INTO flag (flag, value) VALUES (\"$flagCounter\", \"$remarks\")";
        
        $counter = $this->getCounter('loanInstallment');
        
        
    	if ($type == "c")
    		$sqlQuery = "INSERT INTO loaninstallment (id, loanid, amount, month, flag) VALUES (\"$counter\", \"$loanId\", \"$amount\", \"$this->currentMonth\", \"$flagCounter\" )";
    	else 
    		$sqlQuery = "INSERT INTO loaninstallment (id, loanid, amount, month, flag) VALUES (\"$counter\", \"$loanId\", \"".(0 - $amount)."\", \"$this->currentMonth\", \"$flagCounter\" )";
    	$this->processQuery($sqlQuery);	
    	
    	$this->setInstallmentAmount($loanId);
    	$this->insertProcess($loanId, "Direct insertion of amount in the loan account");
    	
    }
    
    public function setInstallmentAmount($loanId){
    	
    	$leftAmount = $this->getLoanPrincipleAmountLeft($loanId, $this->currentMonth);
    	$installment = $this->getLoanInstallmentLeft($loanId, $this->currentMonth);
    	
    	$installmentAmount = ceil($leftAmount / $installment);
    	
    	$sqlQuery = "UPDATE installment SET amount = \"$installmentAmount\" WHERE id = \"$loanId\" ";
    	$this->processQuery($sqlQuery);
    	
    	return true;
    }
    
    public function setNewLoanType($name, $allowanceId, $maxAmount, $maxInstallment){
    	$counter = $this->getCounter('loanType');
    	
    	$sqlQuery = "INSERT INTO loantype (id, allowanceid, name, maxamount, maxinstallment, status) VALUES (\"$counter\", \"$allowanceId\", \"$name\", \"$maxAmount\", \"$maxInstallment\", \"y\")";
    	$this->processQuery($sqlQuery);
    	
    	$this->setPendingWork($counter);
    }
    
    public function setLoanInstallment($id, $amount){
    	$amountLeft = $this->getLoanPrincipleAmountLeft($id, $this->currentMonth);
    	$installment = ceil($amountLeft / $amount);
    	if ($installment > 60)
    		return false;
    		
    	$sqlQuery = "UPDATE installment SET amount = \"$amount\" WHERE id = \"$id\" ";
    	$this->processQuery($sqlQuery);
    	
    	$sqlQuery = "UPDATE loanaccount SET installment = \"$installment\" WHERE id = \"$id\" ";    	
    	$this->processQuery($sqlQuery);
    	    	    	
    	$pendingId = $this->setPendingWork($id);
    	$this->insertProcess($pendingId, "Installment Amount Has Been Changed to $amount");
    	
    	return true;
    }
    
    public function blockInstallmentAmount($id, $month){
    	$sqlQuery = "DELETE FROM stopinstallment WHERE loanid = \"$id\" && month > \"$month\" ";
    	$this->processQuery($sqlQuery);
    	
    	for($count = $this->currentMonth; $count <= $month; ++$count){
    		if($this->isLoanInstallmentBlocked($id, $count))
    			continue;
    		$counter = $this->getCounter('stopLoanInstallment');
    		$sqlQuery = "INSERT INTO stopinstallment (id, loanid, month) VALUES (\"$counter\", \"$id\", \"$count\")";
    		$this->processQuery($sqlQuery);
    	}
    }
    
   
    
    public function entryOldLoan($id, $amount, $date, $comment, $interst){
    	$counter = $this->getCounter('loanInstallment');
    	
    	$sqlQuery = "INSERT INTO loaninstallment (id, loanid, amount, month, flag) VALUES (\"$counter\", \"$id\", \"$amount\", \"$date\", \"$interst\")";
    	$this->processQuery($sqlQuery);
    	return;
    }
       
       
}
?>
