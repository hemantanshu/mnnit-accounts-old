<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    
	ob_start();
    ////error_reporting(0);
    session_start();

    require_once '../include/class.accountInfo.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.employeeInfo.php';
    require_once '../include/class.allowance.php';
    require_once '../include/class.employeePending.php';
    require_once '../include/class.directSalaryAddition.php';
    


    $ePending = new employeePending();
    $accounts = new accounts();
    $salaryAddition = new directSalaryAddition();
    if(!$accounts->checkLogged())
            $accounts->redirect('../');
                
    $personalInfo = new personalInfo();
    $employeeInfo = new employeeInfo();
    $allowance = new allowance();
    ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- allowance Information</title>
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
      	<form action="./process_msalaryf.php" method="post">
        <table align="center" border="0" width="100%">
            <tr>
            	<td align="center" colspan="7"><font class="green">Final Monthly Salary Processing Center</font><br /><hr size="3" /><br /><br /></td>
            </tr>
            <tr>
            	<th width="5%" rowspan="2">SN</th>
                <th width="10%" rowspan="2">Emp. Code</th>
                <th width="30%" rowspan="2">Name</th>
                <th width="50%" colspan ="3">Amount</th>
                <th width="5%" rowspan="2">Check</th>
            </tr>
            <tr>
                <th>Allowance Name</th>
                <th>Value</th>
                <th>Type</th>
            </tr>

            <tr>
            	<td colspan="7" height="20px"><hr size="3" /></td>
            </tr>

            <?php
            if(isset ($_POST) && $_POST['submit'] == "Process Salary Of These Employees"){
			    $i = 0;
			    $processingType = $_POST['type'];
			    $processingValue = $_POST['value'];
			
			    $countPost = 0;
			    $i = 0;
			    while(true){
			        $checkbox = "checkbox".$countPost;
			        $employeeName = "employeeId".$countPost;
			        $days = 'days'.$countPost;
			        $bankTransfer = "bank".$countPost;
			        ++$countPost;
			        if(!isset ($_POST[$employeeName]))
			            break;			
			        if($_POST[$checkbox] == 1){
			 			//*******************************************//
			 			$value = $_POST[$employeeName];
			 			
			 			$personalInfo->getEmployeeInformation($value, true);
	                    $salaryId = $employeeInfo->getMasterSalaryId($value, true);
	
	                    $extraSalaryId = $salaryAddition->getEmployeeAdditionalSalaryIds($value);
	                    
	                    if($extraSalaryId)
	                        $count = sizeof ($salaryId) + 2 + sizeof ($extraSalaryId);
	                    else
	                        $count = sizeof ($salaryId) + 2;                    
	
	                    $details = $employeeInfo->getSalaryIdDetails($salaryId, true);
	
	                    $employeeName = "employeeId".$i;
	                    $checkbox = "checkbox".$i;
	                    $monthDays = "days".$i;
	                    $bank = "bank".$i;
	
	                    ++$i;
	                    if($i % 2 == 0)
	                        $color = "#FFFFFF";
	                    else
	                        $color = "#d7cece";
	
	                    echo "
	                    	<input type=\"hidden\" name=\"".$employeeName."\" value=\"".$value."\" />
	                    	<input type=\"hidden\" name=\"".$monthDays."\" value=\"".$_POST[$days]."\" />
	                    	<input type=\"hidden\" name=\"".$bankTransfer."\" value=\"".$_POST[$bank]."\" />
	                    		
	                        <tr bgcolor=\"".$color."\">
	                            <td align=\"center\" rowspan = \"$count\"><font class=\"green\">".$i."</font></td>
	                            <td align=\"left\" style=\"padding-left:5px\" rowspan = \"$count\"><font class=\"green\">".$personalInfo->getEmployeeCode()."</font></td>
	                            <td align=\"left\" rowspan = \"";
	                            if($extraSalaryId)
	                                echo ($count - 1 - sizeof($extraSalaryId));
	                            else
	                                echo $count;
	                    echo "\"><font class=\"green\">".$personalInfo->getName()."<br />Salary For : ".$_POST[$days]." Days <br />";
	                    if($_POST[$bank] == "1")
	                    	echo "Bank Transfer";
	                    else 	
	                    	echo "Cheque Payment";
	                    echo "</font></td>
	                            <td align=\"left\"><font class=\"error\">Basic</font></td>
	                            <td align=\"left\"><font class=\"error\">".$accounts->getEmployeeBasicSalary($value)."</font></td>
	                            <td align=\"left\"><font class=\"error\">Credit</font></td>
	                            <td align=\"center\" rowspan = \"$count\"><input type=\"checkbox\" name=\"".$checkbox."\" value=\"1\" checked=\"checked\" /></td>
	                        </tr>";
	                    foreach ($salaryId as $options) {
	                        $details = $employeeInfo->getSalaryIdDetails($options, true);
	                        echo "
	                            <tr bgcolor=\"".$color."\">
	                                <td align=\"left\"><font class=\"error\">".$allowance->getAllowanceTypeName($details[4])."</font></td>
	                                <td align=\"left\"><font class=\"error\">".$details[5]."</font></td>";
	                        if($details[6] == 'c')
	                            $type = "Credit";
	                        else
	                            $type = "Debit";
	                        echo "
	                                <td align=\"left\"><font class=\"error\">".$type."</font></td>
	                            </tr>";
	                    }
	                    $status = true;
	                    if($extraSalaryId)
	                        foreach ($extraSalaryId as $options) {
	                            $details = $salaryAddition->getAdditionalSalaryIdDetails($options);
	                            echo "<tr bgcolor=\"".$color."\">";
	
	                            if($status){
	                                echo "
	                                    <td align=\"center\" rowspan = \"".(sizeof($extraSalaryId) + 1)."\"><font class=\"green\">Extra Salary Additions</font></td>";
	                                $status = false;
	                            }
	                            echo "
	                                    <td align=\"left\"><font class=\"error\">".$allowance->getAllowanceTypeName($details[2])."</font></td>
	                                    <td align=\"left\"><font class=\"error\">".$details[3]."</font></td>";
	                            if($details[4] == 'c')
	                                $type = "Credit";
	                            else
	                                $type = "Debit";
	                            echo "
	                                    <td align=\"left\"><font class=\"error\">".$type."</font></td>
	                                </tr>";
	                        }
	                    echo "
	
	                        <tr bgcolor=\"".$color."\">
	                            <td align=\"left\"><font class=\"green\">Total Salary</font></td>
	                            <td align=\"left\"><font class=\"green\">".$accounts->getTotalSalary($value)."</font></td>
	                            <td align=\"left\"><font class=\"green\">Credit</font></td>
	                        </tr>";
	                    
	                    echo "
	                        <tr>
	                            <td colspan=\"7\"><br /><hr size=\"1\" /><br /></td>
	                        </tr>";
				        	
			        	//********************************************//
			        }        
			    }			 
			}            	
            ?>	
            


            <tr>
            	<td colspan="5" align="center"><br /><hr size="1" /><br /><br /><input type="submit" name="submit" value="Confirm Processing Salary Of These Employees" /><input type="button" onclick="window.location='./salary_process.php'" value=" Return Back" /></td>
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