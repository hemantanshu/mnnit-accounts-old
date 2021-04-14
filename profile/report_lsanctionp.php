<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
	//error_reporting(0);
    session_start();
    
    require_once '../include/class.loan.php';
    require_once '../include/class.personalInfo.php';
    
    $loan = new loan();    
    if(!$loan->checkLogged())
        $loan->redirect('../');
    if(!isset($_GET['date']))
    	$loan->redirect('./');
    	
    $month = $_GET['date'];
    $completeLoanId = $loan->getEmployeeNewSanctionedLoanInstallmentIds($month);
    
    if(!$completeLoanId)
    	$loan->palert("No Loan Sanction Record Exists For The Given Month.", './report_lsanction.php');    
                    
    $personalInfo = new personalInfo();
    
    ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- Loan Sanction Report</title>
<script type="text/javascript" src="../include/jquery-latest.js"></script> 
<script type="text/javascript" src="../include/jquery.tablesorter.js"></script>
<script type="text/javascript">
	$(function() {
		$("table").tablesorter({debug: false})
		$("a.append").click(appendData);
		
		
	});
	</script>

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
				font-size:18px;
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
      	<form action="./report_lsanctione.php" method="post">
      	<table border="0" align="center" width="100%">
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>
            <tr>
                <td align="center" width="160px" height="111px"><img src="../img/mnnit_logo.gif" alt="mnnit logo" width="126" height="111px" align="left" /></td>
                <td align="center" width="*"><font class="bigheader">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class="smallheader">
                                                ALLAHABAD - 211004<br /><br />
                                                ACCOUNTS DEPARTMENT -- MONTHLY GPF LOAN SANCTION<br />
                                                FOR THE MONTH <?php echo $loan->nameMonth($month); ?></font></td>
                
            </tr>
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>
        </table> 
      	<table align="center" border="0" width="100%">       	
        	<tr>
            	<th align="center" colspan="7">SHOWING NEW LOAN ACCOUNT REPORT FOR <?php echo $loan->getNumber2Month(substr($month, 4, 2)).", ".substr($month, 0, 4);?></th>
            </tr>
            <tr>
            	<td colspan="8"><hr size="1" /></td>
            </tr>
            <tr>
            	<th width="3%">SN</th>
            	<th width="7%">Emp. Code</th>
            	<th width="25%" align="left">Name</th>
            	<th width="25%" align="left">Loan Type</th>
            	<th width="10%" align="right">Balance Amt.</th>
            	<th width="10%" align="right">New Loan Amt.</th>
            	<th width="10%" align="right">Total Loan Amt</th>            	
            	<th width="10%">Installment</th>
            </tr>
            <tr>
            	<td colspan="8"><br /><hr size="1" /><br /></td>
            </tr> 
            <?php
            	$count = 0;
            	foreach ($completeLoanId as $loanInstallmentId){
					$installmentDetails = $loan->getEmployeeLoanInstallmentIdDetails($loanInstallmentId);            		
            		$details = $loan->getLoanAccountIdDetails($installmentDetails[1]);
            		
            		$loanDetails = $loan->getLoanTypeIdDetails($details[2]);
                    $personalInfo->getEmployeeInformation($details[1], true);                   
                    
            		                    
            		$totalLoanBalance = $loan->getLoanAmountLeft($installmentDetails[1], $month, false);            		
            		$intallmentLeft = $loan->getLoanInstallmentLeft($installmentDetails[1], $month, false);
            		
            		$previousBalance = $totalLoanBalance - $installmentDetails[2];     		
            		            		
            		++$count;
            		
            		echo "
						<tr>
							<td>
								
									".$count."</td>
							<td>".$personalInfo->getEmployeeCode()."</td>
							<td align=\"left\">".$personalInfo->getName()."</td>
							<td align=\"left\">".$loanDetails[2]."</td>
							<td align=\"right\">".number_format($previousBalance, 2, '.', ',')."</td>
							<td align=\"right\">".number_format($installmentDetails[2], 2, '.', ',')."</td>
							<td align=\"right\">".number_format($totalLoanBalance, 2, '.', ',')."</td>
							<td align=\"center\">".$intallmentLeft."</td> 
						</tr>	          
						<tr>
							<td height=\"5px\"></td>
						</tr>";            		
            	}
            	echo "
            		<tr>
            			<td colspan=\"8\"><hr size=\"2\" /></td>
            		</tr>";
            					
			?>	
            <tr>
            	<td colspan="8"><br /><hr size="1" /><br /></td>
            </tr>            
        </table> 
        <div id="print" align="center">
        	<table align="center" width="100%">
                <tr>
                    <td align="center"><br />
                    	<input type="hidden" name="date" value="<?php echo $month; ?>" />
                    	
                        <input type="button" style="width:250px" value="Print The Summary" onclick="window.print() "/>&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="submit" style="width:250px" value="Export To Excel" name="submit"  />&nbsp;&nbsp;&nbsp;
                        <input type="button" style="width:150px" value="Return Back" onclick="window.location='./report_lsanction.php'" /><br />
                    </td>
                </tr>
        </table>
        </div>             
                      
        </form>      
      </div>           
    </div>    
  </div>
</div>
</body>
</html>