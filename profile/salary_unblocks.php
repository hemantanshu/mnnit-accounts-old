<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);
session_start();

require_once '../include/class.blockSalary.php';
require_once '../include/class.accountInfo.php';

$blockSalary = new blockUnblockSalary();
$accounts = new accounts();

if(!$blockSalary->checkLogged())
	$blockSalary->redirect('../');
	
$month = date('Y').date('m');

        
if(isset($_POST) && $_POST['submit'] == 'UnBlock The Salary Of These Employees'){
	$i = 0;
	while(true){		           		
        $checkbox = 'checkbox'.$i;
        $employeeName = 'employeeId'.$i;		
        
        if(!isset($_POST[$employeeName]))
        	break;
        	
        $employeeId = $_POST[$employeeName];	
        
        if($_POST[$checkbox] == '1'){
			if($blockSalary->isEmployeeSalaryBlocked($employeeId)){
				$blockSalary->unblockEmployeeSalary($employeeId);
			}    		
        }
        ++$i;        
	}
	if($blockSalary->isAdmin())
		$blockSalary->palert("The Salary For These Employees Have Been UnBlocked", "./");
	else 
		$blockSalary->palert("The Salary For These Employees Will Be UnBlocked Once The Admin Confirms", "./");
	exit(0);
}


if(!isset ($_GET['type']) || !isset ($_GET['value']))
    $blockSalary->redirect('./');

$processingType = $_GET['type'];
$processingValue = $_GET['value'];


require_once '../include/class.personalInfo.php';
require_once '../include/class.employeeInfo.php';

$employeeInfo = new employeeInfo();
$personalInfo = new personalInfo();

$completeEmployeeId = array();
$variable = $employeeInfo->getEmployeeIds(true, 'block');

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
    $blockSalary->redirect('./');
}
if(!sizeof($completeEmployeeId))
    $blockSalary->palert("No Employee For the Given Record Is Availiable",'./salary_unblock.php');
    

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
<script type="text/javascript">
function loadPHPFile(str)
{
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("infoDiv").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET",str,true);
xmlhttp.send();
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
            	<td align="center" colspan="7"><font class="green">UnBlock Salary Of These Employees</font><br /><hr size="3" /><br /><br /></td>
            </tr>
            <tr>
            	<th width="5%">SN</th>
                <th width="15%">Emp. Code</th>
                <th width="30%">Name</th>
                <th width="15%">Start Date</th>
                <th width="15%">Type</th>
                <th width="10%">Unblock</th>
                <th width="10%">Ignore</th>
                
				                                
            </tr>            
            <tr>
            	<td colspan="7" height="20px"><hr size="3" /></td>
            </tr>
            <?php            	
            	$i = 0; 
            	foreach ($completeEmployeeId as $individualEmployeeId) {            		
            		$checkbox = 'checkbox'.$i;
            		$employeeName = 'employeeId'.$i;
            		++$i;
            		
            		$personalInfo->getEmployeeInformation($individualEmployeeId, true);   
            		$blockingId = $blockSalary->getEmployeeId2BlockedId($individualEmployeeId, true);            		
            		$blockingIdDetails = $blockSalary->getBlockedEmployeeIdDetails($blockingId, true);   
            		
            		echo "
            			<tr>
			            	<td align=\"center\"><font class=\"green\">".$i."</font></td>
			                <td align=\"center\"><font class=\"green\">".$personalInfo->getEmployeeCode()."</font></td>
			                <td align=\"left\"><font class=\"green\">".$personalInfo->getName()."</font></td>
			                <td align=\"center\">".substr($blockingIdDetails[2], 4, 2)."-".substr($blockingIdDetails[2], 0, 4)."</td>
			                <td align=\"center\"><font class=\"green\">".($blockingIdDetails[4] == 'l' ? 'Left College' : ($blockingIdDetails[4] == 'r' ? 'Retired' : 'Others'))."</font></td>
			                <td align=\"center\">
									<input type=\"radio\" ";
            				if($blockingIdDetails[4] != 'l' || $blockingIdDetails[4] != 'r')
            					echo "checked=\"checked\"";
					echo 									" name=\"".$checkbox."\" value=\"1\" />
									<input type=\"hidden\" name=\"".$employeeName."\" value=\"".$individualEmployeeId."\" /></td>
							<td align=\"center\">
									<input type=\"radio\" ";
							if($blockingIdDetails[4] == 'l' || $blockingIdDetails[4] == 'r')
								echo "checked=\"checked\"";
					echo 									"name=\"".$checkbox."\" value=\"0\" /></td>		
			            </tr>
			            <tr>
			            	<td height=\"5\"></td>
			            </tr>
			            <tr>
			            	<td colspan=\"7\"><hr size=\"1\" /></td>
			            </tr>";
            	}
            ?>                       
            <tr>
            	<td colspan="8" align="center"><br /><hr size="1" /><br /><br />
            		<input type="submit" name="submit" value="UnBlock The Salary Of These Employees" style="width:300px" />
            		<input type="button" onclick="window.location='./salary_unblock.php'" value=" Return Back" /></td>
            </tr>
        </table>
        </form>
      </div>
      <div class="sidenav">
      	<hr size="2" />
       <center> <font color="#FF0000" size="+1"><b><?php echo $blockSalary->getOfficerName(); ?></b></font></center>
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