<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);

require_once '../include/class.designation.php';
require_once '../include/class.department.php';
require_once '../include/class.employeeType.php';
require_once '../include/class.personalInfo.php';
require_once '../include/class.employeeInfo.php';
require_once '../include/class.accountInfo.php';


$department = new department();
if(!$department->checkLogged())
        $department->redirect('../');

if(isset ($_POST) && count($_POST) > 0){
    $error = 0;
    $errorLog = array();
    $flag = false;
    if($_POST['financial'] != ""){
        $flag = true;
        $financialId = $_POST['financial'];
    }else{
        $sDate = $_POST['syear'].(strlen($_POST['smonth']) > 1 ? $_POST['smonth'] : '0'.$_POST['smonth']);
        $eDate = $_POST['eyear'].(strlen($_POST['emonth']) > 1 ? $_POST['emonth'] : '0'.$_POST['emonth']);
    }

    if($_POST['submit'] == "View Report Of This Employee Type"){
        $type = "employeeType";
        $value = $_POST['employeeType'];
    }
    if($_POST['submit'] == "View Report Of This Department"){
        $type = "department";
        $value = $_POST['department'];
    }
    if($_POST['submit'] == "View Report Of This Designation"){
        $type = "designation";
        $value = $_POST['designation'];
    }
    if($_POST['submit'] == "View Report Of This Employee"){
        $type = "individual";
        $value = $_POST['employeeId'];
    }
    if($_POST['submit'] == "View Report Of All Employees"){
        $type = "all";
        $value = $_POST['employee'];
    }
    if($value == ""){
        ++$error;
        array_push($errorLog, "Please Select Any Choice");
    }
    if($flag)
        $department->redirect("./report_eannualp.php?financial=".$financialId."&value=".$value."&type=".$type."");
    else
        $department->redirect("./salary_eannualp.php?sdate=".$sDate."&edate=".$eDate."&value=".$value."&type=".$type."");
}

$employeeType = new employeeType();
$designation = new designation();
$employeeInfo = new employeeInfo();
$personalInfo = new personalInfo();
$accounts = new accounts();

ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- annual report generation</title>
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
      	<form action="" method="post">
        <table align="center" width="100%" border="0">
        	<tr>
            	<td align="right">Select Start Date</td>
                <td width="20px"></td>
                <td align="left">
                			<select name="smonth" style="width:150px">
                                            <?php
                                                $i = 1;
                                                while ($i < 13){
                                                	if (date('m') == $i)
                                                		echo "<option value=\"$i\" selected=\"selected\">".$accounts->getNumber2Month($i)."</option>";
                                                	else
                                                		echo "<option value=\"$i\">".$accounts->getNumber2Month($i)."</option>";
                                                    ++$i;
                                                 }
                                            ?>
                            </select>
                            <select name="syear" style="width:75px">
                                           <?php
                                                $i = 2010;
                                                while ($i < 2100){
                                                	if ((date('Y') - 1) == $i)
                                                		echo "<option value=\"$i\" selected=\"selected\">".$i."</option>";
                                                	else
                                                		echo "<option value=\"$i\">".$i."</option>";

                                                    ++$i;
                                                 }
                                            ?>
                			</select>

                </td>
            </tr>
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
            	<td align="right">Select End Date</td>
                <td width="20px"></td>
                <td align="left">
                			<select name="emonth" style="width:150px">
                                            <option value=""></option>
                                            <?php
                                                $i = 1;
                                                while ($i < 13){
                                                	if (date('m') == $i)
                                                		echo "<option value=\"$i\" selected=\"selected\">".$accounts->getNumber2Month($i)."</option>";
                                                	else
                                                		echo "<option value=\"$i\">".$accounts->getNumber2Month($i)."</option>";
                                                    ++$i;
                                                 }
                                            ?>
                            </select>
                            <select name="eyear" style="width:75px">
                				<option value=""></option>
                                           <?php
                                                $i = 2010;
                                                while ($i < 2100){
                                                	if (date('Y') == $i)
                                                		echo "<option value=\"$i\" selected=\"selected\">".$i."</option>";
                                                	else
                                                		echo "<option value=\"$i\">".$i."</option>";

                                                    ++$i;
                                                 }
                                            ?>
                			</select>

                </td>
            </tr>
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
            	<td align="right">Select Financial Year</td>
                <td width="20px"></td>
                <td align="left">
                			<select name="financial" style="width:200px">
                                            <option value="">Please Select One</option>
                                            <?php
                                                $completeIds = $accounts->getSessionIds();
                                                foreach ($completeIds as $value){
                                                    $details = $accounts->getSessionDetails($value);
                                                    echo "<option value=\"$value\">".$details[1]."</option>";
                                                }
                                            ?>
                                        </select> (Higher Priority)</td>
            </tr>
        </table>
        <table align="center" border="0" width="100%">
            <?php
                if(isset ($error) && $error != 0 && is_array($errorLog)){
                    foreach ($errorLog as $value) {
                            echo "<tr>
                                    <td></td>
                                            <td colspan=\"4\" align=\"left\"><font class=\"error\">".$value."</font></td>
                                    </tr>";
                    }
                }
                echo "
                        <tr>
                                <td height = \"10px\"></td>
                        </tr>";
            ?>
            <tr>
            	<td colspan="5"><br /><hr size="1" /><br /><br /></td>
            </tr>
            <tr>
            	<td width="30%" align="right"><font class="error">Employee Type Wise:</font></td>
                <td width="2%"></td>
                <td align="left" width="35%">
                                            <select name="employeeType" style="width:250px">
                                                <option value="">--Select--</option>
                                                <?php
                                                   $employeeTypeId = $employeeType->getEmployeeTypeIds(true);
                                                   foreach($employeeTypeId as $value)
                                                       echo "<option value=\"".$value."\">".$employeeType->getEmployeeTypeName($value)."</option>";
                                                ?>
                                            </select></td>
            	<td width="2%"></td>
                <td align="left" width="30%"><input type="submit" name="submit"  style="width:250px" value="View Report Of This Employee Type" /></td>
            </tr>
            <tr>
            	<td colspan="5"><br /><hr size="1" /><br /><br /></td>
            </tr>
            <tr>
            	<td width="30%" align="right"><font class="error">Department Wise:</font></td>
                <td width="2%"></td>
                <td align="left" width="35%">
                                            <select name="department" style="width:250px">
                                                <option value="">--Select--</option>
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
                                                <option value="">--Select--</option>
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
                                                <option value="">--Select--</option>
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
                <td align="left" width="30%"><input type="submit" name="submit" style="width:250px" value="View Report Of All Employees" /></td>
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