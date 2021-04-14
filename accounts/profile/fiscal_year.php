<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();

    session_start();

    require_once '../include/class.loginInfo.php';
    $loggedInfo = new loginInfo();

    if(!$loggedInfo->checkLogged())
        $loggedInfo->redirect('../');
    if (isset($_POST['submit']) && $_POST['submit'] == "Create New Financial Year"){
    	$errorLog = array();
    	if ($_POST['name'] == "" || $_POST['day'] == "" || $_POST['month'] == "" || $_POST['year'] == "" ){
    		array_push($errorLog, "Please Fill Up The Details Correctly"); 
    	}else{
    		$date = $_POST['year'].(strlen($_POST['month']) > 1 ? $_POST['month'] : "0".$_POST['month']).(strlen($_POST['day']) > 1 ? $_POST['day'] : "0".$_POST['day']);
    		$name = $_POST['name'];
    		
    		$loggedInfo->setNewFinancialYear($name, $date);
    		$loggedInfo->palert("New Financial Year Was Created", './');    	
    	}
    }

    ob_end_flush();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- Financial Year Information</title>
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
      <div class="left"><img class="imgright" src="../img/logo.gif" alt="Forest Thistle" height="105px">&nbsp;Accounts AccountsHead</div>
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
        	<!-- insertion of new accountHeads will be done here -->
            <tr>
            	<td height="10px" align="center"><h1 class="error">Financial Year Information</h1></td>
            </tr>
            <tr>
            	<td height="10px"><hr size="1" /></td>
            </tr>
            <tr>
            	<td align="center" width="100%">
                	<table border="0" align="center" width="100%">
                    	<tr>
                        	<td align="right">Name Of New Financial Year</td>
                            <td align="center" width="5%">:</td>
                            <td align="left"><input type="text" name="name" style="width:300px" /></td>
                        </tr>
                        <tr>
                        	<td height="5px"></td>
                        </tr>
                        <tr>
                        	<td align="right">Start Date</td>
                            <td align="center">:</td>
                            <td align="left">
                            	<select name="day" style="width:100px">
                                	<option value="">--DD--</option>
                            		<?php 
                            			for ($i = 1; $i < 32; ++$i)
                            				echo "<option value=\"$i\">$i</option>";
                            		?>
                                	
                                </select> &nbsp;&nbsp;&nbsp;
                                <select name="month" style="width:100px">
                                	<option value="">--MM--</option>
                                	<?php 
                                		for ($i = 1; $i < 13; ++$i){                                	 
                                				echo "<option value=\"$i\">$i</option>";
                                		}
                                	?>                               
                                </select>&nbsp;&nbsp;&nbsp;
                                <select name="year" style="width:100px">
                                	<option value="">--YY--</option>
                                	<?php 
                                		for ($i = (date('Y') - 1); $i < (date('Y') + 5); ++$i){
                                				echo "<option value=\"$i\">$i</option>";
                                		}
                                	?>
                                </select>
                                </td>
                        </tr>
                        <tr>
                        	<td height="5px"></td>
                        </tr>
                        <tr>
                        	<th colspan="3">Note : The start date of the new financial year will be the last date of the previous financial year</th>
                        </tr>
                        <tr>
                        	<td height="10px"></td>
                        </tr>
                        <tr>
                        	<td colspan="3" align="center"><input type="submit" name="submit" value="Create New Financial Year" style="width:200px" /></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
            	<td align="center" valign="top">
                	<table border="1" align="center" width="100%">
                    	<tr>
                        	<th>SN</th>	
                            <th>Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                        </tr>
						<tr>
                        	<th colspan="4">Current Financial Year </th>
                        </tr>
                        <?php 
                        	$i = 1;
                        	$sessionIds = $loggedInfo->getSessionIds();
                        	foreach ($sessionIds as $value){
                        		$details = $loggedInfo->getSessionDetails($value);
                        		if ($i == 1){
                        			echo "
                        				<tr>
				                        	<th>$i</th>
				                        	<th>$details[1] <a href=\"./fiscal_yeare.php\">edit</a></th>
				                        	<th>$details[2]</th>
				                        	<th>$details[3]</th>
				                        </tr>                      					
				                        <tr>
				                        	<th colspan=\"4\">Previous Financial Year </th>
				                        </tr>";
                        		}else{
                        			echo "
                        				<tr>
				                        	<th>$i</th>
				                        	<th>$details[1]</th>
				                        	<th>$details[2]</th>
				                        	<th>$details[3]</th>
				                        </tr>";
                        		}
                        		++$i;                        				
                        	}
                        ?>                                     
                    </table>
                </td>
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