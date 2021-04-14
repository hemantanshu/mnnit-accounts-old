<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
    //error_reporting(0);
    session_start();

    require_once '../include/class.editPersonalInfo.php';
    $editPersonalInfo = new editPersonalInfo();    

    if(!$editPersonalInfo->checkLogged())
            $editPersonalInfo->redirect("../");

    if(count($_POST) && $_POST['submit'] == "Confirm New Employee Registration"){
        $error = 0;
        $errorLog = array();

        if($_POST['employeeName'] == ""){
            $error++;
            array_push($errorLog, "Error --- Please Enter Employee Name");
        }
        if($_POST['employeeCode'] == ""){
            $error++;
            array_push($errorLog, "Error --- Please Enter Employee Code");
        }
        if($_POST['employeePadd'] == ""){
            $error++;
            array_push($errorLog, "Error --- Please Enter Employee Permanent Address");
        }
        if($_POST['employeeTadd'] == ""){
            $error++;
            array_push($errorLog, "Error --- Please Enter Employee Temporary Address");
        }
        if($_POST['employeeBgrp'] == ""){
            $error++;
            array_push($errorLog, "Error --- Please Enter Employee Blood Group");
        }
        if($_POST['employeeDob'] == ""){
            $error++;
            array_push($errorLog, "Error --- Please Enter Employee Date Of Birth");
        }
        if($_POST['employeeContact'] == ""){
            $error++;
            array_push($errorLog, "Error --- Please Enter Employee Contact Number");
        }
        if($_POST['department'] == ""){
            $error++;
            array_push($errorLog, "Error --- Please Select Department Of Employee");
        }
        if($_POST['employeeType'] == ""){
            $error++;
            array_push($errorLog, "Error --- Please Select The Type Of Employee");
        }
        if($error == 0){
            $dob = explode('-', $_POST['employeeDob']);
            $dob = $dob[2]."-".$dob[1]."-".$dob[0];

            $employeeId = $editPersonalInfo->setPersonalInfo($_POST['employeeCode'], $_POST['salutation'], $_POST['employeeName'], $_POST['employeePadd'], $_POST['employeeTadd'], $_POST['employeeContact'], $dob, $_POST['employeeBgrp'], $_POST['employeeHousing'], $_POST['employeeType'], $_POST['department']);
            $_SESSION['emid'] = $employeeId;
            if($editPersonalInfo->isAdmin())
                    $editPersonalInfo->palert("New Employee Registration Successfully Completed .. Please Enter The Professional Details", "./employee_pnew.php");
            else
                $editPersonalInfo->palert("New Employee Registration In Pending Status Till The Final Consent From The Admin .. Please Enter The Professional Details", "./employee_pnew.php");
            exit(0);
        }
        
    }
    require_once '../include/class.housing.php';
    require_once '../include/class.employeeType.php';

    $housing = new housing();
    $employeeType = new employeeType();

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
            	<td colspan="3" align="center"><font class="error">New Employee Registration</font></td>
            </tr>
            <tr>
            	<td colspan="3"><hr size="1" /></td>
            </tr>
            <?php
                if(isset ($error) && $error != 0 && is_array($errorLog)){
                    foreach ($errorLog as $value) {
                            echo "<tr>
											<td></td>
                                            <td colspan=\"2\" align=\"left\"><font class=\"error\">".$value."</font></td>
                                    </tr>";
                    }
                }
                                    echo "
                                            <tr>
                                                    <td height = \"10px\"></td>
                                            </tr>";
            ?>
            <tr>
            	<td align="right" width="30%"><font class="green">Employee Code : </font></td>
                <td width="5%"></td>
                <td align="left" width="50%"><input type="text" name="employeeCode" value="<?php echo $_POST['employeeCode']; ?>" style="width:200px" /></td>
            </tr>
            <tr>
            	<td colspan="3"><hr size="1" /></td>
            </tr>
            <tr>
            	<td align="right"><font class="green">Name Of Employee : </font></td>
                <td width="5px"></td>
                <td align="left">
                    <select name="salutation" style="width:50px">
                    <?php
                        $salutaiton = new salutation();
                        $salutationIds = $salutaiton->getSalutationIds(true);
                        foreach ($salutationIds as $value) {
                            echo "<option value=\"".$value."\">".$salutaiton->getSalutationName($value)."</option>";
                        }
                    ?>
                    </select>
                	<input type="text" name="employeeName" value="<?php echo $_POST['employeeName']; ?>" style="width:200px" /></td>
            </tr>
            <tr>
            	<td colspan="3"><hr size="1" /></td>
            </tr>
            <tr>
            	<td align="right"><font class="green">Permanent Address :</font></td>
                <td width="5px"></td>
                <td align="left"><textarea cols="23" name="employeePadd" rows="3"><?php echo $_POST['employeePadd']; ?></textarea></td>
            </tr>
            <tr>
            	<td colspan="3"><hr size="1" /></td>
            </tr>
            <tr>
            	<td align="right"><font class="green">Temporary Address :</font></td>
                <td width="5px"></td>
                <td align="left"><textarea name="employeeTadd" cols="23" rows="3"><?php echo $_POST['employeeTadd']; ?></textarea></td>
            </tr>
            <tr>
            	<td colspan="3"><hr size="1" /></td>
            </tr>
            <tr>
            	<td align="right"><font class="green">Blood Group :</font></td>
                <td width="5px"></td>
                <td align="left"><input type="text" name="employeeBgrp" value="<?php echo $_POST['employeeBgrp']; ?>" style="width:200px" /></td>
            </tr>
            <tr>
            	<td colspan="3"><hr size="1" /></td>
            </tr>
            <tr>
            	<td align="right"><font class="green">Date Of Birth :</font></td>
                <td width="5px"></td>
                <td align="left"><input type="text" name="employeeDob" value="<?php echo $_POST['employeeDob']; ?>" style="width:200px" /> * Format DD-MM-YYYY</td>
            </tr>
            <tr>
            	<td colspan="3"><hr size="1" /></td>
            </tr>
            <tr>
            	<td align="right"><font class="green">Contact Number :</font></td>
                <td width="5px"></td>
                <td align="left"><input type="text" name="employeeContact" value="<?php echo $_POST['employeeContact']; ?>" style="width:200px" /></td>
            </tr>
            <tr>
            	<td colspan="3"><hr size="1" /></td>
            </tr>
            <tr>
            	<td align="right"><font class="green">Name Of Department :</font></td>
                <td width="5px"></td>
                <td align="left">
                                            <select name="department" style="width:200px">
                                            	<option value=""></option>
                                            <?php
                                                $department = new department();
                                                $departmentId = $department->getDepartmentIds(true);
                                                foreach ($departmentId as $value){
                                                    if($_POST['department'] == $value){
                                                        echo "<option value=\"".$value."\" selected = \"selected\">".$department->getDepartmentName($value)."</option>";
                                                    }else{
                                                        echo "<option value=\"".$value."\">".$department->getDepartmentName($value)."</option>";
                                                    }
                                                }
                                            ?>
                                            </select></td>
            </tr>            
            <tr>
            	<td colspan="3"><hr size="1" /></td>
            </tr>
            <tr>
            	<td align="right"><font class="green">Housing Type :</font></td>
                <td width="5px"></td>
                <td align="left">
                                            <select name="employeeHousing" style="width:200px">
                                            	<option value="">N/A</option>
                                            <?php
                                                $housingId = $housing->getHousingIds(true);
                                                foreach ($housingId as $value){
                                                    if($_POST['employeeHousing'] == $value){
                                                        echo "<option value=\"".$value."\" selected = \"selected\">".$housing->getHousingTypeName($value)."</option>";
                                                    }else{
                                                        echo "<option value=\"".$value."\">".$housing->getHousingTypeName($value)."</option>";
                                                    }
                                                }
                                            ?>
                                            </select></td>
            </tr>
            <tr>
            	<td colspan="3"><hr size="1" /></td>
            </tr>
            <tr>
            	<td align="right"><font class="green">Employee Type :</font></td>
                <td width="5px"></td>
                <td align="left">
                                            <select name="employeeType" style="width:200px">
                                            	<option value=""></option>
                                            <?php
                                                $employeeTypeId = $employeeType->getEmployeeTypeIds(true);
                                                foreach ($employeeTypeId as $value){
                                                    if($_POST['employeeHousing'] == $value){
                                                        echo "<option value=\"".$value."\" selected = \"selected\">".$employeeType->getEmployeeTypeName($value)."</option>";
                                                    }else{
                                                        echo "<option value=\"".$value."\">".$employeeType->getEmployeeTypeName($value)."</option>";
                                                    }
                                                }
                                            ?>
                                            </select></td>
            </tr>
            <tr>
            	<td height="50px" colspan="3"><hr size="1" /></td>
            </tr>
            <tr>
            	<td align="center" colspan="3"><input type="submit" name="submit" value="Confirm New Employee Registration" /></td>
            </tr>
            
            
            
        </table>
        </form>
      </div>
      <div class="sidenav">
      	<hr size="2" />
       <center> <font color="#FF0000" size="+1"><b><?php echo $editPersonalInfo->getOfficerName(); ?></b></font></center>
       	<hr size="2" /><br />
        <h2><font color="#008000">QUICK NAVIGATION PANEL</font></h2>
        <?php
            include './navigation/employee.php';
        ?>
      </div>
      <div class="clearer"><span></span></div>
    </div>
    <div class="footer">@webteam.<a href="http://www.mnnit.ac.in" title="MNNIT">mnnit</a> Designed And Developed By Hemant Kumar Sah (B.Tech ECE 2011)</div>
  </div>
</div>
</body>
</html>