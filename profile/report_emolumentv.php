<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);

require_once '../include/class.personalInfo.php';
require_once '../include/class.reporting.php';
require_once '../include/class.employeeInfo.php';
require_once '../include/class.department.php';

$department = new department();

if(!$department->checkLogged())
  	$department->redirect('../');
	
$reporting = new reporting();
$personalInfo = new personalInfo();	
$employeeInfo = new employeeInfo();
	
$sDate = $_POST['sDate'];
$eDate = $_POST['eDate'];
ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- Emolument Report</title>
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
    document.Form1.action = "./report_emolumentex.php"
    document.Form1.submit();             // Submit the page
    return true;
}

function printReport()
{
    document.Form1.action = "./report_emolumentp.php"
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
        <thead style="cursor:pointer">            
        	<tr>
            	<th align="center" colspan="7">SHOWING EMOLUMENT REPORT FROM <?php echo $reporting->getNumber2Month(substr($sDate, 4, 2)).", ".substr($sDate, 0, 4);?> TO <?php echo $reporting->getNumber2Month(substr($eDate, 4, 2)).", ".substr($eDate, 0, 4);?></th>
            </tr>

            <tr>
            	<td colspan="7"><hr size="1" /></td>
            </tr>

            <tr>
            	<th width="3%">SN</th>
            	<th width="7%">Emp. Code</th>
            	<th width="25%" align="left">Name</th>
            	<th width="35%" align="left">Department</th>
            	<th align="right" width="10%">Earnings</th>
            	<th align="right" width="10%">Deductions</th>
            	<th align="right" width="*">Net Paid</th>
            </tr>

            <tr>
            	<td colspan="7"><br /><hr size="1" /><br /></td>
            </tr>
        </thead>
            <tbody>
            <?php 
            	$count = 0;
            	$i = 0;
            	while (true){
            		
            		$employeeName = "employee".$count;
            		$checkbox = "checkbox".$count;
            		++$count;
            		
            		$newEmployeeName = "employee".$i;
            		
					if (!isset($_POST[$employeeName]))
						break;            		
            		if($_POST[$checkbox] != 1)
            			continue;
					++$i;
					
					$employeeId = $_POST[$employeeName];					
            		$personalInfo->getEmployeeInformation($employeeId, true);
            		$amount = $reporting->getEmployeeSalaryEmolument($employeeId, $sDate, $eDate);
            		$sum = $amount[0] + $amount[1];		
            		
            		echo "
            			<tr>
			            	<td><input type=\"hidden\" name=\"$newEmployeeName\" value=\"$employeeId\" />$count</td>
			            	<td>".$personalInfo->getEmployeeCode()."</td>
			            	<td align=\"left\">".$personalInfo->getName()."</td>
			            	<td align=\"left\">".$department->getDepartmentName($personalInfo->getDepartment())."</td>
			            	<td style=\"padding-right:5px\" align=\"right\">".number_format($amount[0], 2, '.', ',')."</td>
			            	<td style=\"padding-right:5px\" align=\"right\">".number_format(abs($amount[1]), 2, '.', ',')."</td>
			            	<td style=\"padding-right:5px\" align=\"right\">".number_format($sum, 2, '.', ',')."</td>
			            </tr>" ;
            	}
            ?>     
            </tbody>       
            <tr>
            	<td colspan="7"><br /><hr size="1" /><br /></td>
            </tr>
            <tr>
            	<td colspan="7" align="center">
                	<input type="hidden" name="sDate" value="<?php echo $sDate; ?>" />
                    <input type="hidden" name="eDate" value="<?php echo $eDate; ?>" />                	
                    <input type="button" value="Print The Report" style="width:250px" onclick="return printReport()" />&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" value="Export To Excel" style="width:250px" onclick="return excelExport()" />&nbsp;&nbsp;&nbsp;&nbsp;                    
                    <input type="button" value="Return Back" style="width:150px" onclick="window.location='./report_emolument.php'" /></td>
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