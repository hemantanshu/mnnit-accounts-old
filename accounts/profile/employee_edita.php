<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
    //////error_reporting(0)

    session_start();

    require_once '../include/class.employeeInfo.php';
    require_once '../include/class.editPersonalInfo.php';

    $editPersonalInfo = new editPersonalInfo();
    $employeeInfo = new employeeInfo();

    if(!$editPersonalInfo->checkLogged())
            $editPersonalInfo->redirect("../");
        
    if(isset ($_POST) && $_POST['submit'] == "Confirm Employee Account Details"){
        $i = 0;
        $error = 0;
        $errorLog = array();
        $employeeId = $_POST['employeeId'];

        if(isset ($_POST['allowance0'])){
            $salaryId = $employeeInfo->getMasterSalaryId($employeeId, false);
                if(sizeof($salaryId)){ //there is pending job on the list
                    $i = 0;
                    foreach ($salaryId as $value){
                        $salaryDetails = $employeeInfo->getSalaryIdDetails($value, false);
                        $postName = "allowance".$i;

                        if($_POST[$postName] > 0)
                            $type = 'c';
                        else
                            $type = 'd';
                        ++$i;

                        $details = array();
                        array_push($details, $salaryDetails[0]);
                        array_push($details, $salaryDetails[1]);
                        array_push($details, $salaryDetails[2]);
                        array_push($details, $salaryDetails[3]);
                        array_push($details, $salaryDetails[4]);
                        array_push($details, abs($_POST[$postName]));
                        array_push($details, $type);
                        array_push($details, $salaryDetails[7]);

                        $editPersonalInfo->updateMasterAccountDetails($employeeId, $details);
                    }
                    
                }
                if($editPersonalInfo->isAdmin())
                        $editPersonalInfo->palert("Details Updated Successfully", "./employee.php");
                else
                    $editPersonalInfo->palert("Details In Pending Status ", "./employee.php");
        }else{            
            while (true){
                $amount = "amount".$i;
                $accountSubhead = "allowanceid".$i;
                $checkbox = "checkbox".$i;
                ++$i;

                if(!isset ($_POST[$amount]))
                    break;

                if($_POST[$checkbox] == 1){
                    if($_POST[$amount] == ""){
                        $error++;
                        array_push($errorLog, "Error In Inputs --  Please Fill Again");
                    }
                }
            }
            if($error == 0){
                $i = 0;
                while (true){
                    $amount = "amount".$i;
                    $accountSubhead = "allowanceid".$i;
                    $checkbox = "checkbox".$i;
                    ++$i;
                    if(!isset ($_POST[$amount]))
                        break;
                    if($_POST[$checkbox] == 1){
                        $dependentId = $_POST[$accountSubhead];
                        if(array_key_exists($dependentId, $variable)){
                            $variable[$dependentId] += $_POST[$amount];
                        }else{
                            $variable[$dependentId] = $_POST[$amount];
                        }
                    }
                }
                if($editPersonalInfo->isAdmin())
                    $editPersonalInfo->deActivateSessionMasterSalary($employeeId);
                foreach ($variable as $key=>$value){
                    $details = array();
                    array_push($details, $key);
                    array_push($details, $value);

                    if($details[1] == 0)
                        continue;
                    $editPersonalInfo->setMasterAccountDetails($employeeId, $details);
                }
                unset ($_SESSION['pendingId']);
                if($editPersonalInfo->isAdmin())
                        $editPersonalInfo->palert("Details Updated Successfully", "./employee.php");
                else
                    $editPersonalInfo->palert("Details In Pending Status ", "./employee.php");
            }             
        }

    }elseif(isset ($_GET['id']))
        $employeeId = $_GET['id'];
    else
        $editPersonalInfo->redirect('./employee.php');

    require_once '../include/class.employeeType.php';
    require_once '../include/class.allowance.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.accountInfo.php';
    require_once '../include/class.designation.php';
    


    $personalInfo = new personalInfo();
    $employeeType = new employeeType();
    $allowance = new allowance();
    $accounts = new accounts();
    $designation = new designation();

    
    $personalInfo->getEmployeeInformation($employeeId, true);
    if($personalInfo->getName() == "")
            $editPersonalInfo->redirect('./employee.php');

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
        <input type="hidden" name="employeeId" value="<?php echo $employeeId; ?>" />
        <?php
            echo "
                <table align=\"center\" width=\"100%\" border=\"0\">
                    <tr>
                    <td align=\"center\">
                    		<a href=\"./employee_editp.php?id=".$employeeId."\" >Edit Personal Information</a> || 
                    		<a href=\"./employee_editd.php?id=".$employeeId."\" >Edit Designation Information</a> || 
                    		<a href=\"./employee_editb.php?id=".$employeeId."\" >Edit Accounts Information</a> || 
                    		<a href=\"./employee_edita.php?id=".$employeeId."\" >Edit Allowances Information</a></td>
                </tr>
            </table>";
        ?>
      	<table align="center" border="0" width="100%">
        	<tr>
            	<td colspan="5" align="center"><font class="error">Employee Account Information</font><br /><hr size="1" /></td>
            </tr>
            <?php
                if(isset ($error) && $error != 0 && is_array($errorLog)){
                    foreach ($errorLog as $value) {
                        echo "
                            <tr>
                                <td></td>
                                <td colspan=\"2\" align=\"left\"><font class=\"error\">".$value."</font></td>
                            </tr>";
                    }
                    echo "
                        <tr>
                            <td height = \"10px\"></td>
                        </tr>";
                }

            ?>
            <tr>
                <td width="15%" align="right"><font class="error">Name :</font></td>
                <td width="25%" align="left"><font class="green"><?php echo $personalInfo->getName();?></font></td>
                <td width="5%"></td>
                <td width="15%" align="right"><font class="error">Employee Id :</font></td>
                <td width="25%" align="left"><font class="green"><?php echo $personalInfo->getEmployeeCode();?></font></td>
            </tr>
            <tr>
                <td align="right"><font class="error">Employee Type :</font></td>
                <td align="left"><font class="green"><?php echo $employeeType->getEmployeeTypeName($personalInfo->getEmployeeType()); ?></font></td>
                <td></td>
                <td align="right"><font class="error">Basic Salary :</font></td>
                <td align="left"><font class="green">INR. <?php echo $accounts->getAccountSum($employeeId, "ACT1");?></font></td>
            </tr>
            <?php
                $salaryId = $employeeInfo->getMasterSalaryId($employeeId, false);
                if(sizeof($salaryId)){ //there is pending job on the list
                    echo "
                        <tr>
                            <td height=\"25px\"></td>
                        </tr>
                        <tr>
                            <th>SN</th>
                            <th colspan=\"2\">Allowance</th>
                            <th colspan=\"2\">Magnitude</th>
                        </tr>
                        <tr>
                            <td height=\"25px\"></td>
                        </tr>";
                    $i = 0;
                    foreach ($salaryId as $value) {
                        $salaryDetails = $employeeInfo->getSalaryIdDetails($value, false);
                        if(!$editPersonalInfo->isEditable($salaryDetails[1]))
                                $editPersonalInfo->palert("This Information Cannot Be Edited ---", "./employee_infoview.php?id=".$employeeId);
                        $allowanceId = "allowance".$i;
                        if($salaryDetails[6] == 'd')
                            $amount = 0-$salaryDetails[5];
                        else
                            $amount = $salaryDetails[5];

                        ++$i;
                        echo "                            
                            <tr>
                                <td align=\"center\"><font class=\"error\">".$i."</font></td>
                                <td colspan=\"2\" align=\"center\"><font class=\"green\">".$allowance->getAllowanceTypeName($salaryDetails[4])."</font></td>
                                <td colspan=\"2\" align=\"left\"><input type=\"text\" name=\"$allowanceId\" value=\"".$amount."\" style=\"width:200px\" /></td>
                            </tr>
                            <tr>
                                    <td height=\"10px\"></td>
                            </tr>";
                    }
                    echo "<tr>
                            <td height=\"25px\"></td>
                        </tr>";

                }else{
                    echo "
                        <tr>
                            <td colspan=\"5\" height=\"50px\" align=\"center\"><hr size=\"2\" /><br /><font class=\"error\">Please Check The Applicable Allowances </font><br /><hr size=\"2\" /></td>
                        </tr>
                        <tr>
                            <td colspan=\"5\" align=\"center\" width=\"100%\">
                                    <table border=\"0\" align=\"center\" width=\"100%\">
                                    <tr>
                                        <th width=\"5%\"><font class=\"error\">SN</font></th>
                                        <th width=\"50%\"><font class=\"error\">Allowance Name</font></th>
                                        <th width=\"20%\"><font class=\"error\">Amount</font></th>
                                        <th width=\"25%\"><font class=\"error\">Apply</font></th>
                                    </tr>
                                    <tr>
                                        <td colspan=\"4\" bgcolor=\"#FFFFFF\" height=\"3px\"></td>
                                    </tr>";
                                        $i = 0;
                                        $allowanceId = $allowance->getAllowanceIds(true);
                                        foreach ($allowanceId as $value){
                                            $amount = "amount".$i;
                                            $accountSubhead = "allowanceid".$i;
                                            $checkbox = "checkbox".$i;
                                            ++$i;
                                            if($accounts->getEmployeeSalaryInfo($employeeId, $value)){
                                                $amountValue = $accounts->getEmployeeSalaryInfo($employeeId, $value);
                                                if($amountValue > 0)
                                                    $color = '#00FF00';
                                                else
                                                    $color = '#FFCC66';


                                                echo "
                                                    <tr style=\"background-color:".$color."\">
                                                        <td align=\"center\"><font class=\"green\">".$i."</font></td>
                                                        <td align=\"center\"><font class=\"green\">".$allowance->getAllowanceTypeName($value)."</font></td>
                                                        <td align=\"left\">INR. <input type=\"text\" name=\"".$amount."\" value=\"".$accounts->getEmployeeSalaryInfo($employeeId, $value)."\" style=\"width:100px\" /></td>
                                                        <td align=\"left\"><font color = \"#CC0000\">INR.".$accounts->getAccountSum($employeeId, $value)."</font></td>
                                                        <td align=\"center\"><input type=\"hidden\" name=\"".$accountSubhead."\" value=\"".$value."\" />
                                                                                            <input type=\"checkbox\" checked=\"checked\" name=\"".$checkbox."\" value=\"1\" /></td>
                                                    </tr>";
                                            }else{
                                                echo "
                                                    <tr>
                                                        <td align=\"center\"><font class=\"green\">".$i."</font></td>
                                                        <td align=\"center\"><font class=\"green\">".$allowance->getAllowanceTypeName($value)."</font></td>
                                                        <td align=\"left\">INR. <input type=\"text\" onFocus=\"value=''\" name=\"".$amount."\" value=\"".$accounts->getAccountSum($employeeId, $value)."\" style=\"width:100px\" /></td>
                                                        <td align=\"left\">INR. ".$accounts->getAccountSum($employeeId, $value)."</td>
                                                        <td align=\"center\"><input type=\"hidden\" name=\"".$accountSubhead."\" value=\"".$value."\" />
                                                                                            <input type=\"checkbox\" name=\"".$checkbox."\" value=\"1\" /></td>
                                                    </tr>";
                                            }
                                            echo "                                                
                                                <tr>
                                                        <td colspan=\"4\" bgcolor=\"#FFFFFF\" height=\"3px\"></td>
                                                </tr>";
                                        }

                echo "           </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan=\"5\" height=\"50px\" align=\"center\"><hr size=\"2\" /><br /><font class=\"error\">Designation Benefits</font><br /><hr size=\"2\" /></td>
                        </tr>
                        <tr>
                            <td colspan=\"5\" align=\"center\" width=\"100%\">
                                    <table border=\"0\" align=\"center\" width=\"100%\">
                                    <tr>
                                        <th width=\"5%\"><font class=\"error\">SN</font></th>
                                        <th width=\"50%\"><font class=\"error\">Allowance Name</font></th>
                                        <th width=\"20%\"><font class=\"error\">Amount</font></th>
                                        <th width=\"20%\"><font class=\"error\">Calculated</font></th>
                                        <th width=\"25%\"><font class=\"error\">Apply</font></th>
                                    </tr>
                                    <tr>
                                        <td colspan=\"4\" bgcolor=\"#FFFFFF\" height=\"3px\"></td>
                                    </tr>";
                                        if($editPersonalInfo->isAdmin())
                                            $designationId = $employeeInfo->getEmployeeDesignationIds($employeeId, true);
                                        else
                                            $designationId = $employeeInfo->getEmployeeDesignationIds($employeeId, false);
                                        foreach ($designationId as $ids){
                                            echo "
                                                 <tr>
                                                    <td colspan=\"4\" align=\"center\"><font class=\"error\">Designation Name : ".$designation->getDesignationTypeName($ids, true)."</font><br /><br /></td>
                                                 </tr>
                                                ";
                                            $dependentId = $designation->getDesignationDependents($ids, true);

                                            foreach ($dependentId as $value){
                                                $dependentDetails = $designation->getDesignationDependentDetails($value, true);
                                                $amount = "amount".$i;
                                                $accountSubhead = "allowanceid".$i;
                                                $checkbox = "checkbox".$i;
                                                ++$i;

                                                if($accounts->getEmployeeSalaryInfo($employeeId, $value)){
                                                   echo "
                                                        <tr>
                                                            <td align=\"center\"><font class=\"green\">".$i."</font></td>
                                                            <td align=\"center\"><font class=\"green\">".$allowance->getAllowanceTypeName($dependentDetails[3])."</font></td>
                                                            <td align=\"left\">INR. <input type=\"text\" name=\"".$amount."\" value=\"".$accounts->getEmployeeSalaryInfo($employeeId, $value)."\" style=\"width:100px\" /></td>
                                                            <td align=\"left\">INR. ".$accounts->getRankBenefitTotal($employeeId, $value)."</td>
                                                            <td align=\"center\"><input type=\"hidden\" name=\"".$accountSubhead."\" value=\"".$dependentDetails[3]."\" />
                                                                                                <input type=\"checkbox\" checked=\"checked\" name=\"".$checkbox."\" value=\"1\" /></td>
                                                        </tr>";
                                                }else{
                                                    echo "
                                                        <tr>
                                                            <td align=\"center\"><font class=\"green\">".$i."</font></td>
                                                            <td align=\"center\"><font class=\"green\">".$allowance->getAllowanceTypeName($dependentDetails[3])."</font></td>
                                                            <td align=\"left\">INR. <input type=\"text\" name=\"".$amount."\" value=\"".$accounts->getRankBenefitTotal($employeeId, $value)."\" style=\"width:100px\" /></td>
                                                            <td align=\"left\">INR. ".$accounts->getRankBenefitTotal($employeeId, $value)."</td>
                                                            <td align=\"center\"><input type=\"hidden\" name=\"".$accountSubhead."\" value=\"".$dependentDetails[3]."\" />
                                                                                                <input type=\"checkbox\" name=\"".$checkbox."\" value=\"1\" /></td>
                                                        </tr>";
                                                }                                                
                                                echo "
                                                    <tr>
                                                            <td colspan=\"4\" bgcolor=\"#FFFFFF\" height=\"3px\"></td>
                                                    </tr>";
                                            }

                                        }
                    echo "
                                </table>
                            </td>
                        </tr>
                        ";
                }
            ?>
            
            <tr>
            	<td colspan="5" align="center"><input type="submit" name="submit" value="Confirm Employee Account Details" /></td>
            </tr>
        </table>
        </form>
      </div>
      <div class="sidenav">
      	<hr size="2" />
       <center> <font color="#FF0000" size="+1"><b><?php echo $editPersonalInfo->getOfficerName(); ?></b></font></center>
       	<hr size="2" /><br />
        <h2><font color="#008000">QUICK NAVIGATION PANEL</font></h2>
        <?php
            include './navigation/employee.php';
        ?>
      </div>
      <div class="clearer"><span></span></div>
    </div>
    <div class="footer">@webteam.<a href="http://www.mnnit.ac.in" title="MNNIT">mnnit</a> Designed And Developed By Hemant Kumar Sah (B.Tech ECE 2011)</div>
  </div>
</div>
</body>
</html>
