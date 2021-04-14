<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
	////error_reporting(0);
    session_start();

    require_once '../include/class.employeeInfo.php';
    require_once '../include/class.editloan.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.department.php';


    $loan = new editLoan();
    $employeeInfo = new employeeInfo();
    $personalInfo = new personalInfo();
    $department = new department();

    if(!$loan->checkLoanOfficerLogged())
        $loan->redirect('../');
    if($_POST['submit'] == "CONFIRM SANCTIONS OF BULK LOAN"){
        $i = 0;
        while(true){
            $employeeName = "employee".$i;
            $amountName = "amount".$i;
            ++$i;
            if(!isset ($_POST[$employeeName]))
                break;
            $loanId = $loan->sanctionLoan($_POST[$employeeName], $_POST['loanType'], $_POST[$amountName], $_POST['installment'], $_POST['installmenti'], $_POST['interest']);
            $month = date("YM", mktime(0,0,0,  date('m') +3, date('d'), date('Y')));
            $loan->blockInstallmentAmount($loanId, $month);
        }
        $loan->palert("The Loan Has Been Successfully Sanctioned", './');
    }
    $completeActiveEmployee = $employeeInfo->getEmployeeIds(true);

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
      <div class="largecontent">
      	<form action="./sanction_bulkc1.php" method="post">
        <table align="center" border="0" width="100%">
      		<tr>
            	<td align="center"><h2>BULK LOAN SANCTION MODULE</h2><hr size="2" /><br /><br /></td>
            </tr>
            <tr>
                <td width="100%">
                    <table align="center" width="100%" border="0">
                        <tr>
                            <td align="right" width="25%">Loan Type :</td>
                            <td align="left" width="25%"><input type="hidden" name="loanType" value="<?php echo $_POST['loanType']; ?>" /><?php $details =  $loan->getLoanTypeIdDetails($_POST['loanType']); echo $details[2]; ?></td>
                            <td align="right" width="25%">Interest Rate :</td>
                            <td align="left" width="*"><input type="hidden" name="interest" value="<?php echo $_POST['interest']; ?>" /><?php echo $_POST['interest']; ?></td>
                        </tr>
                        <tr>
                            <td height="5px"></td>
                        </tr>
                        <tr>
                            <td align="right">Installment(P) :</td>
                            <td align="left"><input type="hidden" name="installment" value="<?php echo $_POST['installment']; ?>" /><?php echo $_POST['installment']; ?></td>
                            <td align="right">Installment(I) :</td>
                            <td align="left"><input type="hidden" name="installmenti" value="<?php echo $_POST['installmenti']; ?>" /><?php echo $_POST['installmenti']; ?></td>
                        </tr>
                        <tr>
                            <td height="5px"></td>
                        </tr>
                        <tr>
                            <td align="right">Stop Installment :</td>
                            <td align="left"><input type="hidden" name="stop" value="<?php echo $_POST['stop']; ?>" /><?php echo $_POST['stop']; ?></td>
                        </tr>
                        <tr>
                            <td height="5px"></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
                <td width="100%">
                    <table align="center" width="100%" border="0">
                        <tr>
                            <th>Emp. Code</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Amount</th>
                        </tr>
                        <tr>
                            <td colspan="4"><hr size="1" /></td>
                        </tr>
                        <?php
                            $i = 0;
                            $j = 0;
                            while(true){
                                $employeeName = "employee".$i;
                                $amountName = "amount".$i;
                                ++$i;
                                if(!isset ($_POST[$employeeName]))
                                    break;
                                if($_POST[$amountName] == "")
                                    continue;
                                $newEmployeeName = "employee".$j;
                                $newAmountName = "amount".$j;
                                ++$j;
                                
                                $employeeId = $_POST[$employeeName];
                                $personalInfo->getEmployeeInformation($employeeId, true);
                                echo "
                                    <tr>
                                        <th><input type=\"hidden\" name=\"$newEmployeeName\" value=\"$employeeId\" />".$personalInfo->getEmployeeCode()."</th>
                                        <th align=\"left\">".$personalInfo->getName()."</th>
                                        <th align=\"left\">".$department->getDepartmentName($personalInfo->getDepartment())."</th>
                                        <th><input type=\"hidden\" name=\"$newAmountName\" value=\"$_POST[$amountName]\" />".$_POST[$amountName]."</th>
                                    </tr>
                                    <tr>
                                        <td height=\"10px\"></td>
                                    </tr>";
                            }
                        ?>
                    </table>
                </td>
            </tr>

            <tr>
            	<td colspan="3" align="center">
                    <input type="submit" name="submit" value="CONFIRM SANCTIONS OF BULK LOAN" style="width:300px" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="button" value="Return Back" style="width:200px" onclick="window.location='./sanction_bulk.php'" /></td>

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