<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
    ////error_reporting(0)

    session_start();
    require_once '../include/class.directSalaryAddition.php';
    require_once '../include/class.allowance.php';

    $salaryAddition = new directSalaryAddition();
    $allowance = new allowance();
    
    if(!$salaryAddition->checkLogged())
        $salaryAddition->redirect('../');

    if(isset($_POST) && $_POST['submit'] == "Process The Given Pending Jobs"){
    	//processing the directallowance request types
    	$count = 0;
    	while (true){
    		$checkbox = "checkbox".$count;
            $pendingId = "pendingId".$count;
            ++$count;
            
            if(!isset($_POST[$pendingId]))
            	break;            	
            if($_POST[$checkbox] == 1)
            	$salaryAddition->insertPendingDirectSalary($_POST[$pendingId]);
            elseif ($_POST[$checkbox] == 0)
            	$salaryAddition->dropPendingAdditionalSalaryRequest($_POST[$pendingId]);
    	}
    	//processing the drop salaryaddition request type
    	$count = 0;
    	while (true){
    		$checkbox = "checkboxd".$count;
            $pendingId = "pendingIdd".$count;
            ++$count;
            
            if(!isset($_POST[$pendingId]))
            	break;
            if($_POST[$checkbox] == 1)
            	$salaryAddition->dropAdditionalSalary($_POST[$pendingId]);
            elseif ($_POST[$checkbox] == 0)
            	$salaryAddition->dropPendingAdditionalSalaryRequest($_POST[$pendingId]);
    	}
    	if($salaryAddition->isAdmin())
    		$salaryAddition->palert("The Pending Jobs Has Been Succesfully Processed", './');
    	else
    		$salaryAddition->palert("The Pending Jobs Has Been Updated And Will Be Processed Once Confirmed By The Admin", './');
    }    
        
    require_once '../include/class.personalInfo.php';
    $personalInfo = new personalInfo();    
    
    if(isset($_GET['type']))
    	$type = $_GET['type'];
    else 
    	$type = 'all';
    
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
            	<td colspan="10" align="center">
                	<a href="./salary_extrap.php?type=all" target="_parent">All Pending Jobs</a>&nbsp;&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="./salary_extrap.php?type=direct" target="_parent">Direct Salary Addition Pending Job</a>&nbsp;&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="./salary_extrap.php?type=drop" target="_parent">Drop Salary Addition Pending Jobs</a></td>
            </tr>
            <tr>
            	<td colspan="10"><br /><hr size="2" /></td>
            </tr>
			<?php 
            	$flag = true;
            	if($type == 'all' || $type == 'direct')
            		$completePendingDirectIds = $salaryAddition->getPendingDirectAllowanceIds();
            	if($completePendingDirectIds){
            		$flag = false;
            		echo "
            			<tr>
			            	<td align=\"center\" colspan=\"10\"><h3>Pending Jobs For Direct Salary Additions</h3></td>
			            </tr>	            
			            <tr>
			            	<td colspan=\"10\"><hr size=\"1\" /></td>
			            </tr>
			            <tr>
			            	<th width=\"5%\">S.N.</th>
			                <th width=\"10%\">Emp. Code</th>
			                <th width=\"20%\">Name</th>
			                <th width=\"20%\">Allowance</th>
			                <th width=\"10%\">Amount</th>
			                <th width=\"5%\">Type</th>
			                <th width=\"5%\">Insert Direct</th>                
			                <th width=\"8%\">Allow</th>
			                <th width=\"8%\">Drop</th>
			                <th width=\"*\">Ignore</th>
			            </tr>
			            <tr>
			            	<td colspan=\"10\"><hr size=\"3\" /></td>
			            </tr>";
            		
	            	$count = 0;
	            	foreach ($completePendingDirectIds as $individualPendingDirectId){
	            		$checkbox = "checkbox".$count;
	            		$pendingId = "pendingId".$count;
	            		++$count;
	            		$details = $salaryAddition->getDirectAllowanceIdDetails($individualPendingDirectId);
	            		$personalInfo->getEmployeeInformation($details[1], true);
	            		
	            		echo "
	            			<tr>
				            	<td align=\"center\">".$count."</td>
				                <td align=\"left\">".$personalInfo->getEmployeeCode()."</td>
				                <td align=\"left\">".$personalInfo->getName()."</td>
				                <td align=\"left\">".$allowance->getAllowanceTypeName($details[2])."</td>
				                <td align=\"left\">Rs. ".$details[3]."</td>
				                <td align=\"center\">".strtoupper($details[4])."</td>
				                <td align=\"center\">".strtoupper($details[5])."</td>
				                <td align=\"center\"><input type=\"radio\" name=\"$checkbox\" value=\"1\" checked=\"checked\" /></td>
				                <td align=\"center\"><input type=\"radio\" name=\"$checkbox\" value=\"0\" /></td>
				                <td align=\"center\">
										<input type=\"radio\" name=\"$checkbox\" value=\"5\" />
										<input type=\"hidden\" name=\"$pendingId\" value=\"$individualPendingDirectId\" /></td>            
				            </tr>
				            <tr>
				            	<td height=\"5px\"></td>
				            </tr>
				            <tr>
				            	<td colspan=\"10\"><hr size=\"1\" /></td>
				            </tr>
				            <tr>
				            	<td height=\"5px\"></td>
				            </tr>";
	            	}	
            	}
            	
            ?>    
            <tr>
            	<td height="15px"></td>
            </tr>
            <tr>
            	<td colspan="10"><hr size="3" /></td>
            </tr>
            <?php 
            	if($type == 'all' || $type == 'drop')
            		$completePendingDropIds = $salaryAddition->getPendingSalaryAdditionsDropIds();
            	if($completePendingDropIds){
            		$flag = false;
            		echo "
            			<tr>
			            	<td align=\"center\" colspan=\"10\"><h3>Drop Requests For Direct Salary Additions</h3></td>
			            </tr>	            
			            <tr>
			            	<td colspan=\"10\"><hr size=\"1\" /></td>
			            </tr>
			            <tr>
			            	<th width=\"5%\">S.N.</th>
			                <th width=\"10%\">Emp. Code</th>
			                <th width=\"20%\">Name</th>
			                <th width=\"20%\">Allowance</th>
			                <th width=\"10%\">Amount</th>
			                <th width=\"10%\" colspan=\"2\">Type</th>                
			                <th width=\"8%\">Allow</th>
			                <th width=\"8%\">Drop</th>
			                <th width=\"*\">Ignore</th>
			            </tr>
			            <tr>
			            	<td colspan=\"10\"><hr size=\"3\" /></td>
			            </tr>";
            		
	            	$count = 0;
	            	foreach ($completePendingDropIds as $individualPendingDropId){
	            		$checkbox = "checkboxd".$count;
	            		$pendingId = "pendingIdd".$count;
	            		++$count;
	            		$details = $salaryAddition->getAdditionalSalaryIdDetails($individualPendingDropId);
	            		$personalInfo->getEmployeeInformation($details[1], true);
	            		
	            		echo "
	            			<tr>
				            	<td align=\"center\">".$count."</td>
				                <td align=\"left\">".$personalInfo->getEmployeeCode()."</td>
				                <td align=\"left\">".$personalInfo->getName()."</td>
				                <td align=\"left\">".$allowance->getAllowanceTypeName($details[2])."</td>
				                <td align=\"left\">Rs. ".$details[3]."</td>
				                <td align=\"center\" colspan=\"2\">".($details[4] == 'c' ? 'Credit' : 'Debit')."</td>
				                <td align=\"center\"><input type=\"radio\" name=\"$checkbox\" value=\"1\" checked=\"checked\" /></td>
				                <td align=\"center\"><input type=\"radio\" name=\"$checkbox\" value=\"0\" /></td>
				                <td align=\"center\">
										<input type=\"radio\" name=\"$checkbox\" value=\"5\" />
										<input type=\"hidden\" name=\"$pendingId\" value=\"$individualPendingDropId\" /></td>            
				            </tr>
				            <tr>
				            	<td height=\"5px\"></td>
				            </tr>
				            <tr>
				            	<td colspan=\"10\"><hr size=\"1\" /></td>
				            </tr>
				            <tr>
				            	<td height=\"5px\"></td>
				            </tr>";
	            	}	
            	}
            	if($flag)
            		echo "<tr>
			            	<td colspan=\"10\" align=\"center\"><br /><br /><h2>NO PENDING JOBS FOR THE GIVEN OPTION SELECTED</h2></td>
			            </tr>";
            	else
            		echo "    
			            <tr>
			            	<td colspan=\"10\" align=\"center\">
			                	<input type=\"submit\" name=\"submit\" value=\"Process The Given Pending Jobs\" style=\"width:300px\" />
			                    <input type=\"button\" onclick=\"window.location='./'\" value=\"Return Back\" /></td>
			            </tr>";				
            ?>   
            
        </table>
        </form>
      </div>
      <div class="sidenav">
      	<hr size="2" /><center>
        <font color="#FF0000" size="+1"><b><?php echo $salaryAddition->getOfficerName(); ?></b></font></center>
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