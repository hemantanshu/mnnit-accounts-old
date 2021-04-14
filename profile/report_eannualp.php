<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
////error_reporting(0);

require_once '../include/class.personalInfo.php';
require_once '../include/class.accountHead.php';
require_once '../include/class.reporting.php';
require_once '../include/class.employeeInfo.php';
require_once '../include/class.department.php';
require_once '../include/class.employeeType.php';

$allowance = new accountHead();
if(!$allowance->checkLogged())
  	$allowance->redirect('../');

$reporting = new reporting();
$personalInfo = new personalInfo();
$employeeInfo = new employeeInfo();
$department = new department();
$employeeType = new employeeType();

$flag = false;

if(isset ($_GET['type']) && isset ($_GET['value'])){
        $processingType = $_GET['type'];
        $processingValue = $_GET['value'];
}else
        $reporting->redirect('./report_eannual.php');

if(isset ($_GET['financial'])){
    $financialId = $_GET['financial'];
    $details = $reporting->getSessionDetails($financialId);

    $sDate = date('Ym', mktime(0, 0, 0, substr($details[2], 5, 2) -1 , 15, substr($details[2], 0, 4)));
    $eDate = date('Ym', mktime(0, 0, 0, substr($details[3], 5, 2) - 1 , 15, substr($details[3], 0, 4)));

    $flag = true;
}else{
    $sDate = $_GET['sdate'];
    $eDate = $_GET['edate'];
}

    $completeEmployeeId = array();
    $variable = $employeeInfo->getEmployeeIds(true, 'REPORT');

    if($processingType == "all"){
        foreach ($variable as $value) {
                array_push($completeEmployeeId, $value);
        }
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
        $reporting->palert("No Information Is There For The Salary Slip", './');
    }
    unset ($variable);
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
<meta http-equiv="Content-Type" content="text/html;charset=utf-7" />
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
				font-size:14px;
				font-weight:normal;
			}
                        font.extraSmall{
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
      	
        <?php
            foreach ($completeEmployeeId as $employeeId){
                $personalInfo->getEmployeeInformation($employeeId, true);

                //inputing the header image and information in the page
                echo "
                    <table border=\"0\" align=\"center\" width=\"100%\">
                        <tr>
                            <td colspan=\"2\"><hr size=\"2\" /></td>
                        </tr>
                        <tr>
                            <td align=\"center\" width=\"140px\" height=\"80px\"><img src=\"../img/mnnit_logo.gif\" alt=\"mnnit logo\" width=\"100\" height=\"80px\" align=\"left\" /></td>
                            <td align=\"center\" width=\"*\"><font class=\"bigheader\">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class=\"smallheader\">
                                                            ALLAHABAD - 211004<br /><br />
                                                            ACCOUNTS DEPARTMENT
                                                            </font></td>

                        </tr>
                    </table>";
                //displaying the personal information about the employee
                echo "
                    <table align=\"center\" width=\"100%\" border=\"0\">
                        <tr>
                        <td colspan=\"6\"><hr size=\"1\" /></td>
                    </tr>
                        <tr>
                        <td align=\"right\" width=\"20%\">Name</td>
                        <td width=\"3%\" align=\"center\">:</td>
                        <td align=\"left\" width=\"27%\">".$personalInfo->getName()."</td>
                        <td align=\"right\" width=\"20%\">Employee Code</td>
                        <td width=\"3%\" align=\"center\">:</td>
                        <td align=\"left\" width=\"27%\">".$personalInfo->getEmployeeCode()."</td>
                    </tr>
                    <tr>
                        <td height=\"5px\"></td>
                    </tr>
                    <tr>
                        <td align=\"right\" width=\"20%\">Department</td>
                        <td width=\"3%\" align=\"center\">:</td>
                        <td align=\"left\" width=\"27%\">".$department->getDepartmentName($personalInfo->getDepartment())."</td>
                        <td align=\"right\" width=\"20%\">Employee Type</td>
                        <td width=\"3%\" align=\"center\">:</td>
                        <td align=\"left\" width=\"27%\">".$employeeType->getEmployeeTypeName($personalInfo->getEmployeeType())."</td>
                    </tr>
                    <tr>
                        <td colspan=\"6\"><hr size=\"1\" /></td>
                    </tr>
                </table>";
                $toDisplay = 7;
                $completeAccountHeadIds = $reporting->getEmployeeAccountHeadIds($employeeId, $sDate, $eDate);
                $totalCols = count($completeAccountHeadIds);

                $earningHeads = $deductionHeads = array();
                $totalEarning = $totalDeduction = 0;
                $sumArray = array();

                foreach ($completeAccountHeadIds as  $value){
                    $nature = $allowance->getAccountHeadIdNature($value);
                    if($nature == 'c')
                        array_push ($earningHeads, $value);
                    else
                        array_push ($deductionHeads, $value);
                }
                $earningHeadCount = count($earningHeads);
                $deductionHeadCount = count($deductionHeads);

                $earningCols = $earningHeadCount > $toDisplay ? $toDisplay : $earningHeadCount;
                $deductionCols = $totalCols > $toDisplay ? ($toDisplay - $earningCols + 1) : $deductionHeadCount;
                $skip = 0;
                unset ($completeAccountHeadIds);

                //displaying the table headings
                echo "
                    <table align=\"center\" width=\"100%\" border=\"0\">
                        <tr>
                            <td align=\"center\" width=\"20%\" rowspan=\"2\">Month</td>
                            <td align=\"center\" colspan=\"".$earningCols."\">Earning Heads</td>
                            <td width=\"3%\" rowspan=\"2\" align=\"right\">|</td>
                            <td align = \"center\" colspan=\"".$deductionCols."\">Deduction Heads</td>
                        </tr>
                        <tr>";
                $i = 0;
                foreach ($earningHeads as $id){
                    if($i > $toDisplay)
                        break;
                    echo "<td width=\"9%\" align=\"right\"><font class=\"extraSmall\">".$allowance->getAccountHeadName($id)."</font></td>";
                    ++$i;
                }                
                foreach ($deductionHeads as $id){
                    if($i > $toDisplay)
                        break;
                    ++$skip;
                    echo "<td width=\"9%\" align=\"right\"><font class=\"extraSmall\">".$allowance->getAccountHeadName($id)."</font></td>";
                    ++$i;
                }
                echo "
                        </tr>
                        <tr>
                                <td colspan=\"10\"><hr size=\"1\" /></td>
                        </tr>";

                //header generation complete
                //inserting rows for each month now
                $sMonth = substr($sDate, 4, 2);
                $sYear = substr($sDate, 0 , 4);
                for($i = 0; $i <= 15; ++$i){
                    $date = date('Ym', mktime(0, 0, 0, ($sMonth + $i), 15, $sYear));
                    if($date > $eDate)
                        break;
                    echo "
                        <tr>
                                <th align=\"right\"><font class=\"small\">".$allowance->nameMonth($date)."</font></th>";
                    $j = 0;
                    foreach ($earningHeads as $accountHeadId){
                        $amount = $reporting->getSalaryAllowanceInfo($employeeId, $date, $accountHeadId, false);
                        $sumArray[$accountHeadId] += $amount;
                        $totalEarning += $amount;
                    }
                    foreach ($deductionHeads as $accountHeadId){
                        $amount = 0 - $reporting->getSalaryAllowanceInfo($employeeId, $date, $accountHeadId, false);
                        $sumArray[$accountHeadId] += $amount;
                        $totalDeduction += $amount;
                    }

                    foreach ($earningHeads as $accountHeadId){                        
                       $amount = $reporting->getSalaryAllowanceInfo($employeeId, $date, $accountHeadId, false);
                        if($j > $toDisplay)
                           break;                       
                       echo "<td align=\"right\"><font class=\"small\">".number_format($amount, 2, '.', '')."</font></td>";
                       ++$j;
                    }
                    if($j > $toDisplay)
                        continue;
                    echo "<th align=\"right\">|</th>";

                    foreach ($deductionHeads as $accountHeadId){                       
                       $amount = 0 - $reporting->getSalaryAllowanceInfo($employeeId, $date, $accountHeadId, false);
                        if($j > $toDisplay)
                           break;                       
                       echo "<td align=\"right\"><font class=\"small\">".number_format($amount, 2, '.', '')."</font></td>";
                        ++$j;
                    }
                    echo "</tr>
                          <tr>
                              <td height=\"3px\"></td>
                          </tr>";
                }
                echo "
                        <tr>
                            <th colspan=\"10\" height=\"10px\"><hr size=\"2\" /></th>
                        </tr>
                        <tr>
                            <td align=\"right\"><font class=\"small\">Total Summary</font>";
                $j = 0;
                foreach ($earningHeads as $accountHeadId){
                    if($j > $toDisplay)
                        break;
                    echo "<td align=\"right\"><font class=\"small\">".number_format($sumArray[$accountHeadId], 2, '.', '')."</font></td>";
                    ++$j;
                }
                echo "<th align=\"right\">|</th>";
                foreach ($deductionHeads as $accountHeadId){
                    if($j > $toDisplay)
                        break;
                    echo "<td align=\"right\"><font class=\"small\">".number_format($sumArray[$accountHeadId], 2, '.', '')."</font></td>";
                    ++$j;
                }
                echo "
                        </tr>
                        <tr>
                            <th colspan=\"10\" height=\"5px\"><hr size=\"2\" /></th>
                        </tr>
                        <tr>
                            <td align=\"center\" colspan=\"10\">Total Earnings : ".  number_format($totalEarning, 2, '.', '')."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Total Deductions : ".  number_format($totalDeduction, 2, '.', '')."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Net Total : ".  number_format(($totalEarning - $totalDeduction), 2, '.', '')."</td>
                        </tr>";
                echo "                  
                    </table>";
                echo "<p class=\"break\"></p>";
                //going for the second page display
                if($totalCols > $toDisplay){
                   echo "
                        <table border=\"0\" align=\"center\" width=\"100%\">
                            <tr>
                                <td colspan=\"2\"><hr size=\"2\" /></td>
                            </tr>
                            <tr>
                                <td align=\"center\" width=\"140px\" height=\"80px\"><img src=\"../img/mnnit_logo.gif\" alt=\"mnnit logo\" width=\"100\" height=\"80px\" align=\"left\" /></td>
                                <td align=\"center\" width=\"*\"><font class=\"bigheader\">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class=\"smallheader\">
                                                                ALLAHABAD - 211004<br /><br />
                                                                ACCOUNTS DEPARTMENT
                                                                </font></td>

                            </tr>
                        </table>";
                    //displaying the personal information about the employee
                echo "
                    <table align=\"center\" width=\"100%\" border=\"0\">
                        <tr>
                            <td colspan=\"6\"><hr size=\"1\" /></td>
                        </tr>
                            <tr>
                            <td align=\"right\" width=\"20%\">Name</td>
                            <td width=\"3%\" align=\"center\">:</td>
                            <td align=\"left\" width=\"27%\">".$personalInfo->getName()."</td>
                            <td align=\"right\" width=\"20%\">Employee Code</td>
                            <td width=\"3%\" align=\"center\">:</td>
                            <td align=\"left\" width=\"27%\">".$personalInfo->getEmployeeCode()."</td>
                        </tr>
                        <tr>
                            <td height=\"5px\"></td>
                        </tr>
                        <tr>
                            <td align=\"right\" width=\"20%\">Department</td>
                            <td width=\"3%\" align=\"center\">:</td>
                            <td align=\"left\" width=\"27%\">".$department->getDepartmentName($personalInfo->getDepartment())."</td>
                            <td align=\"right\" width=\"20%\">Employee Type</td>
                            <td width=\"3%\" align=\"center\">:</td>
                            <td align=\"left\" width=\"27%\">".$employeeType->getEmployeeTypeName($personalInfo->getEmployeeType())."</td>
                        </tr>
                        <tr>
                            <td colspan=\"6\"><hr size=\"1\" /></td>
                        </tr>
                    </table>";
                    //displaying the table headings
                    echo "
                    <table align=\"center\" width=\"100%\" border=\"0\">
                        <tr>
                            <td align=\"center\" width=\"20%\" rowspan=\"2\">Month</td>
                            <td align=\"center\" colspan=\"".($deductionHeadCount - $skip)."\">Deduction Heads</td>
                            <td colspan=\"".($toDisplay - 1 - $deductionHeadCount + $skip)."\"></td>    ";

                    echo "</tr>
                          <tr>";
                    $i = 0;
                    foreach ($deductionHeads as $id){
                        ++$i;
                        if($i <= $skip)
                            continue;
                        echo "<td width=\"9%\" align=\"right\"><font class=\"extraSmall\">".$allowance->getAccountHeadName($id)."</font></td>";

                    }
                    echo "
                            </tr>
                            <tr>
                                    <td colspan=\"10\"><hr size=\"1\" /></td>
                            </tr>";
                    for($i = 0; $i <= 15; ++$i){
                    $date = date('Ym', mktime(0, 0, 0, ($sMonth + $i), 15, $sYear));
                    if($date > $eDate)
                        break;
                    echo "
                        <tr>
                                <th align=\"right\" width=\"20%\"><font class=\"small\">".$allowance->nameMonth($date)."</font></th>";
                    $j = 0;
                    foreach ($deductionHeads as $accountHeadId){
                        ++$j;
                        if($j <= $skip)
                           continue;
                       $amount = 0 - $reporting->getSalaryAllowanceInfo($employeeId, $date, $accountHeadId, false);
                       echo "<td align=\"right\"><font class=\"small\">".number_format($amount, 2, '.', '')."</font></td>";

                    }
                    echo "</tr>
                          <tr>
                              <td height=\"3px\"></td>
                          </tr>";
                }
                echo "
                        <tr>
                            <th colspan=\"10\" height=\"10px\"><hr size=\"2\" /></th>
                        </tr>
                        <tr>
                            <td align=\"right\"><font class=\"small\">Total Summary</font>";
                $j = 0;

                foreach ($deductionHeads as $accountHeadId){
                    ++$j;
                    if($j <= $skip)
                        continue;
                    echo "<td align=\"right\"><font class=\"small\">".number_format($sumArray[$accountHeadId], 2, '.', '')."</font></td>";

                }
                echo "
                        </tr>
                        </table>";
                    
                }
            echo "<p class=\"break\"></p>";            
        }
        ?>      	
        <div id="print" align="center">
        	<table align="center" width="100%">
                <tr>
                    <td align="center"><br />
                    	<input type="hidden" name="id" value="<?php echo $id; ?>" />
                        <input type="hidden" name="date" value="<?php echo $month; ?>" />
                        <input type="button" style="width:250px" value="Print The Summary" onclick="window.print() "/>&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="submit" style="width:250px" value="Export To Excel" name="submit"  />&nbsp;&nbsp;&nbsp;
                        <input type="button" style="width:150px" value="Return Back" onclick="window.location='./report_eannual.php'" /><br />
                    </td>
                </tr>
        </table>
        </div>        
      </div>
      <div class="clearer"><span></span></div>
    </div>
  </div>
</div>
</body>
</html>
