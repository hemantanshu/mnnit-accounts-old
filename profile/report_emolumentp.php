<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);

require_once '../include/class.personalInfo.php';
require_once '../include/class.reporting.php';
require_once '../include/class.employeeInfo.php';
require_once '../include/class.department.php';

$department = new department();

if(!$department->checkLogged())
  	$department->redirect('../');
	
$reporting = new reporting();
$personalInfo = new personalInfo();	
$employeeInfo = new employeeInfo();
	
$sDate = $_POST['sDate'];
$eDate = $_POST['eDate'];
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
      	<form action="./report_emolumentex.php" method="post">
      	<table border="0" align="center" width="100%">
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>
            <tr>
                <td align="center" width="160px" height="111px"><img src="../img/mnnit_logo.gif" alt="mnnit logo" width="126" height="111px" align="left" /></td>
                <td align="center" width="*"><font class="bigheader">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class="smallheader">
                                                ALLAHABAD - 211004<br /><br />
                                                ACCOUNTS DEPARTMENT -- EMPLOYEE EMOLUMENT REPORT</font></td>
                
            </tr>
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>		
        </table>
        <table align="center" border="0" width="100%">       	
        <thead style="cursor:pointer">
        	<tr>
            	<th align="center" colspan="7">SHOWING EMOLUMENT REPORT FROM <?php echo $reporting->getNumber2Month(substr($sDate, 4, 2)).", ".substr($sDate, 0, 4);?> TO <?php echo $reporting->getNumber2Month(substr($eDate, 4, 2)).", ".substr($eDate, 0, 4);?></th>
            </tr>
            <tr>
            	<td colspan="7"><hr size="1" /></td>
            </tr>
            <tr>
            	<th width="3%">SN</th>
            	<th width="7%">Emp. Code</th>
            	<th width="25%" align="left">Name</th>
            	<th width="35%" align="left">Department</th>
            	<th align="right" width="10%">Earnings</th>
            	<th align="right" width="10%">Deductions</th>
            	<th align="right" width="*">Net Paid</th>
            </tr>
            
            <tr>
            	<td colspan="7"><br /><hr size="1" /><br /></td>
            </tr>
            </thead>
            <tbody>
            <?php 
            	$count = 0;
            	$i = 0;
            	while (true){              		
            		$employeeName = "employee".$count;
            		$checkbox = "checkbox".$count;
            		++$count;
            		            		
					if (!isset($_POST[$employeeName]))
						break;            		
					
					$employeeId = $_POST[$employeeName];					
            		$personalInfo->getEmployeeInformation($employeeId, true);
            		$amount = $reporting->getEmployeeSalaryEmolument($employeeId, $sDate, $eDate);
            		$sum = $amount[0] + $amount[1];		
            		
            		echo "
            			<tr>
			            	<td><input type=\"hidden\" name=\"$employeeName\" value=\"$employeeId\" />$count</td>
			            	<td>".$personalInfo->getEmployeeCode()."</td>
			            	<td align=\"left\">".ucwords(strtolower($personalInfo->getName()))."</td>
			            	<td align=\"left\">".$department->getDepartmentName($personalInfo->getDepartment())."</td>
			            	<td style=\"padding-right:5px\" align=\"right\">".number_format($amount[0], 2, '.', '')."</td>
			            	<td style=\"padding-right:5px\" align=\"right\">".number_format(abs($amount[1]), 2, '.', '')."</td>
			            	<td style=\"padding-right:5px\" align=\"right\">".number_format($sum, 2, '.', '')."</td>
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
                    	<input type="hidden" name="sDate" value="<?php echo $sDate; ?>" />
                    	<input type="hidden" name="eDate" value="<?php echo $eDate; ?>" />
                        <input type="button" style="width:250px" value="Print The Summary" onclick="window.print() "/>&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="submit" name="submit" value="Export To Excel" style="width:250px" />&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="button" style="width:150px" value="Return Back" onclick="window.location='./report_emolument.php'" /><br />
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