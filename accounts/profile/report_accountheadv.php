<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
////error_reporting(0);

require_once '../include/class.personalInfo.php';
require_once '../include/class.accountHead.php';
require_once '../include/class.reporting.php';
require_once '../include/class.employeeInfo.php';


$accountHead = new accountHead();
if(!$accountHead->checkLogged())
  	$accountHead->redirect('../');

if(!isset($_GET['sdate']) || !isset($_GET['edate']) || !isset($_GET['id']) || !isset($_GET['type']) || !isset($_GET['value']))
	$accountHead->redirect('./');

$reporting = new reporting();
$personalInfo = new personalInfo();	
$employeeInfo = new employeeInfo();
	
$sDate = $_GET['sdate'];
$eDate = $_GET['edate'];
$accountHeadId = $_GET['id'];
$processingType = $_GET['type'];
$processingValue = $_GET['value'];

$variable = $reporting->getDistinctSalaryProcessedEmployeeId($sDate, $eDate);
$completeEmployeeId = array();

if($processingType == "all"){
	$completeEmployeeId = $variable;
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
	$accountHead->palert("No Employee Record Found For The Given Details", './report_accounthead.php');
}
unset($variable);

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
<script language="Javascript">
function excelExport()
{
    document.Form1.action = "./report_accountheade.php"
    document.Form1.submit();             // Submit the page
    return true;
}

function printReport()
{
    document.Form1.action = "./report_accountheadp.php"
    document.Form1.submit();             // Submit the page
    return true;
}
</script>

<!-- table sorter -->
<script type="text/javascript" src="../include/jquery.tablesorter.js"></script>
<script type="text/javascript">
	$(function() {
		$("table").tablesorter({debug: false})
		$("a.append").click(appendData);
		
		
	});
	</script>
<!-- table sorter ends here -->
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
      <div class="contentLarge">
      	<form name="Form1" id="Form1" method="post">	
        <table align="center" border="0" width="100%">
        	<thead>
            <?php
            	$validMonths = array();
            	for ($i= $sDate; $i <= $eDate; ++$i){
            		if ($reporting->isSalaryDataAvailiable($i))
            			array_push($validMonths, $i);
            	} 
            		
            	$colspan = sizeof($validMonths, 0) + 4;
            	
                echo "
                	<tr>
		            	<td align=\"center\" colspan=\"$colspan\"><font class=\"error\">ACCOUNT HEAD REPORT FOR <b>";
                	if($accountHeadId == "total")
                		echo "NET SALARY PAID";
                	elseif ($accountHeadId == "gross")
                		echo "GROSS SALARY";
                	elseif ($accountHeadId == "deduction")
                		echo "TOTAL DEDUCTION";
                	else
                		echo $accountHead->getAccountHeadName($accountHeadId); 
                echo  "</b></font><br /><hr size=\"2\" /></td>
		            </tr>
                	<tr style=\"cursor:pointer\">
		            	<th>SN</th>
		                <th>Code</th>
		                <th>Name</th>
		            ";
                foreach ($validMonths as $i)
                		echo "<th align=\"left\"><font size=\"1px\">".$i."</font></th>";
               	echo "
               		</tr>
			            <tr>
			            	<td colspan=\"$colspan\"><hr size=\"1\" /></td>
			            </tr>";
                ?>                
            </thead>
            <tbody>
            <?php           	
            	$i = 0;
            	foreach ($completeEmployeeId as $employeeId){
            		$employeeName ="employee".$i;
            		++$i;
            		$personalInfo->getEmployeeInformation($employeeId, true);
            		echo "
            			<tr>
			            	<td align=\"center\"><input type=\"hidden\" value=\"$employeeId\" name=\"$employeeName\" />".$i."</td>
			                <td align=\"center\">".$personalInfo->getEmployeeCode()."</td>
			                <td align=\"left\">".$personalInfo->getName()."</td>";
            		foreach ($validMonths as $count)
            			echo "  <td align=\"right\">".number_format($reporting->getSalaryAllowanceInfo($employeeId, $count, $accountHeadId, false), 2, '.', '')."</td>";
			        echo "</tr>" ;
					/*
			       echo "<tr>
			            	<td colspan=\"$colspan\"><hr size=\"1\" /></td>
			            </tr>";		
					*/
            	}
            ?>     	      
            </tbody>
            <tr>
            	<td height="30px"></td>
            </tr>
            <tr>
            	<td align="center" colspan="<?php echo $colspan; ?>">
                	<input type="hidden" name="edate" value="<?php echo $eDate; ?>" />
                    <input type="hidden" name="sdate" value="<?php echo $sDate; ?>" />
                    <input type="hidden" name="id" value="<?php echo $accountHeadId; ?>" />
                    <input type="hidden" name="type" value="<?php echo $processingType; ?>" />
                    <input type="hidden" name="value" value="<?php echo $processingValue; ?>" /> 
                    <input type="hidden" name="option" value="false" />   
                                    
                	<input type="button" value="Print The Report" style="width:250px" onclick="return printReport()" />&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" value="Export To Excel" style="width:250px" onclick="return excelExport()" />&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" value="Return Back" onclick="window.location='./report_accounthead.php'" style="width:200px" /></td>
            </tr>
        </table>
        </form>
      </div>
      <div class="clearer"><span></span></div>
    </div>
    <div class="footer">@webteam.<a href="http://www.mnnit.ac.in" title="MNNIT">mnnit</a> Designed And Developed By Hemant Kumar Sah (B.Tech ECE 2011)</div>
  </div>
</div>
</body>
</html>