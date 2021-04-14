<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);

require_once '../include/class.personalInfo.php';
require_once '../include/class.allowance.php';
require_once '../include/class.reporting.php';
require_once '../include/class.department.php';
require_once '../include/class.employeeType.php';


$allowance = new allowance();

if(!$allowance->checkLogged())
  	$allowance->redirect('../');

if(isset ($_POST) && count($_POST) > 0 && $_POST['submit'] == "Process The Report"){
    $sDate = $_POST['syear'].(strlen($_POST['smonth']) > 1 ? $_POST['smonth'] : '0'.$_POST['smonth']);
    $eDate = $_POST['eyear'].(strlen($_POST['emonth']) > 1 ? $_POST['emonth'] : '0'.$_POST['emonth']);
    $employeeId = $_POST['employeeId'];    
}else
	$allowance->redirect('./');
  	
$reporting = new reporting();
$personalInfo = new personalInfo();	
$department = new department();
$employeeType = new employeeType();

$personalInfo->getEmployeeInformation($employeeId, true);

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
      	<form action="./report_employeeale.php" method="post">
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
        <table align="center" width="100%" border="0">
        	<tr>
            	<td height="20px"></td>
            </tr>
        	<tr>
            	<td align="right" width="15%">Name</td>
                <td align="center" width="5%">:</td>
                <th align="left" width="30%"><?php echo $personalInfo->getName(); ?></th>
                <td align="right" width="15%">Employee Code</td>
                <td align="center" width="5%">:</td>
                <th align="left" width="30%"><?php echo $personalInfo->getEmployeeCode(); ?></th>                
            </tr>
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
            	<td align="right">Department</td>
                <td align="center">:</td>
                <th align="left"><?php echo $department->getDepartmentName($personalInfo->getDepartment()); ?></th>
                <td align="right">Employee Type</td>
                <td align="center">:</td>
                <th align="left"><?php echo $employeeType->getEmployeeTypeName($personalInfo->getEmployeeType()); ?></th>
            </tr>
            <tr>
            	<td height="20px"></td>
            </tr>
        </table>
        <table align="center" border="0" width="100%">
        <?php 
        	
        	$completeAllowanceIds = array();
                        $i = 0;  
                        while ($i < 3){
                        	$checkbox = "checkboxs".$i;
                        	if($_POST[$checkbox] == 'y'){
                        		if($i == 0)
                        			array_push($completeAllowanceIds, 'total');
                        		elseif ($i == 1)	
                        			array_push($completeAllowanceIds, 'gross');
                        		else 
                        			array_push($completeAllowanceIds, 'deduction');                        		
                        	}
                        	++$i;
                        }
                        $i = 0;
                        while (true){
                        	$checkbox = "checkbox".$i;
                        	$allowanceName = "allowance".$i;
                        	if(!isset($_POST[$allowanceName]))
                        		break;                        	
                        	++$i;                        	                        	
                        	if($_POST[$checkbox] == 'y'){
	                        	$allowanceId = $_POST[$allowanceName];
	                        	array_push($completeAllowanceIds, $allowanceId);
                        	}                        	
                        }   
            if (sizeof($completeAllowanceIds, 0))
            	$colspan = sizeof($completeAllowanceIds, 0) + 3;
            else 
            	$allowance->palert("Please Select Any Allowance Type", "./report_employeeal.php");
        	
        ?>
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
			                            <td><input type=\"hidden\" name=\"$variable\" value=\"$allowanceId\" />".$name."</td>
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
                <th align="right">Month</th>
                <?php 
                	$count = 1;
                	foreach ($completeAllowanceIds as $allowanceId){
                		$name = "ACT".$count;
                		++$count;
                		echo "<td align=\"right\">".$name."</td>";
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
            	$validMonths = array();
            	for ($i= $sDate; $i <= $eDate; ++$i){
            		if ($reporting->isSalaryDataAvailiable($i))
            			array_push($validMonths, $i);
            	}
            	
            	$i = 0;            	
            	foreach ($validMonths as $month){
            		$date = "date".$i;            		
            		++$i;            		
            		echo "
            			<tr>
			            	<td><input type=\"hidden\" name=\"$date\" value=\"$month\" />".$i."</td>
			                <td align=\"right\">".$allowance->nameMonth($month)."</td>";
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
      			<td colspan="<?php echo $colspan; ?>" align="center"><br /><br />
                	<input type="hidden" name="sdate" value="<?php echo $sDate; ?>" />
                	<input type="hidden" name="edate" value="<?php echo $eDate; ?>" />
                	<input type="hidden" name="employeeId" value="<?php echo $employeeId; ?>" />                	
                	<input type="button" value="Get Print" onclick="window.print()" style="width:250px" />
                    <input type="submit" name="submit" value="Export Data To Excel" style="width:250px" />&nbsp;&nbsp;&nbsp;
                    <input type="button" value="Return Back" onclick="window.location='./report_employeeal.php'" style="width:150px" /></td>      
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