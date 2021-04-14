<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
   	//error_reporting(0);

    session_start();

    require_once '../include/class.accountInfo.php';
    require_once '../include/class.allowance.php';
    require_once '../include/class.employeeInfo.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.department.php';
    require_once '../include/class.designation.php';

    $accounts = new accounts();

    if(!$accounts->checkLogged())
        $accounts->redirect('../');

    $allowance = new allowance();
    $employeeInfo = new employeeInfo();
    $personalInfo = new personalInfo();
    $department = new department();
    $designation = new designation();

    $variable = $department->getDepartmentIds(true);
    if(isset ($_GET['id']) && isset ($_GET['date'])){
        $date = $_GET['date'];
        $type = $_GET['id'];
    }else
        $accounts->redirect('./');

    $departmentId = array();
   if($type == "all"){
        foreach ($variable as $value)
            array_push($departmentId, $value);
    }else{
        foreach ($variable as $value) {
            if($value == $type)
                array_push($departmentId, $value);
        }
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
      	<form action="" method="post">
        <table align="center" border="0" width="100%">
			<tr>
            	<td align="center">
            		<font class="green"><font class="error">Motilal Nehru National Institute Of Technology, Allahabad<br />Pay Slip For The Month Of :<?php echo substr($date,4,2);?>  Year :<?php echo substr($date,0,4);?></font>
            		</font><br /><hr size="2" /><br /></td>
            </tr>
            <tr>
            	<td width="100%">
                	<table align="center" border="1" width="100%">
                    	<tr>
                            <th width="2%" align="center">SN</th>
                            <th align="left" width="15%">Personal Details</th>
                            <th align="left" width="30%">Earnings</th>
                            <th align="left" width="30%">Deductions</th>
                            <th align="left" width="8%">Gross Pay</th>
                            <th align="left" width="8%">Net Deduc.</th>
                            <th align="left" width="7%">Net Pay</th>
                            <th align="center" width="0%"></th>
                        </tr>
                        <?php
                            $i = 0;
                            $earningTotal = 0;
                            $deductionTotal = 0;
                            $sumTotal = 0;

                            foreach ($departmentId as $value) {
                            echo "
                                <tr>
                                    <td colspan=\"8\" align=\"center\"><font class=\"display\"><br />Department Name : ".$department->getDepartmentName($value)."<br /></font></td>
                                </tr>
                                ";
                             $variable = $employeeInfo->getEmployeeIds(true);
                             foreach ($variable as $employeeId){
                                 $personalInfo->getEmployeeInformation($employeeId, true);
                                 if($personalInfo->getDepartment() == $value){
                                    ++$i;
                                    $salaryId = $accounts->getSalaryReceiptIds($employeeId, $date);
                                    $earningId = array();
                                    $deductionId = array();
                                    $earning = 0;
                                    $deduction = 0;
                                    $sum = 0;
                                    foreach ($salaryId as $options){
                                        $details = $accounts->getSalaryIdDetails($options);
                                        if($details[5] == 'c')
                                            array_push($earningId, $options);
                                        else
                                            array_push($deductionId, $options);
                                    }
                                    echo "
                                        <tr>
                                            <td align=\"center\"><font class=\"error\">".$i."</font></td>
                                            <td align=\"left\"><font class=\"green\">".$personalInfo->getName()."<br />".$personalInfo->getEmployeeCode()."<br />";
                                            $designationId = $employeeInfo->getEmployeeDesignationIds($employeeId, true);
                                            foreach ($designationId as $options)
                                                echo $designation->getDesignationTypeName($options, true)."<br />";
                                    echo "</font></td>";
                                            
                                    echo    "
                                            <td align=\"left\">
                                                <table border=\"0\" width=\"100%\">";
                                            foreach ($earningId as $options) {
                                                $details = $accounts->getSalaryIdDetails($options);
                                                echo "
                                                    <tr>
                                                        <td align=\"right\" width=\"75%\"><font class=\"green\">".$allowance->getAllowanceTypeName($details[3])."</font></td>
                                                        <td align=\"center\" width=\"2%\">:</td>
                                                        <td align=\"left\" width=\"23%\"><font class=\"error\">".$details[4]."</font></td>
                                                    </tr>";
                                                $earning += $details[4];
                                            }
                                    echo "                                              </font>
                                                </table>
                                            </td>
                                            <td align=\"left\">
                                                <table border=\"0\" width=\"100%\">";
                                            foreach ($deductionId as $options) {
                                                $details = $accounts->getSalaryIdDetails($options);
                                                echo "
                                                    <tr>
                                                        <td align=\"right\" width=\"75%\"><font class=\"green\">".$allowance->getAllowanceTypeName($details[3])."</font></td>
                                                        <td align=\"center\" width=\"2%\">:</td>
                                                        <td align=\"left\" width=\"23%\"><font class=\"error\">".$details[4]."</font></td>
                                                    </tr>";
                                                $deduction += $details[4];
                                            }
                                    echo "      </table>
                                            </td>
                                            <td align=\"left\"><font class=\"green\">".$earning."</font></td>
                                            <td align=\"left\"><font class=\"green\">".$deduction."</font></td>
                                            <td align=\"left\"><font class=\"error\">";
                                                 $sum = $earning - $deduction;
                                                 $sumTotal += $sum;
                                                 $earningTotal += $earning;
                                                 $deductionTotal += $deduction;
                                                 echo $sum;
                                    echo "                                              </font></td>
                                            <td align=\"left\"><font class=\"green\"></font></td>
                                        </tr>";

                                 }
                             }
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