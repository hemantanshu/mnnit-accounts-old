<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);


require_once '../include/class.accountHead.php';
require_once '../include/class.reporting.php';
require_once '../include/class.employeeInfo.php';

$allowance = new accountHead();

if(!$allowance->checkLogged())
  	$allowance->redirect('../');

if(!isset($_GET['id']) || !isset($_GET['date']))
	$allowance->redirect('./');
	  	
$month = $_GET['date'];
$id = $_GET['id'];	
$type = $_GET['type'];

$reporting = new reporting();
$employeeInfo = new employeeInfo();
	
$completeEmployeeId = $employeeInfo->getEmployeeIds(true, 'all');

ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- Total GPF Summary</title>
<link rel="stylesheet" type="text/css" href="../include/default.css" media="screen" />
<script type="text/javascript" src="../include/jquery-latest.js"></script> 
<script type="text/javascript" src="../include/jquery.tablesorter.js"></script>
<script type="text/javascript">
	$(function() {
		$("table").tablesorter({debug: false})
		$("a.append").click(appendData);
		
		
	});
	</script>

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
<!-- table sorter -->
<script type="text/javascript" src="../include/jquery.tablesorter.js"></script>
<script type="text/javascript">
	$(function() {
		$("table").tablesorter({debug: false})
		$("a.append").click(appendData);
		
		
	});
	</script>
<!-- table sorter ends here -->
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
        <thead>            
            <tr>
                <td align="center" colspan="3"><h2>Export Head Report To Excel</h2></td>
            </tr>
        </thead>
            <tbody>
			<?php 
				echo "<input type=\"hidden\" name=\"postData00\" value=\"employeeid\" />";    
                echo "<input type=\"hidden\" name=\"postData01\" value=\"Earnings\" />";
                echo "<input type=\"hidden\" name=\"postData02\" value=\"Deduction\" />";
        		$count = 1;
                $sum = 0;
                $debitTotal = $creditTotal = 0;
                                                
        		foreach ($completeEmployeeId as $employeeId){
        			$amount = $reporting->getSalaryAllowanceInfo($employeeId, $month, $id, $type);
        			if ($amount == 0)
        				continue;
                                $debit=$credit=0;
                                if($amount < 0)
                                        $debit = abs($amount);
                                else
                                        $credit = abs($amount);
        			$sum += $amount;
        			$debitTotal += $debit;
        			$creditTotal += $credit;
        			
        			echo "<input type=\"hidden\" name=\"postData".$count."0\" value=\"$employeeId\" />";    
                    echo "<input type=\"hidden\" name=\"postData".$count."1\" value=\"".number_format($credit, 2, '.', '')."\" />";
                    echo "<input type=\"hidden\" name=\"postData".$count."2\" value=\"".number_format($debit, 2, '.', '')."\" />"; 	
        			++$count;
        		}
        			echo "<input type=\"hidden\" name=\"postData".$count."0\" value=\"Total\" />";    
                    echo "<input type=\"hidden\" name=\"postData".$count."1\" value=\"".number_format($creditTotal, 2, '.', '')."\" />";
                    echo "<input type=\"hidden\" name=\"postData".$count."1\" value=\"".number_format($debitTotal, 2, '.', '')."\" />";
        		?>        	
            </tbody>
            <tr>
            	<td colspan="3"><br /><hr size="2" /><br /></td>
            </tr>
            <tr>
            	<td height="30px"></td>
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
            		<input type="hidden" name="totalCols" value="2" />
                	<input type="submit" name="submit" value="Export The Data Into Excel" style="width:200px" /> &nbsp;&nbsp;&nbsp;
                    <input type="button" style="width:200px" value="Export To Excel" onclick="window.location='report_mheada.php?id=<?php echo $id;?>&date=<?php echo $month; ?>'"/>&nbsp;&nbsp;&nbsp;
                    <input type="button" value="Return Back" style="width:150px" onclick="window.location='./report_gpf.php'" /></td>
            </tr>           
        </table>
        </form>
      </div>
      <div class="sidenav">
      	<hr size="2" />
        <center><font color="#FF0000" size="+1"><b><?php echo $allowance->getOfficerName(); ?></b></font></center>
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