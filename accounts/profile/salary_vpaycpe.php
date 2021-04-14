<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
    //error_reporting(0);

    session_start();

    require_once '../include/class.consolidateReport.php';
    require_once '../include/class.accountHead.php';   
	require_once '../include/class.employeeType.php';
	
    $reporting = new consolidateReport();
    
    if(!$reporting->checkLogged())
    	$reporting->redirect('../');
    	
    if(!isset($_GET))
    	$reporting->redirect('./');
    else{
    	$date = $_GET['date'];
    }
    
    $currentMonth = substr($date, 4, 2);
   	$currentYear = substr($date, 0, 4);   

   	$type = $_GET['id'];
   	   	
   	$accountHead = new accountHead();  	
   	$employeeType = new employeeType();
   	
   	if ($type == "all")
   		$reporting->redirect("./salary_vpaycpr.php?date=$date");  		
   	              
    ob_end_flush();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- Salary Consolidated Report For <?php echo $employeeType->getEmployeeTypeName($type); ?></title>
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
	font.extraSmallheader{
		font-family:Verdana, Geneva, sans-serif;
		font-size:13px;
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
      	<form action="" method="post">
      	<table border="0" align="center" width="100%">
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>
            <tr>
                <td align="center" width="160px" height="111px"><img src="../img/mnnit_logo.gif" alt="mnnit logo" width="126" height="111px" align="left" /></td>
                <td align="center" width="*"><font class="bigheader">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class="smallheader">
                                                ALLAHABAD - 211004<br /><br />
                                                ACCOUNTS SECTION </font><br /><font class="extraSmallheader">CONSOLIDATED REPORT FOR <?php echo strtoupper($employeeType->getEmployeeTypeName($type)); ?> FOR <?php echo $reporting->nameMonth($date); ?></font></td>
                
            </tr>
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>		
        </table>
        <table align="center" border="0" width="100%">       	
       		<tr>
                <th>PARTICULARS</th>
                <th align="right" style="padding-right:150px">AMOUNT</th>
            </tr>
            <tr>
            	<td colspan="3"><hr size="1" /></td>
            </tr>
            <tr>
            	<td height="10px"></td>
            </tr>
            <?php
            	$creditHead = $credit = $debitHead = $debit = array();
            	$i = $j = 0;
            	 
            	$completeAccountHead = $reporting->getCompleteAccountHead($date);				
            	$netAmount = $grossAmount = $grossDeduction = $collegeContribution[1] = $collegeContribution[0] = 0;
            	
            	foreach ($completeAccountHead as $accountHeadId){
            		$amount = $reporting->getEmployeeTypeReportAmount($type, $accountHeadId, $date);
            		if ($amount){
            			$netAmount += $amount;
            			if($amount > 0 ){
            				$creditHead[$i] = $accountHeadId;
            				$credit[$i] = $amount;
            				$grossAmount += $amount;
            				++$i;
            			}else{
            				$debitHead[$j] = $accountHeadId;
            				$debit[$j] = $amount;
            				$grossDeduction += $amount;
            				
            				if ($accountHeadId == "ACH22") 
            					$collegeContribution[0] += $reporting->getCompleteCollegeContribution ($accountHeadId, $date ,$type);
            				if ($accountHeadId == "ACH16") 
            					$collegeContribution[1] += $reporting->getCompleteCollegeContribution ($accountHeadId, $date, $type);
            				
            				++$j;
            			}
            		}
            	}
                $totalSalary = $grossAmount + $collegeContribution[1] + $collegeContribution[0];
                $netSalary = $totalSalary + $grossDeduction;
            	$i = 0;
            	foreach ($creditHead as $accountHeadId){
            		echo "
						<tr>
							<th style=\"padding-left:150px\" align=\"left\">".strtoupper($accountHead->getAccountHeadName($accountHeadId))."</th>
							<th align=\"right\" style=\"padding-right:150px\">".number_format(abs($credit[$i]), 2, '.', '')."</th>

						</tr>
						<tr>
							<td height=\"5px\"></td>
						</tr>";
            		++$i;
            	}
            	echo
					"<tr>
						<td colspan=\"3\"><br /><hr size=\"1\" /><br /></td>
					 </tr>";
            	echo "
            		<tr>
	                	<th style=\"padding-left:150px\" align=\"left\">GROSS SALARY</th>
	                    <th style=\"padding-right:150px\" align=\"right\">".number_format($grossAmount, 2, '.', '')."</th>
	                </tr>";
                echo "
                    <tr>
							<td height=\"10px\"></td>
						</tr>
                        <tr>
                            <th style=\"padding-left:150px\" align=\"left\">INSTITUTE CONTRIBUTION (NPS)</th>
                        <th style=\"padding-right:150px\" align=\"right\">".number_format(abs($collegeContribution[0]), 2, '.', '')."</th>
                    </tr>
                    <tr>
                            <th height=\"10px\"></th>
                    </tr>
                    <tr>
                            <th style=\"padding-left:150px\" align=\"left\">INSTITUTE CONTRIBUTION (CPF)</th>
                        <th style=\"padding-right:150px\" align=\"right\">".number_format(abs($collegeContribution[1]), 2, '.', '')."</th>
                    </tr>
                    <tr>
                            <th height=\"10px\" colspan=\"3\"><hr size=\"1\" /></th>
                    </tr>
                    <tr>
                        <th style=\"padding-left:150px\" align=\"left\">GRAND TOTAL</th>
                        <th style=\"padding-right:150px\" align=\"right\">".number_format(abs($totalSalary), 2, '.', '')."</th>
                    </tr>
                    ";
            	echo
					"<tr>
						<td colspan=\"3\"><br /><hr size=\"1\" /><br /></td>
					 </tr>";
				$i = 0;
            	foreach ($debitHead as $accountHeadId){
            		echo "
						<tr>
							<th style=\"padding-left:150px\" align=\"left\">".strtoupper($accountHead->getAccountHeadName($accountHeadId))."</th>
							<th align=\"right\" style=\"padding-right:150px\">".number_format(abs($debit[$i]), 2, '.', '')."</th>

						</tr>
						<tr>
							<td height=\"5px\"></td>
						</tr>";
            		++$i;
            	}
            	echo
					"<tr>
						<td colspan=\"3\"><br /><hr size=\"1\" /></td>
					 </tr>";
            	echo "
            		<tr>
	                	<th style=\"padding-left:150px\" align=\"left\">TOTAL DEDUCTION</th>
	                    <th style=\"padding-right:150px\" align=\"right\">".number_format(abs($grossDeduction), 2, '.', '')."</th>
	                </tr>
                        <tr>
                            <td height=\"5px\"></td>
                        </tr>
                        <tr>
	                	<th style=\"padding-left:150px\" align=\"left\">NET SALARY</th>
	                    <th style=\"padding-right:150px\" align=\"right\">".number_format(abs($netSalary), 2, '.', '')."</th>
	                </tr>
                        <tr>
                            <th colspan=\"3\"><hr size=\"1\" /></th>
                        </tr>
                        ";
            ?><tr>
                	<th style="padding-left:150px" align="left">GRAND TOTAL</th>
                    <th style="padding-right:150px" align="right"><?php echo number_format($totalSalary, 2, '.', ''); ?></th>
                </tr>
        </table>
        
        <div id="print" align="center">        	
        	<table align="center" width="100%">
                <tr>
                    <td align="center"><br />
                    	<input type="hidden" name="date" value="<?php echo $month; ?>" />
                        <input type="button" style="width:250px" value="Print The Summary" onclick="window.print() "/>&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="submit" style="width:250px" value="Export To Excel" name="submit"  />&nbsp;&nbsp;&nbsp;
                        <input type="button" style="width:150px" value="Return Back" onclick="window.location='./'" /><br />
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