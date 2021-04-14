<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
    //error_reporting(0);

    session_start();

    require_once '../include/class.reporting.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.employeeType.php';
    require_once '../include/class.department.php';
    require_once '../include/class.bank.php';
    require_once '../include/class.accountHead.php';
    require_once '../include/class.employeeInfo.php';
    require_once '../include/class.designation.php';

    
    

    $reporting = new reporting();
    if(!$reporting->checkLogged())
    	$reporting->redirect('../');
    	
    if(!isset($_GET))
    	$reporting->redirect('./');
    else{
    	$departmentType = $_GET['department'];
    	$employeeType = $_GET['type'];
    	$date = $_GET['date'];
    }
    
    $currentMonth = substr($date, 4, 2);
   	$currentYear = substr($date, 0, 4);   

   	$personalInfo = new personalInfo();
   	$employee = new employeeType();
   	$department = new department();
   	$bank = new bank();
   	$accountHead = new accountHead();
        $designation = new designation();
        $employeeInfo = new employeeInfo();
   	
   	
   	///*** SELECTING THE DEPARTMENTS FOR WHICH THE PAY BILL HAS TO BE PRINTED  *****///
   	$variable = $department->getDepartmentIds(true);
   	$completeDepartmentId = array();
   	foreach ($variable as $value){
   		if($departmentType == 'all')
   			array_push($completeDepartmentId, $value);
   		else{
   			if($value == $departmentType)
   				array_push($completeDepartmentId, $value);
   		}
   	}
   	if(sizeof($completeDepartmentId) < 1)
   		$reporting->palert('No Record Exists For The Selected Options', './');
   /// *** DONE WITH PROCESSING THE DEPARTMENT TYPES ******//

   	$variable = $employee->getEmployeeTypeIds(true);
   	$completeEmployeeTypeId = array();
   	foreach ($variable as $value){
   		if($employeeType == 'all')
   			array_push($completeEmployeeTypeId, $value);
   		else{
   			if($value == $employeeType)
   				array_push($completeEmployeeTypeId, $value);
   		}
   	}
   	if(sizeof($completeEmployeeTypeId) < 1)
   		$reporting->palert('No Record Exists For The Selected Options', './');
   	                   
    ob_end_flush();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<title>Pay Bill Statement Of <?php echo $reporting->getNumber2Month($currentMonth).", ".$currentYear; ?></title>
        <link rel="stylesheet" type="text/css" href="../include/style1.css" media="screen" />
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <style type="text/css">
			.break {
				page-break-before: always;
			}
			font.bigheader{
				font-family:"Times New Roman", Times, serif;
				font-size:18px;
				font-weight:bold;
				text-decoration:none;
				
			}
			font.smallheader{
				font-family:"Times New Roman", Times, serif;
				font-size:16px;
				font-weight:bold;
				text-decoration:none;
			}
			font.month{
				font-family:"Times New Roman", Times, serif;
				font-size:15px;
				font-weight:bold;
				text-decoration:underline;
				
			}
			font.salaryPrint{
				font-family:Verdana, Geneva, sans-serif;
				font-size:14px;
				font-weight:bold;
			}
                        font.small{
				font-family:Verdana, Geneva, sans-serif;
				font-size:14px;
				font-weight:normal;
			}
        </style>
		<style type="text/css" media="print">
			#print {
				display: none;
			}
			
			#logout {
				display: none;
			}
		</style>

	</head>
	<body onload="window.print();">
            <div id="logout" align="center"><br />
                <input name="back" type="button" value="Print" onclick="window.print()" />&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="b2" type="button" value="Return Back" onclick="location='./salary_vpay.php'" /> &nbsp;&nbsp;&nbsp;
                <input name="b3" type="button" value="Logout" onclick="location='./logout.php'" /> <br /><br />
		</div>
		
		<?php		 
			
			foreach ($completeEmployeeTypeId as $IndividualEmployeeType) {			
				foreach ($completeDepartmentId as $IndividualDepartmentId) {
					$flag = false;
					unset($variable);				
					$variable = $reporting->getEmployeeId($IndividualDepartmentId, $IndividualEmployeeType, $date);
					if($variable){
						$flag = true;
						$count = 0;										
					    foreach ($variable as $employeeId){					    	
					    	if($count % 5 == 0){
					    		if($count != 0)
					    			echo "<p class=\"break\"></p>";
					    		echo "
					        		<table width=\"1024px\" align=\"center\" border=\"1\">
							        	<tr>
							            	<td width=\"100%\" align=\"center\">
							                	<table border=\"0\" align=\"center\" width=\"100%\">
							                        <tr>
							                            <td align=\"center\" width=\"160\" height=\"111px\"><img src=\"../img/mnnit_logo.gif\" alt=\"mnnit logo\" width=\"126\" height=\"111px\" align=\"left\" /></td>
							                            <td align=\"center\" width=\"743\"><font class=\"bigheader\">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class=\"smallheader\">
							                                                            ALLAHABAD - 211004<br />
								                                                            ACCOUNTS DEPARTMENT -- PAY BILL FOR </font>";
								    echo "<font class=\"month\">".$reporting->getNumber2Month($currentMonth)." ".$currentYear."</font><br />";
								    echo "<font class=\"smallheader\">Employee Type : </font><font class=\"month\">".strtoupper($employee->getEmployeeTypeName($IndividualEmployeeType))."</font> <br />";
								    echo "<font class=\"smallheader\">Department Name : </font><font class=\"month\">".strtoupper($department->getDepartmentName($IndividualDepartmentId))."</font>";
								    echo						"</td>
								                            
								                        </tr>                        
								                    </table>
								                </td>
								            </tr>
								        </table>";
								    //header ends here 
								    
								    //the table header starts here
									echo "
										<table align=\"center\" border=\"1\" width=\"1024px\">
											<tr>
												<th width=\"10%\">Emp. Code</th>
												<th width=\"30%\">Employee Details</th>
												<th width=\"30%\">Earnings Summary</th>
												<th width=\"*\">Deductions Summary</th>                
											</tr>
										</table>";
									
									//the entry of the details starts here
					    	}
					    	++$count;
					    		
					    	$personalInfo->getEmployeeInformation($employeeId, true);
					    	$bankDetails = $reporting->getEmployeeReservedBankAccountDetails($employeeId, $date);
					    	$reporting->getProcessedSalaryInformation($employeeId, $date, $date);
					    	
					    	echo "
								<table align=\"center\" border=\"1\" width=\"1024px\" style=\"page-break-inside:avoid\">
									<tr>
										<td align=\"center\" width=\"10%\"><font class=\"salaryPrint\">".$personalInfo->getEmployeeCode()."</font></td>
										<td align=\"center\" width=\"30%\"><font class=\"salaryPrint\">".$personalInfo->getName()."";
                                                $ranks = $employeeInfo->getEmployeeDesignationIds($employeeId, true);
                                                foreach ($ranks as $value)
                                                    echo "<br />".$designation->getDesignationTypeName ($value, true);
                                                echo "<br />".$personalInfo->getDob()."<br />".$bank->getBankName($bankDetails[0])."<br />".$bankDetails[1]."";

                                                
                                                                                    echo "</font></td>
										<td align=\"center\" width=\"30%\">
											<table align=\"center\" width=\"100%\" border=\"0\">";
					    	$earnings = 0;
					    	$deductions = 0;
					    	
					    	foreach ($reporting->totalEarnings as $allowanceId => $amount){
					    		echo "
												<tr>
													<td align=\"right\" width=\"50%\">".$accountHead->getReservedAccountHeadName($allowanceId, $date)."</td>
													<td align=\"center\" width=\"10%\">:</td>
													<td align=\"left\" width=\"*\">Rs. ". number_format($amount, 2, '.', '')."</td>
												</tr>";
					    		$earnings += $amount;	
					    	}
					    	
					    	echo "
											</table>
										</td>
										<td align=\"center\" width=\"30%\">
											<table align=\"center\" width=\"100%\" border=\"0\">";
					    	foreach ($reporting->totalDeductions as $allowanceId => $amount){
					    		echo "
												<tr>
													<td align=\"right\" width=\"50%\">".$accountHead->getReservedAccountHeadName($allowanceId, $date)."</td>
													<td align=\"center\" width=\"10%\">:</td>
													<td align=\"left\" width=\"*\">Rs. ".  number_format($amount, 2, '.', '')."</td>
												</tr>";
					    		$deductions += $amount;	
					    	}
					    	echo "
											</table>
										</td>
									</tr>
									<tr>
										<td colspan=\"2\" align=\"center\"><font class=\"salaryPrint\">Total Summary</font></td>
										<td align=\"center\">Total Earnings :Rs. ".  number_format($earnings, 2, '.', '')."</td>
										<td align=\"center\">Total Deductions :Rs. ".  number_format($deductions, 2, '.', '')."</td>
									</tr>
									<tr>
										<td colspan=\"4\" width=\"100%\">
											<table align=\"center\" width=\"100%\" border=\"0\">";
                                                $collegeContribution = $reporting->getCollegeContributionAmount($employeeId, $date);
                                                if($collegeContribution != ""){
                                                    echo "
                                                           <tr>
													<td width=\"100%\" align=\"left\" style=\"padding-left:20px\"><font class=\"salaryPrint\">
														College Contribution Amount : ".  number_format($collegeContribution, 2, '.', '')."
														</font>
													</td>
												</tr>";
                                                }
                                                echo "
												<tr>
													<td width=\"100%\" align=\"left\" style=\"padding-left:20px\"><font class=\"salaryPrint\">
														Net Payable Amount : ".  number_format(($earnings - $deductions + $collegeContribution), 2, '.', '')."/- (".$reporting->nameAmount($earnings - $deductions)." Only /-)";
													    
					    	echo "
														</font>
													</td>
												</tr>
											</table>
										</td>
									</tr>
								  </table>";					    	  
					}
						
					///**************** HEADER OF THE PAY BILL ***************************//		        	
				    }				    						
				    if($flag)				    	
						echo "<p class=\"break\"></p>";
				}
				if($flag)
					echo "<p class=\"break\"></p>";				
			}		
		?>  
              
				
	</body>
</html>