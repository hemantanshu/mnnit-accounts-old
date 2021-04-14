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
    require_once '../include/class.employeePending.php';
    
    

    $accounts = new accounts();
    $salaryAddition = new directSalaryAddition();
    if(!$accounts->checkLogged())
            $accounts->redirect('../');
    if(isset($_POST) && $_POST['submit'] == "Confirm Processing Of Salary"){
    	$processingType = $_POST['type'];
    	$processingValue = $_POST['value'];
    	$i = 1;
    	while (true){
    		$checkbox = "checkbox".$i;
    		$employeeName = "employee".$i;
    		if(!isset($_POST[$employeeName]))
    			break;
    		if($_POST[$checkbox] != "y")
    			$accounts->palert("Please Check The Employee Salary As There Are Employees With Critical Salaries", "./process_msalaryc.php?type=$processingType&value=$processingValue");
    		++$i;
    	}
    	$accounts->redirect("./process_msalarye.php?type=$processingType&value=$processingValue&process=ok");
    }
                
    $personalInfo = new personalInfo();
    $employeeInfo = new employeeInfo();
    $allowance = new allowance();
    $department = new department();
    $loan = new loan();
    $gpfTotal = new gpfTotal();
    $employeeType = new employeeType();
    $ePending = new employeePending();
    
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
<title>Accounts Section -- Critical Salary Problem</title>
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
      	<form action="" method="post">   
        <table align="center" border="0" width="100%">
            <tr>
            	<td colspan="7" align="center"><h2>EMPLOYEES WITH CRITICAL SALARY, CHECK FOR THESE</h2> <hr size="1" /><br /></td>
            </tr>
            <tr>
                <th align="center" width="5%">S.N.</th>
                <th align="left" width="15%">Emp. Code</th>
                <th align="left" width="25%">Name</th>                        
                <th align="right" width="13%">Gross Salary</th>
                <th align="right" width="13%">Total Deductions</th>
                <th align="right" width="13%">Net Payable</th>
                <th align="center" width="*">Check</th>
            </tr>
            <tr>
            	<td colspan="7"><br /><hr size="1" /><br /></td>
            </tr>
            
            <?php
            	$flag = true;
                $count = 1; 
                
            	foreach ($completeEmployeeId as $employeeId){
            		if($ePending->isEmployeeSalaryInPendingStatus($employeeId) || $ePending->isEmployeeSalaryProcessed($employeeId))
            			continue;            		
            		if(in_array($employeeId, $blockedEmployee))
            			continue;                    
            		$totalSum = $totalCredit = $totalDebit = 0;
            		$personalInfo->getEmployeeInformation($employeeId, true);
            		
            		$salaryId = $employeeInfo->getMasterSalaryId($employeeId, true);            		
            		$totalSum = $accounts->getEmployeeBasicSalary($employeeId);
                    $totalCredit = $totalSum;  		
			                                   
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
			       }    
			 		
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
			       }
			       	
            	   $loanAccount = $loan->getEmployeeActiveLoanId($employeeId);
			       foreach ($loanAccount as $value){
			       		if ($loan->isLoanInstallmentBlocked($value, $accounts->getCurrentMonth()))
			       			continue;
			       		$details = $loan->getLoanAccountIdDetails($value);
			       		$details1 = $loan->getLoanTypeIdDetails($details[2]);
			       		$amount = $loan->getInstallmentAmount($value);
			       		$totalSum -= $amount;
                    	$totalDebit += $amount;			       		
			       }    
			 		
            	   $loanAccount = $gpfTotal->getEmployeeGpfLoanAccountId($employeeId);
			       foreach ($loanAccount as $value){			       				       		
			       		$amount = $loan->getInstallmentAmount($employeeId);
			       		$totalSum -= $amount;
                       	$totalDebit += $amount;	
			       }
	            	$difference = $totalSum - $totalCredit * .25;
	            	if($difference < 0){
	            		$flag = false;
	            		$checkbox = "checkbox".$count;
	            		$employeeName = "employee".$count;
	            		echo "
		                    <tr>
		                        <td align=\"left\"><input type=\"hidden\" name=\"$employeeName\" value=\"$employeeId\" />$count</td>
		                        <td align=\"center\">".$personalInfo->getEmployeeCode()."</td>
		                        <td align=\"left\">".$personalInfo->getName()."</td>
		                        <td align=\"right\">".number_format($totalCredit, 2, '.', ',')."</td>
		                        <td align=\"right\">".number_format($totalDebit, 2, '.', ',')."</td>
		                        <td align=\"right\">".number_format($totalSum, 2, '.', ',')."</td>
		                        <td align=\"center\"><input type=\"checkbox\" name=\"$checkbox\" value=\"y\" /></td>
		                    </tr>
		                    <tr>
		                    	<td height=\"10px\"></td>
		                    </tr>";	
	            		++$count;
	            	}   
			break; 		 			
            	}         	
            	if (!$flag){            		
            		echo "
            			<tr>
			            	<td colspan=\"7\"><br /><hr size=\"1\" /><br /></td>
			            </tr>
            			<tr>
		                    <td colspan=\"7\" align=\"center\">
		                    <input type=\"hidden\" name=\"type\" value=\"$processingType\" />
		                    <input type=\"hidden\" name=\"value\" value=\"$processingValue\" />
		                    <input type=\"hidden\" name=\"option\" value=\"process\" />
		                    <input type=\"submit\" name=\"submit\" value=\"Confirm Processing Of Salary\" style=\"width:300px\" />&nbsp;&nbsp;&nbsp;
		                    <input type=\"button\" name=\"button\" value=\"Return Back\" onclick=\"window.location='./salary_process.php'\" style=\"width:150px\" />&nbsp;&nbsp;&nbsp;
		                    </td>
		               </tr> ";
            	}else{            		
            		$accounts->palert("There Is No Problem In The Salary Amount. Proceed Ahead", "./process_msalarye.php?value=$processingValue&type=$processingType&process=ok");
            	}                  
            ?>
               
            </table>
            </form>
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