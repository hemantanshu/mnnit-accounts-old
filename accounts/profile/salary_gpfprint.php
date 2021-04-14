<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
  	//error_reporting(0);

    session_start();
    require_once '../include/class.employeeInfo.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.loginInfo.php';
    require_once '../include/class.department.php';
    require_once '../include/class.employeeType.php';
    require_once '../include/class.gpftotal.php';
    
	
    $employeeInfo = new employeeInfo();
    $personalInfo = new personalInfo();
    $loggedInfo = new loginInfo();
    $department = new department();
    $gpfTotal = new gpfTotal();
    $employeeType = new employeeType();   
    
    
	if(!$loggedInfo->checkLogged())
        $loggedInfo->redirect('../');
        
	if(isset($_GET['id']))
		$employeeId = $_GET['id'];
	else
		$loggedInfo->redirect('./');
	$fundType = $_GET['fund'];
    
	$completeGpfIds = $gpfTotal->getEmployeeFundIds($employeeId, $fundType);
	if(!$completeGpfIds)
		$loggedInfo->palert("There Is No Record Availiable For The Given Employee. Please Select Another Employee", './salary_gpf.php');    

    $personalInfo->getEmployeeInformation($employeeId, true);
    ob_end_flush();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<title>Complete GPF Statement</title>
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
        <table border="1" align="center" width="1024px">
        	<tr>
            	<td width="100%">
        		<?php 
					echo "
						<table border=\"0\" align=\"center\" width=\"1024px\">
							<tr>
								<td colspan=\"2\"><hr size=\"2\"></td>
							</tr>
							<tr>
								<td align=\"center\" width=\"160\" height=\"111px\"><img src=\"../img/mnnit_logo.gif\" alt=\"mnnit logo\" width=\"126\" height=\"111px\" align=\"left\" /></td>
								<td align=\"center\" width=\"743\"><font class=\"bigheader\">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class=\"smallheader\">
																ALLAHABAD - 211004<br /><br />
																ACCOUNTS DEPARTMENT -- GPF STATEMENT</font></td>
								
							</tr>
							<tr>
								<td colspan=\"2\"><hr size=\"2\"></td>
							</tr>		
						</table>
					";
				?>	        	
                </td>
            </tr>
            <tr>
            	<td width="100%">
           			<table align="center" border="0" width="1024px">        	
                        <tr>
                            <td width="100%">
                                <table align="center" width="100%" border="0">
                                    <tr>
                                        <td align="right" width="15%">Name</td>
                                        <td align="center" width="5%">:</td>
                                        <td align="left" width="30%"><font class="green"><?php echo $personalInfo->getName(); ?></font></td>
                                        <td width="15%" align="right">Employee Code</td>
                                        <td width="5%" align="center">:</td>
                                        <td align="left" width="*"><font class="green"><?php echo $personalInfo->getEmployeeCode(); ?></font></td>
                                    </tr>
                                    <tr>
                                        <td height="10px"></td>
                                    </tr>
                                    <tr>                        
                                        <td align="right">Department :</td>
                                        <td align="center">:</td>
                                        <td align="left"><font class="green"><?php echo $department->getDepartmentName($personalInfo->getDepartment()); ?></font></td>
                                        <td align="right">Employee Type</td>
                                        <td align="center">:</td>
                                        <td align="left"><font class="green"><?php echo $employeeType->getEmployeeTypeName($personalInfo->getEmployeeType()); ?></font></td>
                                    </tr>
                                    <tr>
                                        <td height="10px"></td>
                                    </tr>                        
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td><br /><hr size="3" /><br /></td>
                        </tr>
                        <tr>
                            <td width="100%" align="center">
                                <table align="center" width="100%" border="0">
                                    <tr>
                                        <th width="5%">S.N.</th>
                                        <th width="40%" align="left">Remarks</th>
                                        <th width="15%">Date</th>                            
                                        <th width="13%" align="right">Credit</th>
                                        <th width="13%" align="right">Debit</th>
                                        <th width="*" align="right">Balance</th>
                                    </tr>
                                    <tr>
                                        <td colspan="7"><hr size="2" /></td>
                                    </tr>
                                    <?php 
                                        $count = 0;
                                        $sumTotal = 0; //for the complete sum
                                        $sumDebit = 0; //for the total Debit Amount
                                        $sumCredit = 0; //for the total Credit Amount
                                        foreach ($completeGpfIds as $individualGpfId) {
                                            $details = $gpfTotal->getFundIdDetails($individualGpfId, 'nps');
                                            $debit = $details[2] < 0 ? abs($details[2]) : 0;
                                            $credit = $details[2] > 0 ? abs($details[2]) : 0;
                                            
                                            $sumDebit += $debit;
                                            $sumCredit += $credit;
                                            $sumTotal += -$debit + $credit;
                                            
                                            
                                            ++$count;
                                            echo "
                                                <tr>
                                                    <td align=\"center\">".$count."</td>
                                                    <td align=\"left\">".$gpfTotal->getFlagComment($details[4])."</td>
                                                    <td align=\"left\">".$gpfTotal->getNumber2Month(substr($details[3], 4, 2)).", ".substr($details[3], 0, 4)."</td>
                                                    <td align=\"right\">".number_format($credit, 2, '.', ',')."</td>
                                                    <td align=\"right\">".number_format($debit, 2, '.', ',')."</td>
                                                    <td align=\"right\">".number_format($sumTotal, 2, '.', ',')."</td>
                                                </tr>
                                                <tr>
                                                    <td colspan=\"7\"><hr size=\"1\" /></td>
                                                </tr>";
                                        }
                                    ?>
                                    
                                    <tr>
                                        <td height="10px"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" align="center"><font class="error">Total Sum</font></td>
                                        <td align="right"><font class="green"><?php echo number_format($sumCredit, 2, '.', ','); ?></font></td>
                                        <td align="right"><font class="error"><?php echo number_format($sumDebit, 2, '.', ','); ?></font></td>
                                        <td align="right"><font class="green"><?php echo number_format($sumTotal, 2, '.', ','); ?></font></td>
                                    </tr>
                                    <tr>
                                        <td height="10px"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7"><hr size="3" /></td>
                                    </tr>
                                    <tr>
	                                    <td colspan="7" align="center"><font class="salaryPrint">This is a computer generated statement and does not need any signature.</font><font size="1.2px"> Designed and Developed By Hemant Kumar Sah B.Tech (ECE-2011)</font></td>
	                                </tr>
                                </table>
                            </td>
                        </tr>
                    </table>				     
                </td>
            </tr>
        </table>
		<div id="logout" align="center"><br />
                <input name="back" type="button" value="Print The Statement" style="width:200px" onclick="window.print()" />&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="button" value="Export To Excel" onclick="window.location='salary_gpfexcel.php?id=<?php echo $employeeId; ?>&fund=<?php echo $fundType; ?>'" style="width:200px" />&nbsp;&nbsp;&nbsp;
                <input name="b3" type="button" value="Return Back" onclick="location='./salary_gpf.php'"  style="width:200px" /> <br /><br />
		</div>
		   
	</body>
</html>
