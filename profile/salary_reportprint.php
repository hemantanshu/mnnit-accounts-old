<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
    //error_reporting(0);

    session_start();

    require_once '../include/class.reporting.php';
    require_once '../include/class.employeeType.php';
    require_once '../include/class.department.php';
    require_once '../include/class.accountHead.php';   

    $reporting = new reporting();
    
    if(!$reporting->checkLogged())
    	$reporting->redirect('../');
    	
    if(!isset($_GET))
    	$reporting->redirect('./');
    else{
    	$date = $_GET['date'];
    }
    
    $currentMonth = substr($date, 4, 2);
   	$currentYear = substr($date, 0, 4);   

   	$employee = new employeeType();
   	$accountHead = new accountHead();  	
   	$department = new department();
   	                   
    ob_end_flush();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<title>Pay Bill Report Of <?php echo $reporting->getNumber2Month($currentMonth).", ".$currentYear; ?></title>
        <link rel="stylesheet" type="text/css" href="../include/style1.css" media="screen" />
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <style type="text/css">
			.break {
				page-break-before: always;
			}
			font.bigheader{
				font-family:Verdana, Geneva, sans-serif;
				font-size:18px;
				font-weight:bold;
				text-decoration:none;
				
			}
			font.smallheader{
				font-family:Verdana, Geneva, sans-serif;
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
				font-size:12px;
				font-weight:bold;
			}
                        font.small{
				font-family:Verdana, Geneva, sans-serif;
				font-size:12px;
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
	<body onload="window.print()">
            <div id="logout" align="center"><br />
                <input name="back" type="button" value="Print" onclick="window.print()" />&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="b2" type="button" value="Return Back" onclick="location='./salary_vpay.php'" /> &nbsp;&nbsp;&nbsp;
                <input name="b3" type="button" value="Logout" onclick="location='./logout.php'" /> <br /><br />
			</div>
		
		<?php		 
			$completeEmployeeTypeId = $reporting->getProcessedSalaryEmployeeTypes($date);
			$completeAccountHeads = $reporting->getCompleteAccountHead($date);
			
			$rowSize = (int) (sizeof($completeAccountHeads) / 3 ) + 1;
			$sumTotal = array();
			foreach ($completeEmployeeTypeId as $IndividualEmployeeType) {
				$completeDepartmentId = $reporting->getProcessedSalaryEmployeeTypeDepartments($IndividualEmployeeType, $date);
								echo "
	        		<table width=\"1024px\" align=\"center\" border=\"1\">
			        	<tr>
			            	<td width=\"100%\" align=\"center\">
			                	<table border=\"0\" align=\"center\" width=\"100%\">
			                        <tr>
			                            <td align=\"center\" width=\"160\" height=\"111px\"><img src=\"../img/mnnit_logo.gif\" alt=\"mnnit logo\" width=\"126\" height=\"111px\" align=\"left\" /></td>
			                            <td align=\"center\" width=\"743\"><font class=\"bigheader\">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class=\"smallheader\">
			                                                            ALLAHABAD - 211004<br />
				                                                            ACCOUNTS DEPARTMENT<br />REPORT GENERATION OF INDIVIDUAL EMPLOYEE TYPES </font>";
				    echo "<font class=\"month\">".$reporting->getNumber2Month($currentMonth)." ".$currentYear."</font><br />";
				    echo "<font class=\"smallheader\">Employee Type : </font><font class=\"month\">".strtoupper($employee->getEmployeeTypeName($IndividualEmployeeType))."</font> <br />";
				    echo						"</td>
				                            
				                        </tr>                        
				                    </table>
				                </td>
				            </tr>
				        </table>";
				    //header ends here
					//the table header starts here
					echo "<hr size=\"2\"  width=\"1024px\" />";
					echo "
						<table align=\"center\" border=\"1\" width=\"1024px\">
							<tr>
								<th width=\"25%\">Department Details</th>
								<th width=\"*\">Account Head Summary</th>                
							</tr>
						</table>
						<hr size=\"2px\" width=\"1024px\"/>";	
									
				foreach ($completeDepartmentId as $IndividualDepartmentId) {
					unset($variable);				
					
					$variable = $reporting->getCumulativeProcessedSalaryData($date, $IndividualEmployeeType, $IndividualDepartmentId);
					$sizeOfAllowance = sizeof($variable);
					$totalEmployeeCount = $reporting->getTotalEmployeeCount($date, $IndividualEmployeeType, $IndividualDepartmentId);
					
					if($totalEmployeeCount == 0)
						continue;
					
					if($variable){					    
						echo "
							<table align=\"center\" width=\"1024px\" border=\"1\" style=\"page-break-inside:avoid\">
								<tr>
									<td align=\"center\" width=\"25%\"><font class=\"salaryPrint\">".$department->getDepartmentName($IndividualDepartmentId)."
																			<br />Total Number Of Employees : ".$totalEmployeeCount."<br />Employee Type : ".$employee->getEmployeeTypeName($IndividualEmployeeType)."</font></td>
									<td align=\"center\" width=\"*\">
										<table align=\"center\" width=\"100%\" border=\"0\">";
						$i = 0;						
						foreach ($variable as $accountHeadId => $amount){
							$sumTotal[$IndividualEmployeeType][$IndividualDepartmentId][$accountHeadId] += $amount;			
							if($i == 0){
								echo "		<tr>";
							}
								echo "											
												<td align=\"right\" width=\"20%\">".$accountHead->getAccountHeadName($accountHeadId)."</td>
												<td align=\"center\" width=\"3%\"> : </td>
												<td align=\"right\" width=\"10%\">".number_format($amount, 2, '.', '')."</td>";
							++$i;
							if($i % 3 == 0)
								echo "</tr><tr>";
								
						}
						$employeeTotal[$IndividualEmployeeType][$IndividualDepartmentId] += $totalEmployeeCount;
						
							echo "
											</tr>				
											</table>
										</td>
									</tr>
							</table>
							";
					}			    	
				    echo "<hr size=\"1\" width=\"1024px\" />";	    
				}					
				echo "<p class=\"break\"></p>";
				    				
			}		
		?>
		
		<?php 
			echo "
        		<table width=\"1024px\" align=\"center\" border=\"1\">
		        	<tr>
		            	<td width=\"100%\" align=\"center\">
		                	<table border=\"0\" align=\"center\" width=\"100%\">
		                        <tr>
		                            <td align=\"center\" width=\"160\" height=\"111px\"><img src=\"../img/mnnit_logo.gif\" alt=\"mnnit logo\" width=\"126\" height=\"111px\" align=\"left\" /></td>
		                            <td align=\"center\" width=\"743\"><font class=\"bigheader\">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class=\"smallheader\">
		                                                            ALLAHABAD - 211004<br />
			                                                            ACCOUNTS DEPARTMENT<br />CUMULATIVE REPORT EMPLOYEE TYPES </font>";
			    echo "<font class=\"month\">".$reporting->getNumber2Month($currentMonth)." ".$currentYear."</font><br />";
			    echo						"</td>
			                            
			                        </tr>                        
			                    </table>
			                </td>
			            </tr>
			        </table>";
			foreach ($completeEmployeeTypeId as $IndividualEmployeeType) {
				$completeDepartmentId = $reporting->getProcessedSalaryEmployeeTypeDepartments($IndividualEmployeeType, $date);				
				$sum = 0 ;
				
				foreach ($completeDepartmentId as $IndividualDepartmentId)
					$sum += $employeeTotal[$IndividualEmployeeType][$IndividualDepartmentId];
									
				echo "
					<table align=\"center\" width=\"1024px\" border=\"1\" style=\"page-break-inside:avoid\">
						<tr>
							<td align=\"center\" width=\"25%\"><font class=\"salaryPrint\">".$employee->getEmployeeTypeName($IndividualEmployeeType)."
																	<br />Total Number Of Employees : ".$sum." Department : All</font></td>
							<td align=\"center\" width=\"*\">
								<table align=\"center\" width=\"100%\" border=\"0\">";
				$i = 0;
				
				
				foreach ($completeAccountHeads as $individualAccountHead) {
					$sum = 0;
					foreach ($completeDepartmentId as $IndividualDepartmentId) {
						$sum += $sumTotal[$IndividualEmployeeType][$IndividualDepartmentId][$individualAccountHead];
					};
					if($i == 0){
						echo "		<tr>";
					}
					echo "											
										<td align=\"right\" width=\"20%\">".$accountHead->getAccountHeadName($individualAccountHead)."</td>
										<td align=\"center\" width=\"3%\"> : </td>
										<td align=\"right\" width=\"10%\">".number_format($sum, 2, '.', '')."</td>";
					++$i;
					if($i % 3 == 0)
						echo "</tr><tr>";
				}
				echo "
											</tr>				
											</table>
										</td>
									</tr>
							</table>
							";
				echo "<hr size=\"1\" width=\"1024px\" />";
			}	
			echo "<p class=\"break\"></p>";
		?>  
		
		<?php 
			echo "
        		<table width=\"1024px\" align=\"center\" border=\"1\">
		        	<tr>
		            	<td width=\"100%\" align=\"center\">
		                	<table border=\"0\" align=\"center\" width=\"100%\">
		                        <tr>
		                            <td align=\"center\" width=\"160\" height=\"111px\"><img src=\"../img/mnnit_logo.gif\" alt=\"mnnit logo\" width=\"126\" height=\"111px\" align=\"left\" /></td>
		                            <td align=\"center\" width=\"743\"><font class=\"bigheader\">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class=\"smallheader\">
		                                                            ALLAHABAD - 211004<br />
			                                                            ACCOUNTS DEPARTMENT<br />CUMULATIVE REPORT DEPARTMENT TYPES </font>";
			    echo "<font class=\"month\">".$reporting->getNumber2Month($currentMonth)." ".$currentYear."</font><br />";
			    echo						"</td>
			                            
			                        </tr>                        
			                    </table>
			                </td>
			            </tr>
			        </table>";
			$completeDepartmentId = $reporting->getCompleteDepartmentId($date);    
			foreach ($completeDepartmentId as $IndividualDepartmentId) {				
				$sum = 0 ;				
				foreach ($completeEmployeeTypeId as $IndividualEmployeeType)
					$sum += $employeeTotal[$IndividualEmployeeType][$IndividualDepartmentId];
									
				echo "
					<table align=\"center\" width=\"1024px\" border=\"1\" style=\"page-break-inside:avoid\">
						<tr>
							<td align=\"center\" width=\"25%\"><font class=\"salaryPrint\">".$department->getDepartmentName($IndividualDepartmentId)."
																	<br />Total Number Of Employees : ".$sum." Employee Type : All</font></td>
							<td align=\"center\" width=\"*\">
								<table align=\"center\" width=\"100%\" border=\"0\">";
				$i = 0;	
				
				foreach ($completeAccountHeads as $individualAccountHead) {
					$sum = 0;
					foreach ($completeEmployeeTypeId as $IndividualEmployeeType) {
						$sum += $sumTotal[$IndividualEmployeeType][$IndividualDepartmentId][$individualAccountHead];
					};
					if($i == 0){
						echo "		<tr>";
					}
					echo "											
										<td align=\"right\" width=\"20%\">".$accountHead->getAccountHeadName($individualAccountHead)."</td>
										<td align=\"center\" width=\"3%\"> : </td>
										<td align=\"right\" width=\"10%\">".number_format($sum, 2, '.', '')."</td>";
					++$i;
					if($i % 3 == 0)
						echo "</tr><tr>";
				}
				echo "
											</tr>				
											</table>
										</td>
									</tr>
							</table>
							";
				echo "<hr size=\"1\" width=\"1024px\" />";
			}	
			echo "<p class=\"break\"></p>";
		?>
		
		
		<?php 
			echo "
        		<table width=\"1024px\" align=\"center\" border=\"1\">
		        	<tr>
		            	<td width=\"100%\" align=\"center\">
		                	<table border=\"0\" align=\"center\" width=\"100%\">
		                        <tr>
		                            <td align=\"center\" width=\"160\" height=\"111px\"><img src=\"../img/mnnit_logo.gif\" alt=\"mnnit logo\" width=\"126\" height=\"111px\" align=\"left\" /></td>
		                            <td align=\"center\" width=\"743\"><font class=\"bigheader\">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class=\"smallheader\">
		                                                            ALLAHABAD - 211004<br />
			                                                            ACCOUNTS DEPARTMENT<br />CUMULATIVE REPORT</font>";
			    echo "<font class=\"month\">".$reporting->getNumber2Month($currentMonth)." ".$currentYear."</font><br />";
			    echo						"</td>
			                            
			                        </tr>                        
			                    </table>
			                </td>
			            </tr>
			        </table>";
			$completeDepartmentId = $reporting->getCompleteDepartmentId($date);
			
			$sum = 0;			
			foreach ($completeEmployeeTypeId as $IndividualEmployeeType) {
				foreach ($completeDepartmentId as $IndividualDepartmentId) {
					$sum += $employeeTotal[$IndividualEmployeeType][$IndividualDepartmentId];
				}					
			}
			echo "
				<table align=\"center\" width=\"1024px\" border=\"1\" style=\"page-break-inside:avoid\">
					<tr>
						<td align=\"center\" width=\"25%\"><font class=\"salaryPrint\">Total Number Of Employees : ".$sum." <br />Employee Type : All<br />Department : ALL</font></td>
						<td align=\"center\" width=\"*\">
							<table align=\"center\" width=\"100%\" border=\"0\">";		
					
			$i = 0;						        
			foreach ($completeAccountHeads as $individualAccountHead) {				
				$sum = 0 ;				
				foreach ($completeEmployeeTypeId as $IndividualEmployeeType)
					foreach ($completeDepartmentId as $IndividualDepartmentId)
						$sum += $sumTotal[$IndividualEmployeeType][$IndividualDepartmentId][$individualAccountHead];
				if($i == 0)
					echo "		<tr>";
					
					echo "											
									<td align=\"right\" width=\"20%\">".$accountHead->getAccountHeadName($individualAccountHead)."</td>
									<td align=\"center\" width=\"3%\"> : </td>
									<td align=\"right\" width=\"10%\">".number_format($sum, 2, '.', '')."</td>";
					
					++$i;
					if($i % 3 == 0)
						echo "	</tr>
								<tr>";
			}			
			echo "
								</tr>				
							</table>
						</td>
					</tr>
				</table>";
			echo "<hr size=\"1\" width=\"1024px\" />";	
			echo "<p class=\"break\"></p>";
		?>	
		
				
	</body>
</html>