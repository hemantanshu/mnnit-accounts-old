<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
    ////error_reporting(0);

    session_start();

    require_once '../include/class.consolidateReport.php';
    require_once '../include/class.accountHead.php';   
	require_once '../include/class.employeeType.php';
	require_once '../include/class.department.php';
	
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

   	$type = $_GET['type'];
   	   	
   	$accountHead = new accountHead();  	
   	$employeeType = new employeeType();
   	$department = new department();
   	
   	$completeEmployeeType = array();
   	if ($type == "all")
   		$completeEmployeeType = $employeeType->getEmployeeTypeIds(true);
   	else 
   		array_push($completeEmployeeType, $type);
   	
    ob_end_flush();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- Salary Consolidated Report</title>
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
		font-size:14px;
		font-weight:normal;
	}
				font.small{
		font-family:Verdana, Geneva, sans-serif;
		font-size:13px;
		font-weight:bold;
	}
</style>
<style type="text/css" media="print">
#print {
	display: none;
}
</style>
</head>

<body>
<div>  
  <div class="container">
    <div class="main">
      <div class="contentlarge">
      	<form action="" method="post">
      	<?php 
			$flag = false;
      		foreach ($completeEmployeeType as $employeeTypeId){
      			if ($flag)
      				echo "<p class=\"break\"></p>";
      			echo "
      				<table border=\"0\" align=\"center\" width=\"100%\">
			            <tr>
			                <td colspan=\"2\"><hr size=\"2\" /></td>
			            </tr>
			            <tr>
			                <td align=\"center\" width=\"160px\" height=\"111px\"><img src=\"../img/mnnit_logo.gif\" alt=\"mnnit logo\" width=\"126\" height=\"111px\" align=\"left\" /></td>
			                <td align=\"center\" width=\"*\"><font class=\"bigheader\">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class=\"smallheader\">
			                                                ALLAHABAD - 211004<br /><br />
			                                                ACCOUNTS SECTION</font><br /><font class=\"extraSmallheader\">CONSOLIDATED REPORT FOR ". strtoupper($employeeType->getEmployeeTypeName($employeeTypeId))." FOR ".$reporting->nameMonth($date)."</font></td>
			                
			            </tr>
			            <tr>
			                <td colspan=\"2\"><hr size=\"2\" /></td>
			            </tr>		
			        </table>";
      			$completeDepartmentId = $reporting->getCompleteDepartmentId($date, $employeeTypeId);
      			foreach ($completeDepartmentId as $departmentId){
	      			$creditHead = $credit = $debitHead = $debit = array();
	            	$i = $j = 0;
	            	 
	            	$completeAccountHead = $reporting->getCompleteAccountHead($date);				
	            	$netAmount = $grossAmount = $grossDeduction = 0;
                        $collegeContribution = array();
                        
	            	foreach ($completeAccountHead as $accountHeadId){
	            		$amount = $reporting->getEmployeeTypeDepartmentReportAmount($employeeTypeId, $departmentId, $accountHeadId, $date);
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
	            				++$j;

                                            if ($accountHeadId == "ACH22")
            					$collegeContribution[0] += $reporting->getCompleteCollegeContribution ($accountHeadId, $date ,$type, $departmentId);
                                            if ($accountHeadId == "ACH16")
            					$collegeContribution[1] += $reporting->getCompleteCollegeContribution ($accountHeadId, $date, $type, $departmentId);
	            			}
	            		}
	            	}                        
	            	unset($completeAccountHead);
					echo "
						<table align=\"center\" border=\"1\" width=\"100%\" style=\"page-break-inside:avoid\">
							<tr>
								<th align=\"center\" width=\"20%\">".  ucwords(strtolower($department->getDepartmentName($departmentId)))."</th>
								<td width=\"40%\">
									<table align=\"center\" width=\"100%\" border=\"0\" cellpadding=\"5px\" cellspacing=\"5px\">";
					$i = 0;
					foreach ($creditHead as $accountHeadId) {
						echo "						
										<tr>
											<td align=\"right\" width=\"70%\"><font class=\"small\">".ucwords(strtolower($accountHead->getAccountHeadName($accountHeadId)))."</font></td>
											<th width=\"5%\">:</th>
											<td align=\"right\" width=\"*\"><font class=\"salaryPrint\">".number_format($credit[$i], 2, '.', '')."</font></td>
										</tr>";
						++$i;
					}
					
					echo "
									</table>
								</td>
								<td width=\"*\">
									<table align=\"center\" width=\"100%\" border=\"0\"  cellpadding=\"5px\" cellspacing=\"5px\">";
					$i = 0;
					foreach ($debitHead as $accountHeadId){
						echo "
										<tr>
											<td align=\"right\" width=\"70%\"><font class=\"small\">".ucwords(strtolower($accountHead->getAccountHeadName($accountHeadId)))."</td>
											<th width=\"5%\">:</th>
											<td align=\"right\" width=\"*\"><font class=\"salaryPrint\">".number_format(abs($debit[$i]), 2, '.', '')."</td>
										</tr>";
						++$i;	
					}
					
					echo "
									</table>
								</td>
							</tr>
                                                        <tr>
                                                            <th> Total Summay</th>
                                                            <td>
                                                                <table align=\"center\" width=\"100%\" border=\"0\" cellpadding=\"5px\" cellspacing=\"5px\">

										<tr>
											<td align=\"right\" width=\"70%\"><font class=\"small\">Total Earnings</font></td>
											<th width=\"5%\">:</th>
											<td align=\"right\" width=\"*\"><font class=\"salaryPrint\">".number_format($grossAmount, 2, '.', '')."</font></td>
										</tr>
									</table>
                                                            </td>
                                                            <td>
                                                                <table align=\"center\" width=\"100%\" border=\"0\" cellpadding=\"5px\" cellspacing=\"5px\">

										<tr>
											<td align=\"right\" width=\"70%\"><font class=\"small\">Total Deductions</font></td>
											<th width=\"5%\">:</th>
											<td align=\"right\" width=\"*\"><font class=\"salaryPrint\">".number_format(abs($grossDeduction), 2, '.', '')."</font></td>
										</tr>
									</table>
                                                            </td>
                                                            
                                                        </tr>
                                                        <tr>
                                                            <th colspan=\"2\"></th>                                                            
                                                            <td>
                                                                <table align=\"center\" width=\"100%\" border=\"0\" cellpadding=\"5px\" cellspacing=\"5px\">

										<tr>
											<td align=\"right\" width=\"70%\"><font class=\"small\">Net Pay</font></td>
											<th width=\"5%\">:</th>
											<td align=\"right\" width=\"*\"><font class=\"salaryPrint\">".number_format(($grossAmount + $grossDeduction), 2, '.', '')."</font></td>
										</tr>
									</table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th></th>
                                                            <td>
                                                                <table align=\"center\" width=\"100%\" border=\"0\" cellpadding=\"5px\" cellspacing=\"5px\">

										<tr>
											<td align=\"right\" width=\"70%\"><font class=\"small\">Institute Contribution</font></td>
											<th width=\"5%\">:</th>
											<td align=\"right\" width=\"*\"><font class=\"salaryPrint\">".number_format($collegeContribution[1], 2, '.', '')."</font></td>
										</tr>
									</table>
                                                            </td>
                                                            <td>
                                                                <table align=\"center\" width=\"100%\" border=\"0\" cellpadding=\"5px\" cellspacing=\"5px\">

										<tr>
											<td align=\"right\" width=\"70%\"><font class=\"small\">NPS</font></td>
											<th width=\"5%\">:</th>
											<td align=\"right\" width=\"*\"><font class=\"salaryPrint\">".number_format(abs($collegeContribution[0]), 2, '.', '')."</font></td>
										</tr>
									</table>
                                                            </td>
                                                        </tr>
						</table><br /><br />";
	            	
      			}
      		}
      	?>       
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