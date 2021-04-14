<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();

    session_start();

    require_once '../include/class.allowance.php';
    require_once '../include/class.accountHead.php';
    
    $allowance = new allowance();
    $accountHead = new accountHead();
    
    if(!$allowance->checkLogged())
        $allowance->redirect('../');


    if(count($_POST) > 0 && $_POST['submit'] == "Confirm New allowance Type"){
        $error = 0;
        $errorLog = array();
        
        $count = $_POST['count'];
       
        if($_POST['allowanceName'] == ""){
            $error++;
            array_push($errorLog, "Please Enter The Name Of The New Allowance");
        }
        if($_POST['accountHead'] == ""){
            $error++;
            array_push($errorLog, 'Please Choose The Account Head');
        }

        $i = 0;
        while($i < $count){
            $dependentName = "dependentName".$i;
            $dependentType = "allowanceType".$i;
            $dependentValue  = "allowanceValue".$i;
            ++$i;         	
            
            if($_POST[$dependentType] == "" && $error == 0){
                $error++;
                array_push($errorLog, "Wrong Entry Filling -- Please Fill Up Again");
            }
            if(($_POST[$dependentValue] == "" || !is_numeric($_POST[$dependentValue]) )&& $error == 0){
                $error++;
                array_push($errorLog, "Wrong Entry Filling -- Please Fill Up Again");
            }
            if($error != 0)
                break;
        }
        if($error == 0){
            $i = 0;
            $allowanceName = ucwords(strtolower($_POST['allowanceName']));
            $allowanceHead = $_POST['accountHead'];
            $round = $_POST['round'] == 1 ? 'y' : 'n';
            $update = $_POST['update'] == 1 ? 'y' : 'n';
            $contribution = $_POST['contribution'] == 1 ? 'y' : 'n';
            $nature = $_POST['nature'];
	
            $allowanceId = $allowance->setAllowanceNameDetails($allowanceName, $allowanceHead, $update, $round, $contribution, $nature);
            while($i < $count){
                $dependentName = "dependentName".$i;
                $dependentType = "allowanceType".$i;
                $dependentValue  = "allowanceValue".$i;
                ++$i;

                $dependentName = $_POST[$dependentName];
                $dependentType = $_POST[$dependentType];
                $dependentValue = $_POST[$dependentValue];
                
                $allowance->setAllowanceDetails($allowanceId, $dependentValue, $dependentName, $dependentType);
            }
            if($allowance->isAdmin())
                    $allowance->palert("The Allowance Type Has Been Successfully Added ", $allowance->getUrlOfRedirect("ACT00"));
            else
                $allowance->palert("The Allowance Type Has Been Added And Will Be In Pending Till Final Consent", $allowance->getUrlOfRedirect("ACT00"));
        }
    }

    ob_end_flush();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- allowance/deduction Master</title>
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
<script type="text/javascript">
	function loadPHPFile(str)
	{
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	  }
	else
	  {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	xmlhttp.onreadystatechange=function()
	  {
	  if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
		document.getElementById("infoDiv").innerHTML=xmlhttp.responseText;
		}
	  }
	xmlhttp.open("GET",str,true);
	xmlhttp.send();
}
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
      <div class="content">
        <table align="center" border="0" width="100%">
        	<!-- insertion of new departments will be done here -->

            <tr>
            	<td align="center" >
                	<form action="" method="post">
                	<table border="0" align="center" width="100%">
                        <tr>
                        	<td colspan="4" align="center"><h2>New Allowance/Deduction Type Entry</h2><br /><hr size="1" /></td>
                        </tr>

                        <?php
							if(isset ($error) && $error != 0 && is_array($errorLog)){
								foreach ($errorLog as $value) {
									echo "<tr>
											<td colspan=\"4\" align=\"center\"><font class=\"error\">".$value."</font></td>
										</tr>";
								}
							}
							echo "
								<tr>
									<td height=\"10px\"></td>
								</tr>";

						?>
                    	<tr>
                            <td align="right" width="300px">Name Of New Allowance/Deduction Type</td>
                            <td width="20px"></td>
                            <td align="left"><input type="text" name="allowanceName" value="<?php echo $_POST['allowanceName']; ?>" style="width:200px" /></td>
                            <td width="20px"></td>

                        </tr>
                        <tr>
                        	<td height="10px"></td>
                        </tr>
                        <tr>
                            <td align="right" width="300px">Select The Account Head Name</td>
                            <td width="20px"></td>
                            <td align="left"><select name="accountHead" style="width:200px" >
                            			<?php
                                                    $accountHeadId = $accountHead->getAccountHeadIds(true);
                                                    foreach ($accountHeadId as $value) {
                                                        echo "<option value=\"".$value."\">".$accountHead->getAccountHeadName($value)."</option>";
                                                    }
                                                ?>
                                             </select>
                                            </td>
                            <td width="20px"></td>

                        </tr>
                        <tr>
                        	<td height="10px"></td>
                        </tr>
                        <tr>
                            <td align="right" width="300px">Allowance Nature</td>
                            <td width="20px"></td>
                            <td align="left"><select name="nature" style="width:200px" >
                                                <option value="c">Earning Head</option>
                                                <option value="d">Deduction Head</option>
                                             </select>
                                            </td>
                            <td width="20px"></td>

                        </tr>
                        <tr>
                        	<td height="10px"></td>
                        </tr>
                        <tr>
                            <td align="right" width="300px">Allow Update From Employee</td>
                            <td width="20px"></td>
                            <td align="left"><input type="checkbox" name="update" value="1" /></td>
                            <td width="20px"></td>
                        </tr>
                        <tr>
                        	<td height="10px"></td>
                        </tr>
                        <tr>
                            <td align="right" width="300px">Allow Round Off</td>
                            <td width="20px"></td>
                            <td align="left"><input type="checkbox" name="round" value="1" /></td>
                            <td width="20px"></td>
                        </tr>
                        <tr>
                        	<td height="10px"></td>
                        </tr>
                        <tr>
                            <td align="right" width="300px">College Contribution</td>
                            <td width="20px"></td>
                            <td align="left"><input type="checkbox" name="contribution" value="1" /></td>
                            <td width="20px"></td>
                        </tr>
                        <tr>                        
                        	<td height="10px"></td>
                        </tr>
                        <tr>
                            <td align="right">Select Number Of Dependencies</td>
                            <td></td>
                            <td align="left"><select name="count" style="width:50px" onfocus="loadPhpFile(this.value)">
                            					<?php
                                                                    $i = 1;
                                                                    while($i < 100){
                                                                        echo "<option OnClick=\"loadPHPFile('getOptionField.php?value=".$i."')\" value=\"".$i."\">".$i."</option>";
                                                                        ++$i;
                                                                    }
                                                                ?>
                            				</select></td>
                        </tr>
                        <tr>
                        	<td colspan="4" align="center" width="100%">
                            	<table align="center" width="100%" border="1">
                                	<tr>
                                    	<th width="5%">SN</th>
                                    	<th width="20%">Magnitude</th>
                                        <th width="50%">Dependent</th>
                                        <th width="25%">Type</th>
                                    </tr>
                                    <tr>
                                    	<td height="4px" colspan="3"></td>
                                    </tr>
                                    <tr>
                                    	<td align="center">1</td>
                                    	<td align="center"><input type="text" name="allowanceValue0" value="<?php echo $_POST['houseValue']; ?>" style="width:200px" /></td>
                                        <td align="center">
                                        				<select name="dependentName0" style="width:200px">
                                                            <option value="">None</option>
                                                            <?php
                                                                $allowanceOptions = $allowance->getAllowanceOptions();
                                                                if(is_array($allowanceOptions)){
                                                                    foreach($allowanceOptions as $value)
                                                                        echo "<option value=\"".$value."\">".$allowance->getAllowanceTypeName($value)."</option>";
                                                                }
                                                            ?>
                                                                        
                                                        </select></td>
                                 		<td align="center"><select name="allowanceType0" style="width:200px">
                                                            <option value="c">Credit</option>
                                                            <option value="d">Debit</option>
                                                        </select></td>                       
                                    </tr>
                                    <tr>
                                    	<td colspan="4" align="center">
                                        	<div id="infoDiv"></div>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                        	<td height="30px"></td>
                        </tr>
                        <tr>
                        	<td align="center" colspan="4"><input type="submit" name="submit" value="Confirm New allowance Type" /></td>
                        </tr>
                        <tr>
                        	<td height="60px"></td>
                        </tr>
                    </table>
                    </form>
                </td>
            </tr>

            <tr>
            	<td height="10px"><hr size="1" /></td>
            </tr>


        </table>
      </div>
      <div class="sidenav">
      	<hr size="2" />
        <center> <font color="#FF0000" size="+1"><b><?php echo $allowance->getOfficerName(); ?></b></font></center>
       	<hr size="2" /><br />
        <h2><font color="#008000">QUICK NAVIGATION PANEL</font></h2>
        <?php
            include './navigation/allowance.php';
        ?>
      </div>
      <div class="clearer"><span></span></div>
    </div>
    <div class="footer">@webteam.<a href="http://www.mnnit.ac.in" title="MNNIT">mnnit</a> Designed And Developed By Hemant Kumar Sah (B.Tech ECE 2011) Kedar Panjiyar(CSE 2010-14)</div>
  </div>
</div>
</body>
</html>