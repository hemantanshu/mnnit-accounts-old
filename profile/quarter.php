<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
// 	//error_reporting(0);

    session_start();

    require_once '../include/class.reporting.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.employeeInfo.php';
    require_once '../include/class.department.php';
    require_once '../include/class.designation.php';
    require_once '../include/class.bank.php';
    require_once '../include/class.accountHead.php';
    require_once '../include/class.employeeType.php';
    require_once '../include/class.gpftotal.php';
    require_once '../include/class.remarks.php';
    require_once '../include/class.salutation.php';
    require_once '../include/class.loan.php';
    require_once '../include/class.housing.php';
    
    $personalInfo = new personalInfo();
    $employeeInfo = new employeeInfo();
    $department = new department();
    $accountHead = new accountHead();
    $bank = new bank();
    $designation = new designation();
    $accounts = new reporting();
    $employee = new employeeType();
    $gpfTotal = new gpfTotal();
    $remarks = new remarks();
	$salutation = new salutation();
	$loan = new loan();
	$housing = new housing() ;
    if(!$accounts->checkLogged())
        $accounts->redirect('../');

	if( isset($_GET))
	{
		if(isset($_GET['date']))
			$date = $_GET['date']; 
			else
			$housing->redirect('./report_quarter.php');
	}
	else
		$housing->redirect('./report_quarter.php');

    if(!$accounts->isSalaryDataAvailiable($date))
    	$accounts->palert("No Salary Data Exists For The Given Month. Please Select Another Month", "./report_quarter.php");
    	    
    $currentMonth = substr($date, 4, 2);
   	$currentYear = substr($date, 0, 4);
                    
    $fiscalYear = $accounts->getFiscalYearMonth($date);
    $fiscalStartMonth = $fiscalYear[1] - 1;
    $fiscalStartYear = $fiscalYear[0];
    $flag = true; //to check if there is any salary data availiable for the given employee
    ob_end_flush();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<title>Quarterwise Statement of <?php echo ucwords($accounts->getNumber2Month($currentMonth)).", ".$currentYear; ?></title>
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
        <table width="1024px" align="center" border=".2">
             <tr><td colspan="7" width="100%">
						<table border="0" align="center" width="100%">
						<tr>
						<td align="center" width="160" height="111px"><img src="../img/mnnit_logo.gif" alt="mnnit logo" width="126" height="131px" align="left" /></td>
						<td align="center" width="743"><font class="bigheader">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class="smallheader">
						ALLAHABAD - 211004<br /><br />
						ACCOUNTS DEPARTMENT -- QUARTER WISE SLIP FOR </font>
                        <?php echo "<font class=\"month\">".$accounts->getNumber2Month($currentMonth)." ".$currentYear."</font>"; ?>
                        </td>						
						</tr>
                        </table>
			</td></tr>
        <tr><td><b>Sr. No.</b></td><td><b>Name</b></td><td><b>Quarter</b></td><td><b>House Rent</b></td><td><b>Electric Charge</b></td><td><b>Water Charge</b></td><td><b>Total</b></td></tr>
		<?php
		$count = 0 ;
	/*	$housingType = $housing->getHousingIds(true) ; */
             /* New Code for sorting on the basis of quarter no */

           
	/*	foreach ($housingType as $housingHead)
		{ */
	/*		$empID = $housing->getEmpIDByHousing($housingHead) ; */
                       $empID = $personalInfo->getEmployeeIdsByQuarter(true);
			foreach($empID as $ID)
			{

				$personalInfo-> getEmployeeInformation($ID,true);
				$tempAdd = $personalInfo->getTemporarAddress();
				$name = $personalInfo->getName();
				if($tempAdd == "na" ) continue ;
				$total = 0 ;
				$houseRent = $accounts->getSalaryAllowanceInfo($ID,$date,"ACT24",true) ;
				$electricCharge = $accounts->getSalaryAllowanceInfo($ID,$date,"ACT25",true);
				$waterCharge = $accounts->getSalaryAllowanceInfo($ID,$date,"ACT26",true);
				$total = $houseRent+$electricCharge+$waterCharge ;
                                /* New Code for sorting on the basis of quarter no */

                               

				echo "<tr><td>".++$count."</td><td>".$name."</td><td>".$tempAdd."</td><td>".-1*$houseRent."</td><td>".-1*$electricCharge."</td><td>".-1*$waterCharge."</td><td>".-1*$total."</td></tr>" ;
			}
	/*	} */
		?>
        </table>
        </center>
	</body>
</html>
