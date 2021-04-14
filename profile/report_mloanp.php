<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
	//error_reporting(0);
    session_start();
    
    require_once '../include/class.editloan.php';
    require_once '../include/class.personalInfo.php';
    
    $loan = new editLoan();    
    
    if(!$loan->checkLogged())
        $loan->redirect('../');
    if(!isset($_GET['date']))
    	$loan->redirect('./');
    	
    $month = $_GET['date'];
    $completeLoanId = $loan->getProcessedInstallmentLoanAccountId($month);
    
    if(!$completeLoanId)
    	$loan->palert("No Loan Statement Record Exists For The Given Month.", './report_mloan.php');    
                    
    $personalInfo = new personalInfo();
    
    ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- Employee Emolument Report</title>
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
<!-- table sorter -->
<script type="text/javascript" src="../include/jquery.tablesorter.js"></script>
<script type="text/javascript">
	$(function() {
		$("table").tablesorter({debug: false})
		$("a.append").click(appendData);
		
		
	});
	</script>
<!-- table sorter ends here -->
</head>

<body onload="window.print() ">
<div>  
  <div class="container">
    <div class="main">
      <div class="contentlarge">
      	<form action="./report_mloane.php" method="post">
      	<table border="0" align="center" width="100%">
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>
            <tr>
                <td align="center" width="160px" height="111px"><img src="../img/mnnit_logo.gif" alt="mnnit logo" width="126" height="111px" align="left" /></td>
                <td align="center" width="*"><font class="bigheader">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class="smallheader">
                                                ALLAHABAD - 211004<br /><br />
                                                ACCOUNTS DEPARTMENT -- MONTHLY LOAN STATEMENT</font></td>
                
            </tr>
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>		
        </table>
        <table align="center" border="0" width="100%">
        <thead>       	
        	<tr>
            	<th align="center" colspan="7">SHOWING LOAN RECOVERY STATEMENT REPORT FOR <?php echo $loan->getNumber2Month(substr($month, 4, 2)).", ".substr($month, 0, 4);?></th>
            </tr>
            <tr>
            	<td colspan="7"><hr size="1" /></td>
            </tr>
            <tr style="cursor:pointer">
            	<th width="10%">Emp. Code</th>
            	<th width="30%" align="left">Name</th>
            	<th width="20%" align="left">Loan Name</th>
            	<th align="right" width="13%">Installment Amount</th>
            	<th align="right" width="13%">Amount Left</th>
            	<th align="right" width="*" style="padding-right:15px">Installment Left</th>
            </tr>
            <tr>
            	<td colspan="7"><br /><hr size="1" /><br /></td>
            </tr> 
            </thead>
            <tbody>
            <?php
            	$count = 0;
            	foreach ($completeLoanId as $loanInstallmentId){
            		
            		$details = $loan->getEmployeeLoanInstallmentIdDetails($loanInstallmentId);
            		$loanDetails = $loan->getLoanAccountIdDetails($details[1]);
            		$loanTypeDetails = $loan->getLoanTypeIdDetails($loanDetails[2]);
            		$personalInfo->getEmployeeInformation($loanDetails[1], true);
            		
            		$loanName = "loan".$count;
            		++$count;
            		
            		echo "
						<tr>
							<td>
								<input type=\"hidden\" name=\"$loanName\" value=\"$loanInstallmentId\" />
									".$personalInfo->getEmployeeCode()."</td>
							<td align=\"left\">".$personalInfo->getName()."</td>
							<td align=\"left\">".$loanTypeDetails[2]."</td>
							<td align=\"right\">".number_format(abs($details[2]), 2, '.', '')."</td>
							<td align=\"right\">".number_format($loan->getLoanPrincipleAmountLeft($details[1], $month), 2, '.', '')."</td>
							<td align=\"right\" style=\"padding-right:15px\">".$loan->getLoanInstallmentLeft($details[1], $month)."</td>                
						</tr>	          
						<tr>
							<td height=\"5px\"></td>
						</tr>";
            		
            	}
				
			?>	
            </tbody>
            <tr>
            	<td colspan="7"><br /><hr size="1" /><br /></td>
            </tr>
            
        </table>
        <div id="print" align="center">        	
        	<table align="center" width="100%">
                <tr>
                    <td align="center"><br />
                    	<input type="hidden" name="date" value="<?php echo $month; ?>" />
                        <input type="button" style="width:250px" value="Print The Summary" onclick="window.print() "/>&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="submit" style="width:250px" value="Export To Excel" name="submit"  />&nbsp;&nbsp;&nbsp;
                        <input type="button" style="width:150px" value="Return Back" onclick="window.location='./report_mloan.php'" /><br />
                    </td>
                </tr>
        </table>
        </div>                 
        </form>      
      </div>      
      <div class="clearer"><span></span></div>
    </div>    
  </div>
</div>
</body>
</html>