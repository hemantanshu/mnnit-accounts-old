<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
    //error_reporting(0);

    session_start();
    require_once '../include/class.employeeInfo.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.loginInfo.php';
    require_once '../include/class.department.php';
    require_once '../include/class.employeeType.php';
    require_once '../include/class.gpftotal.php';
    
	
    $employeeInfo = new employeeInfo();
    $personalInfo = new personalInfo();
    $loggedInfo = new loginInfo();
    $department = new department();
    $gpfTotal = new gpfTotal();
    $employeeType = new employeeType();   
    
    
	if(!$loggedInfo->checkLoanOfficerLogged())
        $loggedInfo->redirect('../');
        
	if(isset($_GET['loanid']))
		$loanId = $_GET['loanid'];
	else
		$loggedInfo->redirect('./');
	
	$completeGpfIds = $gpfTotal->getGpfLoanInstallmentIds($loanId);
	if(!$completeGpfIds)
		$loggedInfo->palert("There Is No Record Availiable For The Given Employee. Please Select Another Employee", './gpf_loaninstallment.php');    

	$variable = $gpfTotal->getEmployeeGpfLoanAccountIdDetails($loanId);
    $personalInfo->getEmployeeInformation($variable[1], true);
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
      	<form action="./gpf_loanstatemente.php" method="post">
        <table align="center" border="0" width="100%">        	
            <tr>
            	<td align="center"><h3>COMPLETE GPF LOAN STATEMENT</h3><hr size="3" /><br /></td>
            </tr>
            <tr>
            	<td width="100%">
                	<table align="center" width="100%" border="0">
                    	<tr>
                        	<td align="right" width="15%">Name</td>
                            <td align="center" width="5%">:</td>
                            <td align="left" width="30%"><font class="green"><?php echo $personalInfo->getName(); ?></font></td>
                            <td width="15%" align="right">Employee Code</td>
                            <td width="5%" align="center">:</td>
                            <td align="left" width="*"><font class="green"><?php echo $personalInfo->getEmployeeCode(); ?></font></td>
                        </tr>
                        <tr>
                        	<td height="10px"></td>
                        </tr>
                        <tr>                        
                        	<td align="right">Department :</td>
                            <td align="center">:</td>
                            <td align="left"><font class="green"><?php echo $department->getDepartmentName($personalInfo->getDepartment()); ?></font></td>
                            <td align="right">Employee Type</td>
                            <td align="center">:</td>
                            <td align="left"><font class="green"><?php echo $employeeType->getEmployeeTypeName($personalInfo->getEmployeeType()); ?></font></td>
                        </tr>
                        <tr>
                        	<td height="10px"></td>
                        </tr>                        
                    </table>
                </td>
            </tr>
            <tr>
            	<td><br /><hr size="3" /><br /></td>
            </tr>
            <tr>
            	<td width="100%" align="center">
                	<table align="center" width="100%" border="0">
                    	<tr>
                        	<th width="5%">S.N.</th>
                            <th width="40%" align="left">Remarks</th>
                            <th width="15%">Date</th>                            
                            <th width="13%" align="right">Credit</th>
                            <th width="13%" align="right">Debit</th>
                            <th width="*" align="right">Balance</th>
                        </tr>
                        <tr>
                        	<td colspan="7"><br /><hr size="2" /><br /></td>
                        </tr>
                        <?php 
                        	$count = 0;
                        	$sumTotal = 0; //for the complete sum
                        	$sumDebit = 0; //for the total Debit Amount
                        	$sumCredit = 0; //for the total Credit Amount
                        	
                        	foreach ($completeGpfIds as $individualGpfId) {
                        		$details = $gpfTotal->getGpfLoanInstallmentIdDetails($individualGpfId, true);
                        		$debit = $details[2] < 0 ? abs($details[2]) : 0;
                        		$credit = $details[2] > 0 ? abs($details[2]) : 0;
                        		
                        		$sumDebit += $debit;
                        		$sumCredit += $credit;
                        		$sumTotal += -$debit + $credit;
                        		
                        		
                        		++$count;
                        		echo "
                        			<tr>
			                        	<td align=\"center\">".$count."</td>
			                            <td align=\"left\">".$details[4]."</td>
			                            <td align=\"left\">".$gpfTotal->getNumber2Month(substr($details[3], 4, 2)).", ".substr($details[3], 0, 4)."</td>
			                            <td align=\"right\">".number_format($credit, 2, '.', '')."</td>
			                            <td align=\"right\">".number_format($debit, 2, '.', '')."</td>
			                            <td align=\"right\">".number_format($sumTotal, 2, '.', '')."</td>
			                        </tr>
			                        <tr>
			                        	<td colspan=\"7\"><br /><hr size=\"1\" /><br /></td>
			                        </tr>";
                        	}
                        ?>
                        
                        <tr>
                        	<td height="10px"></td>
                        </tr>
                        <tr>
                        	<td colspan="3" align="center"><font class="error">Total Sum</font></td>
                            <td align="right"><font class="green"><?php echo number_format($sumCredit, 2, '.', ''); ?></font></td>
                            <td align="right"><font class="error"><?php echo number_format($sumDebit, 2, '.', ''); ?></font></td>
                            <td align="right"><font class="green"><?php echo number_format($sumTotal, 2, '.', ''); ?></font></td>
                        </tr>
                        <tr>
                        	<td height="10px"></td>
                        </tr>
                        <tr>
                        	<td colspan="7"><hr size="3" /></td>
                        </tr>
                        <tr>
                        	<td height="10px"></td>
                        </tr>
                        <tr>
                        	<td colspan="7" align="center">
                            	<input type="hidden" name="loanid" value="<?php echo $loanId; ?>" />
                            	<input type="button" value="Get The Print Statement" onclick="window.location='gpf_loanstatementp.php?loanid=<?php echo $loanId; ?>'" style="width:200px" />&nbsp;&nbsp;&nbsp;
                                <input type="submit" name="submit" value="Get Output In Excel"  style="width:200px" />&nbsp;&nbsp;&nbsp;
                                <input type="button" value="Return Back" onclick="window.location='./gpf_loanstatement.php'" style="width:150px" /></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        </form>
      </div>
      <div class="sidenav">
      	<hr size="2" /><center>
        <font color="#FF0000" size="+1"><b><?php echo $loggedInfo->getOfficerName(); ?></b></font></center>
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