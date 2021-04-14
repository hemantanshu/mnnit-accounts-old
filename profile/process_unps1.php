<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();
//error_reporting(0);

require_once '../include/class.personalInfo.php';
require_once '../include/class.employeeInfo.php';
require_once '../include/class.interest.php';
require_once '../include/class.department.php';

$department = new department();
if(!$department->checkLogged())
        $department->redirect('../');
if(!$department->isAdmin())
        $department->palert("Only Administrator Has The Privilege To Process The Interest", "./");        

if(isset ($_GET['type']) && isset ($_GET['value'])){
        $processingType = $_GET['type'];
        $processingValue = $_GET['value'];   
        $sessionId = $_GET['session'];
    }else
        $department->redirect('./process_uinterest.php');
        
$interest = new interest();
$employeeInfo = new employeeInfo();

if (isset($_POST['submit']) && $_POST['submit'] == "UnProcess NPS Interest"){
	$interestRate = $_POST['interest'];
	$sessionId = $_POST['session'];
	
	$i=0;
	while (true){
		$employeeName = "employee".$i;
		$checkBox = "checkBox".$i;
		if (!isset($_POST[$employeeName]))
			break;
		if($_POST['checkbox'] == "y"){
			$employeeId = $_POST[$employeeName];
			$sessionDetails = $interest->checkInterestPrerequisites($sessionId);
			$interest->unProcessFundInterest($employeeId, $sessionDetails[2], "nps");
			++$i;	
		}		
	}
	$interest->palert("The CPF Interest Has Been Successfully Rollbacked", "./");
}      

$personalInfo = new personalInfo();

$completeEmployeeId = array();
    $variable = $employeeInfo->getEmployeeIds(true);

    if($processingType == "all"){
        foreach ($variable as $value) {
                array_push($completeEmployeeId, $value);
        }
    }elseif($processingType == "individual"){
            array_push($completeEmployeeId, $processingValue);
    }else{
        $department->palert("No Information Is There For The Given Employee", './process_uinterest.php');
    }
ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- Fund Interest RollBack Center</title>
<script type="text/javascript" src="../include/jquery-latest.js"></script> 
<script type="text/javascript" src="../include/jquery.tablesorter.js"></script>
<script type="text/javascript">
    $(function() {
        $("table").tablesorter({debug: false})
        $("a.append").click(appendData);
        
        
    });
    </script>

<link rel="stylesheet" type="text/css" href="../include/default.css" media="screen" />
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<style type="text/css">
            .break {
                page-break-before: always;
            }
            font.bigheader{
                font-family:Verdana, Geneva, sans-serif;
                font-size:18px;
                font-weight:bold;
                text-decoration:none;
                
            }
            font.smallheader{
                font-family:Verdana, Geneva, sans-serif;
                font-size:16px;
                font-weight:bold;
                text-decoration:none;
            }
            font.month{
                font-family:"Times New Roman", Times, serif;
                font-size:15px;
                font-weight:bold;
                text-decoration:underline;
            }
            font.salarySlip{
                font-family:Arial, Helvetica, sans-serif;
                font-size:12px;
                font-weight:400;
                text-decoration:none;
                
            }
            font.salaryPrint{
                font-family:Verdana, Geneva, sans-serif;
                font-size:12px;
                font-weight:bold;
            }
                        font.small{
                font-family:Verdana, Geneva, sans-serif;
                font-size:18px;
                font-weight:normal;
            }
        </style>
<style type="text/css" media="print">
#print {
    display: none;
}
</style>
</head>

<body onload="window.print() ">
<div>  
  <div class="container">
    <div class="main">
      <div class="contentlarge">
          <form action="" method="post">
          <table border="0" align="center" width="100%">
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>
            <tr>
                <td align="center" width="160px" height="111px"><img src="../img/mnnit_logo.gif" alt="mnnit logo" width="126" height="111px" align="left" /></td>
                <td align="center" width="*"><font class="bigheader">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class="smallheader">
                                                ALLAHABAD - 211004<br /><br />
                                                ACCOUNTS DEPARTMENT -- NPS INTEREST MODULE
                                                 </font></td>
                
            </tr>
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>
        </table>      	
        <table align="center" border="0" width="100%">
             <tr>
                <td colspan="5" align="center"><h2>UNPROCESSING NPS INTEREST</h2></td>
            </tr>                             
         	 <tr>
            	<th>SN</th>
                <th>Emp Code</th>
                <th align="left">Name</th>
                <th align="right">NPS Amt</th>
              	<th align="right">Interest</th>
              	<th align="center">UnProcess</th>
            </tr>          
            <tr>
            	<th colspan="6"><hr size="3" /></th>
            </tr>
            <?php 
            	$i = 0;            	
            	$sessionDetails = $interest->checkInterestPrerequisites($sessionId);
            	if (!$sessionDetails)
            		$interest->palert("Please Check The Session Id", "./process_uinterest.php");
            	foreach ($completeEmployeeId as $employeeId){           		            		
            		$amount = $interest->getFundAmount($employeeId, "nps", $sessionDetails[2], 'i');		            		
            		if (!$amount || $amount == 0)
            			continue;            		
            		$employeeName = "employee".$i;
            		$checkbox = "checkBox".$i;
            		++$i;
            		$personalInfo->getEmployeeInformation($employeeId, true);
            		echo "
            		<tr>
		            	<td align=\"center\">
		            		<input type=\"hidden\" name=\"$employeeName\" value=\"$employeeId\" />$i</td>
		                <td align=\"center\">".$personalInfo->getEmployeeCode()."</td>
		                <td align=\"left\">".$personalInfo->getName()."</td>
		                <td align=\"right\">".number_format($interest->npsTotalBalance($employeeId, $sessionDetails[2]), 2, '.', ',')."</td>
		               	<td align=\"right\">".number_format($amount, 2, '.', ',')."</td>
		               	<td align=\"center\"><input type=\"checkbox\" name=\"$checkbox\" value=\"y\" checked=\"y\" /></td>
		                
		            </tr>
		            <tr>
		            	<td height=\"5px\"></td>
		            </tr>";            		
            	}
            ?>
            <tr>
            	<th colspan="6"><hr size="3" /></th>
            </tr>
            
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
            	<td colspan="6" align="center">
            		<input type="hidden" name="session" value="<?php echo $sessionId; ?>"/>
            		<input type="submit" name="submit" value="UnProcess NPS Interest" style="width:250px" />
            		<input type="button" value="Return back" onclick="window.location='./process_uinterest.php'" style="width:200px" /></td>
            </tr>
        </table>    
        </form>
      </div>
                                                                                                 
    </div>
    <div class="footer">@webteam.<a href="http://www.mnnit.ac.in" title="MNNIT">mnnit</a> Designed And Developed By Hemant Kumar Sah (B.Tech ECE 2011)</div>
  </div>
</div>
</body>
</html>