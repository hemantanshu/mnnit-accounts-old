<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
	////error_reporting(0);
    session_start();

    require_once '../include/class.personalInfo.php';
    require_once '../include/class.editloan.php';
    
    $personalInfo = new personalInfo();
    $loan = new editLoan();

    if(!$loan->checkLoanOfficerLogged())
        $loan->redirect('../');

    ob_end_flush();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- Loan Interest Information</title>
<link rel="stylesheet" type="text/css" href="../include/default.css" media="screen" />
<script type="text/javascript" src="../include/jquery.min.js"></script>
<script type="text/javascript" src="../include/ddaccordion.js"></script>
<script language="javascript" type="text/javascript">
						var currenttime = "<?php
									date_default_timezone_set('Asia/Calcutta');
									print date("F d, Y H:i:s", time())?>"
			var montharray=new Array("January","February","March","April","May","June","July","August","September","October","November","December")
			var serverdate=new Date(currenttime)

			function padlength(what)
			{
				var output=(what.toString().length==1)? "0"+what : what
				return output
			}

			function displaytime()
			{
				serverdate.setSeconds(serverdate.getSeconds()+1)

				var datestring=montharray[serverdate.getMonth()]+" "+padlength(serverdate.getDate())+", "+serverdate.getFullYear()
				var timestring=padlength(serverdate.getHours())+":"+padlength(serverdate.getMinutes())+":"+padlength(serverdate.getSeconds())
				document.getElementById("servertime").innerHTML=datestring+" "+timestring
			}

			window.onload=function()
			{
				setInterval("displaytime()", 1000)
			}

   	</script>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>

<body>
<div>
  <div class="top">
    <div class="header">
      <div class="left"><img class="imgright" src="../img/logo.gif" alt="Forest Thistle" height="105px">&nbsp;Accounts Department</div>
      <!--<div class="right">
        <div align="center"> MNNIT <br />
          ALLAHABAD</div>-->
    </div>
    </div>
  </div>
  <div class="container">
    <div class="navigation">
    	<a href="./" target="_parent">Home</a>
        <a href="changePassword.php" target="_parent">Change Password</a>
        <a href="./logout.php" target="_parent">Logout</a>
        <a href="#" target="_parent">&nbsp;&nbsp; &nbsp;Server Time : <span id="servertime"></span></a>
<div class="clearer"><span></span> </div>
    </div>

    <div class="main">
      <div class="contentLarge">
      	<form action="./" method="post">
      	<?php 
      		if ($_POST['submit'] != "Get Interest On These Loans")
      			$loan->redirect('./');
      		$i=0;
      		$count = 0;
      		
      		while (true){
      			$checkboxName = "checkbox".$i;
                $loanIdName = "loanId".$i;
                ++$i;                
                if (!isset($_POST[$loanIdName]))
                	break;
                $newCheckboxName = 	"checkbox1".$count;
                $newLoanIdName = "loanId1".$count;
                
                if ($_POST[$checkboxName] == 'y'){
                	++$count;
                	$loanId = $_POST[$loanIdName];
                	$details = $loan->getLoanAccountIdDetails($loanId);
                	$personalInfo->getEmployeeInformation($details[1], true);
                	echo "
                		<table align=\"center\" border=\"0\" width=\"100%\">				        	
				        	<tr>
				            	<td align=\"right\">Employee Name</td>
				                <td align=\"center\" width=\"5%\">:</td>
				                <th align=\"left\">".$personalInfo->getName()."</th>
				            	<td align=\"right\">Employee Code</td>
				                <td align=\"center\" width=\"5%\">:</td>
				                <th align=\"left\">".$personalInfo->getEmployeeCode()."</th>            	
				            </tr>
				            <tr>
				            	<td height=\"5px\"></td>
				            </tr>
				            <tr>
				            	<td align=\"right\">Loan Sanctioned Month</td>
				                <td align=\"center\">:</td>
				                <th align=\"left\">".$loan->nameMonth($details[7])."</th>
				            	<td align=\"right\">Loan Amount Sanctioned</td>
				                <td align=\"center\">:</td>
				                <th align=\"left\">".number_format($details[3], 2, '.', '')."</th>
				            </tr>            
				            <tr>
				            	<td align=\"center\" valign=\"top\" colspan=\"6\">
				                	<table border=\"0\" align=\"center\" width=\"100%\">                    	
				                    	<tr>
				                        	<th width=\"5%\">SN</th>
				                            <th width=\"30%\">Remarks</th>
				                            <th width=\"15%\" align=\"right\">Month</th>
				                            <th width=\"15%\" align=\"right\">Credit</th>
				                            <th width=\"15%\" align=\"right\">Debit</th>
				                            <th width=\"15%\" align=\"right\">Balance</th>
				                            <th width=\"*\" align=\"right\">Interest</th>                            
				                        </tr>
				      					<tr>
				                        	<td colspan=\"7\"><hr size=\"1\" /></td>
				                        </tr>";
                	$completeInstallmentId = $loan->getEmployeeLoanInstallmentId($loanId);
                	$sum = 0;
                	$creditTotal = 0;
                	$debitTotal = 0;
                	$counter = 0;
                	$interestDetails = $loan->getInterestOnLoan($loanId, false);
                	
                	foreach ($completeInstallmentId as $installmentId){               		
                		$details = $loan->getEmployeeLoanInstallmentIdDetails($installmentId);
                		if ($details[3] <= $interestDetails[0]){
                			$sum += $details[2];
                			$creditTotal += $details[2];
                			continue;
                		}		                		
                		
                		$sum += $details[2];
                		$creditTotal += $credit = $details[2] > 0 ? $details[2] : 0 ;
                		$debitTotal += $debit = $details[2] < 0 ? abs($details[2]) : 0 ;                		
                		if ($counter == 0)
	                		echo "				                                          
					                        <tr>
					                        	<th>$counter</th>
					                            <th align=\"left\">Balance Brought Forward</th>
					                            <th align=\"right\">".$loan->nameMonth($details[3])."</th>
					                            <th align=\"right\">".number_format($sum, 2, '.', '')."</th>
					                            <th align=\"right\">".number_format(0, 2, '.', '')."</th>
					                            <th align=\"right\">".number_format($sum, 2, '.', '')."</th>
					                            <th align=\"right\">".number_format(0, 2, '.', '')."</th>
					                        </tr>";
					    else 
					        echo "				                                          
					                        <tr>
					                        	<th>$counter</th>
					                            <th align=\"left\">".ucwords(strtolower($details[4]))."</th>
					                            <th align=\"right\">".$loan->nameMonth($details[3])."</th>
					                            <th align=\"right\">".number_format($credit, 2, '.', '')."</th>
					                            <th align=\"right\">".number_format($debit, 2, '.', '')."</th>
					                            <th align=\"right\">".number_format($sum, 2, '.', '')."</th>
					                            <th align=\"right\">".number_format($interestDetails[$counter], 2, '.', '')."</th>
					                        </tr>";
					       echo "              
				                        <tr>
				                        	<td colspan=\"8\"><hr size=\"1\" /></td>
				                        </tr>";
                		++$counter;
                	}
                	
                	echo "                        
				                    </table>
				                </td>
				            </tr>
				            <tr>
				            	<td colspan=\"6\" height=\"10px\"><hr size=\"2\" /></td>
				            </tr>
				        </table>
				        ";
                }
      		}
      	?>
      	
        </form>
      </div>      
      <div class="clearer"><span></span></div>
    </div>
    <div class="footer">@webteam.<a href="http://www.mnnit.ac.in" title="MNNIT">mnnit</a> Designed And Developed By Hemant Kumar Sah (B.Tech ECE 2011)</div>
  </div>
</div>
</body>
</html>