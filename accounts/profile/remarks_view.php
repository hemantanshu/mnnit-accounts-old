<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
////error_reporting(0)
session_start();
require_once '../include/class.remarks.php';
require_once '../include/class.department.php';
require_once '../include/class.employeeInfo.php';
require_once '../include/class.personalInfo.php';

$personalInfo = new personalInfo();
$employeeInfo = new employeeInfo();
$department = new department();
$remarks = new remarks();

if(!$department->checkLogged())
	$department->redirect('../');
	
if(!isset ($_GET['type']) || !isset ($_GET['value']))
    $department->redirect('./');

$processingType = $_GET['type'];
$processingValue = $_GET['value'];
$date = $_GET['date'];

$completeEmployeeId = array();


$variable = $employeeInfo->getEmployeeIds(true);

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
    $personalInfo->redirect('./');
}
if(!sizeof($completeEmployeeId))
    $department->palert("No Employee For the Given Record Is Availiable", './remarks.php');
    
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
        	<tr>
            	<td colspan="4" align="center"><h3>Showing The Remarks For The Month Of <?php echo substr($date, 4, 2).", ".substr($date, 0, 4);?></h3><br /></td>
            </tr>
			<tr>
            	<th width="15%">S.N</th>
                <th width="20%">Employee ID</th>
                <th width="30%">Name</th>
                <th width="*">Department</th>
            </tr>
            <tr>
            	<td colspan="4"><hr size="2" /></td>
            </tr>
            <?php  
            	$flag = false;
            	$i = 0;          	
            	foreach ($completeEmployeeId as $individualEmployeeId){
            		$completeRemarksId = $remarks->isEmployeeRemarkAvailiable($individualEmployeeId, $date);
            		$personalInfo->getEmployeeInformation($individualEmployeeId, true);
            		if($completeRemarksId){
            			$flag = true;
            			++$i;
            			$count = sizeof($completeRemarksId);
            			$anotherCount = $count + 1;
            			$remarksDetails = $remarks->getRemarkIdDetails($completeRemarksId[0], true);
            			echo "
            				<tr>
				            	<td align=\"center\" rowspan=\".$anotherCount.\">".$i."</td>
				                <td align=\"left\"><font class=\"green\">".$personalInfo->getEmployeeCode()."</font></td>
				                <td align=\"left\"><font class=\"green\">".$personalInfo->getName()."</font></td>
				                <td align=\"left\"><font class=\"green\">".$department->getDepartmentName($personalInfo->getDepartment())."</font></td>                
				            </tr>
				            <tr>
				            	<td align=\"left\" rowspan=\"".$count."\"><font class=\"green\">REMARKS : </font></td>
				                <th colspan=\"3\" align=\"left\">".$remarksDetails[3]."</th>
				            </tr>";
            			$counter=0;
            			foreach ($completeRemarksId as $individualRemarksId){
            				if($counter == 0){
            					++$counter;
            					continue;
            				}
            				$remarksDetails = $remarks->getRemarkIdDetails($individualRemarksId, true);
            				echo "
            					<tr>
					                <th colspan=\"3\" align=\"left\">".$remarksDetails[3]."</th>
					            </tr>";
            			}
            			echo "
            				<tr>
				            	<td align=\"center\" height=\"5px\"></td>
				            </tr>
            				<tr>
				            	<td align=\"center\" colspan=\"4\"><hr size=\"1\" /></td>
				            </tr>
				            <tr>
				            	<td align=\"center\" height=\"5px\"></td>
				            </tr>";            			
            		}
            	}
            ?>            
            
            
        </table>
      </div>
      <div class="sidenav">
      	<hr size="2" />
        <center><font color="#FF0000" size="+1"><b><?php echo $department->getOfficerName(); ?></b></font></center>
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