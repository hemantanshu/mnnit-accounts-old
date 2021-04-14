<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
    ////error_reporting(0)
    session_start();

    require_once '../include/class.editPersonalInfo.php';
    require_once '../include/class.employeePending.php';

    $editPersonalInfo = new editPersonalInfo();

    if(!$editPersonalInfo->checkLogged())
            $editPersonalInfo->redirect("../");    

    if(count($_POST) && ($_POST['submit'] == "Confirm Pending Employee Details" || $_POST['submit'] == "Confirm Employee Details")){
        $error = 0;
        $errorLog = array();
        $employeeId = $_POST['employeeId'];

        if(!$editPersonalInfo->isEditable($employeeId))
            $editPersonalInfo->palert("This Information Cannot Be Edited Right Now ..", "./employee_infoview.php?id=".$employeeId."");
        
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
            $details = array();

            array_push($details, $employeeId);
            array_push($details, $_POST['employeeCode']);
            array_push($details, $_POST['employeeName']);
            array_push($details, $_POST['employeePadd']);
            array_push($details, $_POST['employeeTadd']);
            array_push($details, $_POST['employeeBgrp']);
            array_push($details, $dob);
            array_push($details, $_POST['employeeContact']);
            array_push($details, $_POST['department']);
            array_push($details, $_POST['employeeHousing']);
            array_push($details, $_POST['employeeType']);
            array_push($details, $_POST['salutation']);

            $editPersonalInfo->updatePersonalInfo($details);
            $ePending = new employeePending();           
            
            if($ePending->isEmployeeDesignationInPendingStatus($employeeId))
                    $url = "./employee_editd.php?id=".$employeeId."";
            elseif($ePending->isEmployeeBankInformationInPendingStatus($employeeId))
                    $url = "./employee_editb.php?id=".$employeeId."";
            elseif($ePending->isEmployeeMasterSalaryInPendingStatus($employeeId))
                    $url = "./employee_edita.phpid=".$employeeId."";
            else
                $url = "./employee_infoview.php?id=".$employeeId."";               
            
            if($editPersonalInfo->isAdmin())
                $editPersonalInfo->palert("The Employee Personal Details Has Been SuccessFully Updated", $url);
            else
                $editPersonalInfo->palert("The Employee Personal Details Is In Pending Status", $url);
        }

    }elseif(isset ($_GET[id]))
        $employeeId = $_GET['id'];
    else
        $editPersonalInfo->redirect('./employee.php');

    if(!$editPersonalInfo->isEditable($employeeId))
            $editPersonalInfo->palert("This Information Cannot Be Edited Right Now ..", "./employee_infoview.php?id=".$employeeId."");
    
    require_once '../include/class.housing.php';
    require_once '../include/class.employeeType.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.salutation.php';


    $personalInfo = new personalInfo();
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
          <div id="infoDiv">
      	<form action="" method="post">
            <input type="hidden" name="employeeId" value="<?php echo $employeeId; ?>" />
        <?php
            echo "
                <table align=\"center\" width=\"100%\" border=\"0\">
                    <tr>
                    <td align=\"center\"><a href=\"./employee_editp.php?id=".$employeeId."\" >Edit Personal Information</a> || <a href=\"./employee_editd.php?id=".$employeeId."\" >Edit Designation Information</a> || <a href=\"./employee_editb.php?id=".$employeeId."\" >Edit Accounts Information</a> || <a href=\"./employee_edita.php?id=".$employeeId."\" >Edit Allowances Information</a></td>
                </tr>
            </table>";
        ?>
      	<table align="center" border="0" width="100%">
            <tr>
            	<td colspan="3" align="center"><font class="error">Editing Employee Personal Information</font></td>
            </tr>
            <tr>
            	<td colspan="3"><hr size="1" /></td>
            </tr>
            <?php
                if(isset ($error) && $error != 0 && is_array($errorLog)){
                    foreach ($errorLog as $value) {
                        echo "
                            <tr>
                                <td></td>
                                <td colspan=\"2\" align=\"left\"><font class=\"error\">".$value."</font></td>
                            </tr>";
                    }
                    echo "
                        <tr>
                            <td height = \"10px\"></td>
                        </tr>";
                }
                                    
            ?>
            <tr>           	
            	<td colspan="3" align="center" width="100%">
            	<?php
                    $personalInfo->getEmployeeInformation($employeeId, false);
                    if($personalInfo->getName() == ""){ //the job is not in the pending status
                        $personalInfo->getEmployeeInformation($employeeId, true);
                        echo "
                            <table border=\"0\" align=\"center\" width=\"100%\">
                                <tr>
                                    <td align=\"right\" width=\"30%\"><font class=\"green\">Employee Code : </font></td>
                                    <td width=\"5%\"></td>
                                    <td align=\"left\" width=\"50%\"><input type=\"text\" name=\"employeeCode\" value=\"".$personalInfo->getEmployeeCode()."\" style=\"width:200px\" /></td>
                                </tr>
                                <tr>
                                    <td colspan=\"3\" height = \"10px\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"right\"><font class=\"green\">Name Of Employee : </font></td>
                                    <td width=\"5px\"></td>
                                    <td align=\"left\">";                        
                        echo "
                                <select name=\"salutation\" style=\"width:50\"> ";
                                $salutaiton = new salutation();
                                $salutationIds = $salutaiton->getSalutationIds(true);
                                foreach ($salutationIds as $value) {
                                    if($personalInfo->getSalutationId() == $value)
                                        echo "<option value=\"".$value."\" selected=\"selected\">".$salutaiton->getSalutationName($value)."</option>";
                                    else
                                        echo "<option value=\"".$value."\">".$salutaiton->getSalutationName($value)."</option>";
                                }
                        
                        echo "</select>

                                    <input type=\"text\" name=\"employeeName\" value=\"".$personalInfo->getName()."\" style=\"width:200px\" /></td>
                                </tr>
                                <tr>
                                    <td colspan=\"3\" height = \"10px\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"right\"><font class=\"green\">Permanent Address :</font></td>
                                    <td width=\"5px\"></td>
                                    <td align=\"left\"><textarea cols=\"23\" name=\"employeePadd\" rows=\"3\">".$personalInfo->getPermanentAddress()."</textarea></td>
                                </tr>
                                <tr>
                                    <td colspan=\"3\" height = \"10px\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"right\"><font class=\"green\">Temporary Address :</font></td>
                                    <td width=\"5px\"></td>
                                    <td align=\"left\"><textarea name=\"employeeTadd\" cols=\"23\" rows=\"3\">".$personalInfo->getTemporarAddress()."</textarea></td>
                                </tr>
                                <tr>
                                    <td colspan=\"3\" height = \"10px\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"right\"><font class=\"green\">Blood Group :</font></td>
                                    <td width=\"5px\"></td>
                                    <td align=\"left\"><input type=\"text\" name=\"employeeBgrp\" value=\"".$personalInfo->getBloodGroup()."\" style=\"width:200px\" /></td>
                                </tr>
                                <tr>
                                    <td colspan=\"3\" height = \"10px\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"right\"><font class=\"green\">Date Of Birth :</font></td>
                                    <td width=\"5px\"></td>";
                                    $dob = explode("-", $personalInfo->getDob());
                                    $dob =  $dob[2]."-".$dob[1]."-".$dob[0];
                        echo "
                                    <td align=\"left\"><input type=\"text\" name=\"employeeDob\" value=\"".$dob."\" style=\"width:200px\" /> * Format DD-MM-YYYY</td>
                                </tr>
                                <tr>
                                    <td colspan=\"3\" height = \"10px\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"right\"><font class=\"green\">Contact Number :</font></td>
                                    <td width=\"5px\"></td>
                                    <td align=\"left\"><input type=\"text\" name=\"employeeContact\" value=\"".$personalInfo->getContactNumber()."\" style=\"width:200px\" /></td>
                                </tr>
                                <tr>
                                    <td colspan=\"3\" height = \"10px\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"right\"><font class=\"green\">Name Of Department :</font></td>
                                    <td width=\"5px\"></td>
                                    <td align=\"left\">
                                                    <select name=\"department\" style=\"width:200px\">
                                                        <option value=\"\"></option>";
                                                    $department = new department();
                                                    $departmentId = $department->getDepartmentIds(true);
                                                    foreach ($departmentId as $value){
                                                        if($personalInfo->getDepartment() == $value){
                                                            echo "<option value=\"".$value."\" selected = \"selected\">".$department->getDepartmentName($value)."</option>";
                                                        }else{
                                                            echo "<option value=\"".$value."\">".$department->getDepartmentName($value)."</option>";
                                                        }
                                                    }
                        echo "                                 </select></td>
                                </tr>
                                <tr>
                                    <td colspan=\"3\" height = \"10px\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"right\"><font class=\"green\">Housing Type :</font></td>
                                    <td width=\"5px\"></td>
                                    <td align=\"left\">
                                                    <select name=\"employeeHousing\" style=\"width:200px\">
                                                        <option value=\"\">N/A</option>";

                                                        $housingId = $housing->getHousingIds(true);
                                                        foreach ($housingId as $value){
                                                            if($personalInfo->getHousingType() == $value){
                                                                echo "<option value=\"".$value."\" selected = \"selected\">".$housing->getHousingTypeName($value)."</option>";
                                                            }else{
                                                                echo "<option value=\"".$value."\">".$housing->getHousingTypeName($value)."</option>";
                                                            }
                                                        }

                        echo "                      </select></td>
                                </tr>
                                <tr>
                                    <td colspan=\"3\" height = \"10px\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"right\"><font class=\"green\">Employee Type :</font></td>
                                    <td width=\"5px\"></td>
                                    <td align=\"left\">
                                                    <select name=\"employeeType\" style=\"width:200px\">
                                                        <option value=\"\"></option>";

                                                        $employeeTypeId = $employeeType->getEmployeeTypeIds(true);
                                                        foreach ($employeeTypeId as $value){
                                                            if($personalInfo->getEmployeeType() == $value){
                                                                echo "<option value=\"".$value."\" selected = \"selected\">".$employeeType->getEmployeeTypeName($value)."</option>";
                                                            }else{
                                                                echo "<option value=\"".$value."\">".$employeeType->getEmployeeTypeName($value)."</option>";
                                                            }
                                                        }
                echo "                                </select></td>
                                </tr>
                                <tr>
                                    <td height=\"50px\" colspan=\"3\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"center\" colspan=\"3\"><input type=\"submit\" name=\"submit\" value=\"Confirm Employee Details\" /></td>
                                </tr>
			</table>";
                    }else{  //the job is in the pending status
                        $original = new personalInfo();     //for the actual entry in the main table
                        $original->getEmployeeInformation($employeeId, true);
                        if($original->getName() == ""){
                                $original->getEmployeeInformation($employeeId, false);
                                echo "<center><font class=\"green\">New Employee In Pending Status</font></center>";
                                }
                        echo "
                            <table border=\"0\" align=\"center\" width=\"100%\">
                                <tr>
                                    <th width=\"20%\">Property</th>
                                    <th width=\"2%\"></th>
                                    <th width=\"28%\">New Value</th>
                                    <th width=\"2%\"></th>
                                    <th width=\"28%\" align = \"left\" >Old Value</th>
                                </tr>
                                <tr>
                                    <td colspan=\"5\" height = \"10px\"><hr size=\"3\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"right\"><font class=\"green\">Employee Code : </font></td>
                                    <td></td>
                                    <td align=\"left\"><input type=\"text\" name=\"employeeCode\" value=\"".$personalInfo->getEmployeeCode()."\" style=\"width:200px\" /></td>
                                    <td></td>
                                    <td align=\"left\"><font class=\"error\">".$original->getEmployeeCode()."</font></td>
                                </tr>
                                <tr>
                                    <td colspan=\"5\" height = \"10px\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"right\"><font class=\"green\">Name Of Employee : </font></td>
                                    <td width=\"5px\"></td>
                                    <td align=\"left\"><input type=\"text\" name=\"employeeName\" value=\"".$personalInfo->getName()."\" style=\"width:200px\" /></td>
                                    <td></td>
                                    <td align=\"left\"><font class=\"error\">".$original->getName()."</font></td>
                                </tr>
                                <tr>
                                    <td colspan=\"5\" height = \"10px\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"right\"><font class=\"green\">Permanent Address :</font></td>
                                    <td width=\"5px\"></td>
                                    <td align=\"left\"><textarea cols=\"23\" name=\"employeePadd\" rows=\"3\">".$personalInfo->getPermanentAddress()."</textarea></td>
                                    <td></td>
                                    <td align=\"left\"><font class=\"error\">".$original->getPermanentAddress()."</font></td>
                                </tr>
                                <tr>
                                    <td colspan=\"5\" height = \"10px\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"right\"><font class=\"green\">Temporary Address :</font></td>
                                    <td width=\"5px\"></td>
                                    <td align=\"left\"><textarea name=\"employeeTadd\" cols=\"23\" rows=\"3\">".$personalInfo->getTemporarAddress()."</textarea></td>
                                    <td></td>
                                    <td align=\"left\"><font class=\"error\">".$original->getTemporarAddress()."</font></td>
                                </tr>
                                <tr>
                                    <td colspan=\"5\" height = \"10px\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"right\"><font class=\"green\">Blood Group :</font></td>
                                    <td width=\"5px\"></td>
                                    <td align=\"left\"><input type=\"text\" name=\"employeeBgrp\" value=\"".$personalInfo->getBloodGroup()."\" style=\"width:200px\" /></td>
                                    <td></td>
                                    <td align=\"left\"><font class=\"error\">".$original->getBloodGroup()."</font></td>
                                </tr>
                                <tr>
                                    <td colspan=\"5\" height = \"10px\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"right\"><font class=\"green\">Date Of Birth :</font></td>
                                    <td width=\"5px\"></td>";
                                    $dob = explode("-", $personalInfo->getDob());
                                    $dob =  $dob[2]."-".$dob[1]."-".$dob[0];
                        echo "
                                    <td align=\"left\"><input type=\"text\" name=\"employeeDob\" value=\"".$dob."\" style=\"width:200px\" /> * Format DD-MM-YYYY</td>
                                    <td></td>";
                                    $dob = explode("-", $original->getDob());
                                    $dob =  $dob[2]."-".$dob[1]."-".$dob[0];
                        echo "
                                    <td align=\"left\"><font class=\"error\">".$dob."</font></td>
                                </tr>
                                <tr>
                                    <td colspan=\"5\" height = \"10px\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"right\"><font class=\"green\">Contact Number :</font></td>
                                    <td width=\"5px\"></td>
                                    <td align=\"left\"><input type=\"text\" name=\"employeeContact\" value=\"".$personalInfo->getContactNumber()."\" style=\"width:200px\" /></td>
                                    <td></td>
                                    <td align=\"left\"><font class=\"error\">".$original->getContactNumber()."</font></td>
                                </tr>
                                <tr>
                                    <td colspan=\"5\" height = \"10px\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"right\"><font class=\"green\">Name Of Department :</font></td>
                                    <td width=\"5px\"></td>
                                    <td align=\"left\">
                                                    <select name=\"department\" style=\"width:200px\">
                                                        <option value=\"\"></option>";
                                                    $department = new department();
                                                    $departmentId = $department->getDepartmentIds(true);
                                                    foreach ($departmentId as $value){
                                                        if($personalInfo->getDepartment() == $value){
                                                            echo "<option value=\"".$value."\" selected = \"selected\">".$department->getDepartmentName($value)."</option>";
                                                        }else{
                                                            echo "<option value=\"".$value."\">".$department->getDepartmentName($value)."</option>";
                                                        }
                                                    }
                        echo "                                 </select></td>
                                    <td></td>
                                    <td align=\"left\"><font class=\"error\">".$department->getDepartmentName($original->getDepartment())."</font></td>
                                </tr>
                                <tr>
                                    <td colspan=\"5\" height = \"10px\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"right\"><font class=\"green\">Housing Type :</font></td>
                                    <td width=\"5px\"></td>
                                    <td align=\"left\">
                                                    <select name=\"employeeHousing\" style=\"width:200px\">
                                                        <option value=\"\">N/A</option>";

                                                        $housingId = $housing->getHousingIds(true);
                                                        foreach ($housingId as $value){
                                                            if($personalInfo->getHousingType() == $value){
                                                                echo "<option value=\"".$value."\" selected = \"selected\">".$housing->getHousingTypeName($value)."</option>";
                                                            }else{
                                                                echo "<option value=\"".$value."\">".$housing->getHousingTypeName($value)."</option>";
                                                            }
                                                        }

                        echo "                      </select></td>
                                    <td></td>
                                    <td align=\"left\"><font class=\"error\">".$housing->getHousingTypeName($original->getHousingType())."</font></td>
                                </tr>
                                <tr>
                                    <td colspan=\"5\" height = \"10px\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"right\"><font class=\"green\">Employee Type :</font></td>
                                    <td width=\"5px\"></td>
                                    <td align=\"left\">
                                                    <select name=\"employeeType\" style=\"width:200px\">
                                                        <option value=\"\"></option>";

                                                        $employeeTypeId = $employeeType->getEmployeeTypeIds(true);
                                                        foreach ($employeeTypeId as $value){
                                                            if($personalInfo->getEmployeeType() == $value){
                                                                echo "<option value=\"".$value."\" selected = \"selected\">".$employeeType->getEmployeeTypeName($value)."</option>";
                                                            }else{
                                                                echo "<option value=\"".$value."\">".$employeeType->getEmployeeTypeName($value)."</option>";
                                                            }
                                                        }
                        echo "                      </select></td>
                                    <td></td>
                                    <td align=\"left\"><font class=\"error\">".$employeeType->getEmployeeTypeName($original->getEmployeeType())."</font></td>
                                </tr>
                                <tr>
                                    <td height=\"50px\" colspan=\"5\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td align=\"center\" colspan=\"5\"><input type=\"submit\" name=\"submit\" value=\"Confirm Pending Employee Details\" /></td>
                                </tr>
			</table>
                            ";
                    }
            	?>                
                </td>
            </tr>
        </table>
        </form>
        <?php
            echo "
                <table border=\"1\" align=\"center\" width=\"100%\">
                <tr>
                    <th width=\"5%\">SN</th>
                    <th width=\"40%\">Name</th>
                    <th width=\"8%\">View</th>
                    <th width=\"8%\">Edit</th>
                    <th width=\"8%\">Info</th>
                    <th width=\"8%\">Drop</th>
                </tr>
                <tr>
                    <td align=\"center\"><font class=\"green\">1</font></td>
                    <td align=\"center\" style=\"padding-left:10px\"><a href=\"#\" onclick=\"loadPHPFile('employee_personalInfo.php?type=personal&id=".$employeeId."')\">".$personalInfo->getName()."</a></td>
                    <td align=\"center\"><a href=\"#\"  onclick=\"loadPHPFile('employee_personalInfo.php?type=personal&id=".$employeeId."')\"><img src=\"../img/b_props.png\" alt=\"info\" /></a></td>
                    <td align=\"center\"><a href=\"./employee_edit.php?type=personal&id=".$employeeId."\" target=\"_parent\"><img src=\"../img/b_edit.png\" alt=\"edit\" /></a></td>
                    <td align=\"center\"><a href=\"#\"  onclick=\"loadPHPFile('employee_info.php?type=personal&id=".$employeeId."')\"><img src=\"../img/b_browse.png\" alt=\"info\" /></a></td>
                    <td align=\"center\"><a href=\"./employee_drop.php?id=".$employeeId."\" target=\"_parent\"><img src=\"../img/b_drop.png\" alt=\"delete\" /></a></td>
                </tr>
            </table>";
        ?>
      </div>
        
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
