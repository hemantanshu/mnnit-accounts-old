<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);

require_once '../include/class.personalInfo.php';
require_once '../include/class.accountHead.php';
require_once '../include/class.reporting.php';
require_once '../include/class.employeeInfo.php';
require_once '../include/class.department.php';

$allowance = new accountHead();

if(!$allowance->checkLogged())
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
      	<form action="" method="post">
      	<table border="0" align="center" width="100%">
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>
            <tr>
                <td align="center" width="160px" height="111px"><img src="../img/mnnit_logo.gif" alt="mnnit logo" width="126" height="111px" align="left" /></td>
                <td align="center" width="*"><font class="bigheader">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class="smallheader">
                                                ALLAHABAD - 211004<br /><br />
                                                ACCOUNTS DEPARTMENT -- ACCOUNT HEAD STATEMENT<br />
                                                FOR THE MONTH <?php echo $allowance->nameMonth($month); ?></font></td>
                
            </tr>
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>		
            <tr>
            	<th colspan="2">REPORT FOR ACCOUNT HEAD : <?php echo $allowance->getAccountHeadName($id); ?></th>
            </tr>
        </table>
        <table id="myTable" class="tablesorter" width="100%" cellpadding="5px" cellspacing="14px">
        	<thead>
        		<tr>
        			<th>Emp. Code</th>
	        		<th>Name</th>
	        		<th align="left">Department</th>
	        		<th align="right">Earnings</th>
                                <th align="right">Deductions</th>
        		</tr>
                <tr>
                    <td colspan="5"><hr size="2" /></td>
                </tr>
        	</thead>                    	
        	<tbody>
        		<?php 
        		$sum = 0;
                        $debitTotal = $creditTotal = 0;
                                                
        		foreach ($completeEmployeeId as $employeeId){
        			$personalInfo->getEmployeeInformation($employeeId, true);
        			$amount = $reporting->getSalaryAllowanceInfo($employeeId, $month, $id, false);
        			if ($amount == 0)
        				continue;
                                $debit=$credit=0;
                                if($amount < 0)
                                        $debit = abs($amount);
                                else
                                        $credit = abs($amount);
        			$sum += $amount;	
        			echo "
        				<tr>
		        			<th><font class=\"small\">".$personalInfo->getEmployeeCode()."</font></th>
		                    <th align=\"left\"><font class=\"small\">".$personalInfo->getName()."</th>
		                    <th align=\"left\"><font class=\"small\">".$department->getDepartmentName($personalInfo->getDepartment())."</font></th>
		                    <th align=\"right\"  style=\"padding-right:20px\"><font class=\"small\">".number_format($credit, 2, '.', '')."</font></th>
                                        <th align=\"right\"  style=\"padding-right:20px\"><font class=\"small\">".number_format($debit, 2, '.', '')."</font></th>
		        		</tr>";
        		}
        			
        		?>        		
        	</tbody>
            	<tr>
                	<td colspan="5"><hr size="3" /></td>
                </tr>
        		<tr>
        			<th colspan="3">TOTAL SUM AMOUNT</th>
        			<th align="right" colspan="2" style="padding-right:20px"><?php echo number_format(abs($sum), 2, '.', ''); ?></th>
        		</tr> 
                <tr>
                	<td colspan="5"><hr size="3" /></td>
                </tr>      	          
        </table>
        <div id="print" align="center">
        	<table align="center" width="100%">
                <tr>
                    <td align="center"><br />                    	
                        <input type="button" style="width:250px" value="Print The Summary" onclick="window.print() " style="width:200px" />&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="button" style="width:200px" value="Export To Excel" onclick="window.location='report_mheadae.php?id=<?php echo $id;?>&date=<?php echo $month; ?>&type=false'"/>&nbsp;&nbsp;&nbsp;
                        <input type="button" value="Return Back" onclick="window.location='./report_mhead.php'"  style="width:200px"/><br />
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
