<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);

require_once '../include/class.personalInfo.php';
require_once '../include/class.employeeInfo.php';
require_once '../include/class.interest.php';
require_once '../include/class.department.php';

$department = new department();     
$interest = new interest();
$employeeInfo = new employeeInfo();
if(!$department->checkLogged())
        $department->redirect('../');
if(!$department->isAdmin())
        $department->palert("Only Administrator Has The Privilege To Process The Interest", "./");

if(isset ($_GET['interest04']) 
&& isset ($_GET['interest05']) 
&& isset ($_GET['interest06'])
&& isset ($_GET['interest07'])
&& isset ($_GET['interest08']) 
&& isset ($_GET['interest09']) 
&& isset ($_GET['interest10']) 
&& isset ($_GET['interest11']) 
&& isset ($_GET['interest12']) 
&& isset ($_GET['interest01'])
&& isset ($_GET['interest02'])
&& isset ($_GET['interest03']) 
&& isset ($_GET['value'])){
       $employeeId = $_GET['value'];
       
        $interestRate[0] = $_GET['interest04'];
		$interestRate[1] = $_GET['interest05'];
		$interestRate[2] = $_GET['interest06'];
		$interestRate[3] = $_GET['interest07'];
		$interestRate[4] = $_GET['interest08'];
		$interestRate[5] = $_GET['interest09'];
		$interestRate[6] = $_GET['interest10'];
		$interestRate[7] = $_GET['interest11'];
		$interestRate[8] = $_GET['interest12'];
		$interestRate[9] = $_GET['interest01'];
		$interestRate[10] = $_GET['interest02'];
		$interestRate[11] = $_GET['interest03'];
		
		if(!$interest->isLastYearNPSProcessed()){
			$interest->palert("Please Process the last year fund first","./process_interest.php");
			
		}
       
    }else
        $department->redirect('./process_interest.php');


      //  $interest = new interest();
//$employeeInfo = new employeeInfo();

if (isset($_POST['submit']) && $_POST['submit'] == "Finalise NPS Account"){
	 $interestRate[0] = $_POST['interest04'];
		$interestRate[1] = $_POST['interest05'];
		$interestRate[2] = $_POST['interest06'];
		$interestRate[3] = $_POST['interest07'];
		$interestRate[4] = $_POST['interest08'];
		$interestRate[5] = $_POST['interest09'];
		$interestRate[6] = $_POST['interest10'];
		$interestRate[7] = $_POST['interest11'];
		$interestRate[8] = $_POST['interest12'];
		$interestRate[9] = $_POST['interest01'];
		$interestRate[10] = $_POST['interest02'];
		$interestRate[11] = $_POST['interest03'];
        
	$employeeId = $_POST['employeeId'];
	
	$interest->finaliseEmployeeNPSAccount($employeeId, $interestRate, true);
	$interest->palert("The NPS Account Has Been Finalised", "./");
}
$personalInfo = new personalInfo();


ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- NPS Interest Payment</title>
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
      	<form action="" method="post">
        <table align="center" border="0" width="100%">
         	 <tr>
            	<th>SN</th>
                <th>Emp Code</th>
                <th align="left">Name</th>
                <th align="right">NPS Amt</th>
                <th align="right">Interest</th>
            </tr>          
            <tr>
            	<th colspan="5"><hr size="3" /></th>
            </tr>
            <?php
            	$personalInfo->getEmployeeInformation($employeeId, true);
            	
				$amount = $interest->finaliseEmployeeNPSAccount($employeeId, $interestRate, false); 
            	echo "
            		<tr>
		            	<td align=\"center\">
		            		<input type=\"hidden\" name=\"employeeId\" value=\"$employeeId\" />1</td>
		                <td align=\"center\">".$personalInfo->getEmployeeCode()."</td>
		                <td align=\"left\">".$personalInfo->getName()."</td>
		                <td align=\"right\">".$interest->npsTotalBalance($employeeId, $interest->getCurrentMonth())."</td>
		                <td align=\"right\">".number_format($amount, 2, '.', '')."</td>
		            </tr>";            	
            	
            	
            	
            ?>
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
            	<td colspan="5" align="center">            		
            		<input type="hidden" name="interest04" value="<?php echo $interestRate[0]; ?>"/>
            		<input type="hidden" name="interest05" value="<?php echo $interestRate[1]; ?>"/>
            		<input type="hidden" name="interest06" value="<?php echo $interestRate[2]; ?>"/>
            		<input type="hidden" name="interest07" value="<?php echo $interestRate[3]; ?>"/>
            		<input type="hidden" name="interest08" value="<?php echo $interestRate[4]; ?>"/>
            		<input type="hidden" name="interest09" value="<?php echo $interestRate[5]; ?>"/>
            		<input type="hidden" name="interest10" value="<?php echo $interestRate[6]; ?>"/>
            		<input type="hidden" name="interest11" value="<?php echo $interestRate[7]; ?>"/>
            		<input type="hidden" name="interest12" value="<?php echo $interestRate[8]; ?>"/>
            		<input type="hidden" name="interest01" value="<?php echo $interestRate[9]; ?>"/>
            		<input type="hidden" name="interest02" value="<?php echo $interestRate[10]; ?>"/>
            		<input type="hidden" name="interest03" value="<?php echo $interestRate[11]; ?>"/>
            	
            		<input type="submit" name="submit" value="Finalise NPS Account" style="width:250px" />
            		<input type="button" value="Return back" onclick="window.location='./process_nps.php'" style="width:200px" /></td>
            </tr>
        </table>    
        </form>
      </div>
      
      <div class="clearer"><span></span></div>
    </div>
    <div class="footer">@webteam.<a href="http://www.mnnit.ac.in" title="MNNIT">mnnit</a> Designed And Developed By Hemant Kumar Sah (B.Tech ECE 2011)</div>
  </div>
</div>
</body>
</html>