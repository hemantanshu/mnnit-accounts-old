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

if(isset ($_POST) && count($_POST) > 0){
    if($_POST['submit'] == "View Report of This Employee Type"){
        $processingType = "employeeType";
        $processingValue = $_POST['employeeType'];
    }
    if($_POST['submit'] == "View Report Of This Department"){
        $processingType = "department";
        $processingValue = $_POST['department'];
    }
    if($_POST['submit'] == "View Report Of This Designation"){
        $processingType = "designation";
        $processingValue = $_POST['designation'];
    }
    if($_POST['submit'] == "View Report Of This Employee"){
        $processingType = "individual";
        $processingValue = $_POST['employeeId'];
    }
    if($_POST['submit'] == "View Report Of All Employees"){
        $processingType = "all";
        $processingValue = $_POST['employee'];
    }        
    if($processingType == ""){
    	$processingType = $_POST['type'];
    	$processingValue = $_POST['value'];
    }
}else
	$allowance->redirect('./');
  	
  	
$month = $_POST['month'];	

$reporting = new reporting();
$personalInfo = new personalInfo();	
$employeeInfo = new employeeInfo();
	
$completeEmployeeId = array();
	
if($processingType == "individual")
	array_push($completeEmployeeId, $processingValue);
else{
	$variable = $reporting->getDistinctSalaryProcessedEmployeeId($month, $month);	
	
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
	}
}
if(!sizeof($completeEmployeeId, 1))
	$allowance->palert("No Employee Record Found For The Given Details", './report_mallowancea.php');


unset($variable);
ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Employee Allowance Comparative Statement</title>
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

#logout {
	display: none;
}
</style>


</head>
<body onload="window.print();"> 
<div class="main">
	<div class="container">
      	<form action="./report_mallowancee.php" method="post">
        <table width="100%" align="center" border=".2">
            <tr>
                <td colspan="3" width="100%">
                    <table border="0" align="center" width="100%">
                        <tr>
                            <td align="center" width="160" height="111px"><img src="../img/mnnit_logo.gif" alt="mnnit logo" width="126" height="111px" align="left" /></td>
                            <td align="center" width="743"><font class="bigheader">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class="smallheader">
                                                            ALLAHABAD - 211004<br /><br />
                                                            ALLOWANCE REPORT GENERATION </font>
                            </td>
                            
                        </tr>		
                    </table>
                </td>
            </tr>
        </table>
        <table align="center" border="0" width="100%">
        <?php 
        	$completeAllowanceIds = array();        	
        	if(isset($_POST['total']))
        		array_push($completeAllowanceIds, 'total');
        	if(isset($_POST['gross']))
        		array_push($completeAllowanceIds, 'total');
        	if(isset($_POST['deduction']))
        		array_push($completeAllowanceIds, 'total');
        	
        	$i = 0;
        	while (true){
        		$allowanceName = "allowance".$i;
        		++$i;        		
        		if(!isset($_POST[$allowanceName]))
        			break;
        			
        		array_push($completeAllowanceIds, $_POST[$allowanceName]);
        	}        	
        	$colspan = sizeof($completeAllowanceIds, 0) + 4;
        	
        ?>
      		<tr>
            	<td colspan="<?php echo $colspan; ?>" align="center"><font class="green">ALLOWANCE REPORT OF FOR THE MONTH OF </font><font class="error"><?php echo $allowance->nameMonth($month); ?></font></td>
            </tr>            
            <tr>
            	<td colspan="<?php echo $colspan; ?>"><hr size="2" /></td>
            </tr>
            <tr>
            	<td colspan="<?php echo $colspan; ?>" align="center">
                	<table border="0" width="100%" align="center">
                    	<tr>                 
                            <td width="5%">Code</td>
                            <td width="28%" align="left">Allowance Type</td>
                            <td width="5%">Code</td>
                            <td width="28%" align="left">Allowance Type</td>
                            <td width="5%">Code</td>
                            <td width="*" align="left">Allowance Type</td>                            
                        </tr>
                        <tr>
                        	<td height="3px"></td>
                        </tr>
                        <?php 
                        	$i = 0;
                        	foreach ($completeAllowanceIds as $allowanceId){                        		
                        		$variable = "allowance".$i;                        		
                        		if($i % 3 == 0 && $i != 0)
                        			echo "</tr><tr>";
                        		elseif($i == 0)
                        			echo "<tr>";
								++$i;
								$name = "ACT".$i;
                        		if($allowanceId == 'total')
                        			$allowanceName = "NET SALARY PAID";
                        		elseif ($allowanceId == 'gross')
                        			$allowanceName = "GROSS SALARY PAID";
                        		elseif ($allowanceId == 'deduction')
                        			$allowanceName = "TOTAL DEDUCTION";
                        		else 
                        			$allowanceName = strtoupper($allowance->getAllowanceTypeName($allowanceId));
                        		echo "			                        	
			                            <th><input type=\"hidden\" name=\"$variable\" value=\"$allowanceId\" />".$name."</th>
			                            <td align=\"left\">$allowanceName</td>";                        		
                        	}
                        	echo "</tr>"
                        ?>                        
                    </table>
                </td>
            </tr>
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
            	<td colspan="<?php echo $colspan; ?>"><hr size="2" /></td>
            </tr>
            <tr>
            	<th>S.N</th>
                <th>Code</th>
                <th align="left">Name</th>
                <?php 
                	$count = 1;
                	foreach ($completeAllowanceIds as $allowanceId){
                		$name = "ACT".$count;
                		++$count;
                		echo "<td align=\"right\">$name</td>";
                	}
                ?>                
            </tr>
            <tr>
            	<td colspan="<?php echo $colspan; ?>"><hr size="1" /></td>
            </tr>
      		<tr>
            	<td height="10px"></td>
            </tr>
            <?php 
            	$i = 0;            	
            	foreach ($completeEmployeeId as $employeeId){
            		$employeeName = "employee".$i;
            		$personalInfo->getEmployeeInformation($employeeId, true);
            		++$i;
            		
            		echo "
            			<tr>
			            	<td><input type=\"hidden\" name=\"$employeeName\" value=\"$employeeId\" />".$i."</td>
			                <td>".$personalInfo->getEmployeeCode()."</td>
			                <td align=\"left\">".$personalInfo->getName()."</td>";
            		foreach ($completeAllowanceIds as $allowanceId)
            			echo "  <td align=\"right\">".number_format($reporting->getSalaryAllowanceInfo($employeeId, $month, $allowanceId, true), 2, '.', '')."</td>";
            		echo "</tr>
						  <tr>
						  	<td height=\"5px\"></td>
						  </tr>";
            	}
            ?>      
            <tr>
            	<td height="10px"></td>
            </tr> 
            <tr>
            	<td colspan="<?php echo $colspan; ?>"><hr size="2" /></td>
            </tr>           
            <tr>
                <td colspan="<?php echo $colspan; ?>" align="center" style="padding-top:10px; padding-bottom:10px;"><font class="salaryPrint">This is a computer generated statement and does not need any signature.</font><font size="1.2px"> Designed and Developed By Hemant Kumar Sah B.Tech (ECE-2011)</font></td>
            </tr>
            <tr>
            	<td colspan="<?php echo $colspan; ?>"><hr size="2" /></td>
            </tr>
        </table>
        <div id="print">
		<table width="100%" align="center">
        	<tr>
      			<td colspan="<?php echo $colspan; ?>" align="center">
                	<input type="hidden" name="value" value="<?php echo $processingValue; ?>" />
                	<input type="hidden" name="month" value="<?php echo $month; ?>" />
                    <input type="hidden" name="type" value="<?php echo $processingType; ?>" />
                    
                    <input type="submit" name="submit" value="Export Data To Excel" style="width:250px" />&nbsp;&nbsp;&nbsp;
                    <input type="button" value="Return Back" onclick="window.location='./report_mallowances.php'" style="width:150px" /></td>      
            </tr>	
            <tr>
            	<td height="50px"></td>
            </tr>
        </table>        
        </div>        
        </form>
     </div>
     </div>
</body>
</html>