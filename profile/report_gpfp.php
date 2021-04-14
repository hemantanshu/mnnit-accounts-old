<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);

require_once '../include/class.personalInfo.php';
require_once '../include/class.employeeInfo.php';
require_once '../include/class.gpftotal.php';
require_once '../include/class.department.php';

$gpfTotal = new gpfTotal();
if(!$gpfTotal->checkLogged())
  	$gpfTotal->redirect('../');

if(!isset($_GET['type']) || !isset($_GET['value']))
	$gpfTotal->redirect('./');


$personalInfo = new personalInfo();	
$employeeInfo = new employeeInfo();
$department = new department();
	
$processingType = $_GET['type'];
$processingValue = $_GET['value'];
$fundType = $_GET['option'];


$completeEmployeeId = array();
$variable = $employeeInfo->getEmployeeIds(true, 'all');

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
}
if(!sizeof($completeEmployeeId, 0))
	$gpfTotal->palert("No Employee Record Found For The Given Details", './report_gpf.php');

unset($variable);
ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- Total GPF Summary</title>
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
      	<form action="./report_gpfe.php" method="post">
      	<table border="0" align="center" width="100%">
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>
            <tr>
                <td align="center" width="160px" height="111px"><img src="../img/mnnit_logo.gif" alt="mnnit logo" width="126" height="111px" align="left" /></td>
                <td align="center" width="*"><font class="bigheader">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class="smallheader">
                                                ALLAHABAD - 211004<br /><br />
                                                ACCOUNTS DEPARTMENT -- <?php echo strtoupper($fundType); ?> STATEMENT</font></td>
                
            </tr>
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>		
        </table>
        <table align="center" border="0" width="100%">                                                   
        	<tr>
            	<th width="5%">SN</th>
                <th width="10%">Emp Code</th>
                <th align="left" width="30%">Name</th>
                <th align="left" width="40%">Department</th>                
                <th align="right" width="*">GPF Balance</th>
            </tr>
      		<tr>
            	<td colspan="5"><br /><hr size="2" /><br /></td>
            </tr>
			<?php
            	$count = 0; 
            	foreach ($completeEmployeeId as $employeeId){
            		$employeeName = "employee".$count;
                    $amount = $gpfTotal->getEmployeeTotalFundBalance($employeeId, $fundType);
                    if($amount == "" || $amount == "0")
                        continue;
            		++$count;
            		$personalInfo->getEmployeeInformation($employeeId, true);
            		echo "
            			<tr>
			            	<th align=\"center\">$count</th>
			                <th align=\"center\">".$personalInfo->getEmployeeCode()."</th>
			                <th align=\"left\">
			                	<input type=\"hidden\" name=\"$employeeName\" value=\"$employeeId\" />						
								<a href=\"./salary_gpfview.php?id=$employeeId\" target=\"_parent\">".$personalInfo->getName()."</a></th>
			                <th align=\"left\">".$department->getDepartmentName($personalInfo->getDepartment())."</th>
			                <th align=\"right\" style=\"padding-right:5px\">".number_format($amount, 2, '.', '')."</th>
			            </tr>
			            <tr>
			            	<td height=\"3px\"></td>
			            </tr>";            	
            	}
            ?> 
        </table>
        <div id="print" align="center">
        	<table align="center" width="100%">
                <tr>
                    <td align="center"><br />
                        <input type="button" style="width:250px" value="Print The Summary" onclick="window.print() "/>&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="button" value="Export To Excel" onclick="window.location='./report_gpfe.php?type=<?php echo $processingType; ?>&value=<?php echo $processingValue; ?>&option=<?php echo $fundType?>'" style="width:200px" /> &nbsp;&nbsp;&nbsp;
                        <input type="button" style="width:150px" value="Return Back" onclick="window.location='./report_gpf.php'" /><br />
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