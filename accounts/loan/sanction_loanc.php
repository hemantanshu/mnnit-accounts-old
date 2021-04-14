<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
	//error_reporting(0);
    session_start();
    
    require_once '../include/class.employeeInfo.php';
    require_once '../include/class.editloan.php';
    require_once '../include/class.personalInfo.php';
    
    $loan = new editLoan();    
    $employeeInfo = new employeeInfo();
    $personalInfo = new personalInfo();

    if(!$loan->checkLoanOfficerLogged())
        $loan->redirect('../');

    if(isset($_POST) && ($_POST['submit'] == "SANCTION NEW LOAN" ||  $_POST['submit'] == "CONFIRM SANCTION OF NEW LOAN")){
    	
    	$employeeId = $_POST['employee'];
    	$loanType = $_POST['loanType'];
    	
    	if(!$loan->checkLoanSanction($employeeId, $loanType))
    		$loan->palert("This employee has been already sanctioned the same loan", "./sanction_loan.php");
    		
    	if($_POST[''] == "employee" || $_POST['loanType'] == "" || $_POST['installment'] == "" || $_POST[''] == "amount" || $_POST['interest'] == "" )
    		$loan->palert("Please Fill Up The Form Details Nicely", "./sanction_loan.php");
    	
    	
    	if((!is_numeric($_POST['installment']) || !is_numeric($_POST['installmenti']) || !is_numeric($_POST['amount']) || !is_numeric($_POST['interest'])) && $_POST['submit'] == "SANCTION NEW LOAN"){
    		$loan->palert("Please Fill Up The Form Details Nicely", "./sanction_loan.php");
    	}
    		
    	if($_POST['submit'] == "CONFIRM SANCTION OF NEW LOAN"){
    		$loanId = $loan->sanctionLoan($employeeId, $loanType, $_POST['amount'],$_POST['installment'], $_POST['installmenti'], $_POST['interest']);
    		$loan->palert("New Loan Account Has Been Sanctioned", "./loan_account.php?id=".$loanId);  	
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
      	<form action="./sanction_loanc.php" method="post">
        <table align="center" border="0" width="100%">
      		<tr>
            	<td colspan="3" align="center"><h2>NEW LOAN SANCTION MODULE</h2><hr size="2" /><br /><br /></td>
            </tr>      
            <tr>
            	<td align="right">EMPLOYEE NAME </td>
                <td align="center" width="5%">:</td>
                <th align="left">
                	<?php 
                		$personalInfo->getEmployeeInformation($employeeId, true);
                		echo strtoupper($personalInfo->getName())." (".$personalInfo->getEmployeeCode()." )";
                	?></th>
            </tr>
            <tr>
            	<td height="20px"></td>
            </tr>
            <tr>
            	<td align="right">LOAN TYPE</td>
                <td align="center" width="5px">:</td>
                <th align="left">
                    <?php 
                    	$details = $loan->getLoanTypeIdDetails($loanType);
                    	echo strtoupper($details[2]);
                    ?>
                    
                </th>
            </tr>
            <tr>
            	<td height="20px"></td>
            </tr>
            <tr>
            	<td align="right">TOTAL LOAN INSTALLMENTS </td>
                <td align="center" width="5%">:</td>
                <th align="left">
                	<?php
                		echo $details[4] == 0 ? $_POST['installment'] : ($_POST['installment'] > $details[4] ? $details[4] : $_POST['installment']);                			
                	?>
                </th>
            </tr>
            <tr>
            	<td height="20px"></td>
            </tr>
            <tr>
            	<td align="right">TOTAL INTEREST INSTALLMENTS </td>
                <td align="center" width="5%">:</td>
                <th align="left">
                	<?php
                		echo $_POST['installmenti'];                			
                	?>
                </th>
            </tr>
            <tr>
            	<td height="20px"></td>
            </tr>
            <tr>
            	<td align="right">AMOUNT SANCITONED </td>
                <td align="center">:</td>
                <th align="left"><?php echo $details[3] == 0 ? $_POST['amount'] : ($_POST['amount'] > $details[3] ? $details[3] : $_POST['amount']); ?></th>
            </tr>
            <tr>
            	<td height="20px"></td>
            </tr>
            <tr>
            	<td align="right">INTEREST RATE APPLICABLE</td>
                <td align="center">:</td>
                <th align="left"><?php echo $_POST['interest']; ?></th>
            </tr>
            <tr>
            	<td height="30px"></td>
            </tr>
            <tr>
            	<td colspan="3" align="center">
            		<input type="hidden" name="employee" value="<?php echo $employeeId; ?>" />
            		<input type="hidden" name="loanType" value="<?php echo $loanType; ?>" />
            		<input type="hidden" name="amount" value="<?php echo $details[3] == 0 ? $_POST['amount'] : ($_POST['amount'] > $details[3] ? $details[3] : $_POST['amount']); ?>" />
            		<input type="hidden" name="installment" value="<?php echo $details[4] == 0 ? $_POST['installment'] : ($_POST['installment'] > $details[4] ? $details[4] : $_POST['installment']); ?>" />
            		<input type="hidden" name="installmenti" value="<?php echo $_POST['installmenti']; ?> " />
            		<input type="hidden" name="interest" value="<?php echo $_POST['interest']; ?> " />
            		<input type="submit" name="submit" value="CONFIRM SANCTION OF NEW LOAN" style="width:300px" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            		<input type="button" value="Return Back" style="width:200px" onclick="window.location='./'" /></td>
            </tr>
        </table>
        </form>
      </div>
      <div class="sidenav">
      	<hr size="2" /><center>
        <font color="#FF0000" size="+1"><b><?php echo $loan->getOfficerName(); ?></b></font>
       	<hr size="2" /></center><br />
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
