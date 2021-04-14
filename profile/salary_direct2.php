<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);

require_once '../include/class.allowance.php';
require_once '../include/class.reporting.php';


$allowance = new allowance();
if(!$allowance->checkLogged())
        $allowance->redirect('../');

$accounts = new reporting();

$employeeId = $_POST['employeeId'];
$date = $_POST['date'];

if($date >= 201101)
    $allowance->palert ("Galat Kaam Mat Karo. Sahi jagah entry karo", "./salary_direct1.php?employeeId=$employeeId&date=$date");

if(isset ($_POST) && $_POST['submit'] == "Update The Salary Details"){
    $i = 0;
    while (true){
        $amountName = "amount".$i;
        $optionName = "option".$i;
        $idName = "id".$i;
        ++$i;
        if(!isset ($_POST[$idName]))
            break;

        $accounts->updateSalaryInformation($_POST[$idName], $_POST[$amountName], $_POST[$optionName]);
    }
    $allowance->redirect("./salary_direct1.php?employeeId=$employeeId&date=$date");
}
if(isset ($_POST) && $_POST['submit'] == "Insert New Allowance Entry"){
    $salaryId = $_POST['id0'];
    $allowanceId = $_POST['allowance'];
    $amount = abs($_POST['amount']);
    $type = $_POST['type'];
    $accountHeadId = $allowance->getAllowanceAccountHead($allowanceId);    
    
    $accounts->insertNewRow($salaryId, $allowanceId, $accountHeadId, $amount, $type);
        
    $allowance->redirect("./salary_direct1.php?employeeId=$employeeId&date=$date");
}

if (isset($_GET['sid'])){
	$salaryId = $_GET['sid'];
	$employeeId = $_GET['employeeId'];
	$date = $_GET['date'];
	$accounts->deleteSalaryRow($salaryId);
	$allowance->redirect("./salary_direct1.php?employeeId=$employeeId&date=$date");
}

ob_end_flush();
?>
