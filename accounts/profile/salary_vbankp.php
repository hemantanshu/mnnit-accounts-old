<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
	//error_reporting(0);

    session_start();

    require_once '../include/class.accountInfo.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.employeeInfo.php';
    require_once '../include/class.bank.php';

    $personalInfo = new personalInfo();
    $employeeInfo = new employeeInfo();
    $bank = new bank();
    $accounts = new accounts();


    if(!$accounts->checkLogged())
        $accounts->redirect('../');

    if(isset($_POST) && $_POST['submit'] == "Print Bank Slip"){
    	$date = $_POST['date'];
    	$accounts->redirect("./salary_vbankp.php?date=$date");
    }elseif(isset ($_GET['date'])){
        $date = $_GET['date'];
    }else
        $accounts->redirect('./salary_vbank.php');

    $completeEmployeeId = $employeeInfo->getEmployeeIds(true, 'bank', $date);

    if(!sizeof($completeEmployeeId))
        $accounts->palert("No Information For This Date Combination Exists ", "./salary_vbank.php");
	
    $currentMonth = substr($date, 4, 2);
   	$currentYear = substr($date, 0, 4);            
    
   	function customHeader($accounts, $currentMonth, $currentYear){
   		echo "
				<table width=\"1024px\" align=\"center\" border=\".2\">
					<tr>
						<td colspan=\"3\" width=\"100%\">
							<table border=\"0\" align=\"center\" width=\"100%\">
								<tr>
									<td align=\"center\" width=\"160\" height=\"111px\"><img src=\"../img/mnnit_logo.gif\" alt=\"mnnit logo\" width=\"126\" height=\"111px\" align=\"left\" /></td>
									<td align=\"center\" width=\"743\"><font class=\"bigheader\">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class=\"smallheader\">
																	ALLAHABAD - 211004<br /><br />
																	BANK STATEMENT DETAILS </font>";
			echo "<font class=\"month\">".$accounts->getNumber2Month($currentMonth)." ".$currentYear."</font>";
			echo						"</td>
									
								</tr>		
							</table>
						</td>
					</tr>
				</table>";
   	}
   	
   	
   	ob_end_flush();
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<title>BANK Statement 0f <?php echo ucwords($accounts->getNumber2Month($currentMonth)).", ".$currentYear; ?></title>
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
			font.salaryprint{
				font-family:Verdana, Geneva, sans-serif;
				font-size:20px;
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
		<?php 
			$previousBankDetails = array();
			$chequeEmployee = array();
			$i = 0;
			$total = 0;
			foreach ($completeEmployeeId as $employeeId) {
				$bankDetails = $employeeInfo->getReservedEmployeeBankAccountDetails($employeeId, $date);
				if($bankDetails[0] == ''){
					array_push($chequeEmployee, $employeeId);
					continue;
				}					
				if($previousBankDetails[0] != $bankDetails[0]){
					$totals = 0;
					if($i != 0)
                                               echo "<p class=\"break\"></p>";
					customHeader($accounts, $currentMonth, $currentYear);                   
					echo "						
						<table width=\"1024px\" align=\"center\" border=\"1\">
				        	<tr>
				            	<td align=\"center\" colspan=\"5\">FOR BANK : <b>".$bank->getBankName($bankDetails[0])."</b><br /><hr size=\"1\" /></td>
				            </tr>
				            <tr>
				            	<th>S.N.</th>
				                <th>Emp. Code</th>
				                <th align=\"left\">Name</th>
				                <th align=\"left\">Account Number</th>
				                <th align=\"right\">Amount</th>
				            </tr>";
				}
				++$i;
				$previousBankDetails = $bankDetails;
				$personalInfo->getEmployeeInformation($employeeId, true);
				$sum = $accounts->getProcessedSalarySum($employeeId, $date);
				
				$total += $sum;
				$totals += $sum;
				
				echo "
				<tr style=\"padding-bottom:3px; padding-top:3px\">
		            	<th align=\"center\"><font class=\"salaryprint\">".$i."</font></th>
		                <th align=\"left\"><font class=\"salaryprint\">".$personalInfo->getEmployeeCode()."</font></th>
		                <th align=\"left\"><font class=\"salaryprint\">".strtoupper($personalInfo->getName())."</font></th>
		                <th align=\"left\"><font class=\"salaryprint\">".$bankDetails[1]."</font></th>
		                <th align=\"right\"><font class=\"salaryprint\">".number_format($sum, 2, '.', '')."</font></th>
		            </tr>";
				
			}
                        echo "<tr>
				            	<th colspan=\"4\">Total Amount</th>
				                <th align=\"right\"><font class=\"salaryprint\">".number_format($total, 2, '.', '')."</font></th>
				            </tr>
                            ";		
			
			echo "</table>
                            <table align=\"center\" width=\"1024px\" border=\"0\">
                                <tr>
				            	<th colspan=\"2\" height=\"80px\"></th>
				            </tr>
                              <tr>
                                                <th width=\"40%\"></th>
				            	<th width=\"30%\">____________________________</th>
				                <th width=\"*\">__________________________</th>
				            </tr>
                              <tr>
                                                <th width=\"40%\"></th>
				            	<th>Registrar</th>
				                <th>Director</th>
				            </tr>

                            </table>";

            echo "<p class=\"break\"></p>";
            customHeader($accounts, $currentMonth, $currentYear);
            	echo "						
					<table width=\"1024px\" align=\"center\" border=\"1\">
						<tr>
							<td align=\"center\" colspan=\"4\"><b>SALARY TO BE MADE THROUGH CHEQUE</b><br /><hr size=\"1\" /></td>
						</tr>
						<tr>
							<th width=\"5%\">S.N.</th>
							<th width=\"20%\">Emp. Code</th>
							<th width=\"50%\" align=\"left\">Name</th>
							<th width=\"*\" align=\"right\">Amount</th>
						</tr>";
            $i = 0;
            $chequeAmount = 0;
            foreach ($chequeEmployee as $employeeId){
            	$personalInfo->getEmployeeInformation($employeeId, true);
				$sum = $accounts->getProcessedSalarySum($employeeId, $date);
                                $chequeAmount += $sum;
				++$i;
				echo "
					<tr style=\"padding-bottom:3px; padding-top:3px\">
		            	<th align=\"center\">$i</th>
		                <th align=\"left\" style=\"padding-left:5px;\"><font class=\"salaryprint\">".$personalInfo->getEmployeeCode()."</font></th>
		                <th align=\"left\" style=\"padding-left:20px;\"><font class=\"salaryprint\">".strtoupper($personalInfo->getName())."</font></th>
		                <th align=\"right\" style=\"padding-left:10px;\"><font class=\"salaryprint\">".number_format($sum, 2, '.', '')."</font></th>
		            </tr>	";
            }
            echo "<tr>
				            	<th colspan=\"3\">Total Amount</th>
				                <th align=\"right\"><font class=\"salaryprint\">".number_format($chequeAmount, 2, '.', '')."</font></th>
				            </tr>
                            ";
			echo "</table>
                            <table align=\"center\" width=\"1024px\" border=\"0\">
                                <tr>
				            	<th colspan=\"2\" height=\"80px\"></th>
				            </tr>
                              <tr>
                                                <th width=\"40%\"></th>
				            	<th width=\"30%\">____________________________</th>
				                <th width=\"*\">__________________________</th>
				            </tr>
                              <tr>
                                                <th width=\"40%\"></th>
				            	<th>Registrar</th>
				                <th>Director</th>
				            </tr>

                            </table>";

		?>
        <div id="logout" align="center"><br /><hr size="1" /><br />
                <input name="back" type="button" value="Print" onclick="window.print()" style="width:200px" />&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="button" value="Export Data To Excel" style="width:200px" onclick="window.location='./salary_vbanke.php?month=<?php echo $date; ?>'" />
                <input name="b2" type="button" value="Return Back" onclick="location='./salary_vbank.php'" style="width:200px" /> &nbsp;&nbsp;&nbsp;
                <br /><br /><br />                
        </div>
	</body>
</html>
