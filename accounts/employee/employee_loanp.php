<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
	//error_reporting(0);
    session_start();
    
    require_once '../include/class.loan.php';
    require_once '../include/class.personalInfo.php';
    
    $loan = new loan();
    $personalInfo = new personalInfo();
    
    $employeeId = $loan->checkEmployeeLogged();
	if(!$employeeId)
        $loan->redirect('../');
        
    if(!isset($_GET['id']))
    	$loan->redirect('./');
    
   	$loanAccountId = $_GET['id'];
   	
   	$loanAccountDetails = $loan->getLoanAccountIdDetails($loanAccountId);
   	
   	if($loanAccountDetails[0] == "")
   		$loan->redirect('./');
   		
   	if($loanAccountDetails[1] != $employeeId)
   		$loan->redirect('./');
   		
   	$details = $loan->getLoanTypeIdDetails($loanAccountDetails[2]);
   	$personalInfo->getEmployeeInformation($loanAccountDetails[1], true);
   	
   	ob_end_flush();
?>

        
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<title>Loan Statement</title>
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
	<body onload="window.print() ">  
		<form action="./employee_loane.php" method="post">          
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
																ACCOUNTS DEPARTMENT -- LOAN ACCOUNT STATEMENT</font></td>
								
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
                	<table align="center" border="0" width="100%">
			        	<tr>
			            	<td colspan="6" align="center">LOAN ACCOUNT TYPE : <b><?php echo strtoupper($details[2]); ?></b></td>
			            </tr>
                        <tr>
                        	<td height="10px"></td>
                        </tr>
			            <tr>
			            	<td align="right">Employee Name</td>
			                <td align="center" width="5%">:</td>
			                <th align="left"><?php echo strtoupper($personalInfo->getName()); ?></th>
			            	<td align="right">Employee Code</td>
			                <td align="center" width="5%">:</td>
			                <th align="left"><?php echo strtoupper($personalInfo->getEmployeeCode()); ?></th>            	
			            </tr>
			            <tr>
			            	<td height="10px"></td>
			            </tr>
			            <tr>
			            	<td align="right">Loan Sanctioned Month</td>
			                <td align="center">:</td>
			                <th align="left"><?php echo strtoupper($loan->nameMonth($loanAccountDetails[7])); ?></th>
			            	<td align="right">Loan Amount Sanctioned</td>
			                <td align="center">:</td>
			                <th align="left"><?php echo number_format($loanAccountDetails[3], 2, '.', ''); ?></th>
			            </tr>
			            <tr>
			            	<td height="10px"></td>
			            </tr>
			            <tr>
			            	<td align="right">Installment Amount</td>
			                <td align="center">:</td>
			                <th align="left"><?php echo number_format($loan->getInstallmentAmount($loanAccountDetails[0]), 2, '.', ''); ?></th>
			            	<td align="right">Total Installment Left</td>
			                <td align="center">:</td>
			                <th align="left"><?php echo $loan->getLoanInstallmentLeft($loanAccountDetails[0]); ?></th>
			            </tr>
			            <tr>
			            	<td colspan="6"><hr size="1" /></td>
			            </tr>
			            <tr>
			            	<td colspan="6" align="center">
			                	<table width="100%" align="center" border="0">
			                    	<tr>
			                        	<th width="5%">S.N.</th>
			                            <th width="40%">Remarks</th>
			                            <th width="15%">Month</th>
			                            <th width="13%" align="right">Credit</th>
			                            <th width="13%" align="right">Debit</th>
			                            <th width="13%" align="right">Balance</th>                            
			                        </tr>
			                        <tr>
			                        	<td colspan="6"><hr size="1" /></td>
			                        </tr>            
			                        <?php                        	                        	
			                        	$completeInstallmentId = $loan->getEmployeeLoanInstallmentId($loanAccountId);
			                        	$count = 1;
			                        	$sumTotal = $sumCredit = $sumDebit = 0;
			                        	foreach ($completeInstallmentId as $installmentId){
			                        		$details = $loan->getEmployeeLoanInstallmentIdDetails($installmentId);
			                        		$credit = $details[2] > 0 ? $details[2] : 0;
			                        		$debit = $details[2] < 0 ? abs($details[2]) : 0;
			                        		
			                        		$sumCredit += $credit;
			                        		$sumDebit += $debit;
			                        		$sumTotal += $credit - $debit;
			                        		
			                        		echo "
				                        		<tr>
													<th align=\"center\">$count</th>
													<th align=\"left\">".ucwords(strtolower($details[4]))."</th>
													<th align=\"right\">".ucwords(strtolower($loan->nameMonth($details[3])))."</th>
												    <th align=\"right\">".number_format($credit, 2, '.', '')."</th>
												    <th align=\"right\">".number_format($debit, 2, '.', '')."</th>
												    <th align=\"right\">".number_format($sumTotal, 2, '.', '')."</th>
												</tr>									          
												<tr>
													<th colspan=\"6\"><hr size=\"1\" /></th>
												</tr>";	
											++$count;
			                        	}            
			                        	echo "
			                        		<tr>
												<th align=\"center\">$count</th>
												<th align=\"left\" colspan=\"2\">TOTAL SUMMARY</th>
											    <th align=\"right\">".number_format($sumCredit, 2, '.', '')."</th>
											    <th align=\"right\">".number_format($sumDebit, 2, '.', '')."</th>
											    <th align=\"right\">".number_format($sumTotal, 2, '.', '')."</th>
											</tr>";            	
			                        ?>                          
			                    </table>                    
			                </td>
			            </tr>
			        </table>
                </td>
            </tr>
        </table>
        <div id="logout" align="center"><br />
                <input name="back" type="button" value="Print" style="width:200px" onclick="window.print()" />&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="hidden" name="id" value="<?php echo $loanAccountId; ?>" />
                <input type="submit" name="submit" value="Export In Excel" style="width:200px" /> &nbsp;&nbsp;&nbsp;
                <input name="b3" type="button" value="Return Back" onclick="location='./employee_loan.php'" style="width:200px"/> <br /><br />
		</div>
		</form>
		   
	</body>
</html>
