<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
  	//error_reporting(0);

    session_start();
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.loginInfo.php';
    require_once '../include/class.department.php';
    require_once '../include/class.employeeType.php';
    require_once '../include/class.gpftotal.php';
    
	
    $personalInfo = new personalInfo();
    $loggedInfo = new loginInfo();
    $department = new department();
    $gpfTotal = new gpfTotal();
    $employeeType = new employeeType();   

    $employeeId = $loggedInfo->checkEmployeeLogged();
    
	if(!$employeeId)
        $loggedInfo->redirect('../');
        
	
	$completeGpfIds = $gpfTotal->getEmployeeGpfIds($employeeId, true);
	if(!$completeGpfIds)
		$loggedInfo->palert("There Is No Record Availiable", './');    

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
	<body onload="window.print()">
            <div id="logout" align="center"><br />
                <input name="back" type="button" value="Print" onclick="window.print()" />&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="b2" type="button" value="Return Back" onclick="location='./'" /> &nbsp;&nbsp;&nbsp;
                <input name="b3" type="button" value="Logout" onclick="location='./logout.php'" /> <br /><br />
		</div>
        <table border="1" align="center" width="1024px">
        	<tr>
            	<th width="100%">
        		<?php 
					echo "
						<table border=\"0\" align=\"center\" width=\"1024px\">
							<tr>
								<th colspan=\"2\"><hr size=\"2\"></th>
							</tr>
							<tr>
								<th align=\"center\" width=\"160\" height=\"111px\"><img src=\"../img/mnnit_logo.gif\" alt=\"mnnit logo\" width=\"126\" height=\"111px\" align=\"left\" /></th>
								<th align=\"center\" width=\"743\"><font class=\"bigheader\">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class=\"smallheader\">
																ALLAHABAD - 211004<br /><br />
																ACCOUNTS DEPARTMENT -- GPF STATEMENT</font></th>
								
							</tr>
							<tr>
								<th colspan=\"2\"><hr size=\"2\"></th>
							</tr>		
						</table>
					";
				?>	        	
                </th>
            </tr>
            <tr>
            	<th width="100%">
           			<table align="center" border="0" width="1024px">        	
                        <tr>
                            <th width="100%">
                                <table align="center" width="100%" border="0">
                                    <tr>
                                        <th align="right" width="15%">Name</th>
                                        <th align="center" width="5%">:</th>
                                        <th align="left" width="30%"><font class="green"><?php echo $personalInfo->getName(); ?></font></th>
                                        <th width="15%" align="right">Employee Code</th>
                                        <th width="5%" align="center">:</th>
                                        <th align="left" width="*"><font class="green"><?php echo $personalInfo->getEmployeeCode(); ?></font></th>
                                    </tr>
                                    <tr>
                                        <th height="10px"></th>
                                    </tr>
                                    <tr>                        
                                        <th align="right">Department :</th>
                                        <th align="center">:</th>
                                        <th align="left"><font class="green"><?php echo $department->getDepartmentName($personalInfo->getDepartment()); ?></font></th>
                                        <th align="right">Employee Type</th>
                                        <th align="center">:</th>
                                        <th align="left"><font class="green"><?php echo $employeeType->getEmployeeTypeName($personalInfo->getEmployeeType()); ?></font></th>
                                    </tr>
                                    <tr>
                                        <th height="10px"></th>
                                    </tr>                        
                                </table>
                            </th>
                        </tr>
                        <tr>
                            <th><br /><hr size="3" /><br /></th>
                        </tr>
                        <tr>
                            <th width="100%" align="center">
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
                                        <th colspan="7"><br /><hr size="2" /><br /></th>
                                    </tr>
                                    <?php 
                                        $count = 0;
                                        $sumTotal = 0; //for the complete sum
                                        $sumDebit = 0; //for the total Debit Amount
                                        $sumCredit = 0; //for the total Credit Amount
                                        foreach ($completeGpfIds as $individualGpfId) {
                                            $details = $gpfTotal->getGpfIdDetails($individualGpfId, true);
                                            $debit = $details[2] < 0 ? abs($details[2]) : 0;
                                            $credit = $details[2] > 0 ? abs($details[2]) : 0;
                                            
                                            $sumDebit += $debit;
                                            $sumCredit += $credit;
                                            $sumTotal += -$debit + $credit;
                                            
                                            
                                            ++$count;
                                            echo "
                                                <tr>
                                                    <th align=\"center\">".$count."</th>
                                                    <th align=\"left\">".$gpfTotal->getFlagComment($details[4])."</th>
                                                    <th align=\"left\">".$gpfTotal->getNumber2Month(substr($details[3], 4, 2)).", ".substr($details[3], 0, 4)."</th>
                                                    <th align=\"right\">".number_format($credit, 2, '.', '')."</th>
                                                    <th align=\"right\">".number_format($debit, 2, '.', '')."</th>
                                                    <th align=\"right\">".number_format($sumTotal, 2, '.', '')."</th>
                                                </tr>
                                                <tr>
                                                    <th colspan=\"7\"><hr size=\"1\" /></th>
                                                </tr>";
                                        }
                                    ?>
                                    
                                    <tr>
                                        <th height="10px"></th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" align="center"><font class="error">Total Sum</font></th>
                                        <th align="right"><font class="green"><?php echo number_format($sumCredit, 2, '.', ''); ?></font></th>
                                        <th align="right"><font class="error"><?php echo number_format($sumDebit, 2, '.', ''); ?></font></th>
                                        <th align="right"><font class="green"><?php echo number_format($sumTotal, 2, '.', ''); ?></font></th>
                                    </tr>
                                    <tr>
                                        <th height="10px"></th>
                                    </tr>
                                    <tr>
                                        <th colspan="7"><hr size="3" /></th>
                                    </tr>
                                    <tr>
	                                    <th colspan="7" align="center"><font class="salaryPrint">This is a computer generated statement and does not need any signature.</font><font size="1.2px"> Designed and Developed By Hemant Kumar Sah B.Tech (ECE-2011)</font></th>
	                                </tr>
                                </table>
                            </th>
                        </tr>
                    </table>				     
                </th>
            </tr>
        </table>
		
		   
	</body>
</html>
