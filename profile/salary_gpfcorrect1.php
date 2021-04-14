<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);

require_once '../include/class.allowance.php';
require_once '../include/class.personalInfo.php';
require_once '../include/class.reporting.php';
require_once '../include/class.gpftotal.php';

$allowance = new allowance();
if(!$allowance->checkLogged())
        $allowance->redirect('../');

$accounts = new reporting();
$personalInfo = new personalInfo();
$gpfTotal = new gpfTotal();

if (!isset ($_GET))
    $allowance->redirect ('./salary_gpfcorrect.php');

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
          <form action="./salary_gpfcorrect2.php" method="post">
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
                            <th>Delete</th>
                        </tr>
                        <tr>
                            <td colspan="6"><hr size="1" />
                                <input type="hidden" name="employeeId" value="<?php echo $employeeId; ?>" />
                                <input type="hidden" name="date" value="<?php echo $date; ?>" /><br /><br /></td>
                        </tr>
                        <?php
                            $i = 0;
                            $flag = true;
                            $completeSalaryIds = $accounts->getSalaryReceiptIds($employeeId, $date);                                                            
                            foreach ($completeSalaryIds as $salaryId){
                                $details = $accounts->getSalaryIdDetails($salaryId);
                                if($details[4] == "ACH14"){
                                    echo "
                                        <tr>
                                            <th>$i</th>
                                            <th align=\"right\">".$allowance->getAllowanceTypeName($details[3])."</th>
                                            <th>
                                                <input type=\"hidden\" name=\"gpf\" value=\"$salaryId\" />
                                                <input type=\"text\" name=\"gpfAmount\" value=\"".  abs($details[5])."\" style=\"width:100px\" /></th>
                                            <th><a href=\"./salary_gpfcorrect2.php?sid=$salaryId\">Delete</a></th>
                                        </tr>
                                        <tr>
                                            <td height=\"10px\"></td>
                                        </tr>";   
                                }
                                if($details[4] == "ACH15"){
                                    echo "
                                        <tr>
                                            <th>$i</th>
                                            <th align=\"right\">".$allowance->getAllowanceTypeName($details[3])."</th>
                                            <th>
                                                <input type=\"hidden\" name=\"gpfAdvance\" value=\"$salaryId\" />
                                                <input type=\"text\" name=\"gpfAmountAdvance\" value=\"".  abs($details[5])."\" style=\"width:100px\" /></th>
                                            <th><a href=\"./salary_gpfcorrect2.php?date=$date&employeeId=$employeeId&sid=$salaryId\">Delete</a></th>
                                        </tr>
                                        <tr>
                                            <td height=\"10px\"></td>
                                        </tr>";                                       
                                }                          
                                
                            }
                        ?>                        
                        <tr>
                            <td colspan="6"><hr size="1" /><br /></td>
                        </tr>                         
                        <tr>
                            <td colspan="6" align="center"><input type="submit" name="submit" value="Update The Details" style="width: 200px;" />
                            <input type="button" value="Return Back" onclick="window.location='./salary_gpfcorrect1.php'" style="width: 200px" /><br /><br /></td>
                        </tr>

                    </table>
                </th>
            </tr>
            <tr>
                <th colspan="4">
                    <table border="0" align="center" width="100%">
                        <tr>
                            <td colspan="3" height="40px" align="center">Insert A New Entry In The Database</td>
                        </tr>
                        <tr>
                            <td>
                                <select name="insertType" style="width:300px">
                                    <option value="n">New Loan Taken</option>                                    
                                    <option value="m">Monthly GPF Subscription</option>                                    
                                    <option value="i">GPF Advance Recovery For The Month</option>                                    
                                    <option value="f">GPF Balance Brought Forward</option>                                                                        
                                </select>
                            </td>
                            <td>
                                <input type="text" name="amount" style="width:100px" />
                            </td>
                            <td>
                                <input type="submit" value="Insert New GPF Entry" name="submit" style="width:256px" />                                
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" height="30px"><hr size="1" /></td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                            <input type="button" value="Return Back" onclick="window.location='./salary_gpfcorrect.php'" style="width: 300px;" /><br /><br /></td>
                        </tr>
                    </table>
                </th>
            </tr>
            <tr>
                <th colspan="4">
                    <table border="0" align="center" width="100%">
                        <tr>
                            <td colspan="4" height="40px" align="center">Delete The Additional Row From GPF Total</td>
                        </tr>
                                                
                        <?php 
                        	$gpfIds = $accounts->getEmployeeGPFAdditionalIds($employeeId, $date);
                        	$i = 1;
                        	foreach ($gpfIds as $id){
                        		
                        		$details = $gpfTotal->getGpfIdDetails($id, true);
                        		echo "
                        			<tr>
			                            <td align=\"center\" width=\"5%\">$i</td>
			                            <td align=\"left\" width=\"40%\">".$details[4]."</td>
			                            <td align=\"left\" width=\"20%\">".$accounts->nameMonth($date)."</td>
			                            <td align=\"right\" width=\"15%\">".number_format($details[2], 2, '.', '')."</td>
			                            <td align=\"center\" width=\"*\"><a href=\"./salary_gpfcorrect2.php?lid=$id&employeeId=$employeeId&date=$date\">Delete</a></td>
			                        </tr>
                        			";
                        		++$i;
                        	}
                        ?>
                        <tr>
                            <td colspan="5" height="30px"><hr size="1" /></td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                            <input type="button" value="Return Back" onclick="window.location='./salary_gpfcorrect.php'" style="width: 300px;" /><br /><br /></td>
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