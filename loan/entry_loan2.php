<?php 
	require_once '../include/class.editloan.php';
	
	$loan = new editLoan();
	
	if(!isset($_POST['submit']))
		$loan->redirect('./');
	
	$amount = 0 - $_POST['amount'];
	$date = $_POST['date'];
	$comment = "LOAN RECOVERY FOR THE MONTH";
	$checkbox = '';
	if($_POST['checkbox'] == 'y'){
		$amount = $_POST['amount'];
		$checkbox = 'y';
		$comment = "INTEREST LEIVED ON LOAN";
	}
	
	$loanId = $_POST['loanid'];
		
	$loan->entryOldLoan($loanId, $amount, $date, $comment, $checkbox);
	$loan->palert("The Amount Has Been Successfully Submitted", "./entry_loan1.php?id=$loanId");

?>