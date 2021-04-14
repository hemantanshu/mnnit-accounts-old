<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
	//error_reporting(0);
    session_start();
    
    require_once '../include/class.gpfEdit.php';
    require_once '../include/class.personalInfo.php';
    
    $loan = new gpfEdit();
    $personalInfo = new personalInfo();

    if(!$loan->checkLoanOfficerLogged())
        $loan->redirect('../');

    if(isset($_POST) && ($_POST['submit'] == "SANCTION NEW GPF LOAN" ||  $_POST['submit'] == "CONFIRM SANCTION OF NEW GPF LOAN")){
    	
    	$employeeId = $_POST['employee'];
    	    	
    	if($_POST[''] == "employee" || $_POST['installment'] == "" || $_POST['amount'] == "")
    		$loan->palert("Please Fill Up The Form Details Nicely", "./gpf_sanction.php");
    	
    	
    	if((!is_numeric($_POST['installment']) || !is_numeric($_POST['amount'])) && $_POST['submit'] == "SANCTION NEW GPF LOAN"){
    		$loan->palert("Please Fill Up The Form Details Nicely", "./gpf_sanction.php");
    	}    	    	    	
    	
    	if($_POST['submit'] == "CONFIRM SANCTION OF NEW GPF LOAN"){
    		$loan->sanctionNewLoan($employeeId, $_POST['amount'], $_POST['installment'], $_POST['type']);
    		$loan->palert("The New Loan Has Been Sanctioned", "./gpf_statementv.php?id=$employeeId");
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
      	<form action="" method="post">
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
            	<td align="right">LOAN TYPE </td>
                <td align="center" width="5%">:</td>
                <th align="left"><?php echo $_POST['type'] == 'r' ? "REFUNDABLE LOAN" : "NON REFUNDABLE LOAN"; ?></th>
            </tr>
            <tr>
            	<td height="20px"></td>
            </tr>
            <tr>
            	<td align="right">TOTAL LOAN INSTALLMENTS </td>
                <td align="center" width="5%">:</td>
                <th align="left"><?php echo $_POST['installment']; ?></th>
            </tr>
            <tr>
            	<td height="20px"></td>
            </tr>
            <tr>
            	<td align="right">AMOUNT SANCTIONED </td>
                <td align="center">:</td>
                <th align="left"><?php echo number_format($_POST['amount'], 2, '.', ''); ?></th>
            </tr>
            <?php
            	if($loan->getEmployeeGpfLoanAccountId($employeeId)){
            		echo "
						<tr>
							<td colspan=\"3\"><br /><hr size=\"2\" /><br /></td>
						</tr>
						<tr>
							<td colspan=\"3\" align=\"center\"><h2>ACTIVE GPF LOAN ACCOUNT DETAILS</h2></td>
						</tr>
						<tr>
							<td align=\"right\">LOAN AMOUNT LEFT :</td>
							<td align=\"center\">:</td>
							<th align=\"left\">".number_format($loan->getEmployeeGpfLoanAmountLeft($employeeId), 2, '.', '')."</th>
						</tr>
						<tr>
							<td height=\"10px\"></td>
						</tr>
						<tr>
							<td align=\"right\">INSTALLMENT LEFT TO PAY :</td>
							<td align=\"center\">:</td>
							<th align=\"left\">".$loan->getEmployeeGpfLoanInstallmentLeft($employeeId)."</th>
						</tr>
						<tr>
							<td height=\"10px\"></td>
						</tr>";	
            	}				
			?>
            <tr>
            	<td colspan="3"><br /><hr size="2" /><br /></td>
            </tr>            
            <tr>
            	<td colspan="3" align="center"><h2>INSTALLMENT PAYMENT DETAILS</h2></td>
            </tr>
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
            	<td align="right">LOAN INSTALLMENT AMOUNT</td>
                <td align="center">:</td>
                <th align="left">
                	<?php 
                		$amount = $_POST['amount'] + $loan->getEmployeeGpfLoanAmountLeft($employeeId);
                		$installment = $_POST['installment'] + $loan->getEmployeeGpfLoanInstallmentLeft($employeeId);
                		$installment = $installment > 36 ? 36 : $installment;
                		$installmentAmount = ceil($amount / $installment);
                		echo number_format($installmentAmount, 2, '.', '');
                	?></th>
            </tr>            
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
            	<td align="right">TOTAL LOAN INSTALLMENT</td>
                <td align="center">:</td>
                <th align="left"><?php echo $installment; ?></th>
            </tr>            
            <tr>
            	<td colspan="3"><br /><hr size="1" /><br /></td>
            </tr>
            
            <tr>
            	<td colspan="3" align="center">
            		<input type="hidden" name="employee" value="<?php echo $employeeId; ?>" />
            		<input type="hidden" name="type" value="<?php echo $_POST['type']; ?>" />
            		<input type="hidden" name="amount" value="<?php echo $_POST['amount']; ?>" />
            		<input type="hidden" name="installment" value="<?php echo $_POST['installment']; ?>" />
            		<input type="submit" name="submit" value="CONFIRM SANCTION OF NEW GPF LOAN" style="width:300px" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
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
