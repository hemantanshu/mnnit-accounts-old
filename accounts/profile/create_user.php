<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    //error_reporting(0);
    require_once '../include/class.admin.php';

    $admin = new admin();
    $setUserCreation = false;

    if($_POST['submit'] == "Create A New Officer"){

        $name = $_POST['name'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $password1 = $_POST['password1'];
        $rank = $_POST['rank'];

        $error = 0;
        $errorLog = array();

        if($name == ""){
            ++$error;
            array_push($errorLog, "Please Enter The Name Of The Officer");
        }
        if($password != $password1 || strlen($password) < 6){
            ++$error;
            array_push($errorLog, "Both The Passwords Should Be Same And Minimum Of 6 Characters");
        }
        if($username == ""){
            ++$error;
            array_push($errorLog, "Please Enter The username Of The Officer");
        }
        if(!$admin->checkUserName($username)){
            ++$error;
            array_push($errorLog, "Another User With The Same Username Exists");
        }
        if($rank != "operator" && $rank != "supervisor" && $rank != "admin" && $rank != "loan"){
            ++$error;
            array_push($errorLog, "Please Select The Rank Of The Officer");
        }
        if($error == 0)
            $setUserCreation = true;
    }
    if($_POST['submit'] == "Confirm Officer Details"){

        $name = $_POST['name'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['rank'];

        $admin->createUser($name, $username, $password, $role);
        $admin->palert("Officer Has Been Created", "./");
    }

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
      <div class="content">
        <form action="" method="post">
        <table align="center" border="0" width="100%">
                        <?php
                if(isset ($error) && $error != 0 && is_array($errorLog)){
                    foreach ($errorLog as $value) {
                            echo "<tr>
                                            <td colspan=\"1\" align=\"center\"><font class=\"error\">".$value."</font></td>
                                    </tr>";
                    }
                }
                                    echo "
                                            <tr>
                                                    <td height = \"10px\"></td>
                                            </tr>";
            ?>

            <tr>
            	<td align="right" width="100%">
                    <?php
                        if(!$setUserCreation){
                            echo "
                                <table align=\"center\" border=\"0\" width=\"100%\">
                                    <tr>
                                        <td colspan=\"3\" align=\"center\"><h2>New Officer Creating Page</h2><br /><hr size=\"2\" /><br /></td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\" width=\"30%\">Name Of Officer :</td>
                                        <td></td>
                                        <td align=\"left\"><input type=\"text\" name=\"name\" style=\"width:300px\" value=\"".$_POST['name']."\" /></td>
                                    </tr>
                                    <tr>
                                        <td height=\"10px\"></td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Username Of Officer :</td>
                                        <td></td>
                                        <td align=\"left\"><input type=\"text\" name=\"username\" style=\"width:300px\" value=\"".$_POST['username']."\" /></td>
                                    </tr>
                                    <tr>
                                        <td height=\"10px\"></td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Password :</td>
                                        <td></td>
                                        <td align=\"left\"><input type=\"password\" name=\"password\" style=\"width:300px\" value=\"".$_POST['']."\" /></td>
                                    </tr>
                                    <tr>
                                        <td height=\"10px\"></td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Password (confirm) :</td>
                                        <td></td>
                                        <td align=\"left\"><input type=\"password\" name=\"password1\" style=\"width:300px\" /></td>
                                    </tr>
                                    <tr>
                                        <td height=\"10px\"></td>
                                    </tr>
                                    <tr>
                                        <td align=\"right\">Role Of Officer :</td>
                                        <td></td>
                                        <td align=\"left\">
                                                            <select name=\"rank\" style=\"width:300px\" >
                                                            <option value=\"admin\">Admin</option>
                                                            <option value=\"supervisor\">Supervisor</option>
                                                            <option value=\"operator\">Operator</option>
                                                            <option value=\"loan\">Loan Officer</option>
                                                                        </select></td>
                                    </tr>
                                    <tr>
                                        <td height=\"30px\"></td>
                                    </tr>
                                     <tr>
                                        <td align=\"center\" colspan=\"3\">
                                            <input type=\"submit\" name=\"submit\" value=\"Create A New Officer\" style=\"width:300px;\"/>&nbsp;&nbsp;&nbsp;
                                            <input type=\"button\" value=\"Return Back\" onclick=\"window.location='./'\" style=\"width:200px;\" /></td>
                                    </tr>

                                </table>
                                ";
                        }else{
                            echo "
                                <input type=\"hidden\" name=\"name\" value=\"".$_POST['name']."\" />
                                <input type=\"hidden\" name=\"username\" value=\"".$_POST['username']."\" />
                                <input type=\"hidden\" name=\"password\" value=\"".$_POST['password']."\" />
                                <input type=\"hidden\" name=\"rank\" value=\"".$_POST['rank']."\" />";
                            echo "
                                <table align=\"center\" border=\"0\" width=\"100%\">
                                <tr>
                                    <td colspan=\"3\" align=\"center\"><font class=\"error\">Confirm New Officer Details</font><br /><hr size=\"2\" /><br /></td>
                                </tr>
                                <tr>
                                    <td align=\"right\" width=\"30%\">Name Of Officer :</td>
                                    <td></td>
                                    <td align=\"left\"><font class=\"green\">".$_POST['name']."</font></td>
                                </tr>
                                <tr>
                                    <td height=\"10px\"></td>
                                </tr>
                                <tr>
                                    <td align=\"right\">Username Of Officer :</td>
                                    <td></td>
                                    <td align=\"left\"><font class=\"green\">".$_POST['username']."</font></td>
                                </tr>
                                <tr>
                                    <td height=\"10px\"></td>
                                </tr>
                                <tr>
                                    <td align=\"right\">Password :</td>
                                    <td></td>
                                    <td align=\"left\"><font class=\"green\">".$_POST['password']."</font></td>
                                </tr>
                                <tr>
                                    <td height=\"10px\"></td>
                                </tr>
                                <tr>
                                    <td height=\"10px\"></td>
                                </tr>
                                <tr>
                                    <td align=\"right\">Role Of Officer :</td>
                                    <td></td>
                                    <td align=\"left\"><font class=\"green\">".$_POST['rank']."</font></td>
                                </tr>
                                <tr>
                                    <td height=\"10px\"></td>
                                </tr>
                                <tr>
                                    <td align=\"center\" colspan=\"3\">
                                    <input type=\"submit\" name=\"submit\" value=\"Confirm Officer Details\" style=\"width:300px\" />&nbsp;&nbsp;&nbsp;
                                    <input type=\"button\" value=\"Return Back\" onclick=\"window.location='./'\" style=\"width:200px;\" /></td></td>
                                </tr>
                            </table>
                    ";
                        }
                    ?>
                </td>
            </tr>
        </table>
        </form>
      </div>
      <div class="sidenav">
      	<hr size="2" />
        <center><font color="#FF0000" size="+1"><b><?php echo $admin->getOfficerName(); ?></b></font></center>
       	<hr size="2" /><br />
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
        
        