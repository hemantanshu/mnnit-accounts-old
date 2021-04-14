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
                                                ACCOUNTS DEPARTMENT -- NPS FUND STATEMENT<br />
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
<title>Accounts Section -- NPS Annual Summary</title>
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
                $amount =   $interest->getFundAmount($employeeId, "nps", $startMonth);
                if($amount == "" || $amount == 0) //this employee doesnt have gpf account
                    continue;     
                $personalInfo->getEmployeeInformation($employeeId, true);
                printHeader($sessionDetails[1]); //printing the MNNIT Logo & Header
                
                $interestPaidSubscription = $interest->getFundAmount($employeeId, "nps", $startMonth, 'im');
                $interestPaidContribution = $interest->getFundAmount($employeeId, "nps", $startMonth, 'ic');
                
                $openingBalance['s'] = $totalBalanceSubscription = $interest->npsTotalBalance($employeeId, $startMonth, 'm') + $interestPaidSubscription;
                $openingBalance['c'] = $totalBalanceContribution = $interest->npsTotalBalance($employeeId, $startMonth, 'c') + $interestPaidContribution;   
                
                            
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
                                        <td align=\"right\">Total Balance</td>
                                        <td align=\"center\">:</td>
                                        <th align=\"left\">".number_format($amount, 2, '.', ',')."</th>
                                    </tr>
                                    <tr>
                                        <td height=\"10px\"></td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Opening Balance(S)</td>
                                        <td align=\"center\">:</td>
                                        <th align=\"left\">".number_format($openingBalance['s'], 2, '.', ',')."</th>
                                        <td align=\"right\">Opening Balance(C)</td>
                                        <td align=\"center\">:</td>
                                        <th align=\"left\">".number_format($openingBalance['c'], 2, '.', ',')."</th>
                                    </tr>  
                                    <tr>
                                        <td height=\"5px\"></td>
                                    </tr>
                                    
                                </table>";
                            echo "
                                <table align=\"center\" width=\"100%\" border=\"0\" cellpadding=\"5px\" cellspacing=\"5px\">
                                    <tr>
                                        <td colspan=\"9\"><hr size=\"1\" /></td>
                                    </tr>
                                    <tr>
                                        
                                       <th align=\"right\" width=\"19%\"></th>
									   <th align=\"right\" width=\"19%\"></th>
                                        <th align=\"right\" width=\"19%\">Subscription</th>
                                        <th align=\"right\" width=\"20%\">Prog. Balance</th>
                                        <th align=\"right\" width=\"5%\"></th>
                                        <th align=\"right\" width=\"19%\">Contribution</th>
                                        <th align=\"right\" width=\"*\">Prog. Balance</th>
                                        
                                        
                                    </tr>
                                ";
                            $i = 0;
                            
                            
                            $sumTotalBalanceSubscription = $sumTotalBalanceContribution = 0;
                            $totalSubscription = $totalContribution = $subscription = $contribution = 0; 
                            while(true){
                                $month = date("Ym", mktime(0, 0, 0, substr($startMonth, 4, 2)+$i, 15, substr($startMonth, 0, 4)));
                                $lmonth = date("Ym", mktime(0, 0, 0, substr($startMonth, 4, 2)+1+$i, 15, substr($startMonth, 0, 4)));
                                ++$i;
                                $showMonth = date("Ym", mktime(0, 0, 0, substr($startMonth, 4, 2)+$i, 15, substr($startMonth, 0, 4)));
                                if($month == $endMonth || $month > $interest->getCurrentMonth())    
                                    break;
                                
                                $subscription = $interest->getFundAmount($employeeId, "nps", $month, 'm');
                                $contribution = $interest->getFundAmount($employeeId, "nps", $month, 'c');
                                $interestRate = $interest->getInterestRate($employeeId, $lmonth, 'n');
                                
                                if($month == $startMonth){
                                    $subscription -= $interestPaidSubscription;
                                    $contribution -= $interestPaidContribution;
                                }                                
                                $totalSubscription += $subscription;
                                $totalContribution += $contribution;                              
                                
                                
                                $amount += $subscription + $contribution;                                
                                
                                $totalBalanceSubscription += $subscription;
                                $totalBalanceContribution += $contribution;
                                    
                                $sumTotalBalanceContribution += $totalBalanceContribution;
                                $sumTotalBalanceSubscription += $totalBalanceSubscription;  
                                
                                
                                           
                            }
                            echo "
                                <tr>
                                    <td colspan=\"9\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"center\" colspan = \"2\">Total Summary</td>
                                    <td align=\"right\">".number_format(abs($totalSubscription), 2, '.', ',')."||</td>
                                    <td align=\"right\">".number_format(abs($sumTotalBalanceSubscription), 2, '.', ',')."||</td>
                                    <td></td>
                                    <td align=\"right\">".number_format(abs($totalContribution), 2, '.', ',')."||</td>                            
                                    <td align=\"right\">".number_format(abs($sumTotalBalanceContribution), 2, '.', ',')."||</td>               
                                </tr>
                                <tr>
                                    <td colspan=\"8\"><hr size=\"1\" /></td>
                                </tr>";              
                            
                            $interestRate = $interest->getInterestRate($employeeId, $endMonth, 'n');
                            $interestPaidSubscription = $interest->getFundAmount($employeeId, "nps", $endMonth, 'im');
                            $interestPaidContribution = $interest->getFundAmount($employeeId, "nps", $endMonth, 'ic');
                            
                               echo "                            
                                </table>";
                            echo "
                                <table width=\"100%\" cellpadding=\"0px\" cellspacing=\"0px\" border=\"0px\">
                                                                              
									<tr>
                                        <th align=\"left\" width=\"27%\" colspan=\"3\"><center>Subscription</center></th>
                                        <th align=\"left\" width=\"27%\" colspan=\"3\"><center>Institutional</center></th>
                                    </tr>
									                                    <tr>
                                        <td align=\"right\" width=\"20%\">Opening Balance</td>
                                        <td align=\"center\" width=\"3%\">:</td>
                                        <th align=\"left\" width=\"27%\">".number_format($openingBalance['s'] , 2, '.', ',')."</th>
                                        <td align=\"right\" width=\"20%\">Opening Balance</td>
                                        <td align=\"center\" width=\"2%\">:</td>
                                        <th align=\"left\" width=\"*\">".number_format($openingBalance['c'] , 2, '.', ',')."</th>
                                    </tr>  
                                    <tr>
                                        <td height=\"5px\"></td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\" width=\"20%\">Total Subscription</td>
                                        <td align=\"center\" width=\"3%\">:</td>
                                        <th align=\"left\" width=\"27%\">".number_format($totalSubscription, 2, '.', ',')."</th>
                                        <td align=\"right\" width=\"20%\">Total Contribution</td>
                                        <td align=\"center\" width=\"2%\">:</td>
                                        <th align=\"left\" width=\"*\">".number_format($totalContribution, 2, '.', ',')."</th>
                                    </tr>
                                   
                                    <tr>
                                        <td align=\"right\" width=\"20%\">Interest(Subscription)</td>
                                        <td align=\"center\" width=\"3%\">:</td>
                                        <th align=\"left\" width=\"27%\">".number_format($interestPaidSubscription, 2, '.', ',')."</th>
                                        <td align=\"right\" width=\"20%\">Interest (Contributory)</td>
                                        <td align=\"center\" width=\"2%\">:</td>
                                        <th align=\"left\" width=\"*\">".number_format($interestPaidContribution, 2, '.', ',')."</th>
                                    </tr>
									<tr>
                                        <td height=\"10px\"></td>
                                    </tr>                                        
									<tr>
                                        <th align=\"left\" width=\"27%\" colspan=\"3\"><center><hr /></center></th>
                                        <th align=\"left\" width=\"27%\" colspan=\"3\"><center><hr /></center></th>
                                    </tr>
									
									<tr>
                                        <td align=\"right\" width=\"20%\">Balance</td>
                                        <td align=\"center\" width=\"3%\">:</td>
                                        <th align=\"left\" width=\"27%\">".number_format(($interestPaidSubscription+$openingBalance['s']+$totalSubscription), 2, '.', ',')."</th>
                                        <td align=\"right\" width=\"20%\">Balance</td>
                                        <td align=\"center\" width=\"2%\">:</td>
                                        <th align=\"left\" width=\"*\">".number_format(($interestPaidContribution+$openingBalance['c']+$totalContribution), 2, '.', ',')."</th>
                                    </tr>
                                    <tr>
                                        <td height=\"10px\"></td>
                                    </tr>
                                    
									
                                                        
                                                                
                                                                                  
                                </table>        
                            </td>
                        </tr>
                    </table>          
                	
                    ";    
               
                
            }
				echo"
			<table>
			<tr>
						<td colspan=\"6\" align=\"center\"><font style=\"text-align: center; font-size: 10px; font-style: italic; <font size=\"1.2px\"> Designed and Developed By Hemant Kumar Sah B.Tech (ECE-2011)&& Maintained Kedar Panjiyar(CSE-2014)</font></td>
			</tr>
			</table>";

			
				echo "<p class=\"break\"></p>";
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
