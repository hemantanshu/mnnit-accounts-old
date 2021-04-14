<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
	//error_reporting(0);

    session_start();

    require_once '../include/class.accountInfo.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.employeeInfo.php';
    require_once '../include/class.bank.php';

    $personalInfo = new personalInfo();
    $employeeInfo = new employeeInfo();
    $bank = new bank();
    $accounts = new accounts();


    if(!$accounts->checkLogged())
        $accounts->redirect('../');

    if(isset($_POST) && $_POST['submit'] == "Print Bank Slip"){
    	$date = $_POST['date'];
    	$accounts->redirect("./salary_vbankp.php?date=$date");
    }elseif(isset ($_GET['date'])){
        $date = $_GET['date'];
    }else
        $accounts->redirect('./salary_vbank.php');

    $completeEmployeeId = $employeeInfo->getEmployeeIds(true, 'bank', $date);

    if(!sizeof($completeEmployeeId))
        $accounts->palert("No Information For This Date Combination Exists ", "./salary_vbank.php");

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
      	<form action="" method="post">
        <table align="center" border="0" width="100%">
            <tr>
                <td align="center"><font class="error">Bank Slip For <?php echo $accounts->getNumber2Month(substr($date,4,2));?> ,<?php echo substr($date,0,4);?></font><br /><hr size="2" /><br /></td>
            </tr>
            <tr>
            	<td width="100%" align="center">
                    <table align="center" border="1px" width="100%">
                    	<tr>
                            <th>SN</th>
                            <th>Employee Code</th>
                            <th>Employee Name</th>
                            <th>Bank Name </th>
                            <th>Bank Account</th>
                            <th width="100px">Amount</th>
                        </tr>
                        <?php
                            $i = 0;
                            $total = 0;
                            foreach ($completeEmployeeId as $employeeId) {
                                $personalInfo->getEmployeeInformation($employeeId, true);
                            	$sum = $accounts->getProcessedSalarySum($employeeId, $date);
                                $bankDetails = $employeeInfo->getReservedEmployeeBankAccountDetails($employeeId, $date);

                                $total += $sum;
                                ++$i;

                                echo "
                                    <tr>
                                        <td align=\"center\"><font class=\"green\">".$i."</font></td>
                                        <td align=\"left\"><font class=\"green\">".$personalInfo->getEmployeeCode()."</font></td>
                                        <td align=\"left\"><font class=\"green\">".$personalInfo->getName()."</font></td>
                                        <td align=\"left\"><font class=\"green\">".$bank->getBankName($bankDetails[0])."</font></td>
                                        <td align=\"left\"><font class=\"green\">".$bankDetails[1]."</font></td>
                                        <td align=\"right\"><font class=\"error\">".number_format($sum, 2, '.', '')."</font></td>

                                    </tr>";
                           }
                           	echo "
                                    <tr>
                                        <td align=\"center\"><font class=\"green\">".$i."</font></td>
                                        <td align=\"center\" colspan=\"4\">Total Amount</td>
                                        <td align=\"right\"><font class=\"error\">".number_format($total, 2, '.', '')."</font></td>
                                    </tr>";
                        ?>
                    </table>
                </td>
            </tr>
            <tr>
            	<td height="20px"></td>
            </tr>
            <tr>
            	<td align="center">
                	<input type="hidden" name="date" value="<?php echo $date; ?>" />
                    <input type="submit" name="submit" value="Print Bank Slip" style="width:250px" />&nbsp;&nbsp;
                    <input type="button" value="Export Data To Excel" style="width:200px" onclick="window.location='./salary_vbanke.php?month=<?php echo $date; ?>'" />
                    <input type="button" value="Return Back" onclick="window.location='./salary_vbank.php'" style="width:150px" /></td>
            </tr>
            <tr>
                <td height="20px"></td>
            </tr>
        </table>

        </form>
      </div>
      <div class="sidenav">
      	<hr size="2" />
       <center> <font color="#FF0000" size="+1"><b><?php echo $accounts->getOfficerName(); ?></b></font></center>
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
