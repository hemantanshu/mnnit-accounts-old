<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();

    session_start();

    require_once '../include/class.editDesignation.php';
    $designation = new editDesignation();

    if(!$designation->checkLogged())
        $designation->redirect('../');

    if(isset ($_POST) && $_POST['submit'] == "Confirm Changes To Designations"){
        $error = 0;
        $errorLog = array();

        //checking if the name has been tampered with
        $designationId = ucwords(strtolower($_POST['designationId']));
        if(!$designation->isEditable($designationId))
            $designation->palert("This Designation Type Cannot Be Edited ", "./designation.php");

        if(isset ($_POST['designationName']) && $_POST['designationName'] == ""){
            ++$error;        
            array_push($errorLog, "The Name Of The designation Cannot Be Left Blank");
        }
        
        for($i = 0; $i < 100; ++$i){
            $dependentValue = "designationValue0".$i;
            if(!isset ($_POST[$dependentValue]))
                break;
            
            if(!is_numeric($_POST[$dependentValue])){
                ++$error;
                array_push($errorLog, "The Value Of The Designation Has To Be Numeric && Cannot Be Left Blank");
            }            
        }
        $count = $i;
        if($error == 0){
            $extraCount = $_POST['count'];
            $i = 0;
            while($i < $extraCount){
                $dependentValue  = "designationValue".$i;

                if(!is_numeric($_POST[$dependentValue]) && $error == 0){
                    ++$error;
                    array_push($errorLog, "Error in Inputs ... Please Try Again");
                }
                ++$i;
            }
        }
        if($error == 0){
            if(isset ($_POST['designationName'])){        
                $designation->updateDesignationName($designationId, $_POST['designationName']);
            }            
            for($i = 0; $i < $count; ++$i){
                $dependentValue = "designationValue0".$i;
                $dependentName = "dependentName0".$i;
                $dependentId = "did0".$i;                
                $designation->updateDesignationDependenceInfo($_POST[$dependentId], $_POST[$dependentValue], $_POST[$dependentName]);
            }
            if(isset ($extraCount)){            
                for($i = 0; $i < $extraCount; ++$i){
                    $dependentName = "dependentName".$i;
                    $dependentValue  = "designationValue".$i;
                    $designation->setDesignationDependency($designationId, $_POST[$dependentValue], $_POST[$dependentName]);
                }
            }
            if($designation->isAdmin())
                $designation->palert("The designation Info Has Been Successfully Updated", "./designation.php");
            else
                $designation->palert("The designation Info Has Been Successfully Queued For Operation", "./designation.php");
        }
    }elseif(isset ($_GET['id'])){
        $designationId = $_GET['id'];
        if(!$designation->isEditable($designationId))
            $designation->palert("This Designation Type Cannot Be Edited ", "./designation.php");
    }else
        $designation->redirect("./designation.php");



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
          <form action="" method="post">
        <table align="center" border="0" width="100%">

            <tr>
                <td colspan="3" align="center"><font color="#FF0000">Editing designation Information </font><hr size="1" /></td>
            </tr>
            <?php
                if(isset ($error) && $error != 0 && is_array($errorLog)){
                    foreach ($errorLog as $value) {
                            echo "<tr>
                                            <td colspan=\"3\" align=\"center\"><font class=\"error\">".$value."</font></td>
                                    </tr>";
                    }
                }
                                    echo "
                                            <tr>
                                                    <td height = \"10px\"></td>
                                            </tr>";
            ?>
            <tr>
                <th width="30%"></th>
                <th width="35%">New Value</th>
                <th width="35%">Old Value</th>
            </tr>
            <?php
                if($designation->isWorkInPendingStatus($designationId)){
                        $i = 0;
                        
                        echo "
                            <tr>
                                <td align=\"center\">Name</td>
                                <td align=\"center\"><input type=\"text\" name=\"designationName\" style=\"width:200px\" value=\"".$designation->getDesignationTypeName($designationId, false)."\" /></td>
                                <td align=\"center\"><font class=\"green\">".$designation->getDesignationTypeName($designationId, true)."</font></td>
                            </tr>
                            </tr>
                            <tr>
                                <td colspan=\"3\" height=\"10px\"><hr size=\"1\" /></td>
                            </tr> ";                        

                        $designationPending = $designation->getDesignationDependents($designationId, false);

                        foreach ($designationPending as $value){
                            $dependentValue = "designationValue0".$i;
                            $dependentName = "dependentName0".$i;
                            $dependentId = "did0".$i;

                            $j = $i + 1;
                            
                            $designationPendingDetails = $designation->getDesignationDependentDetails($value, false);
                            
                            $designationOriginalDetails = $designation->getDesignationDependentDetails($value, true);
                            if($designationPendingDetails[4] == "y"){
                                echo "
                                    <tr>
                                        <td colspan=\"3\" align=\"center\"><br /><hr size=\"1\" /><br /><h2>Dependent Number : ".$j." </h2> </td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Value</td>
                                        <td align=\"center\"><input type=\"text\" name=\"$dependentValue\" style=\"width:200px\" value=\"".$designationPendingDetails[2]."\" /></td>
                                        <td align=\"center\"><font class=\"green\">".$designationOriginalDetails[2]."</font></td>
                                    </tr>
                                    <tr>
                                        <td height=\"5px\"></td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Dependency</td>
                                        <td align=\"center\"><select name=\"".$dependentName."\" style=\"width:200px\">";
                                                               echo "<option value=\"\"></option>";
                                                                $designationOptions = $designation->getDesignationOptions();
                                                                if(is_array($designationOptions)){
                                                                    foreach($designationOptions as $options)

                                                                        if($options == $designationPendingDetails[3])
                                                                            echo "<option value=\"".$options."\" selected=\"selected\">".$designation->getAllowanceTypeName($options)."</option>";
                                                                        else
                                                                            echo "<option value=\"".$options."\">".$designation->getAllowanceTypeName($options)."</option>";
                                                                }

                                echo"                          </select></td>
                                        <td align=\"center\"><font class=\"green\">".$designation->getAllowanceTypeName($designationOriginalDetails[3])."</font></td>
                                    </tr>
                                    <tr>
                                        <td height=\"5px\"></td>
                                    </tr>
                                    <tr>
                                        <td height=\"5px\"><input type=\"hidden\" name=\"".$dependentId."\" value=\"".$value."\" /></td>
                                    </tr>
                                    ";
                            }
                            else{
                                $designation->redirect("./designation_ddrop.php?did=".$value."");
                            }
                        }
                }
                else{
                    echo "
                        <tr>
							<td colspan=\"3\" height=\"10px\"><hr size=\"1\" /></td>
						</tr>
						<tr>
                            <td align=\"right\">Add More Dependencies</td>
                            <td align=\"Center\"><select name=\"count\" style=\"width:50px\" onfocus=\"loadPhpFile(this.value)\">";

                                                                    $i = 0;
                                                                    while($i < 100){
                                                                        echo "<option OnClick=\"loadPHPFile('getDesignationOptionField.php?value=".$i."')\" value=\"".$i."\">".$i."</option>";
                                                                        ++$i;
                                                                    }


                    echo "        				</select></td>
                        </tr>
						<tr>
							<td colspan=\"3\" align=\"center\" height=\"30px\"><hr size=\"1\" /></td>
						</tr> 	";
                    echo "
                        <tr>
                            <td align=\"center\">Name</td>
                            <td align=\"center\"><input type=\"text\" name=\"designationName\" style=\"width:200px\" value=\"".$designation->getDesignationTypeName($designationId, true)."\" /></td>
                            <td align=\"center\"><font class=\"green\">".$designation->getDesignationTypeName($designationId, true)."</font></td>
                        </tr>
                        </tr>
                        <tr>
                            <td colspan=\"3\" height=\"10px\"><hr size=\"1\" /></td>
                        </tr>
                        <tr>
                        	<td colspan=\"3\" align=\"center\" width=\"100%\">
                                    <table align=\"center\" width=\"100%\" border=\"1px\">
                                	<tr>
                                    	<th width=\"5%\">SN</th>
                                    	<th width=\"25%\">Magnitude</th>
                                        <th width=\"50%\">Dependent</th>
                                        <th width=\"5%\">Drop</th>
                                    </tr>
                                    <tr>
                                    	<td height=\"10px\" colspan=\"5\" align=\"center\"> <br /><font class=\"error\">Previous Dependencies</font> </td>
                                    </tr>
                                    ";
                       $dependents = $designation->getDesignationDependents($designationId, true);
                       $i = 0;
                       foreach ($dependents as $value){
                            $dependentValue = "designationValue0".$i;
                            $dependentName = "dependentName0".$i;
                            $dependentId = "did0".$i;
                            $i++;
                            $designationOriginalDetails = $designation->getDesignationDependentDetails($value, true);
                           echo "

                                <tr>
                                    <td align=\"center\">$i</td>
                                    <td align=\"center\">
                                            <input type=\"hidden\" name=\"".$dependentId."\" value=\"".$value."\" />
                                            <input type=\"text\" name=\"".$dependentValue."\" value=\"".$designationOriginalDetails[2]."\" style=\"width:200px\" /></td>
                                    <td align=\"center\">
                                                    <select name=\"".$dependentName."\" style=\"width:200px\">
                                                        <option value=\"\">None</option>";
                                                            $designationOptions = $designation->getDesignationOptions();
                                                            if(is_array($designationOptions)){
                                                                foreach($designationOptions as $options){
                                                                    if($designationOriginalDetails[3] == $options)
                                                                        echo "<option value=\"".$options."\" selected=\"selected\">".$designation->getAllowanceTypeName($options)."</option>";
                                                                    else
                                                                        echo "<option value=\"".$options."\">".$designation->getAllowanceTypeName($options)."</option>";
                                                                }
                                                            }
                            echo "                                        </select></td>
                                    
                                    <td align=\"center\"><a href=\"./designation_ddrop.php?did=".$value."\" target=\"_parent\"><img src=\"../img/b_drop.png\" alt=\"drop\" /></a></td>
                                </tr>";
                       }
                    echo "


                        			<tr>
                                    	<td height=\"10px\" colspan=\"5\" align=\"center\"> <br /><font class=\"error\">New Dependencies</font> </td>
                                    </tr>
                                    <tr>
                                    	<td colspan=\"4\" align=\"center\">
                                        	<div id=\"infoDiv\"></div>
                                        </td>
                                    </tr>
                                </table>
                             </td>
                          </tr>
                        ";

                }
            ?>
            <tr>
            	<td height="20px"><input type="hidden" name="designationId" value="<?php echo $designationId; ?>" /></td>
            </tr>
            <tr>
            	<td colspan="3" align="center"><input type="submit" name="submit" value="Confirm Changes To Designations" />&nbsp;&nbsp;
                    <input type="button" onclick="window.location='<?php echo $_SERVER['HTTP_REFERER']; ?>'" value=" Return Back " /></td>
            </tr>


        </table></form>
      </div>
      <div class="sidenav">
      	<hr size="2" /><center>
        <font color="#FF0000" size="+1"><b><?php echo $designation->getOfficerName(); ?></b></font></center>
       	<hr size="2" /><br />
        <h2><font color="#008000">QUICK NAVIGATION PANEL</font></h2>
        <?php
            include './navigation/designation.php';
        ?>
      </div>
      <div class="clearer"><span></span></div>
    </div>
    <div class="footer">@webteam.<a href="http://www.mnnit.ac.in" title="MNNIT">mnnit</a> Designed And Developed By Hemant Kumar Sah (B.Tech ECE 2011)</div>
  </div>
</div>
</body>
</html>