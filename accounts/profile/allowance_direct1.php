<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();

    session_start();

    require_once '../include/class.allowance.php';

    $allowance = new allowance();

    require_once '../include/class.personalInfo.php';
    require_once '../include/class.employeeInfo.php';
    require_once '../include/class.accountInfo.php';
    require_once '../include/class.department.php';

    $personalInfo = new personalInfo();
    $employeeInfo = new employeeInfo();
	$accounts = new accounts();
	$department = new department();

    if(!$allowance->checkLogged())
        $allowance->redirect('../');
    if(isset ($_GET)){
        $allowanceId = $_GET['id'];
        if(isset ($_GET['type'])){
            $type = $_GET['type'];
        }else{
            $type = "all";
        }
    }else
        $allowance->redirect ('./');

	if($allowance->getAllowanceAccountHead($allowanceId) == '')
		$allowance->redirect('./');

        $details = $allowance->getAllowanceHeadDetails($allowanceId);
    ob_end_flush();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- allowance/deduction Information</title>
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
      <div class="contentLarge">
      	<form action="./allowance_directc.php" method="post">
      	<table align="center" border="0" width="100%">
        	<!-- insertion of new departments will be done here -->
            <tr>
            	<td colspan="7" align="center"><font class="error">For Allowance Head : <?php echo $allowance->getAllowanceTypeName($allowanceId);?></font><br /><br /><hr size="1" /><br /></td>
            </tr>
            <tr>
                <td colspan="7" height="10px" align="center">
                        <a href="./allowance_view1.php?id=<?php echo $allowanceId; ?>&type=all" target="_parent">Info </a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_view.php?id=<?php echo $allowanceId; ?>&type=all" target="_parent">Total View </a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_view.php?id=<?php echo $allowanceId; ?>&type=no" target="_parent">Non-Overridden </a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_view.php?id=<?php echo $allowanceId; ?>&type=yes" target="_parent">Over-ridden </a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_direct.php?id=<?php echo $allowanceId; ?>&type=yes" target="_parent">Edit Amount(Direct)</a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_direct1.php?id=<?php echo $allowanceId; ?>&type=yes" target="_parent">Edit Amount(Master-Update)</a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_clear.php?id=<?php echo $allowanceId; ?>" target="_parent">Clear Amount</a>
                </td>
            </tr>
            <tr>
            	<td colspan="7" height="10px"><hr size="1" /></td>
            </tr>
            <tr>
            	<th colspan="7" align="center">Updating The Master Salary Record&nbsp;&nbsp;&nbsp;<?php echo $details[8] == 'c' ? 'EARNING HEAD' : 'DEDUCTION HEAD' ;?>&nbsp;&nbsp;&nbsp;
                                                <input type="hidden" name="option" value="1" />
                </th>
            </tr>
            <tr>
            	<td height="5px" colspan="7"><hr size="1" /></td>
            </tr>
            <tr>
            	<th width="5%">S.N</th>
                <th width="10%">Emp. Code</th>
                <th width="20%">Name</th>
                <th width="20%">Department</th>
                <th width="15%" align="right">S. Amt</th>
                <th width="15%" align="right">C. Amt</th>
                <th width="15%">Amount</th>
            </tr>
            <tr>
            	<td height="10px" colspan="7"><hr size="3" /></td>
            </tr>
            <?php
            	$i = 0;
            	$completeEmployeeId = $employeeInfo->getEmployeeIds(true);
            	foreach ($completeEmployeeId as $individualEmployeeId) {
            		$personalInfo->getEmployeeInformation($individualEmployeeId, true);
            		$amountName = "amount".$i;
            		$employeeIdName = "employeeId".$i;
            		if($allowanceId == 'ACT1')
                            $amount = $accounts->getEmployeeBasicSalary($individualEmployeeId);
                        else
                            $amount = $accounts->getEmployeeSalaryInfo($individualEmployeeId, $allowanceId);
            		++$i;

            		echo "
						<tr>
							<td align=\"center\">".$i."</td>
							<td align=\"left\">".$personalInfo->getEmployeeCode()."</td>
							<td align=\"left\">".$personalInfo->getName()."</td>
							<td align=\"left\">".$department->getDepartmentName($personalInfo->getDepartment())."</td>
                                                        <td align=\"right\" style=\"padding-right:10px\">". number_format($amount, 2, '.', '')."</td>
                                                        <td align=\"right\" style=\"padding-right:10px\">". number_format($accounts->getAccountSum($individualEmployeeId, $allowanceId), 2, '.', '')."</td>
							<td align=\"right\"><input type=\"text\" name=\"".$amountName."\" style=\"width:120px\" />
                                                            <input type=\"hidden\" name=\"".$employeeIdName."\" value=\"".$individualEmployeeId."\" /></td>
						</tr>
						<tr>
							<td colspan=\"7\"><hr size=\"1\" /></td>
						</tr>
						<tr>
							<td height=\"5px\"></td>
						</tr>";
            	}
            ?>
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
            	<td colspan="7" align="center">
                	<input type="hidden" name="allowanceId" value="<?php echo $allowanceId; ?>" />
                    <input type="submit" name="submit" value="Process The Amount For These Employees" style="width:300px" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" onclick="window.location='./allowance.php'" value="Return Back To Allowances" /></td>
            </tr>

        </table>
        </form>
      </div>

      <div class="clearer"><span></span></div>
    </div>
    <div class="footer">@webteam.<a href="http://www.mnnit.ac.in" title="MNNIT">mnnit</a> Designed And Developed By Hemant Kumar Sah (B.Tech ECE 2011) Kedar Panjiyar(CSE 2010-14)</div>
  </div>
</div>
</body>
</html>