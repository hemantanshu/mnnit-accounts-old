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
        
	if(isset($_GET['id']))
		$employeeId = $_GET['id'];
	else
		$loggedInfo->redirect('./');
	
	$completeGpfIds = $gpfTotal->getEmployeeGpfIds($employeeId, true);
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
		$details = $gpfTotal->getGpfIdDetails($individualGpfId, true);
		
		$debit = $details[2] < 0 ? abs($details[2]) : '';
		$credit = $details[2] > 0 ? abs($details[2]) : '';
		
		$sumDebit += $debit;
		$sumCredit += $credit;
		$sumTotal += -$debit + $credit;

		$data[$count] = array();
		array_push($data[$count], $count);
		array_push($data[$count], $details[4]);
		array_push($data[$count], ($gpfTotal->getNumber2Month(substr($details[3], 4, 2)).", ".substr($details[3], 0, 4)));
		array_push($data[$count], $credit);
		array_push($data[$count], $debit);
		array_push($data[$count], $sumTotal);
		
		++$count;
	}
		$data[$count] = array();
		array_push($data[$count], '');
		array_push($data[$count], 'TOTAL SUM');
		array_push($data[$count], '');
		array_push($data[$count], $sumCredit);
		array_push($data[$count], $sumDebit);
		array_push($data[$count], $sumTotal);
	
	$personalInfo->getEmployeeInformation($employeeId, true);	
	$filename = "GPF_Statement_".$personalInfo->getEmployeeCode()."";	
	$excel->makeExcel($data, $filename);
    ob_end_flush();
?>

