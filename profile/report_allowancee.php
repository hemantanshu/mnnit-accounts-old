<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
////error_reporting(0);

require_once '../include/class.personalInfo.php';
require_once '../include/class.allowance.php';
require_once '../include/class.reporting.php';
require_once '../include/class.excel.php';


$allowance = new allowance();
if(!$allowance->checkLogged())
  	$allowance->redirect('../');

if(isset($_POST) && count($_POST) > 0){
	$allowanceId = $_POST['allowance'];
	$eDate = $_POST['edate'];
	$sDate = $_POST['sdate'];
	$processingType = $_POST['type'];
	$processingValue= $_POST['value'];
	
	if($_POST['submit'] == "Print Allowance Report")
		$allowance->redirect("./report_allowancep.php?sdate=$sDate&edate=$eDate&id=$allowanceId&type=$processingValue&value=$processingValue");
}
$reporting = new reporting();
$personalInfo = new personalInfo();	
$excel = new xlsStream();

$i = 0;
$validMonths = array();
for ($i= $sDate; $i <= $eDate; ++$i){
	if ($reporting->isSalaryDataAvailiable($i))
		array_push($validMonths, $i);
}

$data = array();
//writing the header file
$data[0] = array();

array_push($data[0], "S.N");
array_push($data[0], "Employee Code");
array_push($data[0], "Name");
foreach ($validMonths as $count)
	array_push($data[0], $count);


while(true){
	$employeeName = "employee".$i;
	++$i;
	$data[$i] = array();
	if(!isset($_POST[$employeeName]))
		break;
	
	$employeeId = $_POST[$employeeName];
	
	$personalInfo->getEmployeeInformation($employeeId, true);
	array_push($data[$i], $i);
	array_push($data[$i], $personalInfo->getEmployeeCode());
	array_push($data[$i], $personalInfo->getName());
		
	foreach ($validMonths as $count)
		array_push($data[$i], $reporting->getSalaryAllowanceInfo($employeeId, $count, $allowanceId, true));
}
$allowanceName = $allowanceId == "total" ? "NET SALARY PAID" : ($allowanceId == "gross" ? "GROSS SALARY" : ($allowanceId == "deduction" ? "TOTAL DEDUCTIONS" : $allowance->getAllowanceTypeName($allowanceId))); 
$fileName = "allowanceReport".$allowanceName;
$excel->makeExcel($data, $fileName);

ob_end_flush();
?>