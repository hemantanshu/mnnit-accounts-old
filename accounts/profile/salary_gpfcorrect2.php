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
    $allowance->palert ("Galat Kaam Mat Karo. Sahi jagah entry karo", "./salary_gpfcorrect1.php?employeeId=$employeeId&date=$date");

if(isset ($_POST) && $_POST['submit'] == "Update The Details"){
    if (isset($_POST['gpf'])){
    	$amount = $_POST["gpfAmount"];
    	$accounts->updateGpfAmount($employeeId, $date, true, abs($amount));
    }
    if (isset($_POST['gpfAdvance'])){
    	$amount = $_POST["gpfAmountAdvance"];
    	$accounts->updateGpfAmount($employeeId, $date, false, abs($amount));
    }    
    $allowance->redirect("./salary_gpfcorrect1.php?employeeId=$employeeId&date=$date");
}
if(isset ($_POST) && $_POST['submit'] == "Insert New GPF Entry"){
    $amount = abs($_POST['amount']);   
    $type = $_POST['insertType'];
    $accounts->insertGPFRow($employeeId, $date, $type, $amount);        
    $allowance->redirect("./salary_gpfcorrect1.php?employeeId=$employeeId&date=$date");
}

if (isset($_GET['sid'])){
    $salaryId = $_GET['sid'];
    $employeeId = $_GET['employeeId'];
    $date = $_GET['date'];
    $accounts->deleteSalaryGpfEntry($salaryId);
    $allowance->redirect("./salary_gpfcorrect1.php?employeeId=$employeeId&date=$date");
}
if (isset($_GET['lid'])){
    $salaryId = $_GET['lid'];
    $employeeId = $_GET['employeeId'];
    $date = $_GET['date'];
    $accounts->deleteSalaryGpfEntry($salaryId, false);
    $allowance->redirect("./salary_gpfcorrect1.php?employeeId=$employeeId&date=$date");
}



ob_end_flush();
?>
