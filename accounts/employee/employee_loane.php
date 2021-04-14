<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
	//error_reporting(0);
    session_start();
    
    require_once '../include/class.loan.php';
    require_once '../include/class.excel.php';
    
    
    $loan = new loan();
    $excel = new xlsStream();
    
    $employeeId = $loan->checkEmployeeLogged();
	if(!$employeeId)
        $loan->redirect('../');
        
    if(!isset($_POST['id']))
    	$loan->redirect('./');
    
   	$loanAccountId = $_POST['id'];
   	
   	$loanAccountDetails = $loan->getLoanAccountIdDetails($loanAccountId);
   	if($loanAccountDetails[0] == "")
   		$loan->redirect('./');
   	
   	if($loanAccountDetails[1] != $employeeId)
   		$loan->redirect('./');
   		
   	$data = array();
   	$data[0] = array();
   	
   	array_push($data[0], "S.N");
   	array_push($data[0], "Comments");
   	array_push($data[0], "Month");
   	array_push($data[0], "Credit");
   	array_push($data[0], "Debit");
   	array_push($data[0], "Balance");
   	
   	$i = 1;
   	
   	$completeInstallmentId = $loan->getEmployeeLoanInstallmentId($loanAccountId);
	$sumTotal = $sumCredit = $sumDebit = 0;
	
	foreach ($completeInstallmentId as $installmentId){
		$details = $loan->getEmployeeLoanInstallmentIdDetails($installmentId);
		$credit = $details[2] > 0 ? $details[2] : 0;
		$debit = $details[2] < 0 ? abs($details[2]) : 0;
		
		$sumCredit += $credit;
		$sumDebit += $debit;
		$sumTotal += $credit - $debit;
		
		$data[$i] = array();
		
		array_push($data[$i], $i);
		array_push($data[$i], strtoupper($details[4]));
		array_push($data[$i], $loan->nameMonth($details[3]));
		array_push($data[$i], number_format($credit, 2, '.', ''));
		array_push($data[$i], number_format($debit, 2, '.', ''));
		array_push($data[$i], number_format($sumTotal, 2, '.', ''));
		
		++$i;
	}
	$data[$i] = array();
		
	array_push($data[$i], $i);
	array_push($data[$i], "TOTAL SUMMARY");
	array_push($data[$i], '');
	array_push($data[$i], number_format($sumCredit, 2, '.', ''));
	array_push($data[$i], number_format($sumDebit, 2, '.', ''));
	array_push($data[$i], number_format($sumTotal, 2, '.', ''));  

	$filename = "loanstatement";
	$excel->makeExcel($data, $filename);
   	
   	ob_end_flush();
?>