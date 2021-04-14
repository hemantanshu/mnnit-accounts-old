<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();

    session_start();
    
    require_once '../include/class.loggedInfo.php';
    require_once '../include/class.personalInfo.php';
    
    $loggedInfo = new loggedIn();
    $personalInfo = new personalInfo();    

    if(!$loggedInfo->checkEmployeeLogged())
        $loggedInfo->redirect('../');
    $employeeId = $loggedInfo->checkEmployeeLogged();    
 	$personalInfo->getEmployeeInformation($employeeId, true);
    
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
        <table align="center" border="0" width="100%">
  			<tr>
            	<td align="center"><h2>DISPLAYING DESIGNATION INFORMATION</h2><hr size="2" /><br /></td>
            </tr>
  			<tr>
            	<td align="right">
            	<?php 
            		require_once '../include/class.employeeInfo.php';
			        require_once '../include/class.designation.php';
					require_once '../include/class.dateDifference.php';
			
			
			        $designation = new designation();
			        $employeeInfo = new employeeInfo();
			        $dateDifference = new dateDifference();
			
			        $today = date("Y")."-".date("m")."-".date("d");
			        $personalInfo->getEmployeeInformation($employeeId, true);
			        echo "
			            <table align=\"center\" width=\"100%\" border=\"0\">
			                <tr>
			                    <th width=\"5%\">SN</th>
			                    <th width=\"25%\">Designation Name :</th>
			                    <th width=\"25%\">Joining Date</th>
			                    <th width=\"25%\">Leaving Date</th>
			                    <th width=\"20%\">Tenure</th>
			             	</tr>
			                <tr>
			                	<td colspan=\"5\" height = \"25px\" align=\"center\"><font class=\"error\">PRESENT DESIGNATIONS :</font></td>
			                </tr>";
			
			        $rankId = $employeeInfo->getEmployeeRankIds($employeeId, true);
			        $i = 0;
			        foreach ($rankId as $value){
			            $rankDetails = $employeeInfo->getRankDetails($value, true);
			            $dateDifference->getDifference($today, $rankDetails[3]);
			            ++$i;
			            echo  "
			                <tr>
			                    <td align=\"center\"><font class=\"green\">".$i."</font></td>
			                    <td align=\"left\" style=\"padding-left:20px\">".$designation->getDesignationTypeName($rankDetails[2], true)."</td>
			                    <td align=\"center\"><font class=\"green\">".$rankDetails[3]."</font></td>
			                    <td align=\"center\"><font class=\"green\">".$rankDetails[4]."</font></td>
			                    <td align=\"center\"><font class=\"green\">".$dateDifference->getDays()." Days</font></td>
			                </tr>
			                <tr>
			                	<td colspan=\"5\" height=\"5px\"></td>
			                </tr>";
			        }
			        echo  "
			                <tr>
			                	<td colspan=\"5\" height=\"15px\"></td>
			                </tr>
			                <tr>
			                	<td height = \"25px\" colspan=\"5\" align=\"center\"><font class=\"error\">PREVIOUS DESIGNATIONS :</font></td>
			                </tr>";
			        $rankId = $employeeInfo->getEmployeeOldRankIds($employeeId);
			        $i = 0;
			        foreach ($rankId as $value){
			            $rankDetails = $employeeInfo->getRankDetails($value, true);
			            $dateDifference->getDifference($rankDetails[4], $rankDetails[3]);
			            ++$i;
			            echo  "
			                <tr>
			                    <td align=\"center\"><font class=\"green\">".$i."</font></td>
			                    <td align=\"left\" style=\"padding-left:20px\"><a href=\"./designation.php\" target=\"_parent\">".$designation->getDesignationTypeName($rankDetails[2], true)."</a></td>
			                    <td align=\"center\"><font class=\"green\">".$rankDetails[3]."</font></td>
			                    <td align=\"center\"><font class=\"green\">".$rankDetails[4]."</font></td>
			                    <td align=\"center\"><font class=\"green\">".$dateDifference->getDays()."</font></td>
			                </tr>
			                <tr>
			                	<td colspan=\"5\" height=\"5px\"></td>
			                </tr>";
			        }
			        if($i == 0){
			            echo "
			                <tr>
			                    <td colspan=\"5\" height=\"15px\" align = \"center\"><font class = \"green\">No Previous Records</td>
			                </tr>";
			        }
			        echo  "
			            </table>
			            <br /><br />";
            	
            	?>
            	</td>
            </tr>
        </table>
      </div>
      <div class="sidenav">
      	<hr size="2" /><center>
        <font color="#FF0000" size="+1"><b><?php echo $personalInfo->getName(); ?></b></font>
       	<hr size="2" /></center><br />
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
