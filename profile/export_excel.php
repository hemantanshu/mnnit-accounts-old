<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
	//this script has to be used with the excel where the candidate information 
	//is also on display and takes the first post argument as the employeeid
    ob_start();
	////error_reporting(0);
    session_start();
    
    require_once '../include/class.loginInfo.php';
    require_once '../include/class.excel.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.department.php';
    require_once '../include/class.employeeType.php';
    
    $loggedInfo = new loginInfo();

    if(!$loggedInfo->checkLogged())
        $loggedInfo->redirect('../');
    if ($_POST['submit'] != "Export The Data Into Excel")
    	$loggedInfo->redirect("./");
        
    $personalInfo = new personalInfo();
    $department = new department();
    $employeeType = new employeeType();
    
    $totalCols = $_POST['totalCols'];
    $fileName = $_POST['fileName'];
    
    $data = array();
    $i = 0;
    while (true){   
    	$data[$i] = array();
    	$postData = "postData".$i."0";
    		if (!isset($_POST[$postData]))
    			break;	
    			
    	for ($j = 0; $j < $totalCols; ++$j){  	
    		$postData = "postData".$i.$j;		
    		if ($i == 0 && $j == 0){
    			array_push($data[$i], "Employee Code");
    			array_push($data[$i], "Name");
    			array_push($data[$i], "Department");
    			array_push($data[$i], "Employee Type");
    		}
    		elseif ($i != 0 && $j == 0){
    			$employeeId = $_POST[$postData];
    			$personalInfo->getEmployeeInformation($employeeId, true);
    			
    			array_push($data[$i], $personalInfo->getEmployeeCode());
    			array_push($data[$i], $personalInfo->getName());
    			array_push($data[$i], $department->getDepartmentName($personalInfo->getDepartment()));
    			array_push($data[$i], $employeeType->getEmployeeTypeName($personalInfo->getEmployeeType()));    				
    		}else {
    			array_push($data[$i], $_POST[$postData]);
    		}
    	}
    	++$i;	    	    		
    }
    $xlsStream = new xlsStream();
    $xlsStream->makeExcel($data, $fileName);    
    ob_end_flush();
?>
