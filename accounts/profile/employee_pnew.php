<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
    ////error_reporting(0)

    session_start();

    require_once '../include/class.editPersonalInfo.php';
    $editPersonalInfo = new editPersonalInfo();

    if(!$editPersonalInfo->checkLogged())
            $editPersonalInfo->redirect("../");
    if(isset ($_SESSION['emid'])){
        $employeeId = $_SESSION['emid'];
    }else
        $editPersonalInfo->redirect('./employee.php');

    if(count($_POST) && $_POST['submit'] == "Confirm Designation Information"){
        $error = 0;
        $errorLog = array();

        if(isset ($_POST['count'])){
            $count = $_POST['count'];
        }else{
            $error++;
            array_push($errorLog, "You Need To Enter The Designation");
        }
        $i = 0;
        while($i < $count){
            $designationName = "designationName".$i;
            $startdate = "startdate".$i;

            if($_POST[$designationName] == ""){
                ++$error;
                array_push($errorLog, "Please Select The Designation Name");
            }
            if($_POST[$startdate] == ""){
                ++$error;
                array_push($errorLog, "Please Select The Start Date Of The Designation");
            }
            ++$i;
        }

        if($error == 0){
            $i = 0;
            while($i < $count){
                $designationName = "designationName".$i;
                $startdate = "startdate".$i;
                $enddate = "enddate".$i;

                $startdate = explode('-', $_POST[$startdate]);
                $startdate = $startdate[2]."-".$startdate[1]."-".$startdate[0];

                $enddate = explode('-', $_POST[$enddate]);
                $enddate = $enddate[2]."-".$enddate[1]."-".$enddate[0];

                $designationDetails = array();
                array_push($designationDetails, $employeeId);
                array_push($designationDetails, $_POST[$designationName]);
                array_push($designationDetails, $startdate);
                array_push($designationDetails, $enddate);

                $editPersonalInfo->setEmployeeDesignation($designationDetails);
                ++$i;
            }

            
            if($editPersonalInfo->isAdmin())
                $editPersonalInfo->palert("Designation Information Registered Successfully .. Please Enter Accounts Information", "./employee_bnew.php");
            else
                $editPersonalInfo->palert("Designation Information Registered In Pending Status Successfully .. Please Enter Accounts Information", "./employee_bnew.php");
        }
    }


    require_once '../include/class.personalInfo.php';
    $personalInfo = new personalInfo();

    if($editPersonalInfo->isAdmin())
            $personalInfo->getEmployeeInformation($employeeId, true);
    else
        $personalInfo->getEmployeeInformation($employeeId, false);
    if($personalInfo->getName() == "")
            $editPersonalInfo->redirect('./employee1.php');

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
      	<table align="center" border="0" width="100%">
        	<!-- insertion of new departments will be done here -->
            <tr>
                <td height="10px" colspan="2" align="center"><h1 class="error">Employee Professional Information</h1><br />Employee Name : <font class="green"><?php echo $personalInfo->getName(); ?></font></td>
            </tr>
            <tr>
            	<td height="10px" colspan="2"><hr size="1" /></td>
            </tr>
            <tr>
                            <td align="center">Add Designations</td>
                            <td align="left"><select name="count" style="width:50px" onfocus="loadPhpFile(this.value)">
                            					<?php
													$i = 0;
													while($i < 100){
															echo "<option OnClick=\"loadPHPFile('getDesignationOptions.php?value=".$i."')\" value=\"".$i."\">".$i."</option>";
															++$i;
													}
                                                                ?>
									</select></td>
                        </tr>
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>           	
                <td colspan="2"><div id="infoDiv"></div></td>
            </tr>
            <tr>
            	<td colspan="2" align="center"><input type="submit" name="submit" value="Confirm Designation Information" /></td>
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