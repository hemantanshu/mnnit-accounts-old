<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    
	ob_start();
    //error_reporting(0);
    session_start();

    require_once '../include/class.accountInfo.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.employeeInfo.php';
    require_once '../include/class.allowance.php';
    require_once '../include/class.department.php';    
    require_once '../include/class.directSalaryAddition.php';
    require_once '../include/class.gpftotal.php';
    require_once '../include/class.loan.php';
    require_once '../include/class.employeeType.php';
    
    

    $accounts = new accounts();
    $salaryAddition = new directSalaryAddition();
    if(!$accounts->checkLogged())
            $accounts->redirect('../');
                
    $personalInfo = new personalInfo();
    $employeeInfo = new employeeInfo();
    $allowance = new allowance();
    $department = new department();
    $loan = new loan();
    $gpfTotal = new gpfTotal();
    $employeeType = new employeeType();
    
    if( isset ($_GET['type']) && isset ($_GET['value'])){
        $processingType = $_GET['type'];
        $processingValue = $_GET['value'];  
    }else
        $accounts->redirect('./salary_fakeslip.php');
    
    
    $completeEmployeeId = array();
    $variable = $employeeInfo->getEmployeeIds(true, 'salaryProcess');

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
        $accounts->palert("No Information Is There For The Salary Slip", './');
    }
    
    $blockedEmployee = $employeeInfo->getEmployeeIds(true, "block");
    ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- Fake Salary Slip Information</title>
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

<script type="text/javascript">

ddaccordion.init({
	headerclass: "headerbar", //Shared CSS class name of headers group
	contentclass: "submenu", //Shared CSS class name of contents group
	revealtype: "click", //Reveal content when user clicks or onmouseover the header? Valid value: "click", "clickgo", or "mouseover"
	mouseoverdelay: 200, //if revealtype="mouseover", set delay in milliseconds before header expands onMouseover
	collapseprev: true, //Collapse previous content (so only one open at any time)? true/false
	defaultexpanded: [0], //index of content(s) open by default [index1, index2, etc] [] denotes no content
	onemustopen: true, //Specify whether at least one header should be open always (so never all headers closed)
	animatedefault: false, //Should contents open by default be animated into view?
	persiststate: true, //persist state of opened contents within browser session?
	toggleclass: ["", "selected"], //Two CSS classes to be applied to the header when it's collapsed and expanded, respectively ["class1", "class2"]
	togglehtml: ["", "", ""], //Additional HTML added to the header when it's collapsed and expanded, respectively  ["position", "html1", "html2"] (see docs)
	animatespeed: "normal", //speed of animation: integer in milliseconds (ie: 200), or keywords "fast", "normal", or "slow"
	oninit:function(headers, expandedindices){ //custom code to run when headers have initalized
		//do nothing
	},
	onopenclose:function(header, index, state, isuseractivated){ //custom code to run whenever a header is opened or closed
		//do nothing
	}
})

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
      <div class="content">    
        <table align="center" border="0" width="100%">
            <?php 
            	foreach ($completeEmployeeId as $employeeId){
                        $totalSum = $totalCredit = $totalDebit = 0;
            		$personalInfo->getEmployeeInformation($employeeId, true);
            		echo "
            			<tr>
			            	<td height=\"10px\"><hr size=\"3\" style=\"color:red\" /></td>
			            </tr>
			            <tr>
			            	<td width=\"100%\">
			       				<table align=\"center\" width=\"100%\">
			                    	<tr>
			                        	<td align=\"right\">Name</td>
			                            <td align=\"center\">:</td>
			                            <th align=\"left\">".$personalInfo->getName()."</th>
			                            <td align=\"right\">Emp Code</td>
			                            <td align=\"center\">:</td>
			                            <th align=\"left\">".$personalInfo->getEmployeeCode()."</th>
			                        </tr>
			                        <tr>
			                        	<td align=\"right\">Department</td>
			                            <td align=\"center\">:</td>
			                            <th align=\"left\">".$department->getDepartmentName($personalInfo->getDepartment())."</th>
			                            <td align=\"right\">Emp Type</td>
			                            <td align=\"center\">:</td>
			                            <th align=\"left\">".$employeeType->getEmployeeTypeName($personalInfo->getEmployeeType())."</th>
			                        </tr>
			                        <tr>
			       									<td colspan=\"6\"><hr size=\"1\" /></td>
			       								</tr>
			                        <tr>
			                        	<td colspan=\"6\">
			                            	<table align=\"center\" width=\"100%\">";
            		$salaryId = $employeeInfo->getMasterSalaryId($employeeId, true);            		
            		$totalSum = $accounts->getEmployeeBasicSalary($employeeId);
                        $totalCredit = $totalSum;
			                            	
            		
			       echo "                    	<tr>
			             	                      	<td width=\"10%\" align=\"center\">1</td>
			                                        <td width=\"40%\" align=\"right\">Basic Salary</td>
			                                        <td width=\"25%\" align=\"right\">".number_format($totalSum, 2, '.', ',')."</td>
			                                        <td width=\"25%\" align=\"right\">Credit</td>
			                                    </tr>
			                                    ";     
			       $i = 1;                       
			       foreach ($salaryId as $value){
			       		$details = $employeeInfo->getSalaryIdDetails($value, true);
			       		if ($details[6] == "c"){
			       			$type = "Credit";
			       			$totalSum += $details[5];
                                                $totalCredit += $details[5];
			       		}else{
			       			$type = "Debit";
			       			$totalSum -= $details[5];
                                                $totalDebit += $details[5];
			       		}
			       		++$i;
			       		echo "                  <tr>
			             	                      	<td width=\"10%\" align=\"center\">$i</td>
			                                        <td width=\"40%\" align=\"right\">".$allowance->getAllowanceTypeName($details[4])."</td>
			                                        <td width=\"25%\" align=\"right\">".number_format($details[5], 2, '.', ',')."</td>
			                                        <td width=\"25%\" align=\"right\">$type</td>
			                                    </tr>";
			       }    
			 		echo "    <tr>
			       									<td colspan=\"4\"><hr size=\"1\" /></td>
			       								</tr>
			                                </table>
			                            </td>
			                        </tr>
			                        
			                        <tr>
			                        	<th colspan=\"7\">Additional Salary</th>
			                        </tr>                        
			                        <tr>
			                        	<td colspan=\"6\">
			                            	<table align=\"center\" width=\"100%\">";
            	   $extraSalaryId = $salaryAddition->getEmployeeAdditionalSalaryIds($employeeId);
			       foreach ($extraSalaryId as $value){
			       		$details = $salaryAddition->getAdditionalSalaryIdDetails($value);
			       		if ($details[4] == "c"){
			       			$type = "Credit";
			       			$totalSum += $details[3];
                                                $totalCredit += $details[3];
			       		}else{
			       			$type = "Debit";
			       			$totalSum -= $details[3];
                                                $totalDebit += $details[3];
			       		}
			       		++$i;
			       		echo "                  <tr>
			             	                      	<td width=\"10%\" align=\"center\">$i</td>
			                                        <td width=\"40%\" align=\"right\">".$allowance->getAllowanceTypeName($details[2])."</td>
			                                        <td width=\"25%\" align=\"right\">".number_format($details[3], 2, '.', ',')."</td>
			                                        <td width=\"25%\" align=\"right\">$type</td>
			                                    </tr>";
			       }    
			 		echo "    <tr>
			       									<td colspan=\"4\"><hr size=\"1\" /></td>
			       								</tr>
			                                </table>
			                            </td>
			                        </tr>
			                        
			                        
			                        <tr>
			                        	<th colspan=\"6\">Normal Loan Account</th>
			                        </tr>                        
			                        <tr>
			                        	<td colspan=\"6\">
			                            	<table align=\"center\" width=\"100%\">";
            	   $loanAccount = $loan->getEmployeeActiveLoanId($employeeId);
			       foreach ($loanAccount as $value){
			       		if ($loan->isLoanInstallmentBlocked($value, $accounts->getCurrentMonth()))
			       			continue;
			       		$details = $loan->getLoanAccountIdDetails($value);
			       		$details1 = $loan->getLoanTypeIdDetails($details[2]);
			       		$amount = $loan->getInstallmentAmount($value);
			       		$totalSum -= $amount;
                                        $totalDebit += $amount;
			       		++$i;
			       		echo "                  <tr>
			             	                      	<td width=\"10%\" align=\"center\">$i</td>
			                                        <td width=\"40%\" align=\"right\">".$allowance->getAllowanceTypeName($details1[1])."</td>
			                                        <td width=\"25%\" align=\"right\">".number_format($amount, 2, '.', ',')."</td>
			                                        <td width=\"25%\" align=\"right\">Debit</td>
			                                    </tr>";
			       }    
			 		echo "    <tr>
			       									<td colspan=\"4\"><hr size=\"1\" /></td>
			       								</tr>
			                                </table>
			                            </td>
			                        </tr>
			                        <tr>
			                        	<th colspan=\"6\">GPF Loan Account</th>
			                        </tr>                        
			                        <tr>
			                        	<td colspan=\"6\">
			                            	<table align=\"center\" width=\"100%\">";
            	   $loanAccount = $gpfTotal->getEmployeeGpfLoanAccountId($employeeId);
			       foreach ($loanAccount as $value){			       				       		
			       		$amount = $loan->getInstallmentAmount($employeeId);
			       		$totalSum -= $amount;
                                        $totalDebit += $amount;
			       		++$i;
			       		echo "                  <tr>
			             	                      	<td width=\"10%\" align=\"center\">$i</td>
			                                        <td width=\"40%\" align=\"right\">GPF Advance Rec.</td>
			                                        <td width=\"25%\" align=\"right\">".number_format($amount, 2, '.', ',')."</td>
			                                        <td width=\"25%\" align=\"right\">Debit</td>
			                                    </tr>";
			       }    
			       ++$i;
			       
			       	echo "
			       								<tr>
			       									<td colspan=\"4\"><hr size=\"1\" /></td>
			       								</tr>
			       			            <tr>
			             	                      	<th width=\"10%\" align=\"center\">".$i++."</th>
			                                        <th width=\"40%\" align=\"right\">Gross Salary</th>
			                                        <th width=\"25%\" align=\"right\">".number_format($totalCredit, 2, '.', ',')."</th>
			                                        <th width=\"25%\" align=\"right\">Credit</th>
			                                    </tr>
                                                            <tr>
			             	                      	<th width=\"10%\" align=\"center\">".$i++."</th>
			                                        <th width=\"40%\" align=\"right\">Total Deductions</th>
			                                        <th width=\"25%\" align=\"right\">".number_format($totalDebit, 2, '.', ',')."</th>
			                                        <th width=\"25%\" align=\"right\">Debit</th>
			                                    </tr>
                                                            <tr>
                                                                  <td colspan=\"4\"><hr size=\"1\" /></td>
                                                            </tr>
                                                            <tr>
			             	                      	<th width=\"10%\" align=\"center\">".$i++."</th>
			                                        <th width=\"40%\" align=\"right\">Total Amount Payable</th>
			                                        <th width=\"25%\" align=\"right\">".number_format($totalSum, 2, '.', ',')."</th>
			                                        <th width=\"25%\" align=\"right\">Credit</th>
			                                    </tr>";
			 		echo "    
			                                </table>
			                            </td>
			                        </tr>
			                    </table>     				
			            	</td>
			            </tr>";
			 		if(in_array($employeeId, $blockedEmployee))
			 			echo "			            
			            <tr>
			            	<td height=\"15px\" align=\"center\"><h3 style=\"color: red;\">The Salary For This Employee Is Blocked For This Month</h3></td>
			            </tr>";
			 		echo "			            
			            <tr>
			            	<td height=\"15px\"></td>
			            </tr>";
            	}            
            ?>
            </table>
      </div>
      <div class="sidenav">
      	<hr size="2" />
       <center> <font color="#FF0000" size="+1"><b><?php echo $accounts->getOfficerName(); ?></b></font></center>
       	<hr size="2" /><br />
        <h2><font color="#008000">QUICK NAVIGATION PANEL</font></h2>
        <?php
            include './navigation/navigation.php';
        ?>
      </div>
      <div class="clearer"><span></span></div>
    </div>
    <div class="footer">@webteam.<a href="http://www.mnnit.ac.in" title="MNNIT">mnnit</a> Designed And Developed By Hemant Kumar Sah (B.Tech ECE 2011)</div>
  </div>
</div>
</body>
</html>