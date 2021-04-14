<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);
session_start();

require_once '../include/class.blockSalary.php';
require_once '../include/class.accountInfo.php';

$blockSalary = new blockUnblockSalary();

if(isset($_POST) && $_POST['submit'] == 'Process The Selected Requests'){
	$i = 0;
		while(true){
		
		$sMonthName = 'sMonth'.$i;
		$sYearName = 'sYear'.$i;
		$eMonthName = 'eMonth'.$i;
		$eYearName = 'eYear'.$i;
		$type = 'type'.$i;
		$checkbox = 'checkbox'.$i;
		$employeeName = 'employeeId'.$i;
		$blockingName = 'blockingId'.$i;
		$reason = 'reason'.$i;
        
        ++$i;
        if(!isset($_POST[$employeeName]))
        	break;
        	
        if(!$blockSalary->isPendingEditable($_POST[$blockingName]))
        	continue;
        		
        $sDate = $_POST[$sYearName].(strlen($_POST[$sMonthName]) == 1 ? '0'.$_POST[$sMonthName] : $_POST[$sMonthName]);
		$eDate = $_POST[$eYearName].(strlen($_POST[$eMonthName]) == 1 ? '0'.$_POST[$eMonthName] : $_POST[$eMonthName]);
		$reason = $_POST[$type];
		$employeeId = $_POST[$employeeName];	
		
        if($_POST[$checkbox] == '1'){
      		$blockingId = $blockSalary->blockEmployeeSalary($employeeId, $sDate, $eDate, $reason);
        }else{      		
        	$blockSalary->dropPendingEmployeeSalaryBlockage($_POST[$blockingName]);
        }        
	}
	$blockSalary->palert("The Process Has Been Successfully Operated", "./");
	exit(0);
}
	

$accounts = new accounts();

if(!$blockSalary->checkLogged())
	$blockSalary->redirect('../');
	
require_once '../include/class.personalInfo.php';
$personalInfo = new personalInfo();

$completeBlockedEmployee = $blockSalary->getPendingBlockedEmployeeSalaryEmployeeId(true);
if(!$completeBlockedEmployee)
	$blockSalary->palert("There Are No Pending Employees In This Case", './');
    
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
            	<td align="center" colspan="8"><font class="green">Block Salary Of These Employees</font><br /><hr size="3" /><br /><br /></td>
            </tr>
            <tr>
            	<th width="5%">SN</th>
                <th width="150px">Name</th>
                <th>Reason</th>
                <th colspan="3">Select Date</th>
                <th>Select</th>
                <th>Drop</th>                
                                
            </tr>            
            <tr>
            	<td colspan="8" height="20px"><hr size="3" /></td>
            </tr>
            <?php
            	$i = 0; 
            	foreach ($completeBlockedEmployee as $individualBlockedId) {
            		
            		$sMonthName = 'sMonth'.$i;
            		$sYearName = 'sYear'.$i;
            		$eMonthName = 'eMonth'.$i;
            		$eYearName = 'eYear'.$i;
            		$type = 'type'.$i;
            		$checkbox = 'checkbox'.$i;
            		$employeeName = 'employeeId'.$i;
            		$reason = 'reason'.$i;
            		$blockingName = 'blockingId'.$i;
            		++$i;
            		
            		$details = $blockSalary->getBlockedEmployeeIdDetails($individualBlockedId, false);            		
            		$personalInfo->getEmployeeInformation($details[1], true);     		
            		$sMonth = substr($details[2], 4, 2);
            		$sYear = substr($details[2], 0, 4);
            		
            		$eMonth = substr($details[3], 4, 2);
            		$eYear = substr($details[3], 0, 4);
            		
            		
            		echo "
            			<tr>
			            	<td align=\"center\" rowspan=\"3\"><font class=\"green\">".$i."</font></td>
			                <td align=\"left\" rowspan=\"3\" width=\"200px\"><font class=\"green\">".$personalInfo->getName()."</font></td>
			                <td align=\"left\" rowspan=\"3\">
			                    <select name=\"".$type."\" style=\"width:100px\">
			                        <option value=\"r\" ";
            						if($details[4] == 'r')
            							echo "selected=\"selected\"";
            		echo "			>RETIRED</option>
			                        <option value=\"l\"";
            						if($details[4] == 'l')
            							echo "selected=\"selected\"";
            		echo "			>LEFT COLLEGE</option>
			                        <option value=\"o\"";
            						if($details[4] == 'o')
            							echo "selected=\"selected\"";
            		echo "			>OTHERS</option>
			                    </select></td>
			                <td align=\"right\"><font class=\"green\">Start Date :</font></td>
			                <td align=\"left\">
			                		<select name=\"".$sMonthName."\" style=\"width:125px\">";
			                		$count = 1;
			                		while($count < 13){
			                			if($count == $sMonth)
			                				echo "<option value=\"".$count."\" selected=\"selected\">".$accounts->getNumber2Month($count)."</option>";
			                			else 
			                				echo "<option value=\"".$count."\">".$accounts->getNumber2Month($count)."</option>";
			                			++$count;
			                		}
					echo "       	</select></td>
			                <td align=\"left\">
			                		<select name=\"".$sYearName."\" style=\"width:50px\">";
            						$count = 2010;
			                		while($count < 2100){
			                			if($count == $sYear)
			                				echo "<option value=\"".$count."\" selected=\"selected\">".$count."</option>";
			                			else 
			                				echo "<option value=\"".$count."\">".$count."</option>";	
			                			
			                			++$count;
			                		}
					echo "
			                		
			                		</select></td>
							<td align=\"center\" rowspan=\"3\">
									<input type=\"radio\" checked=\"checked\" name=\"".$checkbox."\" value=\"1\" /></td>
							<td align=\"center\" rowspan=\"3\">
									<input type=\"radio\" name=\"".$checkbox."\" value=\"0\" />									
									<input type=\"hidden\" name=\"".$employeeName."\" value=\"".$details[1]."\" />
									<input type=\"hidden\" name=\"".$blockingName."\" value=\"".$details[0]."\" /></td>		
									
			            </tr>
			            <tr>
			            	<td height=\"5\"></td>
			            </tr>
			            <tr>
			            	<td align=\"right\"><font class=\"green\">End Date :</font></td>
			                <td align=\"left\">
			                		<select name=\"".$eMonthName."\" style=\"width:125px\">";
									echo "<option value=\"\"></option>";            						
									$count = 1;
			                		while($count < 13){
			                			if($count == $eMonth)
			                				echo "<option value=\"".$count."\" selected=\"selected\">".$accounts->getNumber2Month($count)."</option>";
			                			else 	
			                				echo "<option value=\"".$count."\">".$accounts->getNumber2Month($count)."</option>";			                			
			                			++$count;
			                		}
					echo "
			                		
			                		</select></td>
			                <td align=\"left\">
			                		<select name=\"".$eYearName."\" style=\"width:50px\">";
									echo "<option value=\"\"></option>";
            						$count = 2010;
			                		while($count < 2100){
			                			if($count == $eYear)
			                				echo "<option value=\"".$count."\" selected=\"selected\">".$count."</option>";
			                			else		                			
			                				echo "<option value=\"".$count."\">".$count."</option>";
			                			++$count;
			                		}
					echo "
			                		
			                		</select></td>
			            </tr>
			            <tr>
			            	<td height=\"5\"></td>
			            </tr>			            
			            <tr>
			            	<td align=\"center\" colspan=\"2\">Reason *</td>
			                <td align=\"right\" colspan=\"6\"><input type=\"text\" name=\"".$reason."\" style=\"width:600px\" /></td>
			            </tr>
			            <tr>
			            	<td colspan=\"8\"><hr size=\"1\" /></td>
			            </tr>
			            <tr>
			            	<td height=\"10\"></td>
			            </tr>";
            	}
            ?>           
            
            <tr>
            	<td colspan="8" align="center"><br /><hr size="1" /><br /><br />
            		<input type="submit" name="submit" value="Process The Selected Requests" style="width:300px" />
            		<input type="button" onclick="window.location='./'" value=" Return Back" /></td>
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