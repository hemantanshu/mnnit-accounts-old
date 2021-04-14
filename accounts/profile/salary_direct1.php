<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);

require_once '../include/class.allowance.php';
require_once '../include/class.personalInfo.php';
require_once '../include/class.reporting.php';


$allowance = new allowance();
if(!$allowance->checkLogged())
        $allowance->redirect('../');

$accounts = new reporting();
$personalInfo = new personalInfo();

if (!isset ($_GET))
    $allowance->redirect ('./salary_direct.php');

$employeeId = $_GET['employeeId'];
$date = $_GET['date'];

$personalInfo->getEmployeeInformation($employeeId, true);

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
      <div class="largecontent">
      	<form action="./salary_direct2.php" method="post">
        <table align="center" style=" border-collapse:collapse" width="100%">
            <tr>
                <th width="20%">Name</th>
                <th> : <?php echo $personalInfo->getName(); ?></th>
                <th width="25%">Employee Code</th>
                <th> : <?php echo $personalInfo->getEmployeeCode(); ?></th>
            </tr>
            <tr>
                <td height="10px"></td>
            </tr>
            <tr>
                <th>Date</th>
                <th>:<?php echo $allowance->nameMonth($date); ?></th>
            </tr>

            <tr>
                <th height="10px" colspan="4"><hr size="1" /></th>
            </tr>            
            <tr>
                <th colspan="4">
                    <table border="0" align="center" width="100%">
                        <tr>
                            <td colspan="6" align="center">Earnings Report</td>
                        </tr>
                        <tr>
                            <th>SN</th>
                            <th width="20%">Allowance Name</th>
                            <th>Amount</th>
                            <th>Credit</th>
                            <th>Debit</th>
                            <th>Delete</th>
                        </tr>
                        <tr>
                            <td colspan="6"><hr size="1" />
                                <input type="hidden" name="employeeId" value="<?php echo $employeeId; ?>" />
                                <input type="hidden" name="date" value="<?php echo $date; ?>" /></td>
                        </tr>
                        <?php
                            $i = 0;
                            $flag = true;
                            $completeSalaryIds = $accounts->getSalaryReceiptIds($employeeId, $date);
                			$earnings = $deductions = $totalSum = 0;
                            
                            foreach ($completeSalaryIds as $salaryId){
                                $details = $accounts->getSalaryIdDetails($salaryId);
                                $amountName = "amount".$i;
                                $optionName = "option".$i;
                                $idName = "id".$i;
                                ++$i;
                                
                                if ($details[6] == 'c')
                                	$earnings += $details[5];
                                else 
                                	$deductions += $details[5];                                                          

                                if ($details[6] == 'd' && $flag){
                                	$flag = false;
                                	echo "
                                		<tr>
                                			<td colspan=\"7\"><hr size=\"4\" /></td>
                                		</tr>
                                		";
                                }
                                echo "
                                    <tr>
                                        <th>$i</th>
                                        <th align=\"right\">".$allowance->getAllowanceTypeName($details[3])."</th>
                                        <th>
                                            <input type=\"hidden\" name=\"$idName\" value=\"$salaryId\" />
                                            <input type=\"text\" name=\"$amountName\" value=\"".  abs($details[5])."\" style=\"width:100px\" /></th>";
                                $debit = $details[6] == 'd' ? 'checked="checked"' : "";
                                $credit = $details[6] == 'c' ? 'checked="checked"' : "";
                                echo "
                                        <th><input type=\"radio\" name=\"$optionName\" value=\"c\" ".$credit." /></th>
                                        <th><input type=\"radio\" name=\"$optionName\" value=\"d\" ".$debit."/></th>";
                                echo "
                                        <th><a href=\"./salary_direct2.php?date=$date&employeeId=$employeeId&sid=$salaryId\">Delete</a></th>
                                    </tr>
                                    <tr>
                                        <td height=\"10px\"></td>
                                    </tr>";                                
                                
                            }
                        ?>                        
                        <tr>
                            <td colspan="6"><hr size="1" /></td>
                        </tr>
                        <tr>
                            <th colspan="2">Earnings : <?php echo number_format($earnings, 2, '.', ''); ?></th>
                            <th colspan="2">Deductions : <?php echo number_format(abs($deductions), 2, '.', ''); ?></th>
                            <th colspan="2">Net Payabale :<?php echo number_format(($earnings - $deductions), 2, '.', ''); ?></th>
                        </tr>
                        <tr>
                            <td colspan="6"><hr size="1" /></td>
                        </tr>
                        
                        <tr>
                            <td colspan="6" align="center"><input type="submit" name="submit" value="Update The Salary Details" />
                            <input type="button" value="Return Back" onclick="window.location='./salary_direct.php'" /></td>
                        </tr>

                    </table>
                </th>
            </tr>
            <tr>
                <th colspan="4">
                    <table border="0" align="center" width="100%">
                        <tr>
                            <td colspan="3" height="40px" align="center">Add New Entry</td>
                        </tr>
                        <tr>
                            <td>
                                <select name="allowance" style="width:300px">
                                    <?php
                                        $completeAllowanceId = $allowance->getAllowanceIds(true);
                                        foreach ($completeAllowanceId as $allowanceId)
                                            echo "<option value=\"$allowanceId\">".$allowance->getAllowanceTypeName($allowanceId)."</option>";
                                    ?>
                                    
                                </select>
                            </td>
                            <td>
                                <input type="text" name="amount" style="width:100px" />
                            </td>
                            <td>
                                <select name="type" style="width:200px">
                                    <option value="c">Credit</option>
                                    <option value="d">Debit</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" height="30px"><hr size="1" /></td>
                        </tr>
                        <tr>
                            <td colspan="3"><input type="submit" value="Insert New Allowance Entry" name="submit" style="width:256px" />
                                            <input type="button" value="Return Back" onclick="window.location='./salary_direct.php'" /></td>
                        </tr>
                    </table>
                </th>
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