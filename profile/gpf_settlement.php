<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
// 	//error_reporting(0);

    session_start();
	require_once '../include/class.reporting.php';
    require_once '../include/class.employeeInfo.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.loginInfo.php';
    require_once '../include/class.department.php';
    require_once '../include/class.employeeType.php';
    require_once '../include/class.gpftotal.php';
	require_once '../include/class.salutation.php';
    require_once '../include/class.bank.php';
    require_once '../include/class.designation.php';
	
	$accounts = new reporting();
    $employeeInfo = new employeeInfo();
	$designation = new designation();
    $personalInfo = new personalInfo();
    $loggedInfo = new loginInfo();
    $department = new department();
    $gpfTotal = new gpfTotal();
    $employeeType = new employeeType(); 
	$bank = new bank();
	$salutation = new salutation();
    if(!$loggedInfo->checkLogged())
        $loggedInfo->redirect('../');
	$date = $personalInfo->getCurrentMonth();
	if(isset($_GET['id']))
		$employeeId = $_GET['id'];
	else
		$loggedInfo->redirect('./');
	$fundType = $_GET['fund'];
    
	$completeGpfIds = $gpfTotal->getEmployeeFundIds($employeeId, $fundType);
	if(!$completeGpfIds)
		$loggedInfo->palert("There Is No Record Availiable For The Given Employee. Please Select Another Employee", './salary_gpf.php');
    $personalInfo->getEmployeeInformation($employeeId, true);
    ob_end_flush();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<title>Quarterwise Statement of <?php //echo ucwords(->getNumber2Month($currentMonth)).", ".$currentYear; ?></title>
        <link rel="stylesheet" type="text/css" href="../include/style1.css" media="screen" />
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
    
	<body onload="window.print()">
            <div id="logout" align="center"><br />
                <input name="back" type="button" value="Print" onclick="window.print()" />&nbsp;&nbsp;&nbsp;&nbsp;
                <input name="b2" type="button" value="Return Back" onclick="location='./salary_slip.php'" /> &nbsp;&nbsp;&nbsp;
                <input name="b3" type="button" value="Logout" onclick="location='./logout.php'" /> <br /><br />
		</div>
        <center>
        <table width="1024px" align="center" border="0" style="border-width:.2px" cellpadding="0" cellspacing="0">
             <tr><td colspan="6" width="100%">
						<table border="0" align="center" width="100%">
						<tr>
						<td align="center" width="160" height="111px"><img src="../img/mnnit_logo.gif" alt="mnnit logo" width="126" height="131px" align="left" /></td>
						<td align="center" width="743"><font class="bigheader">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class="smallheader">
						ALLAHABAD - 211004<br /><br />
						ACCOUNTS DEPARTMENT -- <?php echo strtoupper($_GET['fund']) ; ?> FINAL SETTLEMENT REPORT </font>
                        </td>						
						</tr>
                        </table>
			</td></tr>
        <tr>
        <tr><td colspan="6">
        <?php 
		echo "<table width=\"100%\" border=\"0\" style=\"border-width:.2px\"><tr><td height=\"10px\"  colspan=\"5\"></td>
                                            </tr>
                                            <tr>
                                                <td width=\"13%\" align=\"right\"><font class=\"salarySlip\"><b>Name :</b></font></td>
                                                <td width=\"30%\" align=\"left\"><font class=\"salaryPrint\">".$salutation->getSalutationName($personalInfo->getSalutationId())." ".$personalInfo->getName()."</font></td>
                                                <td width=\"5%\" align=\"center\">||</td>
                                                <td align=\"right\" width=\"13%\"><font class=\"salarySlip\"><b>Employee Code :</b></font></td>
                                                <td align=\"left\" width=\"*\"><font class=\"salaryPrint\">".$personalInfo->getEmployeeCode()."</font></td>
                                            </tr>
                                            <tr>
                                                    <td height=\"10px\" colspan=\"5\"></td>
                                            </tr>
                                            <tr>
                                                <td align=\"right\"><font class=\"salarySlip\"><b>Department :</b></font></td>
                                                <td align=\"left\"><font class=\"salaryPrint\">".$department->getDepartmentName($personalInfo->getDepartment())."</font></td>
                                                <td align=\"center\">||</td>
                                                <td align=\"right\"><font class=\"salarySlip\"><b>Designation :</b></font></td>
                                                <td align=\"left\"><font class=\"salaryPrint\">";
                            $ranks = $employeeInfo->getEmployeeDesignationIds($employeeId, true);  
                            foreach ($ranks as $value){
                                echo $designation->getDesignationTypeName($value, true)."<br />";
                            }
                            echo "</font></td></tr><tr>
                                                    <td height=\"10px\" colspan=\"5\"></td>
                                            </tr></table>" ;
        
		?>
        </td></tr>
        <tr>
                        	<td colspan="6"><hr size="2" /></td>
                        </tr>
        <tr>
                        	<th width="5%">S.N.</th>
                            <th width="40%" align="left">Remarks</th>
                            <th width="15%">Date</th>                            
                            <th width="13%" align="right">Credit</th>
                            <th width="13%" align="right">Debit</th>
                            <th width="*" align="right">Balance</th>
                        </tr>
                        <tr>
                        	<td colspan="6"><hr size="2" /></td>
                        </tr>
                        <?php 
                        	$count = 0;
                        	$sumTotal = 0; //for the complete sum
                        	$sumDebit = 0; //for the total Debit Amount
                        	$sumCredit = 0; //for the total Credit Amount
                        	foreach ($completeGpfIds as $individualGpfId) {
                        		$details = $gpfTotal->getFundIdDetails($individualGpfId, $fundType);
                        		$debit = $details[2] < 0 ? abs($details[2]) : 0;
                        		$credit = $details[2] > 0 ? abs($details[2]) : 0;
                        		
                        		$sumDebit += $debit;
                        		$sumCredit += $credit;
                        		$sumTotal += -$debit + $credit;
                        		
                        		
                        		++$count;
								if($details[4]=='o')
                        		echo "
                        			<tr style=\"padding:0px\">
			                        	<td align=\"center\">".$count."</td>
			                            <td align=\"left\">".$gpfTotal->getFlagComment($details[4])."</td>
			                            <td align=\"left\">".$gpfTotal->getNumber2Month(substr($details[3], 4, 2)).", ".substr($details[3], 0, 4)."</td>
			                            <td align=\"right\">".number_format($credit, 2, '.', ',')."</td>
			                            <td align=\"right\">".number_format($debit, 2, '.', ',')."</td>
			                            <td align=\"right\">".number_format($sumTotal, 2, '.', ',')."</td>
			                        </tr>";
//								echo "<tr><td colspan=\"6\" style=\"padding:0px\"><hr size=\"1\" /></td></tr>" ;
                        	}
                        ?>
        <tr><td colspan="2"></td><td colspan="4"><br />BALANCE AS ON <?php echo $gpfTotal->getNumber2Month(substr($details[3], 4, 2)).", ".substr($details[3], 0, 4).": " ; echo "Rs".$sumTotal ; ?>
        <br />FULL AND FINAL PAYMENT: <?php echo "Rs".$sumTotal ; ?>
        <hr />
        <?php
		echo $accounts->nameAmount($sumTotal) ;
		?>
        </td></tr>
        <tr><td colspan="6" >
        <table width="100%" style="font-weight:1000" height="120px">
        <tr align="center"><td>Assistant</td><td>Superintendent</td><td>Deputy Accounts</td><td>Registrar</td><td>Director</td></tr>
        </table>
        </td></tr>
        <tr><td></td><td colspan="2">
        Voucher Number: <br /><br />
        Voucher Date: <br /><br />
        Cheque No: <br /><br />
        Cheque Date: <br /><br />
        </td><td colspan="2"><div style="border:thin; border-color:#000; width:60px; border-style:solid; text-align:center; height:75px; float:right; padding:10px">Stamp Here</div></td><td></td></tr>
        </table>
        </center>
	</body>
</html>