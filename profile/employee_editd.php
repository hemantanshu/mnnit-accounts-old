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



    if(count($_POST) && $_POST['submit'] == "Confirm Designation Information"){
        $error = 0;
        $errorLog = array();
        $employeeId = $_POST['employeeId'];

        $i = 0;
        while(true){
            $designationName = "designationName0".$i;
            $startdate = "startdate0".$i;
            $enddate = "enddate0".$i;
            $dependentId = "rankId0".$i;

            if(!isset ($_POST[$dependentId]))
                break;
            ++$i;
            
            if($_POST[$designationName] == ""){
                ++$error;
                array_push($errorLog, "Please Select The Designation Name Number :$i");
            }
            if($_POST[$startdate] == ""){
                ++$error;
                array_push($errorLog, "Please Select The Start Date Of The Designation Number :$i");
            }
            if($_POST[$dependentId] == ""){
                $editPersonalInfo->redirect('./employee.php');
            }
        }
        $count = $i;
        if(isset ($_POST['count'])){
            $extraCount = $_POST['count'];
            $i = 0;
            while($i < $extraCount){
                $designationName = "designationName".$i;
                $startdate = "startdate".$i;

                if($_POST[$designationName] == ""){
                    ++$error;
                    array_push($errorLog, "Error  -- Please Input Again");
                }
                if($_POST[$startdate] == ""){
                    ++$error;
                    array_push($errorLog, "Error  -- Please Input Again");
                }
                ++$i;
            }
        }
        if($error == 0){
            $i = 0;
            while ($i < $count){
                $details = array();

                $designationName = "designationName0".$i;
                $startdate = "startdate0".$i;
                $enddate = "enddate0".$i;
                $dependentId = "rankId0".$i;

                array_push($details, $_POST[$dependentId]);
                array_push($details, $employeeId);
                array_push($details, $_POST[$designationName]);
                array_push($details, $editPersonalInfo->getChangedDateFormat($_POST[$startdate]));
                array_push($details, $editPersonalInfo->getChangedDateFormat($_POST[$enddate]));
                $editPersonalInfo->updateEmployeeDesignation($details);
                ++$i;
            }
            $i = 0;
            while($i < $extraCount){
                $designationName = "designationName".$i;
                $startdate = "startdate".$i;
                $enddate = "enddate".$i;

                $designationDetails = array();

                array_push($designationDetails, $employeeId);
                array_push($designationDetails, $_POST[$designationName]);
                array_push($designationDetails, $editPersonalInfo->getChangedDateFormat($_POST[$startdate]));
                array_push($designationDetails, $editPersonalInfo->getChangedDateFormat($_POST[$enddate]));
                
                $editPersonalInfo->setEmployeeDesignation($designationDetails);
                ++$i;
            }

            $ePending = new employeePending();
            
            if($ePending->isEmployeeBankInformationInPendingStatus($employeeId))
                    $url = "./employee_editb.php?id=".$employeeId."";
            elseif($ePending->isEmployeeMasterSalaryInPendingStatus($employeeId))
                    $url = "./employee_edita.php?id=".$employeeId."";
            else
                $url = "./employee_infoview.php?id=".$employeeId."";

            if($editPersonalInfo->isAdmin())
                $editPersonalInfo->palert("The Designation Details Has Been SuccessFully Updated", $url);
            else
                $editPersonalInfo->palert("The Employee Designation Details Is In Pending Status", $url);
        }
    }
    elseif(isset ($_GET['id']))
        $employeeId = $_GET['id'];
    else
        $editPersonalInfo->redirect('./employee.php');

   
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.employeeInfo.php';
    require_once '../include/class.designation.php';

    $personalInfo = new personalInfo();
    $employeeInfo = new employeeInfo();
    $designation = new designation();
    
    
    ob_end_flush();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- Employee Professional Information</title>
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
        <?php
            echo "
                <table align=\"center\" width=\"100%\" border=\"0\">
                    <tr>
                    <td align=\"center\"><input type=\"hidden\" name=\"employeeId\" value=\"".$employeeId."\" /><a href=\"./employee_editp.php?id=".$employeeId."\" >Edit Personal Information</a> || <a href=\"./employee_editd.php?id=".$employeeId."\" >Edit Designation Information</a> || <a href=\"./employee_editb.php?id=".$employeeId."\" >Edit Accounts Information</a> || <a href=\"./employee_edita.php?id=".$employeeId."\" >Edit Allowances Information</a></td>
                </tr>
            </table>";
        ?>
      	<table align="center" border="0" width="100%">
            <tr>
                <td colspan="3" align="center"><font class="error"><br />Editing Employee Designation Information <br />Employee Name : <?php echo $personalInfo->getName(); ?></font></td>
            </tr>
            <tr>
            	<td colspan="3"><hr size="1" /> <br /></td>
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
            <?php
                $rankId = $employeeInfo->getEmployeeRankIds($employeeId, false);
                if(sizeof($rankId)){ //the work is in the pending status
                    echo "
                        <table align=\"center\" border=\"0\" width=\"100%\">
                            <tr>
                            <th width=\"23%\">Property :</th>
                            <th width=\"2%\"></th>
                            <th width=\"35%\">New Value :</th>
                            <th width=\"5%\"></th>
                            <th width=\"35%\" align = \"left\">Old Value :</th>
                        </tr>";

                    $i = 0;
                    foreach ($rankId as $value){
                        $designationName = "designationName0".$i;
                        $startdate = "startdate0".$i;
                        $enddate = "enddate0".$i;
                        $dependentId = "rankId0".$i;
                        ++$i;

                        $rankDetails = $employeeInfo->getRankDetails($value, false);
                        $original = $employeeInfo->getRankDetails($value, true);

                        echo "
                            <tr>
                                <td colspan=\"5\"><br /><input type=\"hidden\" name=\"".$dependentId."\" value=\"".$value."\" /><hr size=\"2\" /><br /></td>
                            </tr>
                            <tr>
                                <td align=\"right\"><font class=\"green\">Designation No : ".$i."</font></td>
                                <td></td>
                                <td align=\"left\"><select name=\"".$designationName."\" style=\"width:200px\">
                                                    <option value=\"\">None</option> ";
                                                       $designationOptions = $designation->getDesignationIds(true);
                                                        if(is_array($designationOptions)){
                                                            foreach($designationOptions as $options){
                                                                if($rankDetails[2] == $options)
                                                                    echo "<option value=\"".$options."\" selected = \"selected\">".$designation->getDesignationTypeName($options, true)."</option>";
                                                                else
                                                                    echo "<option value=\"".$options."\">".$designation->getDesignationTypeName($options, true)."</option>";
                                                            }
                                                        }
                        echo "                      </select>
                                    </td>
                                <td></td>
                                <td align=\"left\"><font class=\"error\">".$designation->getDesignationTypeName($original[2], true)."</font></td>
                            </tr>
                            <tr>
                                <td height=\"10px\"></td>
                            </tr>
                            <tr>
                                <td align=\"right\"><font class=\"green\">Starting Date :</font></td>
                                <td></td>
                                <td align=\"left\"><input type=\"text\" name=\"$startdate\" value=\"".$editPersonalInfo->getChangedDateFormat($rankDetails[3])."\" style=\"width:200px\" /></td>
                                <td></td>
                                <td align=\"left\"><font class=\"error\">".$editPersonalInfo->getChangedDateFormat($original[3])."</font></td>
                            </tr>
                            <tr>
                                <td height=\"10px\"></td>
                            </tr>
                            <tr>
                                <td align=\"right\"><font class=\"green\">Leaving Date :</font></td>
                                <td></td>
                                <td align=\"left\"><input type=\"text\" name=\"$enddate\" value=\"".$editPersonalInfo->getChangedDateFormat($rankDetails[4])."\" style=\"width:200px\" /></td>
                                <td></td>
                                <td align=\"left\"><font class=\"error\">".$editPersonalInfo->getChangedDateFormat($original[4])."</font></td>
                            </tr>
                            <tr>
                                <td height=\"25px\"></td>
                            </tr>";

                    }
                }else{
                    $rankId = $employeeInfo->getEmployeeRankIds($employeeId, true);
                    echo "
                        <table align=\"center\" border=\"0\" width=\"100%\">                            
                            <tr>
                                <td colspan=\"3\" align = \"center\"><font class=\"error\">Existing Designations</font></td>
                            </tr>    
                                ";
                    $i = 0;
                    foreach ($rankId as $value){
                        $designationName = "designationName0".$i;
                        $startdate = "startdate0".$i;
                        $enddate = "enddate0".$i;
                        $dependentId = "rankId0".$i;
                        ++$i;
                        $rankDetails = $employeeInfo->getRankDetails($value, true);
                        echo "
                            <tr>
                                <td colspan=\"5\"><br /><input type=\"hidden\" name=\"".$dependentId."\" value=\"".$value."\" /><hr size=\"2\" /><br /></td>
                            </tr>
                            <tr>
                                <td align=\"right\"><font class=\"green\">Designation No : ".$i."</font></td>
                                <td></td>
                                <td align=\"left\"><select name=\"".$designationName."\" style=\"width:200px\">
                                                    <option value=\"\">None</option> ";
                                                       $designationOptions = $designation->getDesignationIds(true);
                                                        if(is_array($designationOptions)){
                                                            foreach($designationOptions as $options){
                                                                if($rankDetails[2] == $options)
                                                                    echo "<option value=\"".$options."\" selected = \"selected\">".$designation->getDesignationTypeName($options, true)."</option>";
                                                                else
                                                                    echo "<option value=\"".$options."\">".$designation->getDesignationTypeName($options, true)."</option>";
                                                            }
                                                        }
                        echo "                      </select>
                                    </td>
                            </tr>
                            <tr>
                                <td height=\"10px\"></td>
                            </tr>
                            <tr>
                                <td align=\"right\"><font class=\"green\">Starting Date :</font></td>
                                <td></td>
                                <td align=\"left\"><input type=\"text\" name=\"".$startdate."\" value=\"".$editPersonalInfo->getChangedDateFormat($rankDetails[3])."\" style=\"width:200px\" /></td>
                            </tr>
                            <tr>
                                <td height=\"10px\"></td>
                            </tr>
                            <tr>
                                <td align=\"right\"><font class=\"green\">Leaving Date :</font></td>
                                <td></td>
                                <td align=\"left\"><input type=\"text\" name=\"".$enddate."\" value=\"".$editPersonalInfo->getChangedDateFormat($rankDetails[4])."\" style=\"width:200px\" /></td>
                            </tr>";
                    }
                    echo "
                            <tr>
                                <td colspan=\"5\"><br /><hr size=\"2\" /><br /></td>
                            </tr>
                            <tr>
                                <td align=\"right\"><font class = \"green\">Add Designations</font></td>
                                <td align=\"center\" colspan=\"2\"><select name = \"count\" style=\"width:100px\" onfocus=\"loadPhpFile(this.value)\">";
                                $i = 0;
                                while($i < 100){
                                    echo "<option OnClick=\"loadPHPFile('getDesignationOptions.php?value=".$i."')\" value=\"".$i."\">".$i."</option>";
                                    ++$i;
                                }
                                                                
                    echo "						</select></td>
                            </tr>                            
                            <tr>
                                <td height=\"10px\"></td>
                            </tr>
                            <tr>
                                <td colspan=\"3\" align = \"center\"><font class=\"error\">New Designations</font></td>
                            </tr>

                            <tr>
                                <td colspan=\"3\"><div id=\"infoDiv\"></div></td>
                            </tr>
                            <tr>
                                <td height=\"10px\"></td>
                            </tr>";

                }
            ?>     
            <tr>
            	<td colspan="3" align="center"><input type="submit" name="submit" value="Confirm Designation Information" /></td>
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