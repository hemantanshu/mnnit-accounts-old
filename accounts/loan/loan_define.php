<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
	////error_reporting(0);
    session_start();
    
    require_once '../include/class.loginInfo.php';
    require_once '../include/class.allowance.php';
    require_once '../include/class.editloan.php';
    
    
    $loggedInfo = new loginInfo();    
    $allowance = new allowance();
	$loan = new editLoan();
	
    if(!$allowance->checkLoanOfficerLogged())
        $allowance->redirect('../');
        
    if(isset($_POST) && $_POST['submit'] == "Define New Loan Type"){
    	$error = 0;    	
    	$errorLog = array();
    	
    	if($_POST['loanType'] == ""){
    		$error++;
    		array_push($errorLog, "Please Enter The Name Of The New Loan Type");	
    	}
    	if($loan->checkLoanName($_POST['loanType'])){
    		$error++;
    		array_push($errorLog, "Another Loan Type Exists With The Same Name. Please Select Another Name");
    	}
    	
    	if($error == 0){
    		$loan->setNewLoanType($_POST['loanType'], $_POST['allowance'], $_POST['maxAmount'], $_POST['maxInstallment']);
    		$allowance->palert("New Loan Type Has Been Successfully Created", './loan_type.php');
    	}
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
            	<td colspan="3" align="center"><h3>New Loan Type Definition</h3></td>
            </tr>
            <tr>
            	<td colspan="3"><br /><hr size="2" /><br /></td>
            </tr>
            <?php 
            	if(sizeof($errorLog, 0)){
            		echo "
            			<tr>
			            	<td height=\"10px\"></td>
			            </tr>
			            <tr>
			            	<td colspan=\"3\" style=\"padding-left:50px\"><font class=\"error\">";
            			foreach ($errorLog as $value)
            				echo $value."<br />";
            		echo "
            				</font></td>
			            </tr>            
			            <tr>
			            	<td height=\"10px\" colspan=\"3\"><hr size=\"1\" /></td>
			            </tr>
            			<tr>
			            	<td height=\"10px\"></td>
			            </tr>";
            	}
            ?>
                        
            <tr>
            	<td align="right">Name Of The Loan </td>
                <td width="5%" align="center">:</td>
                <td align="left"><input type="text" name="loanType" style="width:250px" value="<?php echo $_POST['loanType']; ?>" /></td>
            </tr>
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
            	<td align="right">Map With Allowance Type</td>
                <td width="5%" align="center">:</td>
                <td align="left">
                		<select name="allowance" style="width:250px">
                		<?php 
                			$completeAllowanceId = $allowance->getAllowanceIds(true);
                			foreach ($completeAllowanceId as $allowanceId){
                				if($allowanceId == $_POST['allowance'])
                					echo "<option value=\"$allowanceId\" selected=\"selected\">".$allowance->getAllowanceTypeName($allowanceId)."</option>";
                				else 
                					echo "<option value=\"$allowanceId\">".$allowance->getAllowanceTypeName($allowanceId)."</option>";
                			}		
                		?>
                		</select>
                 </td>
            </tr>
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
            	<td align="right">Maximum Installments </td>
                <td align="center" width="5%">:</td>
                <td align="left">
                	<select name="maxInstallment" style="width:100px">
                	<?php 
                		for ($count = 0; $count <= 120; ++$count){
                			if($count == $_POST['maxInstallment'])
                				echo "<option value=\"$count\" selected=\"selected\">".$count."</option>";
                			else 
                				echo "<option value=\"$count\">".$count."</option>";
                		}
                			
                	?>	
                	</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (Select 0 For No Limit)	</td>
            </tr>
            <tr>
            	<td height="10px"></td>
            </tr>
            <tr>
            	<td align="right">Maximum Amount Permissible</td>
                <td align="center">:</td>
                <td align="left"><input type="text" name="maxAmount" style="width:100px" value="<?php echo $_POST['maxAmount']; ?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (Put 0 For No Limit)</td>
            </tr>
            <tr>
            	<td height="20px"></td>
            </tr>
            <tr>
            	<td align="center" colspan="3"><input type="submit" name="submit" value="Define New Loan Type" style="width:250px" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Return Back" onclick="window.location='./'" style="width:200px" /></td>
            </tr>
            <tr>
            	<td height="30px"></td>
            </tr>
        </table>
        </form>
      </div>
      <div class="sidenav">
      	<hr size="2" /><center>
        <font color="#FF0000" size="+1"><b><?php echo $allowance->getOfficerName(); ?></b></font>
       	<hr size="2" /></center><br />
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