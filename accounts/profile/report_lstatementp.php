<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
	//error_reporting(0);
    session_start();
    
    require_once '../include/class.loan.php';
    require_once '../include/class.personalInfo.php';
    
    $loan = new loan();
    $personalInfo = new personalInfo();
    
    if(!$loan->checkLogged())
        $loan->redirect('../');
    if(!isset($_GET['loanid']))
    	$loan->redirect('./');
    
   	$loanAccountId = $_GET['loanid'];
   	$loanAccountDetails = $loan->getLoanAccountIdDetails($loanAccountId);
   	if($loanAccountDetails[0] == "")
   		$loan->redirect('./');
   	$details = $loan->getLoanTypeIdDetails($loanAccountDetails[2]);
   	$personalInfo->getEmployeeInformation($loanAccountDetails[1], true);
   	
   	ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<title>Employee GPF Loan Statement</title>
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
    	<form action="./report_gstatemente.php" method="post">
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
																ACCOUNTS DEPARTMENT -- EMPLOYEE GPF STATEMENT</font></th>
								
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
           			<table align="center" border="0" width="100%">
			        	<tr>
			            	<td colspan="6" align="center"><h2>EMPLOYEE LOAN STATEMENT</h2><hr size="2" /><br /></td>
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
			                <th align="left"><?php echo number_format($loan->getTotalLoanAmountSanctioned($loanAccountId), 2, '.', ',') ?></th>
			            </tr>
			            <tr>
			            	<td height="10px"></td>
			            </tr>
			            <tr>
			            	<td align="right">Installment Amount</td>
			                <td align="center">:</td>
			                <th align="left"><?php echo number_format($loan->getInstallmentAmount($loanAccountDetails[0]), 2, '.', ','); ?></th>
			            	<td align="right">Total Installment Left</td>
			                <td align="center">:</td>
			                <th align="left"><?php echo $loan->getLoanInstallmentLeft($loanAccountDetails[0], $loan->getCurrentMonth()); ?></th>
			            </tr>
			            <tr>
			            	<td height="20px"></td>
			            </tr>            
			            <tr>
			            	<td colspan="6"><hr size="1" /></td>
			            </tr>
			            <tr>
			            	<td colspan="6" align="center">
			                	<table width="100%" align="center" border="0">
			                    	<tr>
			                        	<th width="5%">S.N.</th>
			                            <th width="30%">Remarks</th>
			                            <th width="15%">Month</th>
			                            <th width="13%" align="right">Credit</th>
			                            <th width="13%" align="right">Debit</th>
			                            <th width="13%" align="right">Balance</th>                        
			                        </tr>
			                        <tr>
			                        	<td colspan="7"><br /><hr size="1" /><br /></td>
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
													<td align=\"center\">$count</td>
													<td align=\"left\">".$loan->getFlagComment($details[4])."</td>
													<td align=\"right\">".$loan->nameMonth($details[3])."</td>
												    <td align=\"right\">".number_format($credit, 2, '.', ',')."</td>
												    <td align=\"right\">".number_format($debit, 2, '.', ',')."</td>
												    <td align=\"right\">".number_format($sumTotal, 2, '.', ',')."</td>
												</tr>									          
												<tr>
													<td colspan=\"7\" style=\"padding-top:5px; padding-bottom:10px\"><hr size=\"1\" /></td>
												</tr>";	
											++$count;
			                        	}            
			                        	echo "
			                        		<tr>
												<td align=\"center\">$count</td>
												<td align=\"left\" colspan=\"2\">TOTAL SUMMARY</td>
											    <td align=\"right\">".number_format($sumCredit, 2, '.', '')."</td>
											    <td align=\"right\">".number_format($sumDebit, 2, '.', '')."</td>
											    <td align=\"right\">".number_format($sumTotal, 2, '.', '')."</td>
											</tr>";            	
			                        ?>                          
			                    </table>                    
			                </td>
			            </tr>
			            <tr>
			            	<td height="10px" colspan="6"><br /><hr size="1" /><br /></td>
			            </tr>
			        </table>				     
                </th>
            </tr>
        </table>
        <div id="logout" align="center"><br />
        		
                <input name="back" type="button" value="Print Statement" onclick="window.print()" style="width:200px" />&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="b3" type="button" value="Export To Excel" onclick="window.location='./report_lstatemente.php?id=<?php echo $loanAccountId; ?>'" style="width:200px" /> &nbsp;&nbsp;&nbsp;
                <input name="b3" type="button" value="Return Back" onclick="window.location='./report_lstatement.php'" style="width:200px" /> <br /><br />
		</div>	
        </form>   
	</body>
</html>
