<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
    ////error_reporting(0)

    session_start();
    require_once '../include/class.directSalaryAddition.php';
    require_once '../include/class.allowance.php';

    $salaryAddition = new directSalaryAddition();
    $allowance = new allowance();
    
    if(!$salaryAddition->checkLogged())
        $salaryAddition->redirect('../');
        
    if(isset($_POST) && $_POST['submit'] == "Process The Selected Options Of Additional Salary"){
    	$i = 0;
    	$month = $_POST['month'];
    	if($month < $salaryAddition->getCurrentMonth())
    		$salaryAddition->redirect('./');
    	while (true){
    		$checkbox = "checkbox".$i;
            $salaryIdName = "salaryId".$i;
            ++$i;
            if(!isset($_POST[$salaryIdName]))
            	break;
            if($_POST[$checkbox] == 1)
            	$salaryAddition->dropAdditionalSalary($_POST[$salaryIdName]);
    	}
    	if($salaryAddition->isAdmin())
    		$salaryAddition->palert("The Additional Salary Component Has Been Successfully Dropped", "./salary_extrav.php?month=".$month);
    	else 
    		$salaryAddition->palert("The Additional Salary Component Will Be Dropped Once Confirmed By The Admin", "./salary_extrav.php?month=".$month);
    }
        
    if(!isset($_GET['month']))
    	$salaryAddition->redirect('./');
    $month = $_GET['month'];
    
    
    require_once '../include/class.personalInfo.php';
    $personalInfo = new personalInfo();
    
    $completeAdditionalSalaryIds = $salaryAddition->getAdditionalSalaryId($month);
    if(!$completeAdditionalSalaryIds || $month < $salaryAddition->getCurrentMonth())
    	$salaryAddition->palert("There Are No Entries For The Given Month. Please Try Some Other Month", "./salary_extra.php");

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
      	<form action="" method="post">
        <table align="center" border="0" width="100%">
        	<tr>
            	<td align="center" colspan="8">Direct Salary Addition For Month Of <font class="green"><?php echo $salaryAddition->getNumber2Month(substr($month, 4, 2)).", ".substr($month, 0, 4); ?></font></td>
            </tr>
            <tr>
            	<td colspan="8"><hr size="3" /></td>
            </tr>
            <tr>
            	<td height="5px"></td>
            </tr>
            <tr>
            	<td colspan="8" align="center"><a href="./salary_extrav.php?month=<?php echo $month; ?>" target="_parent">View The Summary</a>&nbsp;&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;&nbsp;<a href="./salary_extrae.php?month=<?php echo $month; ?>" target="_parent">Edit The Details</a>&nbsp;&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="./allowance_direct.php" target="_parent">Search For Another Month</a></td>
            </tr>
            <tr>
            	<td height="5px"></td>
            </tr>
            <tr>
            	<td colspan="8"><hr size="3" /></td>
            </tr>
            <tr>
            	<td height="5px"></td>
            </tr>
            <tr>
            	<th align="center" width="5%">S.N.</th>
                <th align="left" width="10%">Emp. Code</th>
                <th align="left" width="25%">Name</th>
                <th align="left" width="30%">Account Head</th>
                <th align="left" width="10%">Amount</th>
                <th align="left" width="10%">Type</th>
                <th align="center" width="10%">Drop</th>   
                <th align="center" width="*">Ignore</th>                                
            </tr>
            <tr>
            	<td height="5px"></td>
            </tr>
            <tr>
            	<td colspan="8"><hr size="2" /></td>
            </tr>
            <?php 
            	$count = 0;
            	foreach ($completeAdditionalSalaryIds as $individualAdditionalSalaryId) {
            		$details = $salaryAddition->getAdditionalSalaryIdDetails($individualAdditionalSalaryId);
            		$personalInfo->getEmployeeInformation($details[1], true);
            		
            		$checkbox = "checkbox".$count;
            		$salaryIdName = "salaryId".$count;
            		++$count;
            		
            		echo "
						<tr>
							<td align=\"center\">".$count."</td>
							<td align=\"left\">".$personalInfo->getEmployeeCode()."</td>
							<td align=\"left\">".$personalInfo->getName()."</td>
							<td align=\"left\">".$allowance->getAllowanceTypeName($details[2])."</td>
							<td align=\"left\">Rs. ".$details[3]."</td>";
            		if($details[4] == 'c')
            			echo "
							<td align=\"left\"><font class=\"green\">Credit</font></td>";
            		else
            			echo "
							<td align=\"left\"><font class=\"error\">Debit</font></td>";
            		echo "
							<td align=\"center\"><input type=\"radio\" name=\"$checkbox\" value=\"1\" /></td>
				            <td align=\"center\">
									<input type=\"radio\" checked=\"checked\" name=\"$checkbox\" value=\"0\" checked=\"checked\" />
									<input type=\"hidden\" name=\"".$salaryIdName."\" value=\"".$individualAdditionalSalaryId."\" /></td>
						</tr>
						<tr>
							<td colspan=\"8\"><br /><hr size=\"1\" /><br /></td>
						</tr>";
            	}
            ?>
      	      <tr>
              	<td colspan="8" align="center">
                	<input type="hidden" name="month" value="<?php echo $month; ?>" />
                	<input type="submit" name="submit" value="Process The Selected Options Of Additional Salary" style="width:400px" />
                    <input type="button" onclick="window.location='./salary_extra.php'" value="Return Back"/></td>
              </tr>	
        </table>
        </form>
      </div>
      <div class="sidenav">
      	<hr size="2" /><center>
        <font color="#FF0000" size="+1"><b><?php echo $salaryAddition->getOfficerName(); ?></b></font></center>
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