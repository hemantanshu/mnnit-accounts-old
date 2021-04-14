<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    require_once '../include/class.loggedInfo.php';
    $loggedInfo = new loggedIn();

    if(!$loggedInfo->checkLogged())
            $loggedInfo->redirect('../');


    if(!isset ($_GET['id']))
        $loggedInfo->redirect('./employee.php');
    
    $employeeId = $_GET['id'];
    $type = $_GET['type'];

    if($type == "allowances")
        $loggedInfo->redirect('./employee_edita.php?id='.$employeeId);
    elseif($type == "accounts")
        $loggedInfo->redirect('./employee_editb.php?id='.$employeeId);
    elseif($type == "designation")
        $loggedInfo->redirect('./employee_editd.php?id='.$employeeId);
    else
        $loggedInfo->redirect('./employee_editp.php?id='.$employeeId);

    exit(0);
?>
