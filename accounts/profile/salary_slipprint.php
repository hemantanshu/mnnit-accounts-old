<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
    //error_reporting(0);

    session_start();

    require_once '../include/class.reporting.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.employeeInfo.php';
    require_once '../include/class.department.php';
    require_once '../include/class.designation.php';
    require_once '../include/class.bank.php';
    require_once '../include/class.accountHead.php';
    require_once '../include/class.employeeType.php';
    require_once '../include/class.gpftotal.php';
    require_once '../include/class.remarks.php';
    require_once '../include/class.loan.php';
    require_once '../include/class.salutation.php';

    $salutation = new salutation();
	$loan = new loan();
    $personalInfo = new personalInfo();
    $employeeInfo = new employeeInfo();
    $department = new department();
    $accountHead = new accountHead();
    $bank = new bank();
    $designation = new designation();
    $accounts = new reporting();
    $employee = new employeeType();
    $gpfTotal = new gpfTotal();
    $remarks = new remarks();

    if(!$accounts->checkLogged())
        $accounts->redirect('../');

    if(!(isset ($_POST) && $_POST['submit'] == "Print Salary Slip")){
        $accounts->redirect('./');
    }
    $date = $_POST['date'];

    $currentMonth = substr($date, 4, 2);
   	$currentYear = substr($date, 0, 4);
                    
                    $fiscalYear = $accounts->getFiscalYearMonth($date);
                    $fiscalStartMonth = $fiscalYear[1] - 1;
                    $fiscalStartYear = $fiscalYear[0];
    ob_end_flush();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<title>Salary Statement 0f <?php echo ucwords($accounts->getNumber2Month($currentMonth)).", ".$currentYear; ?></title>
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
			font.salarySlip{
				font-family:Arial, Helvetica, sans-serif;
				font-size:12px;
				font-weight:400;
				text-decoration:none;
				
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
    
	<body onload="window.print();">
            <div id="logout" align="center"><br />
                <input name="back" type="button" value="Print" onclick="window.print()" />&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="b2" type="button" value="Next Candidate" onclick="location='./salary_slip.php'" /> &nbsp;&nbsp;&nbsp;
                <input name="b3" type="button" value="Logout" onclick="location='./logout.php'" /> <br /><br />
		</div>
		<?php 
                    $count = 0;
                    while(true){
                        $checkBoxName = "checkbox".$count;
                        $employeeIdName = "employeeId".$count;

                        if($_POST[$checkBoxName] != 1)
                            continue;
                            
                        if(!isset($_POST[$employeeIdName]))
                        	break;
                        ++$count;
                        
                        $employeeId = $_POST[$employeeIdName];
                        if(!$accounts->isEmployeeSalaryProcessed($employeeId, $date))
                    		continue;
                        $personalInfo->getEmployeeInformation($employeeId, true);
                        $bankDetails = $employeeInfo->getReservedEmployeeBankAccountDetails($employeeId, $date);
                        

                        echo "
                            <table width=\"1024px\" align=\"center\" border=\".2\">
                                <tr>
									<td colspan=\"3\" width=\"100%\">
										<table border=\"0\" align=\"center\" width=\"100%\">
											<tr>
												<td align=\"center\" width=\"160\" height=\"111px\"><img src=\"../img/mnnit_logo.gif\" alt=\"mnnit logo\" width=\"126\" height=\"111px\" align=\"left\" /></td>
												<td align=\"center\" width=\"743\"><font class=\"bigheader\">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class=\"smallheader\">
																				ALLAHABAD - 211004<br /><br />
																				ACCOUNTS DEPARTMENT -- SALARY SLIP FOR </font>";
                        echo "<font class=\"month\">".$accounts->getNumber2Month($currentMonth)." ".$currentYear."</font>";
                        echo						"</td>
												
											</tr>		
										</table>
									</td>
								</tr>
								
                                <tr>
                                    <td height=\"10px\" colspan=\"3\"></td>
                                </tr>
                                <tr>
                                    <td colspan=\"3\" align=\"center\" width=\"100%\">
                                        <table align=\"center\" border=\"0\" width=\"100%\">
                                            <tr>
                                                 <td height=\"10px\" colspan=\"5\"></td>
                                            </tr>
                                            <tr>
                                                <td width=\"13%\" align=\"right\"><font class=\"salarySlip\">Name :</font></td>
                                                <td width=\"30%\" align=\"left\"><font class=\"salaryPrint\">".$salutation->getSalutationName($personalInfo->getSalutationId())." ".$personalInfo->getName()."</font></td>
                                                <td width=\"5%\" align=\"center\">||</td>
                                                <td align=\"right\" width=\"13%\"><font class=\"salarySlip\">Employee Code :</font></td>
                                                <td align=\"left\" width=\"*\"><font class=\"salaryPrint\">".$personalInfo->getEmployeeCode()."</font></td>
                                            </tr>
                                            <tr>
                                                    <td height=\"10px\" colspan=\"5\"></td>
                                            </tr>
                                            <tr>
                                                <td align=\"right\"><font class=\"salarySlip\">Department :</font></td>
                                                <td align=\"left\"><font class=\"salaryPrint\">".$department->getDepartmentName($personalInfo->getDepartment())."</font></td>
                                                <td align=\"center\">||</td>
                                                <td align=\"right\"><font class=\"salarySlip\">Designation</font></td>
                                                <td align=\"left\"><font class=\"salaryPrint\">";
                                        $ranks = $employeeInfo->getEmployeeDesignationIds($employeeId, true);
                                        foreach ($ranks as $value)
                                            echo $designation->getDesignationTypeName ($value, true)."<br />";
                        echo "</font></td>
                                            </tr>
                                            <tr>
                                                    <td height=\"10px\" colspan=\"5\"></td>
                                            </tr>
                                            <tr>
                                                <td align=\"right\"><font class=\"salarySlip\">Bank Name :</font></td>
                                                <td align=\"left\"><font class=\"salaryPrint\">".$bank->getBankName($bankDetails[0])."<br />MNNIT Allahabad Branch<br />IFSC Code : VIJB0007184</font></td>
                                                <td align=\"center\">||</td>
                                                <td align=\"right\"><font class=\"salarySlip\">Account No :</font></td>
                                                <td align=\"left\"><font class=\"salaryPrint\">".$bankDetails[1]."</font></td>
                                            </tr>
                                            <tr>
                                                    <td height=\"10px\" colspan=\"5\"></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan=\"3\" height=\"10px\"></td>
                                </tr>
                                <tr>
                                    <td align=\"center\" colspan=\"3\">
                                         <table border=\".1\" width=\"100%\" align=\"center\">
                                            <tr>
                                                <th width=\"33%\">EARNINGS</th>
                                                <th width=\"33%\">DEDUCTIONS</th>
                                                <th width=\"*\">SUMMARY</th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table align=\"center\" width=\"100%\" border=\"0\">";
                        
                        					$accounts->getProcessedSalaryInformation($employeeId, $date, $date);
                        					$earnings = 0;
                        					$deductions = 0;                        
                                            foreach ($accounts->totalEarnings as $accountHeadId => $amount) {                                         
                                                echo "
                                                        <tr>
                                                            <td align=\"right\" width=\"60%\">".$accountHead->getReservedAccountHeadName($accountHeadId, $date)." :</td>
                                                            <td align=\"center\" width=\"3%\"></td>
                                                            <td align=\"left\" width=\"*\">Rs. ".number_format($amount, 2, '.', '')."</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan=\"3\" height=\"5px\"></td>
                                                       </tr>";
                                                $earnings += $amount;
                        					}

                        		echo "
                                                    </table>
                                                </td>
                                                <td>
                                                    <table align=\"center\" width=\"100%\" border=\"0\">";
			                                            foreach ($accounts->totalDeductions as $accountHeadId => $amount) {
                            
                            	echo "
	                                                        <tr>
	                                                            <td align=\"right\" width=\"60%\">".$accountHead->getReservedAccountHeadName($accountHeadId, $date)." :</td>
	                                                            <td align=\"center\" width=\"3%\"></td>
	                                                            <td align=\"left\" width=\"*\">Rs. ".number_format($amount, 2, '.', '')."</td>
	                                                        </tr>
	                                                        <tr>
	                                                            <td colspan=\"3\" height=\"5px\"></td>
	                                                        </tr>";
                            								$deductions += $amount;
                        								}

                       			echo "
                                                    </table>
                                                </td>
                                                <td>
                                                    <table align=\"center\" width=\"100%\" border=\"0\">
                                                            <tr>
                                                            <td align=\"right\" width=\"60%\">Total Earnings :</td>
                                                            <td align=\"center\" width=\"3%\"></td>
                                                            <td align=\"left\" width=\"*\">Rs. ".number_format($earnings, 2, '.', '')."</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan=\"3\" height=\"5px\"></td>
                                                        </tr>
                                                        <tr>
                                                            <td align=\"right\" width=\"60%\">Total Deductions :</td>
                                                            <td align=\"center\" width=\"3%\"></td>
                                                            <td align=\"left\" width=\"*\">Rs. ".number_format($deductions, 2, '.', '')."</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan=\"3\" height=\"5px\"></td>
                                                        </tr>
                                                        <tr>
                                                            <td align=\"right\" width=\"60%\">Net Pay :</td>
                                                            <td align=\"center\" width=\"3%\"></td>
                                                            <td align=\"left\" width=\"*\">Rs. ".number_format(($earnings - $deductions), 2, '.', '')."</td>
                                                        </tr>
                                                        ";
                       									$collegeContribution = $accounts->getCollegeContributionAmount($employeeId, $date);
                       									if ($collegeContribution){
                       										echo "
                       											<tr>
		                                                            <td colspan=\"3\" height=\"5px\"></td>
		                                                        </tr>
		                                                        <tr>
		                                                            <td align=\"right\" width=\"60%\">Institute Contribution :</td>
		                                                            <td align=\"center\" width=\"3%\"></td>
		                                                            <td align=\"left\" width=\"*\">Rs. ".number_format($collegeContribution, 2, '.', '')."</td>
		                                                        </tr>";
                       									}
                                                        
                                                        
                                echo "                  <tr>
                                                            <td colspan=\"3\" height=\"5px\"></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan=\"3\" height=\"10px\"></td>
                                </tr>
                                <tr>
                                    <td colspan=\"3\" align=\"left\" style=\"padding-left:20px\"><font class=\"salaryPrint\"><br />NET PAYABLE AMOUNT : ".number_format(($earnings - $deductions), 2, '.', '')."/-  (".strtoupper($accounts->nameAmount($earnings - $deductions))." ONLY )<br /></font>.</td>
                                </tr>
                                <tr>
                                    <td colspan=\"3\" height=\"10px\"></td>
                                </tr>
                                <tr>
                                    <td align=\"center\" colspan=\"3\" width=\"100%\">
                                        <table border=\"0\" align=\"center\" width=\"100%\">
                                            <tr>
                                                <td colspan=\"7\" align=\"center\" width=\"100%\"><font class=\"salaryPrint\">SUMMARY OF EARNINGS FROM ".$accounts->getNumber2Month($fiscalStartMonth).", ".$fiscalStartYear." TO ".$accounts->getNumber2Month($currentMonth).", ".$currentYear."</font><hr size=\"1\" /><td>
                                            </tr>";
		                        $i = 0;
		                        $earnings = 0;
		                        $deductions = 0;

		                        $accounts->getProcessedSalaryInformation($employeeId, $date);		
		                        foreach ($accounts->totalEarnings as $accountHeadId => $amount) {
		                            if($i % 2 == 0)
		                                echo "<tr>
		                                        <td align=\"right\" width=\"20%\">".$accountHead->getReservedAccountHeadName($accountHeadId, $date)." :</td>
		                                        <td width=\"2%\"></td>
		                                        <td align=\"left\"  width=\"26%\">Rs. ".number_format($amount, 2, '.', '')."</td>
		                                        <td width=\"2%\"></td>";
		                            else
		                                echo "
		                                    <td align=\"right\" width=\"20%\">".$accountHead->getReservedAccountHeadName($accountHeadId, $date)." :</td>
		                                    <td width=\"2%\"></td>
		                                    <td align=\"left\" width=\"*\">Rs. ".number_format($amount, 2, '.', '')."</td>
		                                </tr>
		                                <tr>
		                                    <td height=\"5px\"></td>
		                                </tr>
		                                ";
		                                $earnings += $amount;
		                            ++$i;
		                        }
		                        if($i % 2 == 0 || $i == 1)
		                            echo "
		                                </tr>";		                        
		                            echo "                                            
                                        </table>
                                    </td>
                                </tr>                                
                                <tr>
                                    <td colspan=\"3\" height=\"10px\"></td>
                                </tr>
                                <tr>
                                    <td align=\"center\" colspan=\"3\" width=\"100%\">
                                        <table border=\"0\" align=\"center\" width=\"100%\">
                                            <tr>
                                                <td colspan=\"7\" align=\"center\" width=\"100%\"><font class=\"salaryPrint\">SUMMARY OF DEDUCTIONS FROM ".$accounts->getNumber2Month($fiscalStartMonth).", ".$fiscalStartYear." TO ".$accounts->getNumber2Month($currentMonth).", ".$currentYear."</font><hr size=\"1\" /><td>
                                            </tr>";
                        $i = 0;
                        foreach ($accounts->totalDeductions as $accountHeadId => $amount) {                         
                            if($i % 2 == 0)
                                echo "<tr>
                                        <td align=\"right\" width=\"20%\">".$accountHead->getReservedAccountHeadName($accountHeadId, $date)." :</td>
                                        <td width=\"2%\"></td>
                                        <td align=\"left\"  width=\"26%\">Rs. ".number_format($amount, 2, '.', '')."</td>
                                        <td width=\"2%\"></td>";
                            else
                                echo "
                                    <td align=\"right\" width=\"20%\">".$accountHead->getReservedAccountHeadName($accountHeadId, $date)." :</td>
                                    <td width=\"2%\"></td>
                                    <td align=\"left\" width=\"*\">Rs. ".number_format($amount, 2, '.', '')."</td>
                                </tr>
                                <tr>
                                    <td height=\"5px\"></td>
                                </tr>
                                ";
                                $deductions += $amount;
                            ++$i;
                        }
                        if($i % 2 == 0)
                            echo "
                                </tr>";
                        echo "

                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align=\"center\" colspan=\"3\">										
										<table align=\"center\" width=\"100%\" border=\"0\">												
											<tr>
												<td colspan=\"9\" align=\"center\"><font class=\"salaryPrint\">TOTAL SUMMARY FROM ".$accounts->getNumber2Month($fiscalStartMonth).", ".$fiscalStartYear." TO ".$accounts->getNumber2Month($currentMonth).", ".$currentYear."</font></td>
											</tr>
											<tr>
												<td align=\"right\" width=\"15%\"><font class=\"salaryPrint\">Total Earnings</font></td>
												<td align=\"center\" width=\"5%\">:</td>
												<td align=\"left\" width=\"13%\"><font class=\"salaryPrint\">Rs. ".number_format($earnings, 2, '.', '')."</font></td>
												<td align=\"right\" width=\"15%\"><font class=\"salaryPrint\">Total Deductions</font></td>
												<td width=\"5%\" align=\"center\">:</td>
												<td align=\"left\" width=\"13%\"><font class=\"salaryPrint\">Rs. ".number_format($deductions, 2, '.', '')."</font></td>
												<td align=\"right\" width=\"15%\"><font class=\"salaryPrint\">Total Net Pay</font></td>
												<td width=\"5%\" align=\"center\">:</td>
												<td align=\"left\" width=\"*\"><font class=\"salaryPrint\">Rs. ".number_format(($earnings - $deductions), 2, '.', '')."</font></td>
											</tr>
										</table>									
                                	</td>
                                </tr>";
                                                                                 
						$loanIds = $accounts->getReservedEmployeeLoanId($employeeId, $date, 'loan');
						if($loanIds){
							echo "
								<tr>
                                    <td align=\"center\" colspan=\"3\">										
										<table align=\"center\" width=\"100%\" border=\"0\">";								
							foreach ($loanIds as $loanId){
								$details = $accounts->getReservedEmployeeLoanIdDetails($loanId);
								$details1 = $loan->getLoanTypeIdDetails($details[2]);
								echo "
											<tr>
												<td align=\"center\" width=\"33%\"><font class=\"salaryPrint\">".$details1[2]."</font></td>
												<td align=\"right\" width=\"15%\"><font class=\"salaryPrint\">Principle Amt. Left</font></td>
												<td width=\"5%\" align=\"center\">:</td>
												<td align=\"left\" width=\"13%\"><font class=\"salaryPrint\">Rs. ".number_format($details[3], 2, '.', '')."</font></td>
												<td align=\"right\" width=\"15%\"><font class=\"salaryPrint\">Installment Left</font></td>
												<td width=\"5%\" align=\"center\">:</td>
												<td align=\"left\" width=\"*\"><font class=\"salaryPrint\">".$details[4]."</font></td>
											</tr>";
							}
							echo "
									</table>									
                                	</td>
                                </tr>";
						}
                    	$loanIds = $accounts->getReservedEmployeeLoanId($employeeId, $date, 'gpf');
						if($loanIds){
							echo "
								<tr>
                                    <td align=\"center\" colspan=\"3\">										
										<table align=\"center\" width=\"100%\" border=\"0\">";								
							foreach ($loanIds as $loanId){
								$details = $accounts->getReservedEmployeeLoanIdDetails($loanId);
								echo "
											<tr>
												<td align=\"center\" width=\"33%\"><font class=\"salaryPrint\">GPF Loan Account</font></td>												
												<td align=\"right\" width=\"15%\"><font class=\"salaryPrint\">Amount Left</font></td>
												<td width=\"5%\" align=\"center\">:</td>
												<td align=\"left\" width=\"13%\"><font class=\"salaryPrint\">Rs. ".number_format($details[3], 2, '.', '')."</font></td>
												<td align=\"right\" width=\"15%\"><font class=\"salaryPrint\">Installment Left</font></td>
												<td width=\"5%\" align=\"center\">:</td>
												<td align=\"left\" width=\"*\"><font class=\"salaryPrint\">".$details[4]."</font></td>
											</tr>";
							}
							echo "
									</table>									
                                	</td>
                                </tr>";
						}
                    	$amount = $gpfTotal->getEmployeeGpfTotalSum($employeeId, $date);
						if($date > 201011 && $amount != ''){
							echo "	
								<tr>
                                    <td colspan=\"3\" align=\"center\">
                                    	<table align=\"center\" width=\"100%\" border=\"0\">								
											<tr>
												<td align=\"right\" width=\"15%\"><font class=\"salaryPrint\">Total GPF Amount</font></td>
												<td align=\"center\" width=\"5%\">:</td>
												<td align=\"left\" width=\"13%\"><font class=\"salaryPrint\">Rs. ".number_format($amount, 2, '.', '')."</font></td>
												<td width=\"*\"></td>
											</tr>
										</table>									
                                	</td>
                                </tr>";	
						}											
						echo "
                                <tr>
                                    <td> Remarks : *</td><td colspan=\"2\" style=\"padding-top:10px; padding-bottom:10px;\"> ";
                                   $remarkId = $remarks->isEmployeeRemarkAvailiable($employeeId, $date);
                                   foreach ($remarkId as $value) {
                                   		$details = $remarks->getRemarkIdDetails($value, true);
                                       echo "<i>".($details[3])."</i><br />";
                                   }
                        echo "      </td>
                                </tr>
                                <tr>
                                    <td colspan=\"3\" align=\"center\" style8=\"padding-top:10px; padding-bottom:10px;\"><font class=\"salaryPrint\">This is a computer generated statement and does not need any signature.</font><font size=\"1.2px\"> Designed and Developed By Hemant Kumar Sah B.Tech (ECE-2011)</font></td>
                                </tr>
                            </table>
                                    ";
                            echo "<p class=\"break\"></p>";
                            
			}
		?>
        
		
	</body>
</html>