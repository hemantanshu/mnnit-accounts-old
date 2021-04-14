<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
////error_reporting(0);

require_once '../include/class.personalInfo.php';
require_once '../include/class.accountHead.php';
require_once '../include/class.reporting.php';
require_once '../include/class.excel.php';

$allowance = new accountHead();
$employeeId = $allowance->checkEmployeeLogged();
if(!$employeeId)
  	$allowance->redirect('../');

$month = $_POST['month'];	

$reporting = new reporting();
$personalInfo = new personalInfo();	
$excel = new xlsStream();

$sDate = $_POST['sdate'];
$eDate = $_POST['edate'];

$i = 0;
$validMonths = array();
for ($i= $sDate; $i <= $eDate; ++$i){
	if ($reporting->isSalaryDataAvailiable($i))
		array_push($validMonths, $i);
}
	
$i = 0;
$completeAllowanceId = array();
while (true){	
	$variable = "allowance".$i;
	++$i;
	if(!isset($_POST[$variable]))
		break;
	array_push($completeAllowanceId, $_POST[$variable]);
}	
unset($variable);

$data = array();
//managing the header of the array
$data[0] = array();
	array_push($data[0], "S.N.");
	array_push($data[0], "MONTH");
	
foreach ($completeAllowanceId as $allowanceId){
	if($allowanceId == 'total')
		array_push($data[0], "NET SALARY PAID");
	elseif ($allowanceId == 'gross')
		array_push($data[0], "GROSS SALARY PAID");
	elseif ($allowanceId == 'deduction')
		array_push($data[0], "TOTAL DEDUCTIONS");
	else 
		array_push($data[0], $allowance->getAccountHeadName($allowanceId));	
}
//finished arranging the header of the array
$i = 0;
foreach ($validMonths as $month){
	++$i;
	$data[$i] = array();
	$personalInfo->getEmployeeInformation($employeeId, true);
	
	array_push($data[$i], $i);
	array_push($data[$i], $allowance->nameMonth($month));

	foreach ($completeAllowanceId as $allowanceId)
		array_push($data[$i], number_format($reporting->getSalaryAllowanceInfo($employeeId, $month, $allowanceId, false), 2, '.', ''));
}
$fileName = "allowanceReport".$personalInfo->getEmployeeCode();
$excel->makeExcel($data, $fileName);
ob_end_flush();
?>