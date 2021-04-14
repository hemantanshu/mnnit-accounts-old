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
        
	if(isset($_GET['id'])){
		$employeeId = $_GET['id'];
		$type = $_GET['type'];
	}
	else
		$loggedInfo->redirect('./');
	
	$completeGpfIds = $gpfTotal->getEmployeeGpfIds($employeeId, true);
	if(!$completeGpfIds)
		$loggedInfo->palert("There Is No Record Availiable For The Given Employee. Please Select Another Employee", './salary_gpf.php');    

    $personalInfo->getEmployeeInformation($employeeId, true);
    $flag = true;
    
    if($type == 'p')
    	$url = "./gpf_loanstatementp.php?";
    else 
    	$url = "./gpf_loanstatementv.php?";
    
    ob_end_flush();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- GPF Loan Statement</title>
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
                        	<th align="right">Department :</th>
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
            	$loanIds = $gpfTotal->getEmployeeGpfLoanAccountId($employeeId);
            	
            	if($loanIds){
            		$flag = false;
            		echo "
            			<tr>
			            	<th ><br /><hr size=\"3\" /><br /></th>
			            </tr>            
			            <tr>
			            	<th>CURRENT ACTIVE LOAN STATUS</th>                
			            </tr>
			            <tr>
			            	<th height=\"10px\"></th>
			            </tr>
			            <tr>
			            	<th width=\"100%\">
			                	<table border=\"0\" align=\"center\" width=\"100%\">";
	            	foreach ($loanIds as $loanId){
	            		$details = $gpfTotal->getEmployeeGpfLoanAccountIdDetails($loanId);
	            		$url = $url."loanid=".$loanId;
	            		echo "
	            			<tr>
                        	<th align=\"right\" width=\"40%\">LOAN AMOUNT SANCTIONED </th>
                            <th align=\"center\" width=\"3%\">:</th>
                            <th align=\"left\" width=\"7%\">".number_format($details[2], 2, '.', '')."</th>
                            <th align=\"right\" width=\"40%\">TOTAL INSTALLMENTS </th>
                            <th align=\"center\" width=\"3%\">:</th>
                            <th align=\"left\" width=\"*\">".$details[3]."</th>
                        </tr>
                        <tr>
                        	<th height=\"5px\"></th>
                        </tr>
                        <tr>
                        	<th align=\"right\">AMOUNT LEFT </th>
                            <th align=\"center\">:</th>
                            <th align=\"left\">".number_format($gpfTotal->getEmployeeGpfLoanAmountLeft($employeeId), 2, '.', '')."</th>
                            <th align=\"right\">INSTALLMENT LEFT </th>
                            <th align=\"center\">:</th>
                            <th align=\"left\">".$gpfTotal->getEmployeeGpfLoanInstallmentLeft($employeeId)."</th>
                        </tr>
                        <tr>
                        	<th height=\"5px\"></th>
                        </tr>
                        <tr>
                        	<th align=\"right\">INSTALLMENT AMOUNT</th>
                            <th align=\"center\">:</th>
                            <th align=\"left\">".$gpfTotal->getEmployeeGpfLoanInstallmentAmount($employeeId)."</th>
                        </tr>
                        <tr>
                        	<th align=\"center\" colspan=\"6\"><br /><input type=\"button\" value=\"Get Statement Of This Loan\" style=\"width:300px\" onclick=\"window.location='$url'\" /></th>
                        </tr>";
	            	}
	            	echo "
				            		</table>
			                </th>
			            </tr>   ";
            	}            	
            ?>
            <?php 
            	$loanIds = $gpfTotal->getEmployeeGpfLoanAccountId($employeeId, false);            	            	
            	if($loanIds){
            		$flag = false;
            		echo "
            			<tr>
			            	<th ><br /><hr size=\"3\" /><br /></th>
			            </tr>            
			            <tr>
			            	<th>PREVIOUS LOAN ACCOUNT RECORDS ( REFUNDABLE ONES )</th>                
			            </tr>
			            <tr>
			            	<th height=\"10px\"></th>
			            </tr>
			            <tr>
			            	<th width=\"100%\">
			                	<table border=\"0\" align=\"center\" width=\"100%\">";
	            	foreach ($loanIds as $loanId){
	            		$details = $gpfTotal->getEmployeeGpfLoanAccountIdDetails($loanId);
	            		$url = $url."loanid=".$loanId;
	            		echo "
	            			<tr>
                        	<th align=\"right\" width=\"40%\">LOAN AMOUNT SANCTIONED </th>
                            <th align=\"center\" width=\"3%\">:</th>
                            <th align=\"left\" width=\"7%\">".number_format($details[2], 2, '.', '')."</th>
                            <th align=\"right\" width=\"40%\">TOTAL INSTALLMENTS </th>
                            <th align=\"center\" width=\"3%\">:</th>
                            <th align=\"left\" width=\"*\">".$details[3]."</th>
                        </tr>
                        <tr>
                        	<th align=\"center\" colspan=\"6\"><br /><input type=\"button\" value=\"Get Statement Of This Loan\" style=\"width:300px\" onclick=\"window.location='$url'\" /></th>
                        </tr>";
	            	}
	            	echo "
				            		</table>
			                </th>
			            </tr>   ";
            	}            	
            ?>
            <?php 
            	$loanIds = $gpfTotal->getEmployeeGpfLoanAccountId($employeeId, true);            	
            	if($loanIds){
            		$flag = false;
            		echo "
            			<tr>
			            	<th ><br /><hr size=\"3\" /><br /></th>
			            </tr>            
			            <tr>
			            	<th>PREVIOUS LOAN ACCOUNT RECORDS (NON REFUNDABLE ONES )</th>                
			            </tr>
			            <tr>
			            	<th height=\"10px\"></th>
			            </tr>
			            <tr>
			            	<th width=\"100%\">
			                	<table border=\"0\" align=\"center\" width=\"100%\">";
	            	foreach ($loanIds as $loanId){
	            		$details = $gpfTotal->getEmployeeGpfLoanAccountIdDetails($loanId);
	            		echo "
	            			<tr>
                        	<th align=\"right\" width=\"40%\">LOAN AMOUNT SANCTIONED </th>
                            <th align=\"center\" width=\"3%\">:</th>
                            <th align=\"left\" width=\"7%\">".number_format($details[2], 2, '.', '')."</th>
                            <th align=\"right\" width=\"40%\">DATE OF LOAN</th>
                            <th align=\"center\" width=\"3%\">:</th>
                            <th align=\"left\" width=\"*\">".$gpfTotal->getNumber2Month(substr($details[5], 4, 2)).", ".substr($details[5], 0, 4)."</th>
                        </tr>";
	            	}
	            	echo "
				            		</table>
			                </th>
			            </tr>   ";
            	}            	
            	if ($flag)
            		$gpfTotal->palert("No GPF Loan Record Exists For ".$personalInfo->getName().". Please Select Another Employee", './gpf_loanstatement.php');
            ?>                                       
            <tr>
            	<th ><br /><hr size="3" /><br /></th>
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