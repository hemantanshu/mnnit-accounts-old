<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
	//error_reporting(0);
    session_start();
    
    require_once '../include/class.loan.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.department.php';
    
    $loan = new loan();    
    
    if(!$loan->checkLogged())
        $loan->redirect('../');
        
    $month = $loan->getCurrentMonth();
    $completeLoanId = $loan->getLoanAccountId();
    
    if(!$completeLoanId)
    	$loan->palert("No Active Loan Record Exists", './');    
                    
    $personalInfo = new personalInfo();
    $department = new department();
    
    ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- Active Loan Statement</title>
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
      	<form action="./report_gactivee.php" method="post">
      	<table align="center" border="0" width="100%">       	
        	<tr>
            	<th align="center" colspan="8">SHOWING CURRENT ACTIVE LOAN</th>
            </tr>
            <tr>
            	<td colspan="8"><hr size="1" /></td>
            </tr>
            <tr>
            	<td width="5%">SN</td>
            	<td width="7%">Emp. Code</td>
            	<td width="25%" align="left">Name</td>
            	<td width="20%" align="right">Loan Type</td>
            	<td align="right" width="10%">Amount Left</td>
            	<td align="right" width="10%">Inst. Amount</td>            	
            	<td align="right" width="8%">Inst. Left</td>
            	<td align="right" width="*%">Closing Month</td>
            </tr>
            <tr>
            	<td colspan="8"><br /><hr size="1" /><br /></td>
            </tr> 
            <?php
            	$count = 0;
            	foreach ($completeLoanId as $loanId){            		
            		$details = $loan->getLoanAccountIdDetails($loanId);
            		$personalInfo->getEmployeeInformation($details[1], true);
					$loanDetails = $loan->getLoanTypeIdDetails($details[2]);
            		
            		$installmentLeft = $loan->getLoanInstallmentLeft($loanId, $month);            		
            		$nextMonth = date("Ym", mktime(0, 0, 0, date("m")+$installmentLeft, 15, date("Y")));
            		$loanName = "loan".$count;   
            		        		
            		++$count;                                               		
            		echo "
						<tr>
							<td>
								<input type=\"hidden\" name=\"$loanName\" value=\"$loanId\" />
									".$count."</td>
							<td>".$personalInfo->getEmployeeCode()."</td>
							<td align=\"left\">".$personalInfo->getName()."</td>
							<td align=\"left\">".$loanDetails[2]."</td>							
							<td align=\"right\">".number_format($loan->getLoanAmountLeft($loanId, $month), 2, '.', ',')."</td>
							<td align=\"right\">".number_format($loan->getInstallmentAmount($loanId), 2, '.', ',')."</td>
							<td align=\"right\">".$installmentLeft."</td>
							<td align=\"right\">".ucwords(strtolower($loan->nameMonth($nextMonth)))."</td>							                
						</tr>	          
						<tr>
							<td height=\"8px\"></td>
						</tr>";            		
            	}
				
			?>	
            <tr>
            	<td colspan="8"><br /><hr size="1" /><br /></td>
            </tr>
            <tr>
            	<td colspan="8" align="center"><br />
                        <input type="button" style="width:250px" value="Print The Report" onclick="window.location='./report_lactivep.php'"/>&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="submit" style="width:250px" value="Export To Excel" name="submit"  />&nbsp;&nbsp;&nbsp;
                        <input type="button" style="width:150px" value="Return Back" onclick="window.location='./'" /><br />
                    </td>
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
