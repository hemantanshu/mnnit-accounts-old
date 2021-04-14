<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);
session_start();

require_once '../include/class.accountInfo.php';
require_once '../include/class.personalInfo.php';
require_once '../include/class.employeeInfo.php';
require_once '../include/class.employeePending.php';

$accounts = new accounts();
if(!$accounts->checkLogged())
        $accounts->redirect('../');

if(!isset ($_GET['type']) || !isset ($_GET['value']))
    $accounts->redirect('./');

    $processingType = $_GET['type'];
    $processingValue = $_GET['value'];


$employeeInfo = new employeeInfo();
$personalInfo = new personalInfo();
$ePending = new employeePending();

$employeeId = array();
$variable = $employeeInfo->getEmployeeIds(true, 'salaryProcess');

if($processingType == "all"){
    foreach ($variable as $value) {
        if(!$ePending->isEmployeeSalaryInPendingStatus($value) && !$ePending->isEmployeeSalaryProcessed($value))
            array_push($employeeId, $value);
    }
}elseif($processingType == "employeeType"){    
    foreach ($variable as $value) {
        $personalInfo->getEmployeeInformation($value, true);
        if($personalInfo->getEmployeeType() == $processingValue)
            if(!$ePending->isEmployeeSalaryInPendingStatus($value) && !$ePending->isEmployeeSalaryProcessed($value))
                array_push($employeeId, $value);
    }
}elseif($processingType == "designation"){
    foreach ($variable as $value){
        $rankId = $employeeInfo->getEmployeeRankIds($value, true);
        foreach ($rankId as $options) {
            $details = $employeeInfo->getRankDetails($options, true);
            if($details[2] == $processingValue)
                if(!$ePending->isEmployeeSalaryInPendingStatus($value) && !$ePending->isEmployeeSalaryProcessed($value))
                    array_push($employeeId, $value);
        }
    }
}elseif($processingType == "department"){
    foreach ($variable as $value){
        $personalInfo->getEmployeeInformation($value, true);
        if($personalInfo->getDepartment() == $processingValue)
            if(!$ePending->isEmployeeSalaryInPendingStatus($value) && !$ePending->isEmployeeSalaryProcessed($value))
                    array_push($employeeId, $value);
    }
}elseif($processingType == "individual"){
    if(!$ePending->isEmployeeSalaryInPendingStatus($processingValue) && !$ePending->isEmployeeSalaryProcessed($processingValue))
        array_push($employeeId, $processingValue);
}else{
    $accounts->palert("No Employee Of This Information Is Left For Salary Processing", './');
}

if(!sizeof($employeeId))
    $accounts->palert("No Employee Left For Processing For This Type", "./salary_process.php");
    

$totalMonthDays = $accounts->getTotalMonthDays();
ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- allowance Information</title>
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

</script><meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
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
      	<form action="./process_msalaryp.php" method="post">
        <table align="center" border="0" width="100%">
            <tr>
            	<td align="center" colspan="7"><font class="green">Monthly Salary Processing Center</font><br /><hr size="3" /><br /><br /></td>
            </tr>
            <tr>
            	<th width="5%">SN</th>
                <th width="10%">Emp. Code</th>
                <th width="40%">Name</th>
                <th width="15%">Amount</th>
                <th width="10%">Days</th>
                <th width="10%">Bank</th>                               
                <th width="10">Check</th>
            </tr>
            <tr>
            	<td colspan="7" height="20px"><hr size="3" /></td>
            </tr>
            
            <?php
                $i = 0;
                foreach ($employeeId as $value){
                    $personalInfo->getEmployeeInformation($value, true);
                    $checkbox = "checkbox".$i;
                    $employeeName = "employeeId".$i;
                    $days = "days".$i;
                    $bankTransfer = "bank".$i;
                    ++$i;
                    if($i % 2 == 0)
                        $color = "#FFFFFF";
                    else
                        $color = "#D8D3DC";
                    echo "<input type=\"hidden\" name=\"".$employeeName."\" value=\"".$value."\" />
                        <tr bgcolor=\"".$color."\">
                            <th align=\"center\"><font class=\"green\">".$i."</font></th>
                            <th align=\"left\" style=\"padding-left:5px\">".$personalInfo->getEmployeeCode()."</th>
                            <th align=\"left\" style=\"padding-left:20px\">".$personalInfo->getName()."</th>
                            <th align=\"left\">".number_format($accounts->getTotalSalary($value), 2, '.', '')."</td>
							<td align=\"center\">
												<select name=\"$days\" style=\"width:60px\">";
                    		for($j = 1; $j <= $totalMonthDays; ++$j){
                    			if($j == $totalMonthDays)
                    				echo "<option value=\"$j\" selected=\"selected\">".$j."</option>";
                    			else 
                    				echo "<option value=\"$j\">".$j."</option>";
                    		} 
                    echo "
													
												</select></td>
							<td align=\"center\"><input type=\"checkbox\" name=\"".$bankTransfer."\" value=\"1\" checked=\"checked\" /></td>					
                            <td align=\"center\"><input type=\"checkbox\" name=\"".$checkbox."\" value=\"1\" checked=\"checked\" /></td>
                        </tr>
                        <tr>
                            <td colspan=\"7\"><hr size=\"1\" /></td>
                        </tr>";
                }
            ?>   			
            <tr>
            	<td colspan="6" align="center"><br /><hr size="1" /><br /><br />
                <input type="hidden" name="type" value="<?php echo $processingType; ?>" />
                <input type="hidden" name="value" value="<?php echo $processingValue; ?>" />
                <input type="submit" name="submit" value="Process Salary Of These Employees"  style="width:250px"/> &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="button" value="Return Back" onclick="window.location='./process_msalary.php'" style="width:150px" /></td>
            </tr>
            
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