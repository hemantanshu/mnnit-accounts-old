<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
    session_start();

    require_once '../include/class.accountInfo.php';
    $accounts = new accounts();
    
    if(!$accounts->checkLogged())
        $accounts->redirect('../');

    if(isset ($_GET['id']))
        $allowanceId = $_GET['id'];
    else
        $accounts->redirect ('./');

    
    
    $accounts->updateMasterSalaryAllowanceData($allowanceId);
    $accounts->palert("The Allowance Info Has Been Successfully Updated", $accounts->getUrlOfRedirect("ACT00"));

    ob_end_flush();
?>