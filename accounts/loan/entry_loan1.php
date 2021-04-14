<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
	//error_reporting(0);
    session_start();
    
    require_once '../include/class.loan.php';
    require_once '../include/class.personalInfo.php';
    
    $loan = new loan();
    $personalInfo = new personalInfo();
    
    if(!$loan->checkLoanOfficerLogged())
        $loan->redirect('../');
    if(!isset($_GET['id']))
    	$loan->redirect('./');
    
   	$loanAccountId = $_GET['id'];
   	$loanAccountDetails = $loan->getLoanAccountIdDetails($loanAccountId);
   	if($loanAccountDetails[0] == "")
   		$loan->redirect('./');
   	$details = $loan->getLoanTypeIdDetails($loanAccountDetails[2]);
   	$personalInfo->getEmployeeInformation($loanAccountDetails[1], true);
   	
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
      	<form action="./entry_loan2.php" method="post">
        <table align="center" border="0" width="100%">        	
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
            	<td colspan="6" align="center"><h2>LOAN ACCOUNT DETAILS</h2><hr size="2" /><br /></td>
            </tr>
            <tr>
            	<td align="right">Employee Name</td>
                <td align="center" width="5%">:</td>
                <th align="left"><?php echo strtoupper($personalInfo->getName()); ?></th>
            	<td align="right">Employee Code</td>
                <td align="center" width="5%">:</td>
                <th align="left"><?php echo strtoupper($personalInfo->getEmployeeCode()); ?></th>            	
            </tr>
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
            	<td align="right">Installment Amount</td>
                <td align="center">:</td>
                <th align="left"><?php echo $loan->getInstallmentAmount($loanAccountDetails[0]); ?></th>
            	<td align="right">Total Installment Left</td>
                <td align="center">:</td>
                <th align="left"><?php echo $loan->getLoanInstallmentLeft($loanAccountDetails[0]); ?></th>
            </tr>
            <tr>
            	<td height="20px"></td>
            </tr>
           	<tr>
            	<td colspan="6" align="center">
                	<table align="center" width="100%" border="0">
                    	<tr>
                        	<td align="center">
                        		<input type="hidden" name="loanid" value="<?php echo $loanAccountId; ?>" />
                        		Amount : <input type="text" name="amount" value="<?php echo $loan->getInstallmentAmount($loanAccountDetails[0]); ?>" style="width:100px" /></td>
                            <td align="center">Date : 
                                        <select name="date" style="width:100px">
                                        	<?php 
                                        		$i = 0;
	                                        	while($i < 120){
	                                        		$date = date("Y-m-d", mktime(0, 0, 0, 1 + $i, 12, 2001));
	                                        		++$i;
	                                        		$date = explode('-', $date);
	                                        		$date = $date[0].$date[1];
	                                        		echo "<option value=\"$date\">$date</option>";
	                                        	}
                                        	?>
                                        	                                             
                                        </select></td>
                            <td align="center">Check For Interst <input type="checkbox" name="checkbox" value="y" /></td>
                            <td align="center"><input type="submit" name="submit" value="Submit Data" style="width:100px" /></td>                            
                        </tr>
                        <tr>
                        	<td height="10px"></td>
                        </tr>
                    </table>
                </td>
            </tr>
                        
            <tr>
            	<td colspan="6"><hr size="1" /></td>
            </tr>
            <tr>
            	<td colspan="6" align="center">
                	<table width="100%" align="center" border="0">
                    	<tr>
                        	<th width="5%">S.N.</th>
                            <th width="40%">Remarks</th>
                            <th width="15%">Month</th>
                            <th width="13%" align="right">Credit</th>
                            <th width="13%" align="right">Debit</th>
                            <th width="13%" align="right">Balance</th>                            
                        </tr>
                        <tr>
                        	<td colspan="6"><br /><hr size="1" /><br /></td>
                        </tr>            
                        <?php                        	                        	
                        	$completeInstallmentId = $loan->getEmployeeLoanInstallmentId($loanAccountId);
                        	$count = 1;
                        	$sumTotal = $sumCredit = $sumDebit = 0;
                        	foreach ($completeInstallmentId as $installmentId){
                        		$details = $loan->getEmployeeLoanInstallmentIdDetails($installmentId);
                        		$credit = $details[2] > 0 ? $details[2] : 0;
                        		$debit = $details[2] < 0 ? abs($details[2]) : 0;
                        		
                        		$sumCredit += $credit;
                        		$sumDebit += $debit;
                        		$sumTotal += $credit - $debit;
                        		
                        		echo "
	                        		<tr>
										<td align=\"center\">$count</td>
										<td align=\"left\">$details[4]</td>
										<td align=\"right\">".$loan->nameMonth($details[3])."</td>
									    <td align=\"right\">".number_format($credit, 2, '.', '')."</td>
									    <td align=\"right\">".number_format($debit, 2, '.', '')."</td>
									    <td align=\"right\">".number_format($sumTotal, 2, '.', '')."</td>
									</tr>									          
									<tr>
										<td colspan=\"6\" style=\"padding-top:5px; padding-bottom:10px\"><hr size=\"1\" /></td>
									</tr>";	
								++$count;
                        	}            
                        	echo "
                        		<tr>
									<td align=\"center\">$count</td>
									<td align=\"left\" colspan=\"2\">TOTAL SUMMARY</td>
								    <td align=\"right\">".number_format($sumCredit, 2, '.', '')."</td>
								    <td align=\"right\">".number_format($sumDebit, 2, '.', '')."</td>
								    <td align=\"right\">".number_format($sumTotal, 2, '.', '')."</td>
								</tr>";            	
                        ?>                          
                    </table>                    
                </td>
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