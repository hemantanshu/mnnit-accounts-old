<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();

    session_start();

    require_once '../include/class.editemployeeType.php';
    $employeeType = new editemployeeType();

    if(!$employeeType->checkLogged())
        $employeeType->redirect('../');

    if(count($_POST) > 0 && $_POST['submit'] == "Confirm Dropping Of This Job"){
        $employeeTypeId = $_POST['employeeTypeId'];

        if(!$employeeType->isEditable($employeeTypeId))
                $employeeType->palert("This employeeType Type Cannot Be Edited ", "./employeetype.php");

        if($employeeType->dropPendingJob($employeeTypeId))
                $employeeType->palert("The Pending Job Has Been Succesfully Dropped", "./employeetype_pending.php");
        $employeeType->redirect("./employeetype.php");
    }

    if(isset ($_GET['id'])){
        $employeeTypeId = $_GET['id'];
        if(!$employeeType->isEditable($employeeTypeId))
            $employeeType->palert("This employeeType Type Cannot Be Edited ", "./employeetype.php");
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
                <td colspan="3" align="center"><font color="#FF0000">Dropping employeeType Pending Job </font><hr size="1" /></td>
            </tr>

            <?php
                if($employeeType->isWorkInPendingStatus($employeeTypeId)){
                        $j = 0;

                        if($employeeType->getemployeeTypeTypeName($employeeTypeId, false) != ""){
                            echo "
                                <tr>
                                    <th width=\"30%\"></th>
                                    <th width=\"35%\">New Value</th>
                                    <th width=\"35%\">Old Value</th>
                                </tr>
                                <tr>
                                    <td align=\"center\">Name</td>
                                    <td align=\"center\">".$employeeType->getemployeeTypeTypeName($employeeTypeId, false)."</td>
                                    <td align=\"center\"><font class=\"green\">".$employeeType->getemployeeTypeTypeName($employeeTypeId, true)."</font></td>
                                </tr>
                                </tr>
                                <tr>
                                    <td colspan=\"3\" height=\"10px\"><hr size=\"1\" /></td>
                                </tr> ";
                        }else{
                            echo "
                                <tr>
                                    <td colspan=\"3\" align=\"center\"><font class=\"green\">For employeeType Type : ".$employeeType->getemployeeTypeTypeName($employeeTypeId, true)."</font></td>
                                </tr>
                                </tr>
                                <tr>
                                    <th width=\"30%\"></th>
                                    <th width=\"35%\">New Value</th>
                                    <th width=\"35%\">Old Value</th>
                                </tr>";

                        }
                        $employeeTypePending = $employeeType->getemployeeTypeDependents($employeeTypeId, false);
                        $j = 0;
                        foreach ($employeeTypePending as $value){
                            $j++;

                            $employeeTypePendingDetails = $employeeType->getemployeeTypeDependentDetails($value, false);
                            $employeeTypeOriginalDetails = $employeeType->getemployeeTypeDependentDetails($value, true);
                            if($employeeTypePending[$j][6] == "y"){
                                echo "
                                    <tr>
                                        <td colspan=\"3\" align=\"center\"><br /><hr size=\"1\" /><br /><h2>Dependent Number : ".$j." </h2> </td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Value</td>
                                        <td align=\"center\">".$employeeTypePendingDetails[2]."</td>
                                        <td align=\"center\"><font class=\"green\">".$employeeTypeOriginalDetails[2]."</font></td>
                                    </tr>
                                    <tr>
                                        <td height=\"5px\"></td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Dependency</td>
                                        <td align=\"center\">".$employeeType->getAllowanceTypeName($employeeTypePendingDetails[3])."</td>
                                        <td align=\"center\"><font class=\"green\">".$employeeType->getAllowanceTypeName($employeeTypeOriginalDetails[3])."</font></td>
                                    </tr>
                                    <tr>
                                        <td height=\"5px\"></td>
                                    </tr>";
                            }
                        }
                }
                ?>
            <tr>
            	<td height="20px"><input type="hidden" name="employeeTypeId" value="<?php echo $employeeTypeId; ?>" /></td>
            </tr>
            <tr>
            	<td colspan="3" align="center"><input type="submit" name="submit" value="Confirm Dropping Of This Job" /></td>
            </tr>


        </table></form>
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