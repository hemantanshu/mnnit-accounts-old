<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
    error_reporting(0);
  //  session_start();
    require_once './include/class.loggedInfo.php';

    if(isset ($_POST['submit']) && $_POST['submit'] == "Authenticate Myself"){
        $error = 0;

        if($_POST['username'] == "")
            ++$error;
        if($_POST['password'] == "")
            ++$error;

        if(!$error){
            $username = $_POST['username'];
            $password = $_POST['password'];

            $loggedInfo = new loggedIn();
            $loggedInfo->authenticateUser($username, $password);
        }
    }
    ob_end_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section</title>
<link rel="stylesheet" type="text/css" href="./include/default.css" media="screen" />
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body>

<div>
  <div class="top">
    <div class="header">
      <div class="left"><img class="imgright" src="img/logo.gif" alt="Forest Thistle" / height="105px">&nbsp;Accounts Department</div>
      <!--<div class="right">
        <div align="center"> MNNIT <br />
          ALLAHABAD</div>-->
      </div>
    </div>
  </div>
  <div class="container">
    <div class="navigation">
      <div class="clearer"> <span></span></div>
    </div>
    <div class="main">
      <div class="content">
        <h1 align="left">&nbsp;&nbsp;LOGIN PAGE</h1>
        <form method="post" action="" name="login" id="login">
        <table align="center" border="0">          
            <tr>
              <td>Username</td>
              <td width="30px"></td>
              <td><input type="text" name="username" value="username" onfocus="value=''" /></td>
              <td></td>
            </tr>
            <tr>
            	<td height="20px"></td>
            </tr>
            <tr>
              <td>Password</td>
              <td></td>
              <td><input type="password" name="password" value="password" onfocus="value=''" /></td>
              <td></td>
            </tr>
                          <?php
                if(isset($_POST['submit']) && $_POST['submit'] == "Authenticate Myself"){
                        echo "<tr>
            	<td colspan=\"4\" align=\"center\" height=\"60px\"><fieldset><font color=\"#FF0000\"><b>".$loggedInfo->error."</b></font></fieldset></td></tr>";
                    }
              ?>          
            <tr>
            	<td colspan="4" align="center"><font color="#FF0000"><b><?php if($error)
                                                                                    echo "Both Username & Password Field Should Be Filled"; ?></b></font></td>
            </tr>
            <tr>
            	<td height="20px"></td>
            </tr>
            <tr>
              
              <td colspan="4" align="center">
                <input type="submit" value="Authenticate Myself" name="submit" /></td>
              <td></td>
            </tr>
            <tr>
            	<td height="100px"></td>
            </tr>
        </table>        
          </form>
        <h1 align="center"><u>General Instructions</u></h1>
        <ul>
          <li>Please authenticate yourself with your username and password !!</li>
          <li>All Login attempts will be recorded !!</li>
        </ul>
      </div>
      <div class="sidenav">
        <h2>QUICK LINKS</h2>
        <ul>
          <li><a href="http://www.mnnit.ac.in" title="Home">MNNIT Home </a></li>
          <li><a href="http://www.mnnit.ac.in/index.php/contacts.html" title="Contact Us">Contact Us </a></li>
        </ul>
      </div>
      <div class="clearer"><span></span></div>
    </div>
    <div class="footer">@webteam.<a href="http://www.mnnit.ac.in" title="MNNIT">mnnit</a> Designed And Developed By Hemant Kumar Sah (B.Tech ECE 2011) Maintained by Kedar Panjiyar(CSE-2014)</div>
  </div>
</div>
</body>
</html>