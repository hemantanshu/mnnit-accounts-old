<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
    //error_reporting(0);

    session_start();

    include_once '../include/class.reporting.php';
    include_once '../include/class.personalInfo.php';
    include_once '../include/class.employeeInfo.php';
    include_once '../include/class.department.php';
    include_once '../include/class.designation.php';
    include_once '../include/class.accounthead.php';

    $personalInfo = new personalInfo();
    $employeeInfo = new employeeInfo();
    $department = new department();
    $designation = new designation();
    $accounts = new reporting();
    $accountHead = new accountHead();

    if(!$accounts->checkLogged())
        $accounts->redirect('../');

    if(isset ($_GET['date']) && isset ($_GET['type']) && isset ($_GET['value'])){
        $processingType = $_GET['type'];
        $processingValue = $_GET['value'];
        $date = $_GET['date'];
    }else
        $accounts->redirect('./salary_slip.php');


    $employeeId = array();
    $variable = $employeeInfo->getEmployeeIds(true, 'REPORT');

    if($processingType == "all"){
        foreach ($variable as $value) {
                array_push($employeeId, $value);
        }
    }elseif($processingType == "employeeType"){
        foreach ($variable as $value) {
            $personalInfo->getEmployeeInformation($value, true);
            if($personalInfo->getEmployeeType() == $processingValue)
                    array_push($employeeId, $value);
        }
    }elseif($processingType == "designation"){
        foreach ($variable as $value){
            $rankId = $employeeInfo->getEmployeeRankIds($employeeId, true);
            foreach ($rankId as $options) {
                $details = $employeeInfo->getRankDetails($options, true);
                if($details[2] == $processingValue)
                        array_push($employeeId, $value);
            }
        }
    }elseif($processingType == "department"){
        foreach ($variable as $value){
            $personalInfo->getEmployeeInformation($value, true);
            if($personalInfo->getDepartment() == $processingValue)
                        array_push($employeeId, $value);
        }
    }elseif($processingType == "individual"){
            array_push($employeeId, $processingValue);
    }else{
        $accounts->palert("No Information Is There For The Salary Slip", './');
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
      	<form action="./salary_slipprint.php" method="post">
        <table border="1" align="center" width="100%">
            <tr>
                <td align="center" colspan="2"><font class="error">Motilal Nehru National Institute Of Technology, Allahabad<br />Pay Slip For The Month Of :<?php echo substr($date,4,2);?>  Year :<?php echo substr($date,0,4);?></font><br /><hr size="2" /><br /></td>
            </tr>
            <?php
            $count = 0;
            foreach ($employeeId as $employeeIds) {
                $checkBoxName = "checkbox".$count;
                $employeeIdName = "employeeId".$count;
                ++$count;
                $personalInfo->getEmployeeInformation($employeeIds, true);
                echo "
                    <tr>
                        <td width=\"98%\">
                            <table align=\"center\" border=\"0\" width=\"100%\">                                
                                <tr>
                                    <td align=\"right\" width=\"20%\">Employee Name :</td>
                                    <td width=\"2%\"></td>
                                    <td align=\"left\" width=\"26%\"><font class=\"green\">".$personalInfo->getName()."</font></td>
                                    <td width=\"4%\"></td>
                                    <td align=\"right\" width=\"20%\">Employee Code :</td>
                                    <td width=\"2%\"></td>
                                    <td align=\"left\" width=\"26%\"><font class=\"green\">".$personalInfo->getEmployeeCode()."</font></td>
                                </tr>
                                <tr>
                                    <td height=\"5px\"></td>
                                </tr>
                                <tr>
                                    <td align=\"right\">Department Name :</td>
                                    <td></td>
                                    <td align=\"left\"><font class=\"green\">".$department->getDepartmentName($personalInfo->getDepartment())."</font></td>
                                    <td></td>
                                    <td align=\"right\">Designations :</td>
                                    <td></td>
                                    <td align=\"left\"><font class=\"green\">";
                                                                        $designationId = $employeeInfo->getEmployeeDesignationIds($employeeId, true);
                                                                        foreach ($designationId as $value)
                                                                            echo $designation->getDesignationTypeName($value, true)."<br />";

                          echo "    </font></td>
                                </tr>
                                <tr>
                                    <td height=\"10px\" colspan=\"7\"><hr size=\"2\" /></td>
                                </tr>
                                <tr>
                                    <td colspan=\"7\" align=\"center\"><font class=\"error\">Allowances Info : Earnings </font></td>
                                </tr>
                                <tr>
                                    <td height=\"5px\"></td>
                                </tr>";
                                    $accounts->getProcessedSalaryInformation($employeeIds, $date, $date);
                                    $i = 0;
                                    $earning = 0;
                                    $deduction = 0;
                                    foreach ($accounts->totalEarnings as $key => $value) {
                                    	if($i % 2 == 0)
                                            echo "<tr>
                                                    <td align=\"right\">".$accountHead->getReservedAccountHeadName($key, $date)." :</td>
                                                    <td></td>
                                                    <td align=\"left\">".$value."</td>
                                                    <td></td>";
                                        else
                                            echo "
                                                <td align=\"right\">".$accountHead->getReservedAccountHeadName($key, $date)." :</td>
                                                <td></td>
                                                <td align=\"left\">".$value."</td>
                                            </tr>
                                            <tr>
                                                <td height=\"5px\"></td>
                                            </tr>
                                            ";
                                        $earning += $value;    
                                        ++$i;
                                    }
                                    if($i % 2 == 0)
                                        echo "
                                            </tr>
                                            <tr>
                                                <td height=\"5px\" colspan=\"7\"><hr size=\"1\" /></td>
                                            </tr>";

                         echo "<tr>
                                    <td height=\"10px\" colspan=\"7\" align=\"center\"><font class=\"green\">Total Earnings :</font><font class=\"error\">".$earning."</font> </td>
                                </tr>
                                <tr>
                                    <td height=\"20px\" colspan=\"7\"><hr size=\"2\" /></td>
                                </tr>
                                <tr>
                                    <td colspan=\"7\" align=\"center\"><font class=\"error\">Allowances Info : Deductions </font></td>
                                </tr>
                                <tr>
                                    <td height=\"5px\"></td>
                                </tr>";
                                    $i = 0;
                                    foreach ($accounts->totalDeductions as $key => $value) {
                                        if($i % 2 == 0)
                                            echo "<tr>
                                                    <td align=\"right\">".$accountHead->getReservedAccountHeadName($key, $date)." :</td>
                                                    <td></td>
                                                    <td align=\"left\">".$value."</td>
                                                <td></td>";
                                        else
                                            echo "
                                                <td align=\"right\">".$accountHead->getReservedAccountHeadName($key, $date)." :</td>
                                                <td></td>
                                                <td align=\"left\">".$value."</td>
                                            </tr>
                                            <tr>
                                                <td height=\"5px\"></td>
                                            </tr>
                                            ";
                                        $deduction += $value;
                                        ++$i;
                                    }
                                    if($i % 2 == 0)
                                        echo "
                                            </tr>";                        
                          echo "<tr>
                                    <td height=\"10px\" colspan=\"7\" align=\"center\"><font class=\"green\">Total Deductions :</font><font class=\"error\">".$deduction."</font> </td>
                                </tr>
                                <tr>
                                    <td height=\"5px\" colspan=\"7\"><hr size=\"1\" /></td>
                                </tr>";                          
                          echo "<tr>
                                    <td height=\"10px\" colspan=\"7\" align=\"center\"><font class=\"green\">Net Pay :</font><font class=\"error\">Rs. ".$value = ($earning - $deduction)."</font><br /><font class=\"green\">Rs. ".strtoupper($accounts->nameAmount($earning - $deduction))." ONLY/- </font></td>
                                </tr>
                                <tr>
                                    <td height=\"5px\" colspan=\"7\"><hr size=\"1\" /></td>
                                </tr>";
                          echo "<tr>
                                    <td colspan=\"7\" align=\"center\"><font class=\"error\">Fiscal Year Summary</font></td>
                                </tr>
                                <tr>
                                    <td height=\"5px\" colspan=\"7\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td height=\"5px\" colspan=\"7\"><font class=\"green\">Earnings Summary</font></td>
                                </tr>";
                                    $i = 0;
                                    $accounts->getProcessedSalaryInformation($employeeIds, $date);
                                    $earning = 0;
                                    $deduction = 0;
                                    foreach ($accounts->totalEarnings as $key => $value) {
                                        if($i % 2 == 0)
                                            echo "<tr>
                                                    <td align=\"right\">".$accountHead->getReservedAccountHeadName($key, $date)." :</td>
                                                    <td></td>
                                                    <td align=\"left\">".$value."</td>
                                                <td></td>";
                                        else
                                            echo "
                                                <td align=\"right\">".$accountHead->getReservedAccountHeadName($key, $date)." :</td>
                                                <td></td>
                                                <td align=\"left\">".$value."</td>
                                            </tr>
                                            <tr>
                                                <td height=\"5px\"></td>
                                            </tr>
                                            ";
                                        $earning += $value;    
                                        ++$i;
                                    }
                                    if($i % 2 == 0)
                                        echo "
                                            </tr>";
                          
                          echo "
                                <tr>
                                    <td height=\"5px\" colspan=\"7\" align=\"center\"><font class=\"error\">TOTAL EARNINGS : ".$earning."</font><br /><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td height=\"5px\"></td>
                                </tr><tr>
                                    <td height=\"5px\" colspan=\"7\"><hr size=\"1\" /></td>
                                </tr>
                                <tr>
                                    <td height=\"5px\" colspan=\"7\"><font class=\"green\">Deduction Summary</font></td>
                                </tr>";
                                    $i = 0;                                    
                                    foreach ($accounts->totalDeductions as $key => $value) {
                                        if($i % 2 == 0)
                                            echo "<tr>
                                                    <td align=\"right\">".$accountHead->getReservedAccountHeadName($key, $date)." :</td>
                                                    <td></td>
                                                    <td align=\"left\">".$value."</td>
                                                <td></td>";
                                        else
                                            echo "
                                                <td align=\"right\">".$accountHead->getReservedAccountHeadName($key, $date)." :</td>
                                                <td></td>
                                                <td align=\"left\">".$value."</td>
                                            </tr>
                                            <tr>
                                                <td height=\"5px\"></td>
                                            </tr>
                                            ";
                                        $deduction += $value;    
                                        ++$i;
                                    }
                                    if($i % 2 == 0)
                                        echo "
                                            </tr>";
                          
                          echo "
                                <tr>
                                    <td height=\"5px\" colspan=\"7\" align=\"center\"><font class=\"error\">TOTAL DEDUCTIONS : ".$deduction."</font><br /><hr size=\"1\" /></td>
                                </tr>                                
                                <tr>
                                    <td height=\"5px\" colspan=\"1\" align=\"center\">Remarks : </td><td colspan=\"6\" align=\"left\"><font class=\"green\">";
                                                            echo      "</font></td>
                                </tr>
                                <tr>
                                    <td height=\"5px\" colspan=\"7\"><hr size=\"1\" /></td>
                                </tr>
                                
                            </table>
                        </td>
                        <td align=\"center\">
                                            <input type=\"checkbox\" checked=\"checked\" name=\"".$checkBoxName."\" value=\"1\" />
                                            <input type=\"hidden\" name=\"".$employeeIdName."\" value=\"".$employeeIds."\" />
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor=\"#FF0000\" colspan=\"2\" height=\"5px\"><hr size=\"2\" /></td>
                    </tr>
                    ";
            }
            ?>
            <tr>
            	<td colspan="2" align="center"><br />
                    <input type="hidden" name="date" value="<?php echo $date; ?>" />
                    <input type="submit" name="submit" value="Print Salary Slip" /><br />
                </td>
            </tr>
        </table>
        </form>
      </div>
      <div class="sidenav">
      	<hr size="2" />
       <center> <font color="#FF0000" size="+1"><b><?php echo $accounts->getOfficerName(); ?></b></font></center>
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