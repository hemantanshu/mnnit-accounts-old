<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);

require_once '../include/class.personalInfo.php';
require_once '../include/class.allowance.php';
require_once '../include/class.reporting.php';
require_once '../include/class.employeeInfo.php';


$allowance = new allowance();
if(!$allowance->checkLogged())
  	$allowance->redirect('../');

if (!isset($_POST['id']))  	
	if(!isset($_GET['sdate']) || !isset($_GET['edate']) || !isset($_GET['id']) || !isset($_GET['type']) || !isset($_GET['value']))
		$allowance->redirect('./');

$reporting = new reporting();
$personalInfo = new personalInfo();	
$employeeInfo = new employeeInfo();
	
$sDate = $_GET['sdate'] == "" ? $_POST['sdate'] : $_GET['sdate'];
$eDate = $_GET['edate'] == "" ? $_POST['edate'] : $_GET['edate'];
$allowanceId = $_GET['id'] == "" ? $_POST['id'] : $_GET['id'];
$processingType = $_GET['type'] == "" ? $_POST['type'] : $_GET['type'];
$processingValue = $_GET['value'] == "" ? $_POST['value'] : $_GET['value'];

$variable = $reporting->getDistinctSalaryProcessedEmployeeId($sDate, $eDate);
$completeEmployeeId = array();

if($processingType == "all"){
	$completeEmployeeId = $variable;
}elseif($processingType == "employeeType"){
	foreach ($variable as $value) {
		$personalInfo->getEmployeeInformation($value, true);
		if($personalInfo->getEmployeeType() == $processingValue)
				array_push($completeEmployeeId, $value);
	}
}elseif($processingType == "designation"){
	foreach ($variable as $value){
		$rankId = $employeeInfo->getEmployeeRankIds($value, true);
		foreach ($rankId as $options) {
			$details = $employeeInfo->getRankDetails($options, true);
			if($details[2] == $processingValue)
					array_push($completeEmployeeId, $value);
		}
	}
}elseif($processingType == "department"){
	foreach ($variable as $value){
		$personalInfo->getEmployeeInformation($value, true);
		if($personalInfo->getDepartment() == $processingValue)
					array_push($completeEmployeeId, $value);
	}
}elseif($processingType == "individual"){
		array_push($completeEmployeeId, $processingValue);
}else{
	$allowance->palert("No Employee Record Found For The Given Details", './report_allowance.php');
}

unset($variable);
ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- Allowance Summary</title>
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
      	<form action="report_accountheade.php" method="post">
      	<table border="0" align="center" width="100%">
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>
            <tr>
                <td align="center" width="160px" height="111px"><img src="../img/mnnit_logo.gif" alt="mnnit logo" width="126" height="111px" align="left" /></td>
                <td align="center" width="*"><font class="bigheader">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class="smallheader">
                                                ALLAHABAD - 211004<br /><br />
                                                ACCOUNTS DEPARTMENT -- ALLOWANCE STATEMENT</font></td>
                
            </tr>
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>		
        </table>
        <table id="myTable" class="tablesorter" width="100%">
		    <thead>
            <?php
            	$validMonths = array();
            	for ($i= $sDate; $i <= $eDate; ++$i){
            		if ($reporting->isSalaryDataAvailiable($i))
            			array_push($validMonths, $i);
            	}           
            		
            	$colspan = sizeof($validMonths, 0) + 4;
            	
                echo "
                	<tr>
		            	<td align=\"center\" colspan=\"$colspan\"><font class=\"error\">MONTHLY REPORT OF : ";
                	if($allowanceId == "total")
                		echo "NET SALARY PAID";
                	elseif ($allowanceId == "gross")
                		echo "GROSS SALARY PAID";
                	elseif ($allowanceId == "deduction")
                		echo "TOTAL DEDUCTIONS";
                	else
                		echo $allowance->getAllowanceTypeName($allowanceId); 
                echo  "</font><br /><hr size=\"2\" /></td>
		            </tr>
                	<tr style=\"cursor:pointer\">
		            	<th>SN</th>
		                <th>Code</th>
		                <th align=\"left\">Name</th>
		            ";	
                foreach ($validMonths as $i)
                		echo "<td align=\"right\">".$i."</td>";
               	echo "
               		</tr>
			            <tr>
			            	<td colspan=\"$colspan\"><hr size=\"1\" /></td>
			            </tr>";
                ?>                
            </thead>
            <tbody>
            <?php           	
            	$i = 0;
            	foreach ($completeEmployeeId as $employeeId){
            		$employeeName = "employee".$i;
            		++$i;
            		$personalInfo->getEmployeeInformation($employeeId, true);
            		echo "
            			<tr>
			            	<td align=\"center\"><input type=\"hidden\" value=\"$employeeId\" name=\"$employeeName\" />".$i."</td>
			                <td align=\"center\">".$personalInfo->getEmployeeCode()."</td>
			                <td align=\"left\">".$personalInfo->getName()."</td>";
            		foreach ($validMonths as $count)
            			echo "  <td align=\"right\">".number_format($reporting->getSalaryAllowanceInfo($employeeId, $count, $allowanceId, true), 2, '.', '')."</td>";
			        echo "
			        	</tr>
			        	<tr>
			            	<td height=\"5px\"></td>
			            </tr>";		
            	}
            ?>
            </tbody>       
        </table>
        <div id="logout" align="center"><br />            	
            	<input type="hidden" name="edate" value="<?php echo $eDate; ?>" />
                <input type="hidden" name="sdate" value="<?php echo $sDate; ?>" />
                <input type="hidden" name="id" value="<?php echo $allowanceId; ?>" />
                <input type="hidden" name="type" value="<?php echo $processingType; ?>" />
                <input type="hidden" name="value" value="<?php echo $processingValue; ?>" />  
                <input type="hidden" name="option" value="true" />
                              	
                <input name="back" type="button" value="Print The Report" onclick="window.print()" style="width:200px" />&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="b3" type="submit" value="Export To Excel" style="width:200px" />&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="b2" type="button" value="Return Back" onclick="location='./report_accounthead.php'" style="width:200px" /> &nbsp;&nbsp;&nbsp;
                
		</div>
        </form>     
      </div>      
      <div class="clearer"><span></span></div>
    </div>    
  </div>
</div>
</body>
</html>