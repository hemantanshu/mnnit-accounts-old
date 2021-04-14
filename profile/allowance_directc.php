
<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
   

    session_start();
    
    require_once '../include/class.allowance.php';
    require_once '../include/class.directSalaryAddition.php';
	
    $allowance = new allowance();    
    
    $salaryAddition = new directSalaryAddition();
      
    if(!$allowance->checkLogged())
        $allowance->redirect('../');
        
	if(isset($_POST) && $_POST['submit'] == "Confirm Processing Of The Amount For These Employees"){
		$allowanceId = $_POST['allowanceId'];
                $details = $allowance->getAllowanceHeadDetails($allowanceId);
		$direct = $_POST['option'] == '1' ? 'n' : 'y';
		$count = 0;
		while(true){
			$newAmountName = "amount".$count;
                        $newEmployeeIdName = "employeeId".$count;
                        ++$count;
			if(!isset($_POST[$newEmployeeIdName]))
				break;
                        if($details[8] == 'd')
                              $amount = 0 - $_POST[$newAmountName];
                        else
                              $amount = $_POST[$newAmountName];
                        $salaryAddition->insertDirectSalary($_POST[$newEmployeeIdName], $allowanceId, $amount, $direct);
		}
		if($allowance->isAdmin())
			$allowance->palert("The Allowance Information Was Successfully Updated", './allowance.php');
		else 
			$allowance->palert("The Allowance Information Was Updated And Is In Pending Status Till The Final Consent From The Admin", './allowance.php');
			
	}
        
    if(isset($_POST) && $_POST['submit'] == "Process The Amount For These Employees"){
    	$allowanceId = $_POST['allowanceId'];
    }else{
    	$allowance->redirect('./');
    } 
    
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.department.php';
        
    $personalInfo = new personalInfo();
	$department = new department();
	   
	if($allowance->getAllowanceAccountHead($allowanceId) == '')
		$allowance->redirect('./');
    ob_end_flush();
    
    
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- allowance/deduction Information</title>
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
      <div class="contentLarge">          
      	<form action="./allowance_directc.php" method="post">
      	<table align="center" border="0" width="100%">
        	<!-- insertion of new departments will be done here -->
            <tr>
            	<td colspan="6" align="center"><font class="error">For Allowance Head : <?php echo $allowance->getAllowanceTypeName($allowanceId);?></font><br /><br /><hr size="1" /><br /></td>
            </tr>
            <tr>
                <td colspan="6" height="10px" align="center">
                        <a href="./allowance_view1.php?id=<?php echo $allowanceId; ?>&type=all" target="_parent">Info </a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_view.php?id=<?php echo $allowanceId; ?>&type=all" target="_parent">Total View </a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_view.php?id=<?php echo $allowanceId; ?>&type=no" target="_parent">Non-Overridden </a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_view.php?id=<?php echo $allowanceId; ?>&type=yes" target="_parent">Over-ridden </a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_direct.php?id=<?php echo $allowanceId; ?>&type=yes" target="_parent">Edit Amount(Direct)</a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_direct1.php?id=<?php echo $allowanceId; ?>&type=yes" target="_parent">Edit Amount(Master-Update)</a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_clear.php?id=<?php echo $allowanceId; ?>" target="_parent">Clear Amount</a>
               	 </td>
            </tr>
            <tr>
            	<td colspan="6" height="10px"><hr size="1" /></td>
            </tr>
            <tr>
            	<td colspan="6" align="center">
                	<input type="hidden" name="option" value="<?php echo $_POST['option']; ?>" />
                <h3>OPTION SELECTED : 
            		<?php 
            			if($_POST['option'] == '1')
            				echo "UPDATE THE MASTER SALARY INFORMATION";
            			else 
            				echo "INSERT DIRECT TO THE SALARY ADDITIONS";
            		?></h3></td>
            </tr>         
            <tr>
            	<td height="5px" colspan="6"><hr size="1" /></td>
            </tr>  
            <tr>
            	<th width="5%">S.N</th>
                <th width="10%">Emp. Code</th>
                <th width="30%" align="left">Name</th>
                <th width="30%" align="left">Department</th>
                <th width="20%" align="right">Amount</th>
            </tr>
            <tr>
            	<td height="10px" colspan="5"><hr size="3" /></td>
            </tr>
            <?php 
            	$i = 0;
            	$count = 0;
            	while (true){
            		$amountName = "amount".$i;
            		$employeeIdName = "employeeId".$i;
            		++$i;
            		
            		if(!isset($_POST[$employeeIdName]))
            			break;
            		if($_POST[$amountName] != ""){
            			$newAmountName = "amount".$count;
            			$newEmployeeIdName = "employeeId".$count;
            			++$count;            			
            			
            			$personalInfo->getEmployeeInformation($_POST[$employeeIdName], true);
            			
            			echo "
							<tr>
								<td align=\"center\">".$count."</td>
								<td align=\"left\">".$personalInfo->getEmployeeCode()."</td>
								<td align=\"left\">".$personalInfo->getName()."</td>
								<td align=\"left\">".$department->getDepartmentName($personalInfo->getDepartment())."</td>
								<td align=\"right\" style=\"padding-right:20px\">
										<input type=\"hidden\" name=\"".$newAmountName."\" value=\"".$_POST[$amountName]."\" /><font class=\"error\">Rs. ".  number_format($_POST[$amountName], 2, '.', '')."								
										<input type=\"hidden\" name=\"".$newEmployeeIdName."\" value=\"".$_POST[$employeeIdName]."\" /></font></td>
							</tr>
							<tr>
								<td colspan=\"6\"><hr size=\"1\" /></td>
							</tr>
							<tr>
								<td height=\"5px\"></td>
							</tr>";
            		}        				
            	}
            ?> 
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
            	<td colspan="6" align="center">
                	<input type="hidden" name="allowanceId" value="<?php echo $_POST['allowanceId']; ?>" />
                    <input type="submit" name="submit" value="Confirm Processing Of The Amount For These Employees" style="width:400px" />
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" onclick="window.location='./allowance_direct.php?id=<?php echo $allowanceId;?>'" value="Return Back To Allowances" /></td>
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