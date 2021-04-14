<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);

require_once '../include/class.department.php';
require_once '../include/class.personalInfo.php';
require_once '../include/class.employeeInfo.php';


$department = new department();
if(!$department->checkLogged())
        $department->redirect('../');
$employeeInfo = new employeeInfo();
$personalInfo = new personalInfo();

ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- Direct Fund Addition</title>
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
      	<form action="./direct_fund1.php" method="post">
        <table align="center" border="0" width="100%">
            <tr>
            	<td align="center" colspan="5"><font class="error">Please Select The Correct Operation To Be Performed</font><br /><hr size="2" /><br /><br /></td>
            </tr>

            <tr>
            	<td width="40%" align="right"><font class="error">Individual Employee :</font></td>
                <td width="2%"></td>
                <td align="left" width="50%">
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
            </tr>
            <tr>
            	<td colspan="3" height="10px"></td>
            </tr>
            <tr>
            	<td width="40%" align="right"><font class="error">Type Of Fund:</font></td>
                <td width="2%"></td>
                <td align="left" width="50%">
                                            <select name="fund" style="width:250px">
                                                <option value="GPF">GPF</option>
                                                <option value="CPF">CPF</option>
                                                <option value="NPS">NPS</option>
                                            </select></td>
            </tr>
            <tr>
            	<td colspan="3" height="10px"></td>
            </tr>
            <tr>
            	<td width="40%" align="right"><font class="error">Select Month:</font></td>
                <td width="2%"></td>
                <td align="left" width="50%">
                                            <select name="month" style="width:250px">
                                            <?php
                                                $i = 0;
                                                while (true){
                                                    $month = date('Ym', mktime(0, 0, 0, 2+$i, 15, 2010));
                                                    echo "<option value=\"$month\">".$department->nameMonth($month)."</option>";
                                                    ++$i;
                                                    if($month > $department->getCurrentMonth())
                                                            break;
                                                }



                                            ?>
                                            </select></td>
            </tr>
            <tr>
            	<td colspan="3" height="10px"></td>
            </tr>
            <tr>
            	<td width="40%" align="right"><font class="error">Amount To Add:</font></td>
                <td width="2%"></td>
                <td align="left" width="50%"><input type="text" name="amount" style="width:200px" /></td>
            </tr>
            <tr>
            	<td colspan="3" height="10px"></td>
            </tr>
            <tr>
            	<td align="right">Fund Insertion Type : </td>
                <td align="center">:</td>
                <td align="left">
                	<select name="fund_type" style="width:250px">
                    <?php 
                    	$fund_types = $department->getFlagIds();                    	
                    	foreach ($fund_types as $flag)
                    		echo "<option value=\"$flag\">".$department->getFlagComment($flag)."</option>";
                    ?>	                        
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="3" align="center"><br /><br />
                    <input type="submit" value="Add This To Account" style="width:200px" />
                    <input type="button" value="Return Back" style="width:200px" />
                </td>
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