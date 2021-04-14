<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
	//this script has to be used with the excel where the candidate information 
	//is also on display and takes the first post argument as the employeeid
    ob_start();
	////error_reporting(0);
    session_start();
    
    require_once '../include/class.loginInfo.php';
    require_once '../include/class.excel.php';
    
    $loggedInfo = new loginInfo();

    if(!$loggedInfo->checkLogged())
        $loggedInfo->redirect('../');
    if ($_POST['submit'] != "Export The Data Into Excel")
    	$loggedInfo->redirect("./");
        
    
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
    		array_push($data[$i], $_POST[$postData]);   		
    	}
    	++$i;	    	    		
    }
    $xlsStream = new xlsStream();
    $xlsStream->makeExcel($data, $fileName);    
    ob_end_flush();
?>
