<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
////error_reporting(0);

require_once '../include/class.personalInfo.php';
require_once '../include/class.allowance.php';
require_once '../include/class.reporting.php';
require_once '../include/class.employeeInfo.php';
require_once '../include/class.department.php';
require_once '../include/class.employeeType.php';
require_once '../include/class.accountHead.php';
require_once '../include/class.excel.php';


$allowance = new allowance();
if(!$allowance->checkLogged())
  	$allowance->redirect('../');

if(!isset($_GET['id']) || !isset($_GET['date']))
	$allowance->redirect('./');
	  	
$month = $_GET['date'];
$id = $_GET['id'];	

$reporting = new reporting();
$personalInfo = new personalInfo();	
$employeeInfo = new employeeInfo();
$department = new department();
$excel = new xlsStream();
$accountHead = new accountHead();
$employeeType = new employeeType();
	
$completeEmployeeId = $employeeInfo->getEmployeeIds(true, 'all');

$flag = true;
$data = array();
$data[0] = array();

array_push($data[0], "SN");
array_push($data[0], "EMPLOYEE CODE");
array_push($data[0], "NAME");
array_push($data[0], "DEPARTMENT");
array_push($data[0], "EMPLOYEE TYPE");
array_push($data[0], "AMOUNT");

if (substr($id, 0, 3) == "ACH"){
	$flag = false;	
}
$i = 0;
foreach ($completeEmployeeId as $employeeType){
	$amount = $reporting->getSalaryAllowanceInfo($employeeId, $month, $id, $flag);
	if ($amount == 0)
		continue;
	$data[$i] = array();	
	array_push($data[$i], $i);
	array_push($data[$i], $personalInfo->getEmployeeCode());
	array_push($data[$i], $personalInfo->getName());
	array_push($data[$i], $department->getDepartmentName($personalInfo->getDepartment()));
	array_push($data[$i], $employeeType->getEmployeeTypeName($personalInfo->getEmployeeType()));
	array_push($data[$i], number_format($amount), 2, '.', '');
	++$i;		
}

$name = "report".$id;
$excel->makeExcel($data, $name);
ob_end_flush();
?>
