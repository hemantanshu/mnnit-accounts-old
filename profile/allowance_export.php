<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();

    session_start();

    require_once '../include/class.allowance.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.employeeType.php';
    require_once '../include/class.department.php';
    require_once '../include/class.excel.php';


    $allowance = new allowance();
    $personalInfo = new personalInfo();
    $department = new department();
    $employeeType = new employeeType();
    $excel = new xlsStream();

    if(!$allowance->checkLogged())
        $allowance->redirect('../');
    
    if(isset ($_POST) && $_POST['submit'] == "Export Allowance To Excel"){
        $allowanceId = $_POST['accountHead'];
        $details = $allowance->getSalaryAllowanceEmployeeInfo($allowanceId, 'all');

        $i = 0;
        $variable[0][0] = 'S.N';
        $variable[0][1] = 'EMPLOYEE CODE';
        $variable[0][2] = 'NAME';
        $variable[0][3] = 'TYPE';
        $variable[0][4] = 'DEPARTMENT';
        $variable[0][5] = 'AMOUNT';

        $sum = 0;

        while(true){
            if(!sizeof($details[$i]))
                break;
            $personalInfo->getEmployeeInformation($details[$i][0], true);
            $j = $i + 1;
            $variable[$j][0] = $j;
            $variable[$j][1] = $personalInfo->getEmployeeCode();
            $variable[$j][2] = $personalInfo->getName();
            $variable[$j][3] = $employeeType->getEmployeeTypeName($personalInfo->getEmployeeType());
            $variable[$j][4] = $department->getDepartmentName($personalInfo->getDepartment());
            $variable[$j][5] = $details[$i][2] == 'Credit' ? $details[$i][1] : (0 - $details[$i][1]);

            $i++;
            $sum += $variable[$j][5];
        }
        ++$i;
        $variable[$i][0] = '';
        $variable[$i][1] = 'TOTAL';
        $variable[$i][2] = '';
        $variable[$i][3] = '';
        $variable[$i][4] = '';
        $variable[$i][5] = $sum;

        $filename = $allowance->getAllowanceTypeName($allowanceId)."_details";

        $excel->makeExcel($variable, $filename);        
    }else{
        $allowance->redirect('./allowance.php');
    }

    ob_end_flush();
?>
