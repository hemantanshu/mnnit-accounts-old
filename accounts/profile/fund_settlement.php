<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);
function greatermonth($month1,$month2)
{
	$year1 = substr($month1,0,4);
	$year2 = substr($month2,0,4);
	$m1=intval(substr($month1,4,2));
	$m2=intval(substr($month2,4,2));
	if( $year1>$year2)
		return true ;
	elseif( $year2>$year1 )
		return false ;
	else
	{
		if($m1>=$m2)
			return true ;
			else
			return false ;
	}
}
require_once '../include/class.department.php';
require_once '../include/class.personalInfo.php';
require_once '../include/class.employeeInfo.php';
require_once '../include/class.employeeType.php';
require_once '../include/class.designation.php';
require_once '../include/class.interest.php';
require_once '../include/class.reporting.php';
require_once '../include/class.gpftotal.php';

$department = new department();
if(!$department->checkLogged())
        $department->redirect('../');
 
if(isset ($_GET['employeeid'])){
    $employeeId = $_GET['employeeid'];
}else
    $department->redirect('./salary_gpf.php');
 
$gpfTotal = new gpfTotal();
$reporting = new reporting();
$employeeInfo = new employeeInfo();
$personalInfo = new personalInfo();
$employeeType = new employeeType();
$designation = new designation();
$interest = new interest();
   

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
                                                ACCOUNTS DEPARTMENT -- FUND SETTLEMENT STATEMENT<br /></font></td>
                
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
<title>Accounts Section -- Fund Settlement Statement</title>
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
                
            $personalInfo->getEmployeeInformation($employeeId, true);
            printHeader($sessionDetails[1]); //printing the MNNIT Logo & Header

            echo "
                <table align=\"center\" width=\"100%\" border=\"0\">
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
                                <tr>
                                    <td height=\"10px\"></td>
                                </tr> 
                                <tr>
                                    <td colspan=\"6\"><hr /><br />
                                        <table align=\"center\" border=\"0\" width=\"100%\">";
                        $flag = true;
                        $amount = 0;
                        $settlementDetails = $interest->getSettlementDetails($employeeId, 'g');
                        if($settlementDetails[0] != 0 || $settlementDetails[0] != ""){
							 // done for upper header
							
							 $completeGpfIds = $gpfTotal->getEmployeeFundIds($employeeId, 'gpf');
							 foreach($completeGpfIds as $gpfID) 
							 {  
							 	$details = $gpfTotal->getFundIdDetails($gpfID, 'gpf');
								if($details['flag']=='o')
								{
									$settlementMonth = $details['month'];
									break ;
								}
							 }
							 $settlementYear = substr($settlementMonth,0,4);							 
							 $startMonth = $settlementYear."03" ;
							 $completeGpfIds = $gpfTotal->getEmployeeFundIds($employeeId, 'gpf');
							 $sumDebit = 0 ;
							 $sumCredit = 0 ;
                        	 $sumTotal = 0;
							 foreach($completeGpfIds as $gpfID) 
							 {  
//echo $gpfID."<br/>";

							 	$details = $gpfTotal->getFundIdDetails($gpfID, 'gpf');
								if($details['flag']=='i' && $details['month'] == $settlementMonth)
									$settlementIntrest = $details['amount'];
								if($details['flag']=='i' && $details['month'] == $startMonth)
									$financialIntrest = $details['amount'];

								if( greatermonth($details['month'],$startMonth) != false &&( ($details['flag'] == 'n') || ($details['flag'] == 'm') || ($details['flag'] == 'r')) ) 	
								{ //echo $details['month']."<br/>";
//echo $details[2]."<br/>";


									$debit = ($details[2] < 0) ? abs($details[2]) : 0;
                        			$credit = ($details[2] > 0) ? abs($details[2]) : 0;
									$sumDebit += $debit;
                        			$sumCredit += $credit;
                        			$sumTotal += -$debit + $credit;
								}
							 }
							 
							 $openingAmount =  $interest->getFundAmount($employeeId, "gpf", $startMonth) + $financialIntrest;
							 //	upper header ends here	
							 //
                            $amount += $settlementDetails[0];
                            $flag = false;
                            echo "
                                            <tr>
                                                <td colspan=\"3\"><hr size=\"1\" /></td>
                                            </tr>
                                            <tr>
                                                <td colspan=\"3\" align=\"center\">GPF Fund Balance Details</td>
                                            </tr>
											<tr>
                                                <td height=\"5px\" colspan=\"3\"></td>
                                            </tr>
											<tr><td colspan=\"3\">
													<table width=\"100%\">
													<tr>
                                                        <th>Opening Balance</th>
														<th>Total Debit</th>
														<th>Total Credit</th>
														
														<th>Interest</th>
                                                                                                               
													</tr>
													<tr><td colspan=\"4\" height=\"5px\"></td></tr>
													<tr align=\"center\">
														<td>".number_format($openingAmount,2,'.',',')."</td>
                                                        <td>".number_format($sumDebit,2,'.','')."</td>
														<td>".number_format($sumCredit,2,'.','')."</td>
											
														<td>".number_format($settlementIntrest,2,'.',',')."</td>

													</tr>
													</table>
											</td></tr>
                                            <tr>
                                                <td height=\"5px\" colspan=\"3\"></td>
                                            </tr>
                                            <tr>
                                                <td align=\"right\" width=\"20%\">GPF Final Balance</td>
                                                <td align=\"center\" width=\"3%\">:</td>
                                                <td align=\"left\">".  number_format($settlementDetails[0], 2, '.', '')."</td>
                                            </tr>
                                            <tr>
                                                <td height=\"5px\"></td>
                                            </tr>
                                            <tr>
                                                <td align=\"right\">Fund Closure Month</td>
                                                <td align=\"center\">:</td>
                                                <td align=\"left\">".$interest->nameMonth($settlementDetails[1])."</td>
                                            </tr>";                            
                        }
                        $settlementDetails = $interest->getSettlementDetails($employeeId, 'n');
                        if($settlementDetails[0] != 0 || $settlementDetails[0] != ""){
							// done for settlement header
							
							 $completeGpfIds = $gpfTotal->getEmployeeFundIds($employeeId, 'nps');
							 foreach($completeGpfIds as $gpfID) 
							 {  
							 	$details = $gpfTotal->getFundIdDetails($gpfID, 'nps');
								if($details['flag']=='o')
								{
									$settlementMonth = $details['month'];
									break ;
								}
							 }
							 $settlementYear = substr($settlementMonth,0,4);							 
							 $startMonth = $settlementYear."03" ;
							 $completeGpfIds = $gpfTotal->getEmployeeFundIds($employeeId, 'nps');
							 $sumDebit = 0 ;
							 $sumCredit = 0 ;
                        	 $sumTotal = 0;
							 foreach($completeGpfIds as $gpfID) 
							 {  
							 	$details = $gpfTotal->getFundIdDetails($gpfID, 'nps');
								if($details['flag']=='i' && $details['month'] == $settlementMonth)
									$settlementIntrest = $details['amount'];
								if($details['flag']=='i' && $details['month'] == $startMonth)
									$financialIntrest = $details['amount'];
								if( greatermonth($details['month'],$startMonth) && !$details['flag'] == 'o') 	
								{
									$debit = $details[2] < 0 ? abs($details[2]) : 0;
                        			$credit = $details[2] > 0 ? abs($details[2]) : 0;
									$sumDebit += $debit;
                        			$sumCredit += $credit;
                        			$sumTotal += -$debit + $credit;
								}
							 }
							 
							 $openingAmount =  $interest->getFundAmount($employeeId, "nps", $startMonth) + $financialIntrest;
							 //	upper header ends here	
							 //
                            $amount += $settlementDetails[0];
                            $flag = false;
                            echo "
                                            <tr>
                                                <td colspan=\"3\"><hr size=\"1\" /></td>
                                            </tr>
                                            <tr>
                                                <td colspan=\"3\" align=\"center\">NPS Fund Balance Details</td>
                                            </tr>
                                            <tr>
                                                <td height=\"5px\"></td>
                                            </tr>
											<tr><td colspan=\"3\">
													<table width=\"100%\">
													<tr>
													    <th>Opening Balance</th>
														<th>Total Debit</th>
														<th>Total Credit</th>
														<th>Interest</th>
													</tr>
													<tr><td colspan=\"4\" height=\"5px\"></td></tr>
													<tr align=\"center\">
													     <td>".number_format($openingAmount,2,'.',',')."</td>
                                                        <td>".number_format($sumDebit,2,'.','')."</td>
														<td>".number_format($sumCredit,2,'.','')."</td>
														<td>".number_format($settlementIntrest,2,'.',',')."</td>
													</tr>
													</table>
											</td></tr>
											<tr>
                                                <td height=\"5px\" colspan=\"3\"></td>
                                            </tr>
                                            <tr>
                                                <td align=\"right\" width=\"20%\">NPS Final Balance</td>
                                                <td align=\"center\" width=\"3%\">:</td>
                                                <td align=\"left\">".number_format($settlementDetails[0], 2, '.', ',')."</td>
                                            </tr>
                                            <tr>
                                                <td height=\"5px\"></td>
                                            </tr>
                                            <tr>
                                                <td align=\"right\">Fund Closure Month</td>
                                                <td align=\"center\">:</td>
                                                <td align=\"left\">".$interest->nameMonth($settlementDetails[1])."</td>
                                            </tr>";
                        }
                        $settlementDetails = $interest->getSettlementDetails($employeeId, 'c');
                        if($settlementDetails[0] != 0 || $settlementDetails[0] != ""){
							// done for cpf header
							// done for settlement header
							 $completeGpfIds = $gpfTotal->getEmployeeFundIds($employeeId, 'cpf');
							 foreach($completeGpfIds as $gpfID) 
							 {  
							 	$details = $gpfTotal->getFundIdDetails($gpfID, 'cpf');
								if($details['flag']=='o')
								{
									$settlementMonth = $details['month'];
									break ;
								}
							 }
							 $settlementYear = substr($settlementMonth,0,4);							 
							 $startMonth = $settlementYear."03" ;
							 $completeGpfIds = $gpfTotal->getEmployeeFundIds($employeeId, 'cpf');
							 $sumDebit = 0 ;
							 $sumCredit = 0 ;
                        	 $sumTotal = 0;
							 foreach($completeGpfIds as $gpfID) 
							 {  
							 	$details = $gpfTotal->getFundIdDetails($gpfID, 'cpf');
								if($details['flag']=='i' && $details['month'] == $settlementMonth)
									$settlementIntrest = $details['amount'];
								if($details['flag']=='i' && $details['month'] == $startMonth)
									$financialIntrest = $details['amount'];
								if( greatermonth($details['month'],$startMonth) && !$details['flag'] == 'o') 	
								{
									$debit = $details[2] < 0 ? abs($details[2]) : 0;
                        			$credit = $details[2] > 0 ? abs($details[2]) : 0;
									$sumDebit += $debit;
                        			$sumCredit += $credit;
                        			$sumTotal += -$debit + $credit;
								}
							 }
							 
							 $openingAmount =  $interest->getFundAmount($employeeId, "cpf", $startMonth) + $financialIntrest;
							//
							//
                            $amount += $settlementDetails[0];
                            $flag = false;                        
                            echo "
                                            
                                            <tr>
                                                <td colspan=\"3\"><hr size=\"1\" /></td>
                                            </tr>
                                            <tr>
                                                <td colspan=\"3\" align=\"center\">CPF Fund Balance Details</td>
                                            </tr>
                                             <tr>
                                                <td height=\"5px\"></td>
                                            </tr>
											<tr><td colspan=\"3\">
													<table width=\"100%\">
													<tr>
													   <th>Opening Balance</th>
														<th>Total Debit</th>
														<th>Total Credit</th>
														
														<th>Interest</th>
													</tr>
													<tr><td colspan=\"4\" height=\"5px\"></td></tr>
													<tr align=\"center\">
													     <td>".number_format($openingAmount,2,'.',',')."</td>
														<td>".number_format($sumDebit,2,'.',',')."</td>
														<td>".number_format($sumCredit,2,'.',',')."</td>
														
														<td>".number_format($settlementIntrest,2,'.',',')."</td>
													</tr>
													</table>
											</td></tr>
											<tr>
                                                <td height=\"5px\" colspan=\"3\"></td>
                                            </tr>
                                            <tr>
                                                <td align=\"right\" width=\"20%\">CPF Final Balance</td>
                                                <td align=\"center\" width=\"3%\">:</td>
                                                <td align=\"left\" >".number_format($settlementDetails[0], 2, '.', '')."</td>
                                            </tr>
                                            <tr>
                                                <td height=\"5px\"></td>
                                            </tr>
                                            <tr>
                                                <td align=\"right\">Fund Closure Month</td>
                                                <td align=\"center\">:</td>
                                                <td align=\"left\">".$interest->nameMonth($settlementDetails[1])."</td>
                                            </tr>";
                        }
                        if($flag)
                            $interest->palert ("The Final Settlement Of This Employee Has Not Been Done Yet", "./salary_gpf.php");
                        echo "
                                        </table>
                                    </td>
                                </tr>
                                <tr>    
                                    <td colspan=\"6\"><hr size=\"2\" /></td>
                                </tr>
                                <tr>
                                    <td height=\"5px\"></td>
                                </tr>
                                <tr>
                                    <th align=\"right\">Final Balance</th>
                                    <td align=\"center\">:</td>
                                    <th align=\"left\">".  number_format($amount, 2, '.', '')."</th>
                                </tr>
                                <tr>
                                    <td height=\"5px\"></td>
                                </tr>
                                <tr>
                                    <th align=\"right\">Amount In Words</th>
                                    <td align=\"center\">:</td>
                                    <td align=\"left\" colspan=\"3\">".$reporting->nameAmount($amount)."</td>
                                </tr>
                                <tr>
                                    <td height=\"5px\"></td>
                                </tr>
                                <tr>
                                    <td colspan=\"6\"><hr size=\"2\" /></td>
                                </tr>
                                <tr>
                                    <td height=\"10px\"></td>
                                </tr>
                                <tr>
                                    <td colspan=\"6\"><br /><br />
                                        <table align=\"center\" border=\"0\" width=\"100%\">
                                            <tr>
                                                <td width=\"20%\" align=\"center\">-------------------------</td>
                                                <td width=\"20%\" align=\"center\">-------------------------</td>
                                                <td width=\"20%\" align=\"center\">-------------------------</td>
                                                <td width=\"20%\" align=\"center\">-------------------------</td>
                                                <td width=\"20%\" align=\"center\">-------------------------</td>                                                
                                            </tr>
                                            <tr>
                                                <td align=\"center\">Assistant</td>
                                                <td align=\"center\">Supritendent</td>
                                                <td align=\"center\">Dy.Registrar(A/C)</td>
                                                <td align=\"center\">Registrar</td>
                                                <td align=\"center\">Director</td>                                                
                                            </tr>
                                            
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height=\"10px\"></td>
                                </tr>                                
                                <tr>
                                    <td colspan=\"6\"><hr size=\"2\" /></td>
                                </tr>                                
                                <tr>
                                    <td height=\"10px\"></td>
                                </tr>
                                <tr>
                                    <td align=\"right\">Voucher Number</td>
                                    <td align=\"center\">:</td>
                                    <td align=\"left\"></td>
									<td></td>
									<td></td>
									<td rowspan=\"7\"><div style=\"border:thin; height:100px; width:80px; border-style:solid; text-align:center; vertical-align:middle\"><br />Revenue Stamp</div></td>
                                </tr>
                                <tr>
                                    <td height=\"10px\"></td>
                                </tr>
                                <tr>
                                    <td align=\"right\">Voucher Date</td>
                                    <td align=\"center\">:</td>
                                    <th align=\"left\"></th>
									<td></td>
									<td></td>
                                </tr>
                                <tr>
                                    <td height=\"10px\"></td>
                                </tr>
                                <tr>
                                    <td align=\"right\">Cheque Number</td>
                                    <td align=\"center\">:</td>
                                    <th align=\"left\"></th>
									<td></td>
									<td></td>
                                </tr>
                                <tr>
                                    <td height=\"10px\"></td>
                                </tr>
                                <tr>
                                    <td align=\"right\">Cheque Date</td>
                                    <td align=\"center\">:</td>
                                    <th align=\"left\"></th>
									<td></td>
									<td></td>
                                </tr>
                                
                                <tr>
                                    <td height=\"10px\"></td>
                                </tr>
                            </table>                        
                        </td>
                    </tr>
                </table>
                ";    
            
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
