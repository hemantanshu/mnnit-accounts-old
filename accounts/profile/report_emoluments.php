<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);

require_once '../include/class.designation.php';
require_once '../include/class.department.php';
require_once '../include/class.employeeType.php';
require_once '../include/class.personalInfo.php';
require_once '../include/class.employeeInfo.php';


$department = new department();
if(!$department->checkLogged())
        $department->redirect('../');

$option = $_POST['type'];
$sDate = $_POST['syear'].(strlen($_POST['smonth']) > 1 ? $_POST['smonth'] : "0".$_POST['smonth']) ;
$eDate = $_POST['eyear'].(strlen($_POST['emonth']) > 1 ? $_POST['emonth'] : "0".$_POST['emonth']) ;

$employeeType = new employeeType();
$designation = new designation();
$employeeInfo = new employeeInfo();
$personalInfo = new personalInfo();

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
      </div> </div>
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
      	<form action="./report_emolumente.php" method="post">
        <table align="center" border="0" width="100%">            
        	<tr>
            	<td align="center" colspan="5"><font class="error">Please Select The Employee Pool To Get The Report</font></td>
            </tr>
            <tr>
            	<td colspan="5"><br /><hr size="1" /><br /></td>
            </tr>
            <tr>
            	<td width="30%" align="right"><font class="error">Employee Type Wise:</font></td>
                <td width="2%"></td>
                <td align="left" width="35%">
                                            <select name="employeeType" style="width:250px">                                                
                                                <?php
                                                   $employeeTypeId = $employeeType->getEmployeeTypeIds(true);
                                                   foreach($employeeTypeId as $value)
                                                       echo "<option value=\"".$value."\">".$employeeType->getEmployeeTypeName($value)."</option>";
                                                ?>
                                            </select></td>
            	<td width="2%"></td>
                <td align="left" width="30%"><input type="submit" name="submit"  style="width:250px" value="View Report of This Employee Type" /></td>
            </tr>
            <tr>
            	<td colspan="5"><br /><hr size="1" /><br /><br /></td>
            </tr>
            <tr>
            	<td width="30%" align="right"><font class="error">Department Wise:</font></td>
                <td width="2%"></td>
                <td align="left" width="35%">
                                            <select name="department" style="width:250px">
                                                <?php
                                                    $departmentId = $department->getDepartmentIds(true);
                                                    foreach ($departmentId as $value)
                                                        echo "<option value=\"".$value."\">".$department->getDepartmentName($value)."</option>";
                                                ?>
                                            </select></td>
            	<td width="2%"></td>
                <td align="left" width="30%"><input type="submit" name="submit" style="width:250px" value="View Report Of This Department" /></td>
            </tr>
            <tr>
            	<td colspan="5"><br /><hr size="1" /><br /><br /></td>
            </tr>
            <tr>
            	<td width="30%" align="right"><font class="error">Designation Wise :</font></td>
                <td width="2%"></td>
                <td align="left" width="35%">
                                            <select name="designation" style="width:250px">
                                                <?php
                                                    $designationId = $designation->getDesignationIds(true);
                                                    foreach ($designationId as $value)
                                                        echo "<option value=\"".$value."\">".$designation->getDesignationTypeName($value, true)."</option>";
                                                ?>
                                            </select></td>
            	<td width="2%"></td>
                <td align="left" width="30%"><input type="submit" name="submit" style="width:250px" value="View Report Of This Designation" /></td>
            </tr>
            <tr>
            	<td colspan="5"><br /><hr size="1" /><br /><br /></td>
            </tr>
            <tr>
            	<td width="30%" align="right"><font class="error">Individual Employee :</font></td>
                <td width="2%"></td>
                <td align="left" width="35%">
                                            <select name="employeeId" style="width:250px">
                                                <?php
                                                    $employeeId = $employeeInfo->getEmployeeIds(true);
                                                    foreach($employeeId as $value){
                                                        $personalInfo->getEmployeeInformation($value, true);
                                                        echo "<option value=\"".$value."\">".$personalInfo->getName()."-->".$personalInfo->getEmployeeCode()."</option>";
                                                    }
                                                ?>
                                            </select></td>
            	<td width="2%"></td>
                <td align="left" width="30%"><input type="submit" name="submit" style="width:250px" value="View Report Of This Employee" /></td>
            </tr>
            <tr>
            	<td colspan="5"><br /><hr size="1" /><br /><br /></td>
            </tr>
            <tr>
            	<td width="30%" align="right"><font class="error">All Employee :</font></td>
                <td width="2%"></td>
                <td align="left" width="35%">
                                            <select name="employee" style="width:250px">
                                                <option value="all">All</option>

                                            </select></td>
            	<td width="2%"></td>
                <td align="left" width="30%">
                		<input type="hidden" name="edate" value="<?php echo $eDate; ?>" />
                        <input type="hidden" name="sdate" value="<?php echo $sDate; ?>" />
                        <input type="hidden" name="option" value="<?php echo $option; ?>" />
                        <input type="submit" name="submit" style="width:250px" value="View Report Of All Employees" /></td>
            </tr>
            <tr>
            	<td colspan="5"><br /><hr size="1" /><br /><br /></td>
            </tr>
        </table>
        </form>
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