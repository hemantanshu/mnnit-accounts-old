<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
    //error_reporting(0);
    session_start();
    
    require_once '../include/class.loggedInfo.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.allowance.php';
    require_once '../include/class.accountInfo.php';
    require_once '../include/class.accountHead.php';    
    require_once '../include/class.directSalaryAddition.php';    
    
    
    $loggedInfo = new loggedIn();
    $personalInfo = new personalInfo();    
    $allowance = new allowance();
	$accounts = new accounts();
	$accountHead = new accountHead();
	$salaryAddition = new directSalaryAddition();
    
	$employeeId = $loggedInfo->checkEmployeeLogged();
    if(!$employeeId)
        $loggedInfo->redirect('../');
    if(isset($_POST) && $_POST['submit'] == "Update Allowance Info"){
    	$i = 0;
    	while (true){
    		$option = "option".$i;
			$allowanceName = "allowance".$i;
			$amountName = "amount".$i;
			$checkbox = "checkbox".$i;
			++$i;

			if(!isset($_POST[$allowanceName]))
				break;
			if($_POST[$checkbox] == 1 && $_POST[$amountName] != 0 && is_numeric($_POST[$amountName])){				
				$amount = $_POST[$option] == 'c' ? abs($_POST[$amountName]) : (0 - abs($_POST[$amountName]));
				$salaryAddition->insertDirectSalary($employeeId, $_POST[$allowanceName], $amount, 'n');
			}
    	}
    	$allowance->palert("Your Request Has Been Accepted And Is In Pending Status Till The Final Consent From The Admin.", './');
    }
        
 	$personalInfo->getEmployeeInformation($employeeId, true);
    
    ob_end_flush();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section</title>
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
       			<td colspan="6" align="center"><h3>UPDATING ALLOWANCE / DEDUCTION HEADS</h3></td>
            </tr>
            <tr>
            	<td colspan="6"><hr size="1" /></td>
            </tr>
            <tr>
            	<th width="5%">S.N.</th>
                <th width="40%" align="left">Allowance Name</th>
                <th width="15%" align="left">Previous Amt.</th>
                <th width="15%" align="center">Amount</th>                
                <th width="15%" align="center">Type</th>
                <th width="*" align="center">Select</th>
            </tr>
            <tr>
            	<td colspan="6" style="padding-top:10px; padding-bottom:10px"><hr size="2" /></td>
            </tr>
            <?php 
            	$completeAllowanceIds = $allowance->getAllowanceIds(true);
            	$i = 0;
            	foreach ($completeAllowanceIds as $allowanceId){
            		if($allowance->isAllowanceUpdateable($allowanceId)){
            			$amount = $accounts->getEmployeeSalaryInfo($employeeId, $allowanceId);
            			
            			$option = "option".$i;
            			$allowanceName = "allowance".$i;
            			$amountName = "amount".$i;
            			$checkbox = "checkbox".$i;
            			++$i;
            			echo "
            				<tr>
								<td align=\"center\">$i</td>
								<td align=\"left\">".$accountHead->getAccountHeadName($allowance->getAllowanceAccountHead($allowanceId))."</td>
								<td align=\"left\">".$amount."</td>
								<td align=\"center\">
									<input type=\"hidden\" name=\"$allowanceName\" value=\"$allowanceId\" />
									<input type=\"text\" name=\"$amountName\" value=\"".abs($amount)."\" style=\"width:80px\" /></td>
								<td align=\"center\">
									<select name=\"$option\" style=\"width:80px\">
										<option value=\"c\">Credit</option>                    
										<option value=\"d\">Debit</option>
									</select></td>
								<td align=\"center\" width=\"*\"><input type=\"checkbox\" name=\"$checkbox\" value=\"1\" /></td>
							</tr>
							<tr>
								<td style=\"padding-bottom:5px; padding-top:5px;\" colspan=\"6\"><hr size=\"1\" /></td>
							</tr>";
            		}
            	}
            ?>
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
            	<td colspan="6" align="center"><input type="submit" name="submit" value="Update Allowance Info" />&nbsp;&nbsp;&nbsp;<input type="button" value="Return Back" onclick="window.location='./'" /></td>
            </tr>
            
        </table>
        </form>
      </div>
      <div class="sidenav">
      	<hr size="2" /><center>
        <font color="#FF0000" size="+1"><b><?php echo $personalInfo->getName(); ?></b></font>
       	<hr size="2" /></center><br />
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