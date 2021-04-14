<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
////error_reporting(0);

require_once '../include/class.personalInfo.php';
require_once '../include/class.allowance.php';
require_once '../include/class.reporting.php';
require_once '../include/class.employeeInfo.php';
require_once '../include/class.department.php';

$allowance = new allowance();

if(!$allowance->checkLoanOfficerLogged())
  	$allowance->redirect('../');

if(!isset($_GET['id']) || !isset($_GET['date']))
	$allowance->redirect('./');
	  	
$month = $_GET['date'];
$id = $_GET['id'];	

$reporting = new reporting();
$personalInfo = new personalInfo();	
$employeeInfo = new employeeInfo();
$department = new department();
	
$completeEmployeeId = $employeeInfo->getEmployeeIds(true, 'all');

ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- Total Head Summary</title>
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
      	<form action="./report_mheade.php" method="post">
      	<table border="0" align="center" width="100%">
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>
            <tr>
                <td align="center" width="160px" height="111px"><img src="../img/mnnit_logo.gif" alt="mnnit logo" width="126" height="111px" align="left" /></td>
                <td align="center" width="*"><font class="bigheader">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class="smallheader">
                                                ALLAHABAD - 211004<br /><br />
                                                ACCOUNTS DEPARTMENT -- ALLOWANCE / DEDUCTION REPORT STATEMENT</font></td>
                
            </tr>
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>		
            <tr>
            	<th colspan="2">REPORT FOR ALLOWANCE / DEDUCTION : <?php echo $allowance->getAllowanceTypeName($id); ?></th>
            </tr>
        </table>
        <table id="myTable" class="tablesorter" width="100%">
        	<thead>
        		<tr>
        			<th>Emp. Code</th>
	        		<th>Name</th>
	        		<th align="left">Department</th>
	        		<th align="right">Amount</th>
        		</tr>
                <tr>
                    <td colspan="4"><hr size="2" /></td>
                </tr>
        	</thead>                    	
        	<tbody>
        		<?php 
        		foreach ($completeEmployeeId as $employeeId){
        			$personalInfo->getEmployeeInformation($employeeId, true);
        			$amount = $reporting->getSalaryAllowanceInfo($employeeId, $month, $id, true);
        			if ($amount == 0)
        				continue;
        			echo "
        				<tr>
		        			<th>".$personalInfo->getEmployeeCode()."</th>
		                    <th align=\"left\">".$personalInfo->getName()."</th>
		                    <th align=\"left\">".$department->getDepartmentName($personalInfo->getDepartment())."</th>
		                    <th align=\"right\"  style=\"padding-right:20px\">".number_format($amount, 2, '.', '')."</th>
		        		</tr>";
        		}
        			
        		?>        		
        	</tbody>       	          
        </table>
        <div id="print" align="center">
        	<table align="center" width="100%">
                <tr>
                    <td align="center"><br />
                    	<input type="hidden" name="id" value="<?php echo $id; ?>" />
                        <input type="hidden" name="date" value="<?php echo $month; ?>" />
                        <input type="button" style="width:250px" value="Print The Summary" onclick="window.print() "/>&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="submit" style="width:250px" value="Export To Excel" name="submit"  />&nbsp;&nbsp;&nbsp;
                        <input type="button" style="width:150px" value="Return Back" onclick="window.location='./report_mhead.php'" /><br />
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