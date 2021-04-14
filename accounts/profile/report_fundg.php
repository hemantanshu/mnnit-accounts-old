<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);

require_once '../include/class.department.php';
require_once '../include/class.personalInfo.php';
require_once '../include/class.employeeInfo.php';
require_once '../include/class.employeeType.php';
require_once '../include/class.designation.php';
require_once '../include/class.interest.php';


$department = new department();
if(!$department->checkLogged())
        $department->redirect('../');
 
if(isset ($_GET['session']) && isset ($_GET['type']) && isset ($_GET['value'])){
    $processingType = $_GET['type'];
    $processingValue = $_GET['value'];
    $sessionId = $_GET['session'];
}else
    $department->redirect('./report_fund.php');


$employeeInfo = new employeeInfo();
$personalInfo = new personalInfo();
$employeeType = new employeeType();
$designation = new designation();
$interest = new interest();
   
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
    $department->palert("No Information Is There For The Fund Slip", './');
}
$sessionDetails = $department->getSessionDetails($sessionId);
$startMonth = date("Ym", mktime(0, 0, 0, substr($sessionDetails[2], 5, 2) - 1, 15, substr($sessionDetails[2], 0 , 4)));
$endMonth = date("Ym", mktime(0, 0, 0, substr($sessionDetails[3], 5, 2), 15, substr($sessionDetails[3], 0 , 4))); 

unset($variable);

function printHeader($name){
	echo "
		<table border=\"0\" align=\"center\" width=\"100%\">
            <tr>
                <td colspan=\"2\"><hr size=\"2\" /></td>
            </tr>
            <tr>
                <td align=\"center\" width=\"160px\" height=\"111px\"><img src=\"../img/mnnit_logo.gif\" alt=\"mnnit logo\" width=\"126\" height=\"111px\" align=\"left\" /></td>
                <td align=\"center\" width=\"*\"><font class=\"bigheader\">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class=\"smallheader\">
                                                ALLAHABAD - 211004<br /><br />
                                                ACCOUNTS DEPARTMENT -- GPF FUND STATEMENT<br />
                                                FOR THE SESSION ".$name."</font></td>
                
            </tr>
            <tr>
                <td colspan=\"2\"><hr size=\"2\" /></td>
            </tr>
        </table>";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- GPF Annual Summary</title>
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

<body onload="window.print()">
<div>  
  <div class="container">
    <div class="main">
      <div class="contentlarge">          
        <?php            
            foreach($completeEmployeeId as $employeeId){
                $amount =   $interest->getFundAmount($employeeId, "gpf", $startMonth);
                $interestPaidLastSession = $interest->getFundAmount($employeeId, "gpf", $startMonth, 'i');
                $amount += $interestPaidLastSession;
                
                if($amount == "" || $amount == 0) //this employee doesnt have gpf account
                    continue;     
                $personalInfo->getEmployeeInformation($employeeId, true);
                printHeader($sessionDetails[1]); //printing the MNNIT Logo & Header
                
                echo "
                    <table align=\"center\" width=\"100%\" border=\"1\">
                        <tr>
                            <td width=\"100%\" align=\"center\">
                                <table width=\"100%\" cellpadding=\"0px\" cellspacing=\"0px\" border=\"0px\">
                                    <tr>
                                        <td align=\"right\" width=\"20%\">Name</td>
                                        <td align=\"center\" width=\"3%\">:</td>
                                        <th align=\"left\" width=\"27%\">".$personalInfo->getName()."</th>
                                        <td align=\"right\" width=\"20%\">Emp. Code</td>
                                        <td align=\"center\" width=\"2%\">:</td>
                                        <th align=\"left\" width=\"*\">".$personalInfo->getEmployeeCode()."</th>
                                    </tr>                        
                                    <tr>
                                        <td height=\"10px\"></td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Department</td>
                                        <td align=\"center\">:</td>
                                        <th align=\"left\">".$department->getDepartmentName($personalInfo->getDepartment())."</th>
                                        <td align=\"right\">Emp. Type</td>
                                        <td align=\"center\">:</td>
                                        <th align=\"left\">".$employeeType->getEmployeeTypeName($personalInfo->getEmployeeType())."</th>
                                    </tr>
                                    <tr>
                                        <td height=\"10px\"></td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Designation</td>
                                        <td align=\"center\">:</td>
                                        <th align=\"left\">";
                                                    $ranks = $employeeInfo->getEmployeeDesignationIds($employeeId, true);                            
                                        foreach ($ranks as $value){
                                            echo $designation->getDesignationTypeName($value, true)."<br />";
                                        }
                            echo "
                                        </th>
                                        <td align=\"right\">Forwarded Balance</td>
                                        <td align=\"center\">:</td>
                                        <th align=\"left\">".number_format(($amount), 2, '.', ',')."</th>
                                    </tr>
                                    <tr>
                                        <td height=\"10px\"></td>
                                    </tr>                                           
                                </table>";
                            echo "
                                <table align=\"center\" width=\"100%\" border=\"0\" cellpadding=\"5px\" cellspacing=\"5px\">
                                    <tr>
                                        <td colspan=\"9\"><hr size=\"1\" /></td>
                                    </tr>
                                    <tr>
                                        <th width=\"5%\">S.N.</th>
                                        <th align=\"right\" width=\"20%\">Month</th>
                                        <th align=\"right\" width=\"12%\">Subscription</th>
                                        <th align=\"right\" width=\"12%\">Recovery</th>
                                        <th align=\"right\" width=\"12%\">Loan</th>
                                        <th align=\"right\" width=\"12%\">Other</th>
                                        <th align=\"right\" width=\"12%\">Monthly. Total</th>
                                        <th align=\"right\" width=\"*\">Progressive Balance</th>
                                        <th align=\"right\" width=\"*\">Interest<br/>Rate</th>
                                    </tr>
                                    <tr>
                                        <td colspan=\"9\"><hr size=\"1\" /></td>
                                    </tr>";
                            $i = 0;
                            $totalSubscription = $totalRecovery = $totalLoan = $totalOther = $subscription = $recovery = $loan = $other = $monthly = $mtotal = 0;
                            $ptotal = 0; 
                            while(true){
                                $month = date("Ym", mktime(0, 0, 0, substr($startMonth, 4, 2)+$i, 15, substr($startMonth, 0, 4)));
                                $lmonth = date("Ym", mktime(0, 0, 0, substr($startMonth, 4, 2)+1+$i, 15, substr($startMonth, 0, 4)));
                                ++$i;                                
                                $displayMonth = date("Ym", mktime(0, 0, 0, substr($startMonth, 4, 2)+$i, 15, substr($startMonth, 0, 4)));
                                if($month == $endMonth || $month > $interest->getCurrentMonth())    
                                    break;
                                $totalSubscription += $subscription = $interest->getFundAmount($employeeId, "gpf", $month, 'm');
                                $totalRecovery += $recovery = $interest->getFundAmount($employeeId, "gpf", $month, 'r');
                                $totalLoan += $loan = $interest->getFundAmount($employeeId, "gpf", $month, 'n');
                                $totalOther += $other = $interest->getFundAmount($employeeId, "gpf", $month, 'm', 'n', 'r');
                               
                                $interestRate = $interest->getInterestRate($employeeId, $lmonth, 'g');
                                if ($month == $startMonth){
                                	$other -= $interestPaidLastSession;
                                }                                                                 
                                $monthly =  $subscription + $recovery + $loan + $other;
                                $amount += $monthly;
                                $mtotal += $monthly;                           
                                $ptotal += $amount;
                                echo "
                                    <tr>
                                        <td align=\"left\">$i</td>
                                        <td align=\"right\">".$interest->nameMonth($displayMonth)."</td>
                                        <td align=\"right\">".number_format(abs($subscription), 2, '.', ',')."</td>
                                        <td align=\"right\">".number_format(abs($recovery), 2, '.', ',')."</td>
                                        <td align=\"right\">".number_format(abs($loan), 2, '.', ',')."</td>
                                        <td align=\"right\">".number_format($other, 2, '.', ',')."</td>
                                        <td align=\"right\">".number_format(abs($monthly), 2, '.', ',')."</td>
                                        <td align=\"right\">".number_format(abs($amount), 2, '.', ',')."</td>  
                                        <td align=\"right\">".number_format($interestRate, 2, '.', '')."</td>
                                                   
                                    </tr>
                                    <tr>
                                        <td height=\"5px\"></td>
                                    </tr>";              
                            }
                            echo "
                                <tr>
                                    <td colspan=\"9\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"center\" colspan = \"2\">Total Summary</td>
                                    <td align=\"right\">".number_format(abs($totalSubscription), 2, '.', ',')."</td>
                                    <td align=\"right\">".number_format(abs($totalRecovery), 2, '.', ',')."</td>
                                    <td align=\"right\">".number_format(abs($totalLoan), 2, '.', ',')."</td>
                                    <td align=\"right\">".number_format(abs($totalOther), 2, '.', ',')."</td>
                                    <td align=\"right\">".number_format($mtotal, 2, '.', ',')."</td>    
                                    <td align=\"right\">".number_format(abs($ptotal), 2, '.', ',')."</td>           
                                </tr>
                                <tr>
                                    <td colspan=\"9\"><hr size=\"1\" /></td>
                                </tr>";              
                            
                           
                            $interestPaid = $interest->getFundAmount($employeeId, "gpf", $endMonth, 'i');
                            $closingBalance = $amount + $interestPaid;
                               echo "                            
                                </table>";
                            echo "
                                <table width=\"100%\" cellpadding=\"0px\" cellspacing=\"0px\" border=\"0px\">
                                    <tr>
                                        <td height=\"20px\"></td>
                                    </tr>                                           
                                    <tr>
                                        
                                        <td align=\"right\" width=\"20%\" >Interest paid</td>
                                        <td align=\"center\" width=\"2%\">:</td>
                                        <th align=\"left\" width=\"*\" colspan=\"4\">".number_format($interestPaid, 2, '.', ',')."</th>
                                    </tr>  
                                    <tr>
                                        <td height=\"10px\"></td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\" width=\"20%\">Starting Balance</td>
                                        <td align=\"center\" width=\"3%\">:</td>
                                        <th align=\"left\" width=\"27%\">".number_format($interest->getFundAmount($employeeId, "gpf", $startMonth) + $interestPaidLastSession, 2, '.', ',')."</th>
                                        <td align=\"right\" width=\"20%\">Final Balance</td>
                                        <td align=\"center\" width=\"2%\">:</td>
                                        <th align=\"left\" width=\"*\">".number_format($closingBalance, 2, '.', ',')."</th>
                                    </tr>  
                                    <tr>
                                        <td height=\"20px\"></td>
                                    </tr>
                                    <tr>
                                        <td colspan=\"6\"><hr size=\"1\" /></td>
                                    </tr> 
                                    <tr>
                                        <td colspan=\"6\" align=\"left\"><font style=\"font-size: 12px;\">*Please Check The Detailed GPF Statement, or Contact Dy. Registrar (Accounts) For Any Clarification</font></td>
                                    </tr>                         
                                    <tr>
                                        <td height=\"20px\"></td>
                                    </tr>
                                    <tr>
                                        <td colspan=\"6\"><hr size=\"1\" /></td>
                                    </tr>                        
                                    <tr>
                                        <td colspan=\"6\" align=\"center\"><font style=\"text-align: center; font-size: 10px; font-style: italic; font-weight: bold;\">This is a computer generated statement and does not need any signature.</font><font size=\"1.2px\"> Designed and Developed By Hemant Kumar Sah B.Tech (ECE-2011)</font></td>
                                    </tr>                                
                                    <tr>
                                        <td colspan=\"6\"><hr size=\"1\" /></td>
                                    </tr>                                                
                                </table>        
                            </td>
                        </tr>
                    </table>          
                	
                    ";    
               	echo "<p class=\"break\"></p>";
            }
        ?>    
          
          
        
        
        <div id="print" align="center">
            <table align="center" width="100%">
                <tr>
                    <td align="center"><br />
                        <input type="button" style="width:250px" value="Print The Summary" onclick="window.print() "/>&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="button" style="width:150px" value="Return Back" onclick="window.location='./report_fund.php'" /><br />
                    </td>
                </tr>
        </table>
        </div>       
      </div>        
    </div>    
  </div>
</div>
</body>
</html>
