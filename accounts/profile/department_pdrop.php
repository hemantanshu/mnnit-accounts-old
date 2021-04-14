<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();

    session_start();

    require_once '../include/class.editDepartment.php';
    $loggedInfo = new editDepartment();

    if(!$loggedInfo->checkLogged())
        $loggedInfo->redirect('../');

    if(count($_POST) > 0 && $_POST['submit'] == "Confirm Deletion Of This Pending Work"){
        $departmentId = $_POST['deptId'];

        if(!$loggedInfo->isWorkInPendingStatus($departmentId))
                $loggedInfo->palert("No Work Is In Pending Status For This Department ", "./department.php");
        if(!$pendingId = $loggedInfo->getPendingNumber($departmentId))
                $loggedInfo->palert("No Work Is In Pending Status For This Department ", "./department.php");

        if($loggedInfo->dropPendingJob($departmentId))
                $loggedInfo->palert("The Pending Job Has Been Successfully Dropped", "./department_pending.php");
    }elseif(isset ($_GET['id'])){
        $departmentId = $_GET['id'];
        if(!$loggedInfo->isWorkInPendingStatus($departmentId))
            $loggedInfo->palert("No Work Is In Pending Status For This Department ", "./department.php");
    }else
        $loggedInfo->redirect("./department.php");



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
             	<td colspan="3" align="center"><h2>Dropping Pending Job Work <br /><font color="#FF0000"><?php echo $loggedInfo->getDepartmentName($departmentId); ?></font></h2></td>
            </tr>
            <tr>
            	<td height="10px" colspan="3"><hr size="2" /></td>
            </tr>
            <?php
                
                $departmentPending = $loggedInfo->getPendingDepartmentInfo($departmentId);
                if($departmentPending[1] == "y")
                    echo "<tr>
                            <td align=\"right\">Proposed New Name : </td>
                            <td width=\"20px\"></td>
                            <td align=\"left\"><font class=\"error\">".$departmentPending[0]."</font></td>
                        </tr>";
                else
                    echo "<tr>
                            <td colspan=\"3\" align=\"center\"><font class=\"error\">The Department Has Been Opted To Be Dropped.<br />Drop Department Name</font></td>
                            
                        </tr>";

                ?>


            <tr>
            	<td height="20px"></td>
            </tr>
            <tr>
            	<td colspan="3" align="center" width="100%">
                	<table align="center" border="1" width="100%">
                        <tr>
                            <th width="33%">Operator</th>
                            <th width="33%">Supervisor</th>
                            <th width="33%">Admin</th>
                        </tr>
                        <?php
                            if($value = $loggedInfo->getPendingNumber($departmentId)){
                                $logInfo = $loggedInfo->getPendingLogIdInfo($value);
                                    echo "
                                        <tr>
                                            <td align=\"center\"><font class=\"display\">".$logInfo[0]."</font></td>
                                            <td align=\"center\"><font class=\"display\">".$logInfo[1]."</font></td>
                                            <td align=\"center\"><font class=\"display\">".$logInfo[2]."</font></td>
                                        </tr>
                                        <tr>
                                            <td align=\"center\"><font class=\"display\">".$loggedInfo->getOfficerNameNotLogged($logInfo[3])."</font></td>
                                            <td align=\"center\"><font class=\"display\">".$loggedInfo->getOfficerNameNotLogged($logInfo[4])."</font></td>
                                            <td align=\"center\"><font class=\"display\">".$loggedInfo->getOfficerNameNotLogged($logInfo[5])."</font></td>
                                        </tr>
                                        <tr>
                                            <td colspan=\"3\" height=\"5px\"><hr size=\"3\" color=\"#FF0000\" /></td>
                                        </tr>
                                        ";
                            }
                                    else
                                        $loggedInfo->redirect('./department.php');
                               
                        ?>                        
                    </table>
                </td>
            </tr>
            <tr>
            	<td height="30px"></td>
            </tr>
            <tr>
            	<td align="center" colspan="3"><input type="hidden" name="deptId" value="<?php echo $departmentId; ?>" /><input type="submit" name="submit" value="Confirm Deletion Of This Pending Work" /></td>
            </tr>
            <tr>
            	<td height="30px"></td>
            </tr>
        </table>        
        </form>
      </div>
      <div class="sidenav">
      	<hr size="2" />
        <center><font color="#FF0000" size="+1"><b><?php echo $loggedInfo->getOfficerName(); ?></b></font></center>
       	<hr size="2" /><br />
        <h2><font color="#008000">QUICK NAVIGATION PANEL</font></h2>
        <?php
            include './navigation/department.php';
        ?>
      </div>
      <div class="clearer"><span></span></div>
    </div>
    <div class="footer">@webteam.<a href="http://www.mnnit.ac.in" title="MNNIT">mnnit</a> Designed And Developed By Hemant Kumar Sah (B.Tech ECE 2011)</div>
  </div>
</div>
</body>
</html>