<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);
session_start();

require_once '../include/class.blockSalary.php';

$blockSalary = new blockUnblockSalary();

if(!$blockSalary->checkLogged())
	$blockSalary->redirect('../');
        
if(isset($_POST) && $_POST['submit'] == 'Process Pending UnBlock Jobs'){
	$i = 0;
	while(true){		           		
        $checkbox = 'checkbox'.$i;
        $employeeName = 'employeeId'.$i;
        $blocingIdName = "blockingId".$i;	
        
        if(!isset($_POST[$employeeName]))
        	break;
        	
        $employeeId = $_POST[$employeeName];	
        
        if($_POST[$checkbox] == '1'){
			if($blockSalary->isEmployeeSalaryBlocked($employeeId)){
				$blockSalary->unblockEmployeeSalary($employeeId);
			}    		
        }elseif($_POST[$checkbox == '0']){
        	$blockSalary->dropPendingEmployeeSalaryBlockage($_POST[$blocingIdName]);
        }
        ++$i;        
	}
	if($blockSalary->isAdmin())
		$blockSalary->palert("The Pending Job Has Been Successfully Processed", "./");
	else 
		$blockSalary->palert("The Salary For These Employees Will Be UnBlocked Once The Admin Confirms", "./");
	exit(0);
}

require_once '../include/class.personalInfo.php';
$personalInfo = new personalInfo();

$completeUnBlockingId = $blockSalary->getPendingBlockedEmployeeSalaryEmployeeId(false);

if(!sizeof($completeUnBlockingId))
    $blockSalary->palert("No Employee For the Given Record Is Availiable", $blockSalary->getUrlOfRedirect("BSP00"));
    

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
                <th width="8%">Unblock</th>
                <th width="8%">Drop</th>
                <th width="10%">Ignore</th>				                                
            </tr>            
            <tr>
            	<td colspan="8" height="10px"><hr size="3" /></td>
            </tr>
            <?php            	
            	$i = 0; 
            	foreach ($completeUnBlockingId as $individualBlockingId) {            		
            		$checkbox = 'checkbox'.$i;
            		$employeeName = 'employeeId'.$i;
            		$blocingIdName = "blockingId".$i;
            		++$i;
            		           		
            		$blockingIdDetails = $blockSalary->getBlockedEmployeeIdDetails($individualBlockingId, false);   
            		$personalInfo->getEmployeeInformation($blockingIdDetails[1], true);
            		echo "
            			<tr>
			            	<td align=\"center\"><font class=\"green\">".$i."</font></td>
			                <td align=\"center\"><font class=\"green\">".$personalInfo->getEmployeeCode()."</font></td>
			                <td align=\"left\"><font class=\"green\">".$personalInfo->getName()."</font></td>
			                <td align=\"center\">".substr($blockingIdDetails[2], 4, 2)."-".substr($blockingIdDetails[2], 0, 4)."</td>
			                <td align=\"center\"><font class=\"green\">".($blockingIdDetails[4] == 'l' ? 'Left College' : ($blockingIdDetails[4] == 'r' ? 'Retired' : 'Others'))."</font></td>";
            		if($blockSalary->isPendingEditable($blockingIdDetails[0])){
            			echo "
				                <td align=\"center\">
										<input type=\"radio\" name=\"".$checkbox."\" checked=\"checked\" value=\"1\" />
										<input type=\"hidden\" name=\"".$employeeName."\" value=\"".$blockingIdDetails[1]."\" />
										<input type=\"hidden\" name=\"".$blocingIdName."\" value=\"".$individualBlockingId."\" />
										</td>
								<td align=\"center\">
										<input type=\"radio\" name=\"".$checkbox."\" value=\"0\" /></td>
								<td align=\"center\">
										<input type=\"radio\" name=\"".$checkbox."\" value=\"5\" /></td>				
				            </tr>";	
            		}
            		echo "
            		    <tr>
			            	<td height=\"5\"></td>
			            </tr>
			            <tr>
			            	<td colspan=\"8\"><hr size=\"1\" /></td>
			            </tr>";
            	}
            ?>                       
            <tr>
            	<td colspan="8" align="center"><br /><hr size="1" /><br /><br />
            		<input type="submit" name="submit" value="Process Pending UnBlock Jobs" style="width:300px" />
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