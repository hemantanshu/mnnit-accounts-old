<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
   //error_reporting(0);
    
    require_once '../include/class.accountInfo.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.employeeInfo.php';
    require_once '../include/class.employeePending.php';


    $accounts = new accounts();
    if(!$accounts->checkLogged())
        $accounts->redirect('../');

    if(isset ($_GET['type']))
        $type = $_GET['type'];
    else
        $accounts->redirect('./');

    $personalInfo = new personalInfo();
    $employeeInfo = new employeeInfo();
    $ePending = new employeePending();

    $variable = $employeeInfo->getEmployeeIds(true);
    $employeeId = array();

    if($type == "all"){
        foreach ($variable as $value){
            array_push($employeeId, $value);
        }
    }

    if($type == "done"){
        foreach ($variable as $value){
            if($ePending->isEmployeeSalaryProcessed($value))
                    array_push($employeeId, $value);
        }
    }
    if($type == "pending"){
        foreach ($variable as $value){
            if($ePending->isEmployeeSalaryInPendingStatus($value))
                    array_push($employeeId, $value);
        }
    }
    if($type == "notdone"){
        foreach ($variable as $value){
            if(!$ePending->isEmployeeSalaryProcessed($value) && !$ePending->isEmployeeSalaryInPendingStatus($value))
                    array_push($employeeId, $value);
        }
    }
    if(!sizeof($employeeId)){
        echo "<center><font class=\"error\">No Records Under This Category</font></center>";
        exit (0);
    }

    echo "
        <table align=\"center\" width=\"100%\" border=\"0\">
            <tr>
                <th width=\"5%\" >SN</th>
                <th width=\"15%\" align=\"left\">Employee Code</th>
                <th width=\"25%\" align=\"left\">Name</th>
                <th width=\"25%\" align=\"left\">Amount</th>
            </tr>
            <tr>
                <td colspan=\"4\"><hr size=\"3\" /></td>
            </tr>
        ";
    $i = 0;
    foreach ($employeeId as $value) {
        ++$i;
        $personalInfo->getEmployeeInformation($value, true);
        echo "
            <tr>
                <td align=\"center\"><font class=\"green\">".$i."</font></td>
                <td align=\"left\"><font class=\"green\">".$personalInfo->getEmployeeCode()."</font></td>
                <td align=\"left\"><font class=\"green\">".$personalInfo->getName()."</font></td>
                <td align=\"left\"><font class=\"error\">INR. ".$accounts->getTotalSalary($value)."</font></td>
            </tr>
            <tr>
                <td colspan=\"4\"><hr size=\"1\" /></td>
            </tr>";
    }
    echo "</table>"
?>
