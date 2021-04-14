<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);

require_once '../include/class.allowance.php';
require_once '../include/class.accountHead.php';
$allowance = new allowance();
$accountHead = new accountHead();
if(!$accountHead->checkLogged())
  	$accountHead->redirect('../');
if (isset($_POST) && $_POST['submit'] == "Get The Quarterwise Report"){
		$sDate = $_POST['syear'].(strlen($_POST['smonth']) == 1 ? '0'.($_POST['smonth']) : $_POST['smonth']);
		$url = "quarter.php?date=$sDate";
		$allowance->redirect($url);
	}



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
      <div class="left">&nbsp;&nbsp;&nbsp;Accounts Department</div>
      <div class="right">
        <div align="center"> MNNIT <br />
          ALLAHABAD</div>

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
        <table align="center" style=" border-collapse:collapse" width="100%">
        	<tr>
            	<td align="center" colspan="3"><font class="error">Please Select Month for Quarterwise Report</font><br /><hr size="2" /><br /><br /></td>
            </tr>
            <tr>
            	<td align="right" width="40%">Select Date</td>
                <td width="20px"></td>
                <td align="left">
                			<select name="smonth" style="width:150px">
                                            <option value=""></option>
                                            <?php
                                                $i = 1;
                                                while ($i < 13){
                                                	if($i != date('m'))
                                                    	echo "<option value=\"$i\">".$allowance->getNumber2Month($i)."</option>";
                                                    else
                                                    	echo "<option selected=\"selected\" value=\"$i\">".$allowance->getNumber2Month($i)."</option>";	
                                                    ++$i;
                                                 }
                                            ?>
                            </select>
                            <select name="syear" style="width:75px">
                				<option value=""></option>
                                           <?php
                                                $i = 2010;
                                                while ($i < 2100){
                                                	if ($i == date('Y'))
                                                    	echo "<option selected=\"selected\" value=\"$i\">".$i."</option>";
                                                    else
                                                    	echo "<option value=\"$i\">".$i."</option>";
                                                    	
                                                    ++$i;
                                                 }
                                            ?>
                			</select>
                			
                </td>
            </tr>
            <tr>
            	<td height="20px" colspan="3"></td>
            </tr>                        
           
            	<td colspan="3" align="center"><br /><br /><input type="submit" name="submit" value="Get The Quarterwise Report" style="width:200px" />&nbsp;&nbsp;<input type="button" value="Return Back" onclick="window.location='./'" style="width:200px" /><br /><br /></td>
            </tr>
        </table>
        </form>
      </div>
      <div class="sidenav">
      	<hr size="2" />
        <center><font color="#FF0000" size="+1"><b><?php echo $allowance->getOfficerName(); ?></b></font></center>
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
