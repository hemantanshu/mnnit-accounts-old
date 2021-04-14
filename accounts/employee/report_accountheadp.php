<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);

require_once '../include/class.personalInfo.php';
require_once '../include/class.accountHead.php';
require_once '../include/class.reporting.php';

$allowance = new accountHead();
$employeeId = $allowance->checkEmployeeLogged();
if(!$employeeId)
  	$allowance->redirect('../');

if(isset ($_POST) && count($_POST) > 0 && $_POST['submit'] == "Process The Report"){
    $sDate = $_POST['syear'].(strlen($_POST['smonth']) > 1 ? $_POST['smonth'] : '0'.$_POST['smonth']);
    $eDate = $_POST['eyear'].(strlen($_POST['emonth']) > 1 ? $_POST['emonth'] : '0'.$_POST['emonth']);    
}else
	$allowance->redirect('./');
  	
  	
$month = $_POST['month'];	

$reporting = new reporting();
$personalInfo = new personalInfo();	

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
      	<form action="./report_accountheade.php" method="post">
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
        	
        	        	
        	$colspan = sizeof($completeAllowanceIds, 0) + 3;
        	
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
                        			$allowanceName = strtoupper($allowance->getAccountHeadName($allowanceId));
                        		echo "			                        	
			                            <th><input type=\"hidden\" name=\"$variable\" value=\"$allowanceId\" />".$name."</th>
			                            <th align=\"left\">$allowanceName</th>";                        		
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
                		echo "<th align=\"right\">".$name."</th>";
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
			            	<th><input type=\"hidden\" name=\"$date\" value=\"$month\" />".$i."</th>
			                <th align=\"right\">".$allowance->nameMonth($month)."</th>";
            		foreach ($completeAllowanceIds as $allowanceId)
            			echo "  <th align=\"right\">".number_format($reporting->getSalaryAllowanceInfo($employeeId, $month, $allowanceId, false), 2, '.', '')."</th>";
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
                	<input type="hidden" name="sdate" value="<?php echo $sDate; ?>" />
                	<input type="hidden" name="edate" value="<?php echo $eDate; ?>" />
                	<input type="button" value="Get Print" onclick="window.print()" style="width:150px" />
                    <input type="submit" name="submit" value="Export Data To Excel" style="width:250px" onclick="return excelExport(); " />&nbsp;&nbsp;&nbsp;
                    <input type="button" value="Return Back" onclick="window.location='./report_accounthead.php'" style="width:150px" /></td>      
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