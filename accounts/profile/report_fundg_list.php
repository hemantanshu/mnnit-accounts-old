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
    $department->redirect('./report_fund_list.php');


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
	echo "
		<table border=\"0\" align=\"center\" width=\"100%\" bgcolor=\"#0099FF\">
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
		
	
function printHeader($name){
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
		echo "
                                <table align=\"center\" width=\"100%\" border=\"0\" cellpadding=\"5px\" cellspacing=\"5px\">
                                    <tr>
                                        <td colspan=\"9\"><hr size=\"1\" /></td>
                                    </tr>
                                    <tr>
                                       <th align=\"right\" width=\"20%\">Name</th>
                                        <th align=\"right\" width=\"20%\">Emp.code</th>
										<th align=\"right\" width=\"20%\">O.Balance</th>
                                        <th align=\"right\" width=\"12%\">Subs</th>
                                        <th align=\"right\" width=\"12%\">Recovery</th>
                                        <th align=\"right\" width=\"12%\">Loan</th>
                                        <th align=\"right\" width=\"12%\">I.paid</th>
                                    
                                        <th align=\"right\" width=\"*\">P.Balance</th>
                                        <th align=\"right\" width=\"*\">C.Balance</th>
                                    </tr>
                                    <tr>
                                        <td colspan=\"9\"><hr size=\"1\" /></td>
                                    </tr>";


            foreach($completeEmployeeId as $employeeId){
							
                $amount =   $interest->getFundAmount($employeeId, "gpf", $startMonth);
                $interestPaidLastSession = $interest->getFundAmount($employeeId, "gpf", $startMonth, 'i');
                $amount += $interestPaidLastSession;
                
                if($amount == "" || $amount == 0) //this employee doesnt have gpf account
                    continue;     
                $personalInfo->getEmployeeInformation($employeeId, true);
                printHeader($sessionDetails[1]); //printing the MNNIT Logo & Header
                
                                                              $i = 0;
                            $totalSubscription = $totalRecovery = $totalLoan = $totalOther = $subscription = $recovery = $loan = $other = $monthly = $mtotal = 0;
                            $ptotal = 0; 
                            while(true){
                                $month = date("Ym", mktime(0, 0, 0, substr($startMonth, 4, 2)+$i, 15, substr($startMonth, 0, 4)));
                                $lmonth = date("Ym", mktime(0, 0, 0, substr($startMonth, 4, 2)+1+$i, 15, substr($startMonth, 0, 4)));
                                ++$i;                                
                                //$displayMonth = date("Ym", mktime(0, 0, 0, substr($startMonth, 4, 2)+$i, 15, substr($startMonth, 0, 4)));
                                if($month == $endMonth || $month > $interest->getCurrentMonth())    
                                    break;
                                $totalSubscription += $subscription = $interest->getFundAmount($employeeId, "gpf", $month, 'm');
                                $totalRecovery += $recovery = $interest->getFundAmount($employeeId, "gpf", $month, 'r');
                                $totalLoan += $loan = $interest->getFundAmount($employeeId, "gpf", $month, 'n');
                               // $totalOther += $other = $interest->getFundAmount($employeeId, "gpf", $month, 'm', 'n', 'r');
                               
                                //$interestRate = $interest->getInterestRate($employeeId, $lmonth, 'g');
                                //if ($month == $startMonth){
                                	//$other -= $interestPaidLastSession;
                                //}                                                                 
                                $monthly =  $subscription + $recovery + $loan + $other;
                                $amount += $monthly;
                                $mtotal += $monthly;                           
                                $ptotal += $amount;
                                            
                            }
							$interestPaid = $interest->getFundAmount($employeeId, "gpf", $endMonth, 'i');
                            $closingBalance = $amount + $interestPaid;
							
                            echo "
                                
                                <tr>
								    
                                    <td align=\"right\">".$personalInfo->getName()."</td>
									<td align=\"right\">".$personalInfo->getEmployeeCode()."</td>
									<td align=\"right\">".number_format($interest->getFundAmount($employeeId, "gpf", $startMonth) + $interestPaidLastSession, 2, '.', ',')."</td>
                                    <td align=\"right\">".number_format(abs($totalSubscription), 2, '.', ',')."</td>
                                    <td align=\"right\">".number_format(abs($totalRecovery), 2, '.', ',')."</td>
                                    <td align=\"right\">".number_format(abs($totalLoan), 2, '.', ',')."</td>
                                    <td align=\"right\">".number_format($interestPaid, 2, '.', ',')."</td>
                                        
                                    <td align=\"right\">".number_format(abs($ptotal), 2, '.', ',')."</td>
									<td align=\"right\">".number_format($closingBalance, 2, '.', ',')."</td>
                                </tr>
                                <tr>
                                    <td colspan=\"9\"><hr size=\"1\" /></td>
                                </tr>";              
                            
                           
                            
            }
			echo"
			<table>
			<tr>
						<td colspan=\"6\" align=\"center\"><font style=\"text-align: center; font-size: 10px; font-style: italic; <font size=\"1.2px\"> Designed and Developed By Hemant Kumar Sah B.Tech (ECE-2011)&& Maintained Kedar Panjiyar(CSE-2014)</font></td>
			</tr>
			</table>";
			//echo "<p class=\"break\"></p>";
        ?>    
          
          <div id="print" align="center">
            <table align="center" width="100%">
                <tr>
                    <td align="center"><br />
                        <input type="button" style="width:250px" value="Print The Summary" onclick="window.print() "/>&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="button" style="width:150px" value="Return Back" onclick="window.location='./report_fund_list.php'" /><br />
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
