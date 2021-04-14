<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();

    session_start();

    require_once '../include/class.employeeType.php';
    require_once '../include/class.department.php';
    require_once '../include/class.excel.php';
    require_once '../include/class.reporting.php';
    require_once '../include/class.accountHead.php';
    require_once '../include/class.allowance.php';
    

    $personalInfo = new personalInfo();
    $department = new department();
    $employeeType = new employeeType();
    $excel = new xlsStream();
	$accountHead = new accountHead();
	$reporting  = new reporting();
	$allowance = new allowance();
	
    if(!$reporting->checkLogged())
        $reporting->redirect('../');
    
    if(isset ($_GET)){
    	$date = $_GET['date'];
    	        
        $i = 0;
        $completeAccountHead = $reporting->getCompleteAccountHead($date);        
        $variable[0][0] = 'S.N';
        $variable[0][1] = 'EMPLOYEE CODE';
        $i = 2;
        foreach ($completeAccountHead as $individualAccountHead) {
        	$variable[0][$i] = $accountHead->getAccountHeadName($individualAccountHead);
        	++$i;
        }
        $variable[0][$i] = 'TOTAL';        

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
