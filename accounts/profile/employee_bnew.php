<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
    ////error_reporting(0)

    session_start();

    require_once '../include/class.editPersonalInfo.php';
    $editPersonalInfo = new editPersonalInfo();

    if(!$editPersonalInfo->checkLogged())
            $editPersonalInfo->redirect("..");
    if(isset ($_SESSION['emid']))
        $employeeId = $_SESSION['emid'];
    else
        $editPersonalInfo->redirect('./employee.php');

    if(count($_POST) && $_POST['submit'] == "Confirm Employee Bank Account Details"){
        $error = 0;
        $errorLog = array();
        
        if($_POST['basicSalary'] == "" || !is_numeric($_POST['basicSalary'])){
            ++$error;
            array_push($errorLog, "Basic Salary Cannot Be Left Empty & Has To Be Numeric");
        }
        
        if($_POST['salaryAccount'] == ""){
            ++$error;
            array_push($errorLog, "Salary Account Number Cannot Be Left Empty");
        }

        if($_POST['salaryBank'] == ""){
            ++$error;
            array_push($errorLog, "Please Select Salary Bank Account Name");
        }

        if($_POST['pensionBank'] != "" || $_POST['pensionAccount'] != ""){
            if($_POST['salaryAccount'] == ""){
                ++$error;
                array_push($errorLog, "Pension Account Number Cannot Be Left Empty");
            }

            if($_POST['salaryBank'] == ""){
                ++$error;
                array_push($errorLog, "Please Select Pension Bank Account Name");
            }
        }
        if($error == 0){
            $details = array();
            array_push($details, $employeeId);
            array_push($details, $_POST['salaryAccount']);
            array_push($details, $_POST['salaryBank']);
            array_push($details, $_POST['pensionAccount']);
            array_push($details, $_POST['pensionBank']);
            array_push($details, $_POST['basicSalary']);
            
            $editPersonalInfo->setEmployeeBankInfo($details);

            if($editPersonalInfo->isAdmin())
                $editPersonalInfo->palert("The Bank Account Information Has Been Successfully Updated -- Please Enter The Allowances Info", "./employee_anew.php");
            else
                $editPersonalInfo->palert("The Bank Account Information Is In Pending Status -- Please Enter The Allowances Info", "./employee_anew.php");
        }
    }
    require_once '../include/class.employeeType.php';
    require_once '../include/class.department.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.employeeInfo.php';
    require_once '../include/class.bank.php';


    $personalInfo = new personalInfo();
    $department = new department();
    $employeeType = new employeeType();
    $bank = new bank();
    $designation = new designation();
    $employeeInfo = new employeeInfo();

    if($editPersonalInfo->isAdmin())
        $personalInfo->getEmployeeInformation($employeeId, true);
    else
        $personalInfo->getEmployeeInformation($employeeId, false);

    if($personalInfo->getName() == "")
            $editPersonalInfo->redirect('./employee.php');

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
            	<td colspan="5" align="center"><font class="error">Employee Account Information</font><br /><hr size="1" /></td>
            </tr>
            <?php
                if($error != 0 && is_array($errorLog)){
                    foreach ($errorLog as $value) {
                            echo "<tr>
                                            <td colspan=\"5\" align=\"center\"><font class=\"error\">".$value."</font></td>
                                    </tr>";
                    }
                }
                                    echo "
                                        <tr>
                                                    <td height = \"10px\"></td>
                                            </tr>";
            ?>

            <tr>
                <td width="15%" align="right"><font class="error">Name :</font></td>
                <td width="25%" align="left"><font class="green"><?php echo $personalInfo->getName();?></font></td>
                <td width="5%"></td>
                <td width="15%" align="right"><font class="error">Employee Id :</font></td>
                <td width="25%" align="left"><font class="green"><?php echo $personalInfo->getEmployeeId();?></font></td>
            </tr>
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
                <td align="right"><font class="error">Department :</font></td>
                <td align="left"><font class="green"><?php echo $department->getDepartmentName($personalInfo->getDepartment());?></font></td>
                <td></td>
                <td align="right"><font class="error">DOB :</font></td>
                <td align="left"><font class="green"><?php echo $personalInfo->getDob();?></font></td>
            </tr>
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
                <td align="right"><font class="error">Employee Type :</font></td>
                <td align="left"><font class="green"><?php echo $employeeType->getEmployeeTypeName($personalInfo->getEmployeeType()); ?></font></td>
                <td></td>
            </tr>
            <tr>
            	<td colspan="5" height="50px" align="center"><hr size="2" /><br /><font class="error"></font><br /></td>
            </tr>
            <tr>
            	<td colspan="2" align="right"><font class="green">Basic Salary (INR): </font></td>
                <td></td>
                <td colspan="2" align="left"><input type="text" name="basicSalary" style="width:300px" value="<?php echo $_POST['basicSalary']; ?>" /></td>
            </tr>
            <tr>
            	<td colspan="5" height="50px" align="center"><hr size="2" /><br /><font class="error">Employee Salary Bank Information</font><br /><hr size="2" /></td>
            </tr>
            
            <tr>
            	<td colspan="2" align="right"><font class="green">Name Of The Bank :</font></td>
                <td></td>
                <td colspan="2" align="left">
                                            <select name="salaryBank" style="width:300px">
                                            	<option value=""></option>
                                            <?php
                                                $bankId = $bank->getBankIds(true);
                                                foreach ($bankId as $value){
                                                    if($_POST['salaryBank'] == $value)
                                                        echo "<option value=\"".$value."\" selected =\"selected\">".$bank->getBankName($value)."</option>";
                                                    else
                                                        echo "<option value=\"".$value."\">".$bank->getBankName($value)."</option>";
                                                }
                                            ?>
                							</select></td>
            </tr>
            <tr>
            	<td height="5px"></td>
            </tr>
            <tr>
            	<td colspan="2" align="right"><font class="green">Bank Account Number :</font></td>
                <td></td>
                <td align="left" colspan="2"><input type="text" name="salaryAccount" style="width:300px" value="<?php echo $_POST['salaryAccount']; ?>" /></td>
            </tr>
            <tr>
            	<td colspan="5" height="50px" align="center"><hr size="2" /><br /><font class="error">Employee Pension Bank Information</font><br /><hr size="2" /></td>
            </tr>
            <tr>
            	<td colspan="2" align="right"><font class="green">Name Of The Bank :</font></td>
                <td></td>
                <td colspan="2" align="left">
                                            <select name="pensionBank" style="width:300px">
                                            	<option value=""></option>
                                            <?php
                                                $bankId = $bank->getBankIds(true);
                                                foreach ($bankId as $value){
                                                    if($_POST['salaryBank'] == $value)
                                                        echo "<option value=\"".$value."\" selected =\"selected\">".$bank->getBankName($value)."</option>";
                                                    else
                                                        echo "<option value=\"".$value."\">".$bank->getBankName($value)."</option>";
                                                }
                                            ?>
                                            </select></td>
            </tr>
            <tr>
            	<td height="5px"></td>
            </tr>
            <tr>
            	<td colspan="2" align="right"><font class="green">Bank Account Number :</font></td>
                <td></td>
                <td align="left" colspan="2"><input type="text" name="pensionAccount" style="width:300px" value="<?php echo $_POST['pensionAccount']; ?>" /></td>
            </tr>
            <tr>
            	<td colspan="5" align="center"><input type="submit" name="submit" value="Confirm Employee Bank Account Details" /></td>
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
