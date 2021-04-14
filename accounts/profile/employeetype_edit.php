<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();

    session_start();

    require_once '../include/class.editEmployeeType.php';
    $employeeType = new editEmployeeType();

    if(!$employeeType->checkLogged())
        $employeeType->redirect('../');

    if(count($_POST) > 0 && $_POST['submit'] == "Confirm EmployeeType Name Change"){
        $employeeTypeId = $_POST['deptId'];
        $error = 0;
        $errorLog = array();


        if(!$employeeType->isEditable($employeeTypeId, true)){
            $error++;
            $employeeType->palert("The EmployeeType Cannot Be Edited", "./employeetype.php");
        }
        if($_POST['deptName'] == ""){
            $error++;
            array_push($errorLog, "The EmployeeType Field Cannot Be Left Empty");
        }
        if(trim($_POST['deptName']) == $employeeType->getEmployeeTypeName($employeeTypeId) && !$employeeType->isWorkInPendingStatus($employeeTypeId)){
            $error++;
            array_push($errorLog, "The Updated Name Is Same As The Previous One");
        }

        if($error == 0){
            $deptName = ucwords(strtolower($_POST['deptName']));
            if($employeeType->updateEmployeeTypeName($employeeTypeId, $deptName)){
                if($employeeType->isAdmin())
                    $employeeType->palert("The EmployeeType Name Has Been Successfully Edited", "./employeetype.php");
                else
                    $employeeType->palert("The EmployeeType Name Has Been Queued For Being Edited", "./employeetype.php");
            }                
        }

    }elseif(isset ($_GET['id'])){
        $employeeTypeId = $_GET['id'];
        if(!$employeeType->isEditable($employeeTypeId))
            $employeeType->palert("This EmployeeType Cannot Be Edited ", "./employeetype.php");
    }else
        $employeeType->redirect("./employeetype.php");

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
      <div class="left"><img class="imgright" src="../img/logo.gif" alt="Forest Thistle" height="105px">&nbsp;Accounts Employee Type</div>
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
            	<td colspan="3" align="center"><h2><font color="#FF0000">Editing EmployeeType Information </font><br /><br /><?php echo $employeeType->getEmployeeTypeName($employeeTypeId); ?></h2><br /><hr size="1" /></td>
            </tr>
         	<tr>
            	<td height="10px"></td>
            </tr>
                <?php
                    if(isset ($error) && $error != 0 && is_array($errorLog)){
                        foreach ($errorLog as $value) {
                                echo "<tr>
                                                <td colspan=\"3\" align=\"center\"><font class=\"error\">".$value."</font></td>
                                        </tr>";
                        }
                    }
                ?>

            <?php
                if($employeeType->isWorkInPendingStatus($employeeTypeId)){
                        $employeeTypePending = $employeeType->getPendingEmployeeTypeInfo($employeeTypeId);
                        if($employeeTypePending[1] == "y")
                            echo "<tr>
                                    <td align=\"center\">Proposed New Name : </td>
                                    <td width=\"20px\"></td>
                                    <td align=\"left\"><input type=\"text\" name=\"deptName\" value=\"".$employeeTypePending[0]."\" style=\"width:350px\" /></td>
                                </tr>";
                        else
                            echo "<tr>
                                    <td colspan=\"2\" align=\"center\"><font class=\"error\">The EmployeeType Has Been Opted To Be Dropped.  Confirm Deletion By Checking The CheckBox <br />Drop EmployeeType Name</font></td>
                                    <td width=\"100px\" align=\"center\"><input type=\"checkbox\" name=\"dropDept\" /></td>
                                </tr>";
                }else
                    echo "<tr>
                            <td align=\"center\">Name Of EmployeeType</td>
                            <td width=\"20px\"></td>
                            <td align=\"left\"><input type=\"text\" name=\"deptName\" value=\"".$employeeType->getEmployeeTypeName($employeeTypeId)."\" style=\"width:300px\" /></td>
                        </tr>"
                ?>


            <tr>
            	<td height="20px"></td>
            </tr>
            <tr>
            	<td colspan="3" align="center">
                    <input type="hidden" name="deptId" value="<?php echo $employeeTypeId; ?>" />
                    <input type="submit" name="submit" value="<?php
                                                                if($employeeType->isWorkInPendingStatus($employeeTypeId)){
                                                                   if($employeeTypePending[1] == "y")
                                                                       echo "Confirm EmployeeType Name Change";
                                                                   else
                                                                       echo "Confirm EmployeeType Deletion";
                                                                }
                                                                   else
                                                                       echo "Confirm EmployeeType Name Change";

                                                               ?>" />
                    <input type="button" name="Return Back" value="Return Back" onclick="window.location='<?php echo $_SERVER['HTTP_REFERER']; ?>'" /></td>
            </tr>
            <tr>
            	<td colspan="3" height="30px"><hr size="2" /></td>
            </tr>
            <tr>
            	<td colspan="3" align="center" valign="top">
                	<table align="center" border="0" width="100%">
        	<!-- insertion of new employeeTypes will be done here -->
            <tr>
            	<td height="10px" align="center"><h1 class="error">View / Edit EmployeeType Information</h1></td>
            </tr>
            <tr>
            	<td height="10px"><hr size="1" /></td>
            </tr>
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
            	<td align="center" valign="top">
                	<table border="1" align="center"  >
                            <tr>
                                <th width="400px">EmployeeType Name</th>
                                <th width="80px">Employees</th>
                                <th width="50px">Edit</th>
                                <th width="50px">Info</th>
                                <th width="50px">Delete</th>
                            </tr>
                            <?php

                                if(is_array($employeeType->getEmployeeTypeIds(true))){
                                    foreach ($employeeType->getEmployeeTypeIds(true) as $value){
                                        echo "<tr>
                                                <td style=\"padding-left:40px\" align=\"left\"><font class=\"display\">".$employeeType->getEmployeeTypeName($value)."</font></td>
                                                <td align=\"center\"><a href=\"#\"  onclick=\"loadPHPFile('employeetype_employee.php?id=".$value."')\" ><font class=\"display\">".$employeeType->getEmployeeTypeEmployeeCount($value)."</font></a></td>
                                                <td align=\"center\"><a href=\"./employeetype_edit.php?id=".$value."\" target=\"_parent\"><img src=\"../img/b_edit.png\" alt=\"edit\" /></a></td>
                                                <td align=\"center\"><a href=\"#\"  onclick=\"loadPHPFile('employeetype_info.php?id=".$value."')\"><img src=\"../img/b_browse.png\" alt=\"info\" /></a></td>
                                                <td align=\"center\"><a href=\"./employeetype_drop.php?id=".$value."\" target=\"_parent\"><img src=\"../img/b_drop.png\" alt=\"delete\" /></a></td>
                                            </tr>
                                            <tr>
                                                <td colspan=\"5\" height=\"3px\"></td>
                                            </tr>";
                                    }
                                }else{
                                    echo "<tr>
                                            <td colspan=\"5\" align=\"center\"><font class=\"error\">No EmployeeType Record Exists</font></td>
                                        </tr>";
                                }
                            ?>

                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="5" align="center">
                            <table align="center" border="0" width="100%">
                            <tr>
                                    <td align="center"><div id="infoDiv"></div></td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>
                </td>
            </tr>
            <tr>
                <td colspan="3" height="30px"></td>
            </tr>           
        </table>
        </form>
      </div>
      <div class="sidenav">
      	<hr size="2" />
       <center> <font color="#FF0000" size="+1"><b><?php echo $employeeType->getOfficerName(); ?></b></font></center>
       	<hr size="2" /><br />
        <h2><font color="#008000">QUICK NAVIGATION PANEL</font></h2>
        <?php
            include './navigation/employeeType.php';
        ?>
      </div>
      <div class="clearer"><span></span></div>
    </div>
    <div class="footer">@webteam.<a href="http://www.mnnit.ac.in" title="MNNIT">mnnit</a> Designed And Developed By Hemant Kumar Sah (B.Tech ECE 2011)</div>
  </div>
</div>
</body>
</html>