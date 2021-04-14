<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
	////error_reporting(0);
    session_start();

    require_once '../include/class.loan.php';
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.employeeInfo.php';


    $loan = new loan();
    $personalInfo = new personalInfo();
    $employeeInfo = new employeeInfo();

    if(!$loan->checkLoanOfficerLogged())
        $loan->redirect('../');

    if(isset ($_POST['submit']) && $_POST['submit'] == "Get The Print Of The Statement"){
        $i = 0;
        $completeLoanId = array();        
        while(true){
            $bankName ="bank".$i;
            $loanIdName = "loan".$i;
            ++$i;
            if(!isset ($_POST[$bankName]))
                break;
            if($_POST[$bankName] == "b")
                array_push ($completeLoanId, $_POST[$loanIdName]);
        }
    }    
    $month = $_POST['date'];
    

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

<body onload="window.print() ">
<div>
  <div class="container">
    <div class="main">
      <div class="contentlarge">      	
      	<table border="0" align="center" width="100%">
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>
            <tr>
                <td align="center" width="160px" height="111px"><img src="../img/mnnit_logo.gif" alt="mnnit logo" width="126" height="111px" align="left" /></td>
                <td align="center" width="*"><font class="bigheader">MOTILAL NEHRU NATIONAL INSTITUTE OF TECHNOLOGY</font><br /><font class="smallheader">
                                                ALLAHABAD - 211004<br /><br />
                                                ACCOUNTS DEPARTMENT -- LOAN SANCTION STATEMENT<br />
                                                FOR THE MONTH <?php echo $loan->nameMonth($month); ?></font></td>

            </tr>
            <tr>
                <td colspan="2"><hr size="2" /></td>
            </tr>            
        </table>
        <table id="myTable" class="tablesorter" width="100%" cellpadding="5px" cellspacing="14px">
        	<thead>
        		<tr>
        			<th>Emp. Code</th>
	        		<th>Name</th>
                                <th align="left">Loan Type</th>
	        		<th align="left">Bank Account</th>
	        		<th align="right">Amount</th>
        		</tr>
                <tr>
                    <td colspan="5"><hr size="2" /></td>
                </tr>
        	</thead>
        	<tbody>
        		<?php
        		$sum = 0;
                        foreach ($completeLoanId as $loanId){
                            $bankName ="bank".$i;
                            $loanIdName = "loan".$i;
                            ++$i;

                            $details = $loan->getLoanAccountIdDetails($loanId);
                            $loanDetails = $loan->getLoanTypeIdDetails($details[2]);
                            $personalInfo->getEmployeeInformation($details[1], true);
                            $bankAccount = $employeeInfo->getEmployeeBankAccoutDetails($details[1], true);

                            $sum += $details[3];
                            echo "
                                <tr>
                                    <th><input type=\"hidden\" name=\"$loanIdName\" value=\"$loanId\" />".$personalInfo->getEmployeeCode()."</th>
                                    <th align=\"left\">".$personalInfo->getName()."</th>
                                    <th align=\"left\">".$loanDetails[2]."</th>
                                    <th align=\"left\">".$bankAccount[2]."</th>
                                    <th align=\"right\" style=\"padding-right:20px\">".  number_format($details[3], 2, '.', '')."</th>
                                    <th></th>
                                </tr>";
                        }
                        ?>
        		
        	</tbody>
            	<tr>
                	<td colspan="5"><hr size="3" /></td>
                </tr>
        		<tr>
        			<th colspan="3">TOTAL SUM AMOUNT</th>
        			<th align="right" colspan="2" style="padding-right:20px"><?php echo number_format(abs($sum), 2, '.', ''); ?></th>
        		</tr>
                <tr>
                	<td colspan="5"><hr size="3" /></td>
                </tr>
        </table>
        <div id="print" align="center">
        	<table align="center" width="100%">
                <tr>
                    <td align="center"><br />
                    	<input type="hidden" name="id" value="<?php echo $id; ?>" />
                        <input type="hidden" name="date" value="<?php echo $month; ?>" />
                        <input type="button" style="width:250px" value="Print The Summary" onclick="window.print() "/>&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="submit" style="width:250px" value="Export To Excel" name="submit"  />&nbsp;&nbsp;&nbsp;
                        <input type="button" style="width:150px" value="Return Back" onclick="window.location='./loan_bank.php'" /><br />
                    </td>
                </tr>
        </table>
        </div>
        
      </div>
      <div class="clearer"><span></span></div>
    </div>
  </div>
</div>
</body>
</html>
