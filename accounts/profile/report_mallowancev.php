<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);

require_once '../include/class.personalInfo.php';
require_once '../include/class.allowance.php';
require_once '../include/class.reporting.php';
require_once '../include/class.employeeInfo.php';


$allowance = new allowance();
if(!$allowance->checkLogged())
  	$allowance->redirect('../');

if(isset ($_POST) && count($_POST) > 0){
    if($_POST['submit'] == "View Report of This Employee Type"){
        $processingType = "employeeType";
        $processingValue = $_POST['employeeType'];
    }
    if($_POST['submit'] == "View Report Of This Department"){
        $processingType = "department";
        $processingValue = $_POST['department'];
    }
    if($_POST['submit'] == "View Report Of This Designation"){
        $processingType = "designation";
        $processingValue = $_POST['designation'];
    }
    if($_POST['submit'] == "View Report Of This Employee"){
        $processingType = "individual";
        $processingValue = $_POST['employeeId'];
    }
    if($_POST['submit'] == "View Report Of All Employees"){
        $processingType = "all";
        $processingValue = $_POST['employee'];
    }        
}else
	$allowance->redirect('./');
  	
  	
$month = $_POST['month'];	

$reporting = new reporting();
$personalInfo = new personalInfo();	
$employeeInfo = new employeeInfo();
	
$completeEmployeeId = array();
	
if($processingType == "individual")
	array_push($completeEmployeeId, $processingValue);
else{
	$variable = $reporting->getDistinctSalaryProcessedEmployeeId($month, $month);	
	
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
	}
}
if(!sizeof($completeEmployeeId, 1))
	$allowance->palert("No Employee Record Found For The Given Details", './report_mallowances.php');


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
    document.Form1.action = "./report_mallowancee.php"
    document.Form1.submit();             // Submit the page
    return true;
}

function printStatement()
{
    document.Form1.action = "./report_mallowancep.php"
    document.Form1.submit();             // Submit the page
    return true;
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
      <div class="contentLarge">
      	<form name="Form1" id="Form1" method="post">
        <table align="center" border="0" width="100%">
        <?php 
        	$completeAllowanceIds = array();
        	if(isset($_POST['total']))
        		array_push($completeAllowanceIds, 'total');
        	if(isset($_POST['gross']))
        		array_push($completeAllowanceIds, 'gross');
        	if(isset($_POST['deduction']))
        		array_push($completeAllowanceIds, 'deduction');        	
        	
        	$i = 0;
        	while (true){
        		$allowanceName = "allowance".$i;
        		++$i;        	
        		if(!isset($_POST[$allowanceName]))
        			break;
        		array_push($completeAllowanceIds, $_POST[$allowanceName]);
        	} 
        	$colspan = sizeof($completeAllowanceIds, 0) + 4;
        ?>
      		<tr>
            	<td colspan="<?php echo $colspan; ?>" align="center"><font class="green">ALLOWANCE REPORT OF FOR THE MONTH OF </font><font class="error"><?php echo $allowance->nameMonth($month); ?></font></td>
            </tr>            
            <tr>
            	<td colspan="<?php echo $colspan; ?>"><hr size="2" /></td>
            </tr>
            <tr>
            	<td colspan="<?php echo $colspan; ?>" align="center">
                	<table border="0" width="100%" align="center">
                    	<tr>                 
                            <td width="5%">Code</td>
                            <td width="28%" align="left">Allowance Type</td>
                            <td width="5%">Code</td>
                            <td width="28%" align="left">Allowance Type</td>
                            <td width="5%">Code</td>
                            <td width="*" align="left">Allowance Type</td>                            
                        </tr>
                        <tr>
                        	<td height="3px"></td>
                        </tr>
                        <?php 
                        	$i = 0;
                        	foreach ($completeAllowanceIds as $allowanceId){
                        		if($i % 3 == 0 && $i != 0)
                        			echo "</tr><tr>";
                        		elseif($i == 0)
                        			echo "<tr>";								
                        		
                        		$variable = "allowance".$i;                        		
                        		++$i;			
                        		$name = "ACT".$i;
                        							
                        		if($allowanceId == 'total')
                        			$allowanceName = "NET SALARY PAID";
                        		elseif ($allowanceId == 'gross')
                        			$allowanceName = "GROSS SALARY PAID";
                        		elseif ($allowanceId == 'deduction')
                        			$allowanceName = "TOTAL DEDUCTION";
                        		else 
                        			$allowanceName = strtoupper($allowance->getAllowanceTypeName($allowanceId));
                        		echo "			                        	
			                            <th><input type=\"hidden\" name=\"$variable\" value=\"$allowanceId\" />".$name."</th>
			                            <td align=\"left\">$allowanceName</td>";                        		
                        	}
                        	echo "</tr>"
                        ?>                        
                    </table>
                </td>
            </tr>
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
            	<td colspan="<?php echo $colspan; ?>"><hr size="2" /></td>
            </tr>
            <tr>
            	<th>S.N</th>
                <th>Code</th>
                <th align="left">Name</th>
                <?php 
                	$count = 1;
                	foreach ($completeAllowanceIds as $allowanceId){
                		$name = "ACT".$count;
                		++$count;
                		echo "<td align=\"right\">$name</td>";
                	}
                ?>                
            </tr>
            <tr>
            	<td colspan="<?php echo $colspan; ?>"><hr size="1" /></td>
            </tr>
      		<tr>
            	<td height="10px"></td>
            </tr>
            <?php 
            	$i = 0;            	
            	foreach ($completeEmployeeId as $employeeId){
            		$employeeName = "employee".$i;
            		$personalInfo->getEmployeeInformation($employeeId, true);
            		++$i;
            		
            		echo "
            			<tr style=\"padding-top:10px; padding-bottom:10px\">
			            	<td><input type=\"hidden\" name=\"$employeeName\" value=\"$employeeId\" />".$i."</td>
			                <td>".$personalInfo->getEmployeeCode()."</td>
			                <td align=\"left\">".$personalInfo->getName()."</td>";
            		foreach ($completeAllowanceIds as $allowanceId)
            			echo "  <td align=\"right\">".number_format($reporting->getSalaryAllowanceInfo($employeeId, $month, $allowanceId, true), 2, '.', '')."</td>";
            		echo "</tr>";
            	}
            ?>      
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
      			<td colspan="<?php echo $colspan; ?>" align="center">
                	<input type="hidden" name="value" value="<?php echo $processingValue; ?>" />
                	<input type="hidden" name="month" value="<?php echo $month; ?>" />
                    <input type="hidden" name="type" value="<?php echo $processingType; ?>" />
                    
                    <input type="button" value="Export Data To Excel" style="width:250px" onclick="return excelExport()" />&nbsp;&nbsp;&nbsp;
                    <input type="button" value="Print The Statement" style="width:250px" onclick="return printStatement()" />&nbsp;&nbsp;&nbsp;
                    <input type="button" value="Return Back" onclick="window.location='./report_mallowances.php'" style="width:150px" /></td>      
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