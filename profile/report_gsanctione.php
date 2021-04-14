<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
	//error_reporting(0);
    session_start();
    
    require_once '../include/class.gpftotal.php';
    
    $loan = new gpfTotal();        
    if(!$loan->checkLogged())
        $loan->redirect('../');
    if(!isset($_POST['date']))
    	$loan->redirect('./');
    	
    $month = $_POST['date'];
    $completeLoanId = $loan->getMonthlyNewGpfLoanAccountInstallmentId($month, true);
    $nonRefundableLoanId = $loan->getMonthlyNewGpfLoanAccountInstallmentId($month, false);
    
    if(!$completeLoanId && !$nonRefundableLoanId)
    	$loan->palert("No GPF Loan Statement Record Exists For The Given Month.", './report_gsanctionv.php');    
                    
    $data = array();
    $data[0] = array();

    array_push($data[0], "Emp Id");
    array_push($data[0], "Previous Balance");
    array_push($data[0], "Loan Sanctioned");
    array_push($data[0], "Total Loan Balance");
    array_push($data[0], "Installment Left");
    
    $count = 1;
    foreach ($completeLoanId as $loanInstallmentId){
    	$data[$count] = array();
    	$details = $loan->getGpfLoanInstallmentIdDetails($loanInstallmentId, true);
   		$loanDetails = $loan->getEmployeeGpfLoanAccountIdDetails($details[1]);
   		
   		$totalLoanBalance = $loan->getEmployeeGpfLoanAmountLeft($loanDetails[1], $month, "all");
        $intallmentLeft = $loan->getEmployeeGpfLoanInstallmentLeft($loanDetails[1], $month, 'all');
        $previousBalance = $totalLoanBalance - $details[2];
        
        array_push($data[$count], $loanDetails[1]);
        array_push($data[$count], number_format($previousBalance, 2, '.', ''));
        array_push($data[$count], number_format($details[2], 2, '.', ''));
        array_push($data[$count], number_format($totalLoanBalance, 2, '.', ''));
        array_push($data[$count], $intallmentLeft);
        ++$count;            		
    } 
    
    foreach ($nonRefundableLoanId as $loanId){
    	$details = $loan->getEmployeeGpfLoanAccountIdDetails($loanId);
    	array_push($data[$count], $loanDetails[1]);
        array_push($data[$count], number_format(0, 2, '.', ''));
        array_push($data[$count], number_format($details[2], 2, '.', ''));
        array_push($data[$count], number_format(0, 2, '.', ''));
        array_push($data[$count], 0);
        ++$count;    		
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
      	<form action="./export_excel.php" method="post">
        <table align="center" border="0" width="100%">
            <tr>
                <td align="center" colspan="3"><h2>Exporting Account Head Data</h2><br /><hr size="2" /><br /></td>
            </tr>
        	<?php 
        		$i = 0;
        		while (true){
        			if ($data[$i][0] == "" || !isset($data[$i][0]))
        				break;
        			for ($j = 0; $j < sizeof($data[0], 0); ++$j){
        				$postName = "postData".$i.$j;
        				echo "<input type=\"hidden\" name=\"$postName\" value=\"".$data[$i][$j]."\">";
        			}
        			++$i;
        		}
        	?>
            <tr>
                <td height="50px"></td>
            </tr>
            <tr>
                <th align="right" width="200px">Please Input The File Name</th>
                <th align="center" width="20px">:</th>
                <td align="left" width="*"><input type="text" name="fileName" style="width:300px" /></td>
            </tr>
            <tr>
                <td height="50px"></td>
            </tr>
            <tr>
                <td colspan="3" align="center">
                    <input type="hidden" name="totalCols" value="<?php echo sizeof($data[0], 0); ?>" />
                    <input type="submit" name="submit" value="Export The Data Into Excel" style="width:200px" />&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" name="button" value="Return Back" onclick="window.location='./report_gsanction.php.php'" style="width:200px" />
                
                </td>
            </tr>
        </table>
		</form>
      </div>
      <div class="sidenav">
      	<hr size="2" />
       <center> <font color="#FF0000" size="+1"><b><?php echo $loan->getOfficerName(); ?></b></font></center>
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