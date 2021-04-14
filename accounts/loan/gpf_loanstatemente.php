<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
  	//error_reporting(0);

    session_start();
    require_once '../include/class.loginInfo.php';
    require_once '../include/class.gpftotal.php';
    require_once '../include/class.excel.php';
    require_once '../include/class.personalInfo.php';
	
    $loggedInfo = new loginInfo();
    $gpfTotal = new gpfTotal();
    $excel = new xlsStream();
    $personalInfo = new personalInfo();
    
    
	if(!$loggedInfo->checkLoanOfficerLogged())
        $loggedInfo->redirect('../');
        
	if(isset($_POST['loanid']))
		$loanId = $_POST['loanid'];
				
	$completeGpfIds = $gpfTotal->getGpfLoanInstallmentIds($loanId);
	if(!$completeGpfIds)
		$loggedInfo->palert("There Is No Record Availiable For The Given Employee. Please Select Another Employee", './gpf_statement.php');    

	
	//writing down the header inputs
	$data[0][0] = 'S.N.';
	$data[0][1] = 'REMARKS';
	$data[0][2] = 'MONTH';
	$data[0][3] = 'CREDIT';
	$data[0][4] = 'DEBIT';
	$data[0][5] = 'BALANCE'; 
                        	
	$count = 1;
	$sumTotal = 0; //for the complete sum
	$sumDebit = 0; //for the total Debit Amount
	$sumCredit = 0; //for the total Credit Amount
	
	foreach ($completeGpfIds as $individualGpfId) {		
		$details = $gpfTotal->getGpfLoanInstallmentIdDetails($individualGpfId, true);
		
		$debit = $details[2] < 0 ? abs($details[2]) : 0;
		$credit = $details[2] > 0 ? abs($details[2]) : 0;
		
		$sumDebit += $debit;
		$sumCredit += $credit;
		$sumTotal += -$debit + $credit;

		$data[$count] = array();
		array_push($data[$count], $count);
		array_push($data[$count], $details[4]);
		array_push($data[$count], ($gpfTotal->getNumber2Month(substr($details[3], 4, 2)).", ".substr($details[3], 0, 4)));
		array_push($data[$count], number_format($credit, 2, '.', ''));
		array_push($data[$count], number_format($debit, 2, '.', ''));
		array_push($data[$count], number_format($sumTotal, 2, '.', ''));
		
		++$count;
	}
		$data[$count] = array();
		array_push($data[$count], '');
		array_push($data[$count], 'TOTAL SUM');
		array_push($data[$count], '');
		array_push($data[$count], number_format($sumCredit, 2, '.', ''));
		array_push($data[$count], number_format($sumDebit, 2, '.', ''));
		array_push($data[$count], number_format($sumTotal, 2, '.', ''));
	
	$details = $gpfTotal->getGpfIdDetails($loanId, true);
	$personalInfo->getEmployeeInformation($details[1], true);	
	$filename = "GPF_Statement_".$personalInfo->getEmployeeCode()."";	
	$excel->makeExcel($data, $filename);
    ob_end_flush();
?>

