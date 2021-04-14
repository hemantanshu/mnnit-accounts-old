<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();

    session_start();

    require_once '../include/class.allowance.php';
    require_once '../include/class.personalInfo.php';

    $allowance = new allowance();
    $personalInfo = new personalInfo();

    if(!$allowance->checkLogged())
        $allowance->redirect('../');
    if(isset ($_GET)){
        $allowanceId = $_GET['id'];
        if(isset ($_GET['type'])){
            $type = $_GET['type'];
        }else{
            $type = "all";
        }
    }else
        $allowance->redirect ('./');

    ob_end_flush();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- allowance/deduction Information</title>
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
      <div class="contentLarge">
      	<form action="./allowance_export.php" method="post">
      	<table align="center" border="0" width="100%">
        	<!-- insertion of new departments will be done here -->
            <tr>
                <td height="10px" align="center">
                        <a href="./allowance_view1.php?id=<?php echo $allowanceId; ?>&type=all" target="_parent">Info </a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_view.php?id=<?php echo $allowanceId; ?>&type=all" target="_parent">Total View </a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_view.php?id=<?php echo $allowanceId; ?>&type=no" target="_parent">Non-Overridden </a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_view.php?id=<?php echo $allowanceId; ?>&type=yes" target="_parent">Over-ridden </a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_direct.php?id=<?php echo $allowanceId; ?>&type=yes" target="_parent">Edit Amount(Direct)</a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_direct1.php?id=<?php echo $allowanceId; ?>&type=yes" target="_parent">Edit Amount(Master-Update)</a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_clear.php?id=<?php echo $allowanceId; ?>" target="_parent">Clear Amount</a>
                </td>
            </tr>
            <tr>
            	<td height="10px"><hr size="1" /></td>
            </tr>
            <tr>
                <td height="10px" align="center"><font class="green">
                                                    <?php
                                                        if($type == 'all')
                                                            $display = 'COMPLETE SUMMARY';
                                                        else
                                                            $display = $type == 'yes' ? 'OVERRIDDEN' : 'NOT OVERRIDDEN';

                                                        echo strtoupper($allowance->getAllowanceTypeName($allowanceId))." :</font>";
                                                        echo "<font class=\"error\">&nbsp;&nbsp;&nbsp;&nbsp;".$display."</font>";
                                                    ?></font></td>
            </tr>
            <tr>
            	<td align="center" valign="top">
                    <table border="1" align="center" width="100%">
                    	<tr>
                            <th width="4%">SN</th>
                            <th width="10%">Emp. Code</th>
                            <th width="50%">Name</th>
                            <th width="15%">Amount</th>
                            <th width="15%">Type</th>
                        </tr>
                        <?php                            
                            if($allowanceId == "ACT1"){
                                if($type == 'all' || $type == 'yes'){
                                    require_once '../include/class.employeeInfo.php';
                                    require_once '../include/class.accountInfo.php';
                                    
                                    $employeeInfo = new employeeInfo();                                    
                                    $accounts = new accounts();
                                    
                                    $completeEmployeeIds = $employeeInfo->getEmployeeIds(true);                                    
                                    
                                    $i = 1;
                                    foreach ($completeEmployeeIds as $employeeId) {                                    
                                        $personalInfo->getEmployeeInformation($employeeId, true);                                        
                                        $amount = $accounts->getEmployeeBasicSalary($employeeId);
                                        
                                        echo "
                                            <tr>
                                                <td align=\"center\">".$i."</td>
                                                <td align=\"center\"><font class=\"green\">".$personalInfo->getEmployeeCode()."</font></td>
                                                <td align=\"left\"  style=\"padding-left:10px\"><font class=\"green\">".$personalInfo->getName()."</font></td>
                                                <th align=\"center\"><font class=\"error\">".number_format($amount, 2, '.', '')."</font></th>
                                                <td align=\"left\"><font class=\"green\">Credit</font></td>
                                            </tr>";
                                        ++$i;
                                    }
                                    
                                }else{
                                    echo "
                                        <tr>
                                           <td height=\"4px\" colspan=\"8\" align = \"center\"><font class = \"error\">No Records Availiable</font></td>
                                        </tr>";
                                }
                            }else{
                                $details = $allowance->getSalaryAllowanceEmployeeInfo($allowanceId, $type);

                                if($details){
                                    $i = 0;
                                    $j = 0;
                                    while(true){
                                        if(!sizeof($details[$i]))
                                            break;

                                        $personalInfo->getEmployeeInformation($details[$i][0], true);
                                        $j = $i + 1;
                                        echo "
                                            <tr>
                                                <td align=\"center\">".$j."</td>
                                                <td align=\"center\"><font class=\"green\">".$personalInfo->getEmployeeCode()."</font></td>
                                                <td align=\"left\"  style=\"padding-left:10px\"><font class=\"green\">".$personalInfo->getName()."</font></td>
                                                <td align=\"center\"><font class=\"error\">".$details[$i][1]."</font></td>
                                                <td align=\"left\"><font class=\"green\">".$details[$i][2]."</font></td>
                                            </tr>";
                                        ++$i;
                                    }
                               }else{
                                   echo "
                                        <tr>
                                           <td height=\"4px\" colspan=\"8\" align = \"center\"><font class = \"error\">No Records Availiable</font></td>
                                        </tr>";

                               }
                            }
                        ?>
						<tr>
                        	<td align="center" colspan="5"><input type="hidden" name="accountHead" value="<?php echo $allowanceId; ?>" /><input type="submit" value="Export Allowance To Excel" name="submit" /></td>
                        </tr>
                        <tr>
                            <td colspan="8" align="center"><div id="infoDiv"></div></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        </form>
      </div>

      <div class="clearer"><span></span></div>
    </div>
    <div class="footer">@webteam.<a href="http://www.mnnit.ac.in" title="MNNIT">mnnit</a> Designed And Developed By Hemant Kumar Sah (B.Tech ECE 2011) Kedar Panjiyar(CSE 2010-14)</div>
  </div>
</div>
</body>
</html>