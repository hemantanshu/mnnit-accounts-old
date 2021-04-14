<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
	//error_reporting(0);
    session_start();
    
    require_once '../include/class.gpftotal.php';
    require_once '../include/class.personalInfo.php';
    
    $loan = new gpfTotal();        
    if(!$loan->checkLoanOfficerLogged())
        $loan->redirect('../');
    if(!isset($_GET['date']))
    	$loan->redirect('./');
    	
    $month = $_GET['date'];
    $completeLoanId = $loan->getMonthlyNewGpfLoanAccountInstallmentId($month, true);
    $nonRefundableLoanId = $loan->getMonthlyNewGpfLoanAccountInstallmentId($month, false);
    
    if(!$completeLoanId && !$nonRefundableLoanId)
    	$loan->palert("No GPF Loan Statement Record Exists For The Given Month.", './gpf_mstatus.php');    
                    
    $personalInfo = new personalInfo();
    
    ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- GPF Loan Statement Report</title>
<link rel="stylesheet" type="text/css" href="../include/default.css" media="screen" />
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
</style>
</head>

<body onload="window.print() ">
<div>  
  <div class="container">
    <div class="main">
      <div class="contentlarge">
      	<form action="./gpf_mstatuse.php" method="post">
      	<table border="0" align="center" width="100%">
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>
            <tr>
                <td align="center" width="160px" height="111px"><img src="../img/mnnit_logo.gif" alt="mnnit logo" width="126" height="111px" align="left" /></td>
                <td align="center" width="*"><font class="bigheader">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class="smallheader">
                                                ALLAHABAD - 211004<br /><br />
                                                ACCOUNTS DEPARTMENT -- MONTHLY GPF LOAN STATEMENT</font></td>
                
            </tr>
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>		
        </table>
      	<table align="center" border="0" width="100%">       	
        	<tr>
            	<th align="center" colspan="7">SHOWING NEW GPF LOAN ACCOUNT REPORT FOR <?php echo $loan->getNumber2Month(substr($month, 4, 2)).", ".substr($month, 0, 4);?></th>
            </tr>
            <tr>
            	<td colspan="8"><hr size="1" /></td>
            </tr>
            <tr>
            	<th width="3%">SN</th>
            	<th width="7%">Emp. Code</th>
            	<th width="25%" align="left">Name</th>
            	<th width="10%" align="right">Balance Amt.</th>
            	<th width="10%" align="right">New Loan Amt.</th>
            	<th width="10%" align="right">Total Loan Amt</th>
            	<th width="10%" align="right">Installment Amt.</th>
            	<th width="10%">Installment</th>
            </tr>
            <tr>
            	<td colspan="8"><br /><hr size="1" /><br /></td>
            </tr> 
            <?php
            	$count = 0;
            	foreach ($completeLoanId as $loanInstallmentId){            		
            		$details = $loan->getGpfLoanInstallmentIdDetails($loanInstallmentId, true);
            		$loanDetails = $loan->getEmployeeGpfLoanAccountIdDetails($details[1]);
            		$personalInfo->getEmployeeInformation($loanDetails[1], true);
            		
            		$totalLoanBalance = $loan->getEmployeeGpfLoanAmountLeft($loanDetails[1], $month, "all");
            		$installmentAmt = $loan->getEmployeeGpfLoanInstallmentAmount($loanDetails[1]);
            		$intallmentLeft = $loan->getEmployeeGpfLoanInstallmentLeft($loanDetails[1], $month, 'all');
            		
            		$previousBalance = $totalLoanBalance - $details[2];
            		
            		$loanName = "loan".$count;
            		++$count;
            		
            		echo "
						<tr>
							<th>
								<input type=\"hidden\" name=\"$loanName\" value=\"$loanInstallmentId\" />
									".$count."</th>
							<th>".$personalInfo->getEmployeeCode()."</th>
							<th align=\"left\">".$personalInfo->getName()."</th>
							<th align=\"right\">".number_format($previousBalance, 2, '.', '')."</th>
							<th align=\"right\">".number_format($details[2], 2, '.', '')."</th>
							<th align=\"right\">".number_format($totalLoanBalance, 2, '.', '')."</th>
							<th align=\"right\">".number_format($installmentAmt, 2, '.', '')."</th>
							<th>".$intallmentLeft."</th>							                
						</tr>	          
						<tr>
							<td height=\"5px\"></td>
						</tr>";            		
            	}
            	echo "
            		<tr>
            			<td colspan=\"8\"><hr size=\"2\" /></td>
            		</tr>";
            	foreach ($nonRefundableLoanId as $loanId){
            		$details = $loan->getEmployeeGpfLoanAccountIdDetails($loanId);
            		$personalInfo->getEmployeeInformation($details[1], true);
            		$loanName = "loan".$count;
            		
            		++$count;
            		
            		echo "
						<tr>
							<th>
								<input type=\"hidden\" name=\"$loanName\" value=\"$loanInstallmentId\" />
									".$count."</th>
							<th>".$personalInfo->getEmployeeCode()."</th>
							<th align=\"left\">".$personalInfo->getName()."</th>
							<th align=\"right\">--------</th>
							<th align=\"right\">".number_format($details[2], 2, '.', '')."</th>
							<th align=\"right\">--------</th>
							<th align=\"right\">--------</th>
							<th>--------</th>							                
						</tr>	          
						<tr>
							<td height=\"5px\"></td>
						</tr>";
            		
            	}				
			?>	
            <tr>
            	<td colspan="8"><br /><hr size="1" /><br /></td>
            </tr>
            <tr>
            	<td colspan="8" align="center"><br />
                    	<input type="hidden" name="date" value="<?php echo $month; ?>" />
                        <input type="button" style="width:250px" value="Print The Report" onclick="window.print()"/>&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="submit" style="width:250px" value="Export To Excel" name="submit"  />&nbsp;&nbsp;&nbsp;
                        <input type="button" style="width:150px" value="Return Back" onclick="window.location='./loan_status.php'" /><br />
                    </td>
            </tr>
            <tr>
            	<td colspan="8"><br /><br /><br /></td>
            </tr>
            
        </table>               
        </form>      
      </div>     
      <div class="clearer"><span></span></div>
    </div>    
  </div>
</div>
</body>
</html>