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

        
        
        
if(isset ($_GET['interest04']) 
&& isset ($_GET['interest05']) 
&& isset ($_GET['interest06'])
&& isset ($_GET['interest07'])
&& isset ($_GET['interest08']) 
&& isset ($_GET['interest09']) 
&& isset ($_GET['interest10']) 
&& isset ($_GET['interest11']) 
&& isset ($_GET['interest12']) 
&& isset ($_GET['interest01'])
&& isset ($_GET['interest02'])
&& isset ($_GET['interest03'])
&& isset ($_GET['type']) 
&& isset ($_GET['value'])){
        $processingType = $_GET['type'];
        $processingValue = $_GET['value'];
        $interestRate[0] = $_GET['interest04'];
		$interestRate[1] = $_GET['interest05'];
		$interestRate[2] = $_GET['interest06'];
		$interestRate[3] = $_GET['interest07'];
		$interestRate[4] = $_GET['interest08'];
		$interestRate[5] = $_GET['interest09'];
		$interestRate[6] = $_GET['interest10'];
		$interestRate[7] = $_GET['interest11'];
		$interestRate[8] = $_GET['interest12'];
		$interestRate[9] = $_GET['interest01'];
		$interestRate[10] = $_GET['interest02'];
		$interestRate[11] = $_GET['interest03'];
        $sessionId = $_GET['session'];
    }else
        $department->redirect('./process_interest.php');
             
$interest = new interest();
$employeeInfo = new employeeInfo();

if (isset($_POST['submit']) && $_POST['submit'] == "Process GPF Interest"){
	//echo $_GET['interest04'];
	
	//echo $_POST['interest12'];
	    $interestRate[0] = $_POST['interest04'];
		$interestRate[1] = $_POST['interest05'];
		$interestRate[2] = $_POST['interest06'];
		$interestRate[3] = $_POST['interest07'];
		$interestRate[4] = $_POST['interest08'];
		$interestRate[5] = $_POST['interest09'];
		$interestRate[6] = $_POST['interest10'];
		$interestRate[7] = $_POST['interest11'];
		$interestRate[8] = $_POST['interest12'];
		$interestRate[9] = $_POST['interest01'];
		$interestRate[10] = $_POST['interest02'];
		$interestRate[11] = $_POST['interest03'];
        
	$sessionId = $_POST['session'];
	
	$i=0;
	while (true){
		$employeeName = "employee".$i;
		if (!isset($_POST[$employeeName]))
			break;
		$employeeId = $_POST[$employeeName];
		$sessionDetails = $interest->checkInterestPrerequisites($sessionId);
		$amount = $interest->getTotalGPFInterest($employeeId, $interestRate, $sessionDetails, true);
		++$i;
	}
	$interest->palert("The GPF Interest Has Been Applied", "./");
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
        $department->palert("No Information Is There For The Salary Slip", './process_interest.php');
    }

ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- Total Head Summary</title>
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

<body>
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
                                                ACCOUNTS DEPARTMENT -- GPF INTEREST MODULE
                                                 </font></td>
                
            </tr>
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>
        </table>      	
        <table align="center" border="0" width="100%">
             <tr>
                <td colspan="5" align="center"><h2>PROCESSING GPF INTEREST</h2></td>
            </tr>                             
         	 <tr>
            	<th>SN</th>
                <th>Emp Code</th>
                <th align="left">Name</th>
                <th align="right">GPF Amt</th>
                <th align="right">Interest</th>
            </tr>          
            <tr>
            	<th colspan="5"><hr size="3" /></th>
            </tr>
            <?php 
            	$i = 0;            	
            	$sessionDetails = $interest->checkInterestPrerequisites($sessionId);
            	if (!$sessionDetails)
            		$interest->palert("Please Check The Session Id", "./process_interest.php");
            	foreach ($completeEmployeeId as $employeeId){
            		$amount = $interest->getTotalGPFInterest($employeeId, $interestRate, $sessionDetails, false);            		            		
            		if (!$amount || $amount == 0)
            			continue;            		
            		$employeeName = "employee".$i;
            		++$i;
            		$personalInfo->getEmployeeInformation($employeeId, true);
            		echo "
            		<tr>
		            	<td align=\"center\">
		            		<input type=\"hidden\" name=\"$employeeName\" value=\"$employeeId\" />$i</td>
		                <td align=\"center\">".$personalInfo->getEmployeeCode()."</td>
		                <td align=\"left\">".$personalInfo->getName()."</td>
		                 <td align=\"right\">".number_format($interest->gpfTotalBalance($employeeId, $sessionDetails[2]), 2, '.', '')."</td>
		                  <td align=\"right\">".number_format($amount, 2, '.', '')."</td>
		               
		                 </tr>";
            	}
            ?>
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
            	<td colspan="5" align="center">
            	
            		<input type="hidden" name="session" value="<?php echo $sessionId; ?>"/>
            		<input type="hidden" name="interest04" value="<?php echo $interestRate[0]; ?>"/>
            		<input type="hidden" name="interest05" value="<?php echo $interestRate[1]; ?>"/>
            		<input type="hidden" name="interest06" value="<?php echo $interestRate[2]; ?>"/>
            		<input type="hidden" name="interest07" value="<?php echo $interestRate[3]; ?>"/>
            		<input type="hidden" name="interest08" value="<?php echo $interestRate[4]; ?>"/>
            		<input type="hidden" name="interest09" value="<?php echo $interestRate[5]; ?>"/>
            		<input type="hidden" name="interest10" value="<?php echo $interestRate[6]; ?>"/>
            		<input type="hidden" name="interest11" value="<?php echo $interestRate[7]; ?>"/>
            		<input type="hidden" name="interest12" value="<?php echo $interestRate[8]; ?>"/>
            		<input type="hidden" name="interest01" value="<?php echo $interestRate[9]; ?>"/>
            		<input type="hidden" name="interest02" value="<?php echo $interestRate[10]; ?>"/>
            		<input type="hidden" name="interest03" value="<?php echo $interestRate[11]; ?>"/>
            		<input type="submit" name="submit" value="Process GPF Interest" style="width:250px" />
            		<input type="button" value="Return back" onclick="window.location='./process_interest.php'" style="width:200px" /></td>
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