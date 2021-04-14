<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
    //error_reporting(0);

    session_start();
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.department.php';
    require_once '../include/class.loan.php';
    require_once '../include/class.employeeType.php';
    
	$personalInfo = new personalInfo();
    $department = new department();
    $loan = new loan();
    $employeeType = new employeeType();
    
    
	if(!$loan->checkLogged())
        $loan->redirect('../');
        
	if(isset($_GET['id'])){
		$employeeId = $_GET['id'];
		$type = $_GET['type'];
	}
	else
		$loan->redirect('./');
	
	$month = $loan->getCurrentMonth();
	$activeLoanIds = $loan->getEmployeeActiveLoanId($employeeId);
	$inactiveLoanIds = $loan->getEmployeeActiveLoanId($employeeId, false);
	
	if(!$activeLoanIds && !$inactiveLoanIds)
		$loan->palert("There Is No Record Availiable For The Given Employee. Please Select Another Employee", './report_lstatement.php');    

    $personalInfo->getEmployeeInformation($employeeId, true);
    $flag = true;
    
    if($type == 'p')
    	$url = "./report_lstatementp.php?";
    else 
    	$url = "./report_lstatementv.php?";
    
    ob_end_flush();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- Monthly Loan Statement</title>
<link rel="stylesheet" type="text/css" href="../include/default.css" media="screen" />
<script type="text/javascript" src="../include/jquery.min.js"></script>
<script type="text/javascript" src="../include/ddaccordion.js"></script>
<script language="javascript" type="text/javascript">
			var currenttime = "<?php
									date_default_timezone_set('Asia/Calcutta');
									print date("F d, Y H:i:s", time())
								?>"
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
<div class="contentlarge">
      	<form action="" method="post">
        <table align="center" border="0" width="100%">            
            <tr>
            	<th width="100%">
                	<table align="center" width="100%" border="0">
                    	<tr>
                        	<th align="right" width="15%">Name</th>
                            <th align="center" width="5%">:</th>
                            <th align="left" width="30%"><font class="green"><?php echo $personalInfo->getName(); ?></font></th>
                            <th width="15%" align="right">Employee Code</th>
                            <th width="5%" align="center">:</th>
                            <th align="left" width="*"><font class="green"><?php echo $personalInfo->getEmployeeCode(); ?></font></th>
                        </tr>
                        <tr>
                        	<th height="10px"></th>
                        </tr>
                        <tr>                        
                        	<th align="right">Department</th>
                            <th align="center">:</th>
                            <th align="left"><font class="green"><?php echo $department->getDepartmentName($personalInfo->getDepartment()); ?></font></th>
                            <th align="right">Employee Type</th>
                            <th align="center">:</th>
                            <th align="left"><font class="green"><?php echo $employeeType->getEmployeeTypeName($personalInfo->getEmployeeType()); ?></font></th>
                        </tr>
                        <tr>
                        	<th height="10px"></th>
                        </tr>                        
                    </table>
                </th>
            </tr>
            <?php           	
            	if($activeLoanIds){
            		$flag = false;
            		echo "
            			<tr>
			            	<th ><br /><hr size=\"3\" /><br /></th>
			            </tr>            
			            <tr>
			            	<th>CURRENT ACTIVE LOAN STATUS</th>                
			            </tr>
			            <tr>
			            	<th height=\"10px\"><hr size=\"1\" /><br /></th>
			            </tr>
			            <tr>
			            	<td width=\"100%\">
			                	<table border=\"0\" align=\"center\" width=\"100%\">";
	            	foreach ($activeLoanIds as $loanId){
	            		$details = $loan->getLoanAccountIdDetails($loanId);
	            		$loanDetails = $loan->getLoanTypeIdDetails($details[2]);
	            		$newurl = $url."loanid=".$loanId;
	            		echo "
	            		<tr>
                        	<td align=\"right\" width=\"20%\">LOAN TYPE</td>
                            <td align=\"center\" width=\"3%\">:</td>
                            <td align=\"left\" width=\"27%\">".$loanDetails[2]."</td>
                            <td align=\"right\" width=\"20%\">Sanctioned Month</td>
                            <td align=\"center\" width=\"3%\">:</td>
                            <td align=\"left\" width=\"*\">".$loan->nameMonth($details[7])."</td>
                        </tr>
                        <tr>
                        	<td height=\"5px\"></td>
                        </tr>                        
	            		<tr>
                        	<td align=\"right\">Loan Amount Sanctioned</td>
                            <td align=\"center\">:</td>
                            <td align=\"left\">".number_format($loan->getTotalLoanAmountSanctioned($loanId), 2, '.', '')."</td>
                            <td align=\"right\">Interest Rate</td>
                            <td align=\"center\">:</td>
                            <td align=\"left\">".$details[6]."</td>
                        </tr>
                        <tr>
                        	<td height=\"5px\"></td>
                        </tr>                        
	            		<tr>
                        	<td align=\"right\">Total Installment (N)</td>
                            <td align=\"center\">:</td>
                            <td align=\"left\">".$details[4]."</td>
                            <td align=\"right\">Total Installment (I)</td>
                            <td align=\"center\">:</td>
                            <td align=\"left\">".$details[5]."</td>
                        </tr>
                        <tr>
                        	<td height=\"5px\"></td>
                        </tr>
                        
                        <tr>
                        	<td align=\"right\">Loan Amount Left</td>
                            <td align=\"center\">:</td>
                            <td align=\"left\">".number_format($loan->getLoanAmountLeft($loanId, $month), 2, '.', ',')."</td>
                            <td align=\"right\">Principle Amt. Left</td>
                            <td align=\"center\">:</td>
                            <td align=\"left\">".number_format($loan->getLoanPrincipleAmountLeft($loanId, $month), 2, '.', ',')."</td>
                        </tr>
                        <tr>
                        	<td height=\"5px\"></td>
                        </tr>
                        <tr>
                        	<td align=\"right\">Installment Amount</td>
                            <td align=\"center\">:</td>
                            <td align=\"left\">".number_format($loan->getInstallmentAmount($loanId), 2, '.', ',')."</td>
                            <td align=\"right\">Installment Left</td>
                            <td align=\"center\">:</td>
                            <td align=\"left\">".$loan->getLoanInstallmentLeft($loanId, $month)."</td>
                        </tr>
                        <tr>
                        	<td align=\"center\" colspan=\"6\"><br /><input type=\"button\" value=\"Get Statement Of This Loan\" style=\"width:300px\" onclick=\"window.location='$newurl'\" /></td>
                        </tr>
                        <tr>
                        	<td colspan=\"6\" height=\"5px\"><br /><hr size=\"2\" /><br /></td>
                        </tr>";
	            	}
	            	echo "
				            		</table>
			                </td>
			            </tr>   ";
            	}            	
            ?>
            <?php            	
            	if($inactiveLoanIds){
            		$flag = false;
            		echo "
            			<tr>
			            	<td ><br /><hr size=\"3\" /><br /></td>
			            </tr>            
			            <tr>
			            	<td>PREVIOUS LOAN ACCOUNT RECORDS</td>                
			            </tr>
			            <tr>
			            	<td height=\"10px\"></td>
			            </tr>
			            <tr>
			            	<td width=\"100%\">
			                	<table border=\"0\" align=\"center\" width=\"100%\">";
	            	foreach ($inactiveLoanIds as $loanId){
	            		$details = $loan->getLoanAccountIdDetails($loanId);
	            		$loanDetails = $loan->getLoanTypeIdDetails($details[2]);
	            		$newurl = $url."loanid=".$loanId;
	            		echo "
	            		<tr>
                        	<td align=\"right\" width=\"40%\">Loan Type</td>
                            <td align=\"center\" width=\"3%\">:</td>
                            <td align=\"left\" width=\"7%\">".$details[2]."</td>
                            <td align=\"right\" width=\"40%\">Sanctioned Month</td>
                            <td align=\"center\" width=\"3%\">:</td>
                            <td align=\"left\" width=\"*\">".$loan->nameMonth($details[7])."</td>
                        </tr>
                        <tr>
			            	<td height=\"10px\"></td>
			            </tr>
			            <tr>
                        	<td align=\"right\" width=\"40%\">Loan Amount</td>
                            <td align=\"center\" width=\"3%\">:</td>
                            <td align=\"left\" width=\"7%\">".number_format($loan->getTotalLoanAmountSanctioned($loanId))."</td>
                        </tr>
                        <tr>
                        	<td align=\"center\" colspan=\"6\"><br /><input type=\"button\" value=\"Get Statement Of This Loan\" style=\"width:300px\" onclick=\"window.location='$newurl'\" /></td>
                        </tr>";
	            	}
	            	echo "
				            		</table>
			                </td>
			            </tr>   ";
            	}            	
            ?>
                                                   
            <tr>
            	<td ><br /><hr size="3" /><br /></td>
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