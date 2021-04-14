<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
ob_start();

session_start();

require_once '../include/class.editAllowance.php';
require_once '../include/class.accountHead.php';

$allowance = new editAllowance();


if ( !$allowance->checkLogged() )
    $allowance->redirect('../');

if ( isset ($_POST) && $_POST['submit'] == "Confirm Changes To Allowances" ) {
    $error = 0;
    $errorLog = array();


    //checking if the name has been tampered with
    $allowanceId = $_POST['allowanceId'];
    $allowanceDetails = $allowance->getAllowanceDetails($allowanceId);

    if ( isset ($_POST['allowanceName']) && $_POST['allowanceName'] == "" ) {
        ++$error;
        array_push($errorLog, "The Name Of The Allowance Cannot Be Left Blank");
    }
    if ( isset ($_POST['accountHead']) && $_POST['accountHead'] == "" ) {
        ++$error;
        array_push($errorLog, "The Account Head Name Of The Allowance Cannot Be Left Blank");
    }

    if ( $allowance->isWorkInPendingStatus($allowanceId) )
        $count = $allowance->getAllowancePendingDependentCount($allowanceId);
    else
        $count = $allowance->getAllowanceDependentCount($allowanceId);


    for ( $i = 0; $i < $count; ++$i ) {
        $dependentValue = "allowanceValue0" . $i;
        $dependentName = "dependentName0" . $i;
        $dependentType = "allowanceType0" . $i;

        if ( !is_numeric($_POST[ $dependentValue ]) ) {
            ++$error;
            array_push($errorLog, "The Value Of The Allowance Has To Be Numeric && Cannot Be Left Blank");
        }
        if ( $_POST[ $dependentType ] == "" ) {
            ++$error;
            array_push($errorLog, "The Type Of The Allowance Cannot Be Left Blank");
        }
    }

    if ( $error == 0 ) {
        $extraCount = $_POST['count'];
        $i = 1;
        while ( $i < $extraCount ) {
            $dependentName = "dependentName" . $i;
            $dependentType = "allowanceType" . $i;
            $dependentValue = "allowanceValue" . $i;

            if ( !is_numeric($_POST[ $dependentValue ]) && $error == 0 ) {
                ++$error;
                array_push($errorLog, "Error in Inputs ... Please Try Again");
            }
            if ( $_POST[ $dependentType ] == "" && $error == 0 ) {
                ++$error;
                array_push($errorLog, "Error in Inputs ... Please Try Again");
            }
            ++$i;
        }
    }

    if ( $error == 0 ) {
        $allowanceName = $_POST['allowanceName'];
        $accountHead = $_POST['accountHead'];
        $update = $_POST['update'];
        $roundOff = $_POST['round'];
        $contribution = $_POST['contribution'];
        $nature = $_POST['nature'];
        $allowance->updateAllowanceNameInfo($allowanceId, $allowanceName, $accountHead, $update, $roundOff, $contribution, $nature);

        for ( $i = 0; $i < $count; ++$i ) {
            $dependentValue = "allowanceValue0" . $i;
            $dependentName = "dependentName0" . $i;
            $dependentType = "allowanceType0" . $i;
            $dependentId = "did0" . $i;
            $allowance->updateAllowanceInfo($_POST[ $dependentId ], $_POST[ $dependentValue ], $_POST[ $dependentName ], $_POST[ $dependentType ]);
        }
        if ( isset ($extraCount) )
            for ( $i = 1; $i < $extraCount; ++$i ) {

                $dependentName = "dependentName" . $i;
                $dependentType = "allowanceType" . $i;
                $dependentValue = "allowanceValue" . $i;
                $allowance->setAllowanceDetails($allowanceId, $_POST[ $dependentValue ], $_POST[ $dependentName ], $_POST[ $dependentType ]);
            }
        if ( $allowance->isAdmin() )
            $allowance->redirect('./allowance_masterUpdate.php?id=' . $allowanceId . '');
        else
            $allowance->palert("The Allowance Info Has Been Successfully Queued For Operation", $allowance->getUrlOfRedirect("ACT00"));

    }
} elseif ( isset ($_GET['id']) ) {
    $allowanceId = $_GET['id'];
    if ( !$allowance->isEditable($allowanceId) )
        $allowance->palert("This allowance Type Cannot Be Edited ", $allowance->getUrlOfRedirect("ACT00"));
} else
    $allowance->redirect($allowance->getUrlOfRedirect("ACT00"));

$accountHead = new accountHead();

ob_end_flush();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Accounts Section</title>
    <link rel="stylesheet" type="text/css" href="../include/default.css" media="screen"/>
    <script type="text/javascript" src="../include/jquery.min.js"></script>
    <script type="text/javascript" src="../include/ddaccordion.js"></script>
    <script language="javascript" type="text/javascript">
        var currenttime = "<?php
            date_default_timezone_set('Asia/Calcutta');
            print date("F d, Y H:i:s", time())?>"
        var montharray = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December")
        var serverdate = new Date(currenttime)

        function padlength(what) {
            var output = (what.toString().length == 1) ? "0" + what : what
            return output
        }

        function displaytime() {
            serverdate.setSeconds(serverdate.getSeconds() + 1)

            var datestring = montharray[serverdate.getMonth()] + " " + padlength(serverdate.getDate()) + ", " + serverdate.getFullYear()
            var timestring = padlength(serverdate.getHours()) + ":" + padlength(serverdate.getMinutes()) + ":" + padlength(serverdate.getSeconds())
            document.getElementById("servertime").innerHTML = datestring + " " + timestring
        }

        window.onload = function () {
            setInterval("displaytime()", 1000)
        }

    </script>

    <script type="text/javascript">

        ddaccordion.init({
            headerclass    : "headerbar", //Shared CSS class name of headers group
            contentclass   : "submenu", //Shared CSS class name of contents group
            revealtype     : "click", //Reveal content when user clicks or onmouseover the header? Valid value: "click", "clickgo", or "mouseover"
            mouseoverdelay : 200, //if revealtype="mouseover", set delay in milliseconds before header expands onMouseover
            collapseprev   : true, //Collapse previous content (so only one open at any time)? true/false
            defaultexpanded: [0], //index of content(s) open by default [index1, index2, etc] [] denotes no content
            onemustopen    : true, //Specify whether at least one header should be open always (so never all headers closed)
            animatedefault : false, //Should contents open by default be animated into view?
            persiststate   : true, //persist state of opened contents within browser session?
            toggleclass    : ["", "selected"], //Two CSS classes to be applied to the header when it's collapsed and expanded, respectively ["class1", "class2"]
            togglehtml     : ["", "", ""], //Additional HTML added to the header when it's collapsed and expanded, respectively  ["position", "html1", "html2"] (see docs)
            animatespeed   : "normal", //speed of animation: integer in milliseconds (ie: 200), or keywords "fast", "normal", or "slow"
            oninit         : function (headers, expandedindices) { //custom code to run when headers have initalized
                //do nothing
            },
            onopenclose    : function (header, index, state, isuseractivated) { //custom code to run whenever a header is opened or closed
                //do nothing
            }
        })

    </script>
    <script type="text/javascript">
        function loadPHPFile(str) {
            if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {// code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById("infoDiv").innerHTML = xmlhttp.responseText;
                }
            }
            xmlhttp.open("GET", str, true);
            xmlhttp.send();
        }
    </script>


    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
</head>

<body>

<div>
    <div class="top">
        <div class="header">
            <div class="left"><img class="imgright" src="../img/logo.gif" alt="Forest Thistle" height="105px">&nbsp;Accounts
                Department
            </div>
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
        <div class="clearer"><span></span></div>
    </div>

    <div class="main">
        <div class="content">
            <form action="" method="post">
                <table align="center" border="0" width="100%">

                    <tr>
                        <td colspan="3" align="center"><font color="#FF0000">Editing Allowance/Deduction
                                Information </font>
                            <hr size="1"/>
                        </td>
                    </tr>
                    <?php
                    if ( isset ($error) && $error != 0 && is_array($errorLog) ) {
                        foreach ( $errorLog as $value ) {
                            echo "<tr>
                                            <td colspan=\"3\" align=\"center\"><font class=\"error\">" . $value . "</font></td>
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
                    $allowanceDetails = $allowance->getAllowanceDetails($allowanceId);
                    if ( $allowance->isWorkInPendingStatus($allowanceId) ) {
                        $allowancePending = $allowance->getPendingallowanceInfo($allowanceId);
                        $j = 0;
                        $details = $allowance->getPendingAllowanceNameInfo($allowanceId);
                        echo "
                        <tr>
                            <td align=\"center\">Name</td>
                            <td align=\"center\"><input type=\"text\" name=\"allowanceName\" style=\"width:200px\" value=\"" . $details[0] . "\" /></td>
                            <td align=\"center\"><font class=\"green\">" . $allowance->getallowanceTypeName($allowanceId) . "</font></td>
                        </tr>
                        <tr>
                            <td colspan=\"3\" height=\"10px\"><hr size=\"1\" /></td>
                        </tr>
                        <tr>
                            <td align=\"right\">Account Head Name</td>
                            <td align=\"left\"><select name=\"accountHead\" style=\"width:200px\" >";
                        $accountHeadId = $accountHead->getAccountHeadIds(true);
                        foreach ( $accountHeadId as $value ) {
                            if ( $value == $details[1] )
                                echo "<option value=\"" . $value . "\" selected=\"selected\">" . $accountHead->getAccountHeadName($value) . "</option>";
                            else
                                echo "<option value=\"" . $value . "\">" . $accountHead->getAccountHeadName($value) . "</option>";
                        }
                        echo "			 </select>
                                                                    </td>
                                <td align=\"center\"><font class=\"green\">" . $accountHead->getAccountHeadName($accountHead->getAllowanceAccountHead($id)) . "</font></td>

                        </tr>
                        <tr>
                            <td height=\"10px\"></td>
                        </tr>
                        <tr>
                            <td align=\"right\">Allowance Nature</td>
                            <td align=\"left\"><select name=\"nature\" />";
                        if ( $details[6] == 'c' ) {
                            echo "<option value=\"c\" selected=\"selected\">Earning Head</option>";
                            echo "<option value=\"d\">Deduction Head</option>";
                        } else {
                            echo "<option value=\"d\" selected=\"selected\">Deduction Head</option>";
                            echo "<option value=\"c\">Earning Head</option>";

                        }
                        echo "</select></td>
                                <td align=\"center\">" . ( $details[6] == 'c' ? 'Earning Head' : 'Deduction Head' ) . "</td>
                        </tr>
						<tr>
                            <td height=\"10px\"></td>
                        </tr>
                        <tr>
                            <td align=\"right\">Allow Update</td>
                            <td align=\"left\"><input type=\"checkbox\" name=\"update\" value=\"y\" ";
                        if ( $details[2] == 'y' )
                            echo "checked=\"checked\"";
                        echo " /></td>
                                <td align=\"center\">" . $details[2] . "</td>
                        </tr>
						<tr>
                            <td height=\"10px\"></td>
                        </tr>
                        <tr>
                            <td align=\"right\">Allow Rounding Off</td>
                            <td align=\"left\"><input type=\"checkbox\" name=\"round\" value=\"y\" ";
                        if ( $details[4] == 'y' )
                            echo "checked=\"checked\"";
                        echo " /></td>
                                <td align=\"center\">" . $details[4] . "</td>
                        </tr>
                        <tr>
                            <td height=\"10px\"></td>
                        </tr>
                        <tr>
                            <td align=\"right\">College Contribution</td>
                            <td align=\"left\"><input type=\"checkbox\" name=\"contribution\" value=\"y\" ";
                        if ( $details[5] == 'y' )
                            echo "checked=\"checked\"";
                        echo " /></td>
                                <td align=\"center\">" . $details[5] . "</td>
                        </tr>
                        <tr>
                            <td height=\"10px\"></td>
                        </tr>";

                        $i = 0;
                        $details = $allowance->getPendingAllowanceInfo($allowanceId);
                        for($i =0; $i < sizeof($details); ++$i) {
                            $dependentValue = "allowanceValue0" . $i;
                            $dependentName = "dependentName0" . $i;
                            $dependentType = "allowanceType0" . $i;
                            $dependentId = "did0" . $i;

                            $j = $i + 1;
                            if ( $details[ $i ][4] == 'y' ) {
                                echo "
                                <tr>
                                    <td colspan=\"3\" align=\"center\"><br /><hr size=\"1\" /><br /><h2>Dependent Number : " . $j . " </h2> </td>
                                </tr>
                                <tr>
                                    <td align=\"right\">Value</td>
                                    <td align=\"center\"><input type=\"text\" name=\"$dependentValue\" style=\"width:200px\" value=\"" . $details[ $i ][1] . "\" /></td>
                                    <td align=\"center\"><font class=\"green\">" . $allowanceDetails[2] . "</font></td>
                                </tr>
                                <tr>
                                    <td height=\"5px\"></td>
                                </tr>
                                <tr>
                                    <td align=\"right\">Dependency</td>
                                    <td align=\"center\"><select name=\"" . $dependentName . "\" style=\"width:200px\">";
                                $allowanceOptions = $allowance->getAllowanceOptions();
                                if ( is_array($allowanceOptions) ) {
                                    echo "<option value=\"\">None</option>";
                                    foreach ( $allowanceOptions as $value )

                                        if ( $value == $details[ $i ][2] )
                                            echo "<option value=\"" . $value . "\" selected=\"selected\">" . $allowance->getAllowanceTypeName($value) . "</option>";
                                        else
                                            echo "<option value=\"" . $value . "\">" . $allowance->getAllowanceTypeName($value) . "</option>";
                                }

                                echo "                          </select></td>
                                    <td align=\"center\"><font class=\"green\">" . $allowance->getAllowanceTypeName($allowanceDetails[3]) . "</font></td>
                                </tr>
                                <tr>
                                    <td height=\"5px\"></td>
                                </tr>
                                <tr>
                                    <td align=\"right\">Type</td>
                                    <td align=\"center\"><select name=\"" . $dependentType . "\" style=\"width:200px\">";
                                if ( $details[ $i ][3] == 'c' ) {
                                    echo "
                                                            <option value=\"c\">Credit</option>
                                                            <option value=\"d\">Debit</option>";
                                } else {
                                    echo "
                                                            <option value=\"d\">Debit</option>
                                                            <option value=\"c\">Credit</option>";


                                }

                                echo "                        </select></td>
                                    <td align=\"center\"><font class=\"green\">";
                                if ( $allowanceDetails[4] == 'c' )
                                    echo "Credit";
                                else
                                    echo "Debit";
                                echo "</font></td>
                                </tr>
                                <tr>
                                    <td height=\"5px\"><input type=\"hidden\" name=\"" . $dependentId . "\" value=\"" . $details[ $i ][0] . "\" /></td>
                                </tr>
                                ";
                            } else {
                                $allowance->redirect("./allowance_ddrop?did=" . $details[ $i ][0] . "");
                            }
                        }
                    } else {

                        echo "
                        <tr>
                            <td colspan=\"3\" height=\"10px\"><hr size=\"1\" /></td>
                        </tr>
                        <tr>
                            <td align=\"right\">Add More Dependencies</td>
                            <td align=\"Center\"><select name=\"count\" style=\"width:50px\" onfocus=\"loadPhpFile(this.value)\">";
                        $i = 1;
                        while ( $i < 100 ) {
                            echo "<option OnClick=\"loadPHPFile('getOptionField.php?value=" . $i . "')\" value=\"" . $i . "\">" . $i . "</option>";
                            ++$i;
                        }
                        echo "       	</select></td>
                        </tr>
                        <tr>
                                <td colspan=\"3\" align=\"center\" height=\"30px\"><hr size=\"1\" /></td>
                        </tr> 	";
                        echo "                        
                        <tr>
                            <td align=\"center\">Name</td>
                            <td align=\"center\"><input type=\"text\" name=\"allowanceName\" style=\"width:200px\" value=\"" . $allowance->getallowanceTypeName($allowanceId) . "\" /></td>
                            <td align=\"center\"><font class=\"green\">" . $allowance->getallowanceTypeName($allowanceId) . "</font></td>
                        </tr>
                        <tr>
                            <td height=\"10px\"></td>
                        </tr>
                        <tr>
                            <td align=\"center\">Account Head Name</td>
                            <td align=\"center\"><select name=\"accountHead\" style=\"width:200px\" >";
                        $details = $allowance->getallowanceHeadDetails($allowanceId);
                        $accountHeadId = $accountHead->getAccountHeadIds(true);
                        foreach ( $accountHeadId as $value ) {
                            if ( $value == $details[3] )
                                echo "<option value=\"" . $value . "\" selected=\"selected\">" . $accountHead->getAccountHeadName($value) . "</option>";
                            else
                                echo "<option value=\"" . $value . "\">" . $accountHead->getAccountHeadName($value) . "</option>";
                        }
                        echo "			 </select>
                                                                    </td>

                             <td align=\"center\"><font class=\"\">" . $accountHead->getAccountHeadName($details[3]) . "</font></td>

                        </tr>

                        <tr>
                            <td height=\"10px\"></td>
                        </tr>
                        <tr>
                            <td align=\"center\">Allowance Nature</td>
                            <td align=\"center\"><select name=\"nature\" style=\"width:200px\"/>";
                        if ( $details[8] == 'c' ) {
                            echo "<option value=\"c\" selected=\"selected\">Earning Head</option>";
                            echo "<option value=\"d\">Deduction Head</option>";
                        } else {
                            echo "<option value=\"d\" selected=\"selected\">Deduction Head</option>";
                            echo "<option value=\"c\">Earning Head</option>";

                        }
                        echo "</select></td>
                                <td align=\"center\">" . ( $details[8] == 'c' ? 'Earning Head' : ( $details[8] == 'd' ? 'Deductino Head' : 'Not Set' ) ) . "</td>
                        </tr>
						<tr>
                            <td height=\"10px\"></td>
                        </tr>
                        <tr>
                            <td align=\"center\">Allow Update</td>
                            <td align=\"center\"><input type=\"checkbox\" name=\"update\" value=\"y\" ";
                        if ( $details[4] == 'y' )
                            echo "checked=\"checked\"";
                        echo " /></td>
                                <td align=\"center\">" . $details[4] . "</td>
                        </tr>
                        <tr>
                            <td height=\"10px\"></td>
                        </tr>
                        <tr>
                            <td align=\"center\">Allow Rounding Off</td>
                            <td align=\"center\"><input type=\"checkbox\" name=\"round\" value=\"y\" ";
                        if ( $details[5] == 'y' )
                            echo "checked=\"checked\"";
                        echo " /></td>
                                <td align=\"center\">" . $details[5] . "</td>
                        </tr>
                        <tr>
                            <td height=\"10px\"></td>
                        </tr>
                        <tr>
                            <td align=\"center\">College Contribution</td>
                            <td align=\"center\"><input type=\"checkbox\" name=\"contribution\" value=\"y\" ";
                        if ( $details[6] == 'y' )
                            echo "checked=\"checked\"";
                        echo " /></td>
                                <td align=\"center\">" . $details[6] . "</td>
                        </tr>
                        <tr>
                            <td height=\"10px\"></td>
                        </tr>
                        <tr>
                            <td colspan=\"3\" height=\"10px\"><hr size=\"1\" /></td>
                        </tr>
                        <tr>
                            <td colspan=\"3\" align=\"center\" width=\"100%\">
                                <table align=\"center\" width=\"100%\" border=\"1px\">
                                    <tr>
                                    <th width=\"5%\">SN</th>
                                    <th width=\"20%\">Magnitude</th>
                                    <th width=\"50%\">Dependent</th>
                                    <th width=\"20%\">Type</th>
                                    <th width=\"5%\">Drop</th>
                                </tr>
                                <tr>
                                    <td height=\"10px\" colspan=\"5\" align=\"center\"> <br /><font class=\"error\">Previous Dependencies</font> </td>
                                </tr>
                                    ";
                        $count = $allowance->getAllowanceDependentCount($allowanceId);
                        for ( $i = 0; $i < $count; ++$i ) {
                            $dependentValue = "allowanceValue0" . $i;
                            $dependentName = "dependentName0" . $i;
                            $dependentType = "allowanceType0" . $i;
                            $dependentId = "did0" . $i;
                            $j = $i + 1;

                            echo "
                                <tr>
                                    <td align=\"center\">$j</td>
                                    <td align=\"center\"><input type=\"hidden\" name=\"" . $dependentId . "\" value=\"" . $allowanceDetails[ $i ][0] . "\" /><input type=\"text\" name=\"" . $dependentValue . "\" value=\"" . $allowanceDetails[ $i ][2] . "\" style=\"width:200px\" /></td>
                                    <td align=\"center\">
                                            <select name=\"" . $dependentName . "\" style=\"width:200px\">
                                                <option value=\"\">None</option>";
                            $allowanceOptions = $allowance->getAllowanceOptions();
                            if ( is_array($allowanceOptions) ) {
                                foreach ( $allowanceOptions as $value ) {
                                    if ( $allowanceDetails[ $i ][3] == $value )
                                        echo "<option value=\"" . $value . "\" selected=\"selected\">" . $allowance->getAllowanceTypeName($value) . "</option>";
                                    else
                                        echo "<option value=\"" . $value . "\">" . $allowance->getAllowanceTypeName($value) . "</option>";
                                }
                            }
                            echo "                                        </select></td>
                                    <td align=\"center\"><select name=\"" . $dependentType . "\" style=\"width:200px\">";
                            if ( $allowanceDetails[ $i ][4] == 'c' )
                                echo "
                                           <option value=\"c\">Credit</option>
                                           <option value=\"d\">Debit</option>";
                            else
                                echo "
                                             <option value=\"d\">Debit</option>
                                            <option value=\"c\">Credit</option>
                                           ";

                            echo "                </select></td>
                                    <td align=\"center\"><a href=\"./allowance_ddrop.php?did=" . $allowanceDetails[ $i ][0] . "\" target=\"_parent\"><img src=\"../img/b_drop.png\" alt=\"drop\" /></a></td>
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
                        <td height="20px"><input type="hidden" name="allowanceId" value="<?php echo $allowanceId; ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center"><input type="submit" name="submit"
                                                              value="Confirm Changes To Allowances"/>&nbsp;&nbsp;
                            <input type="button" onclick="window.location='<?php echo $_SERVER['HTTP_REFERER']; ?>'"
                                   value=" Return Back "/></td>
                    </tr>


                </table>
            </form>
        </div>
        <div class="sidenav">
            <hr size="2"/>
            <center><font color="#FF0000" size="+1"><b><?php echo $allowance->getOfficerName(); ?></b></font></center>
            <hr size="2"/>
            <br/>
            <h2><font color="#008000">QUICK NAVIGATION PANEL</font></h2>
            <?php
            include './navigation/allowance.php';
            ?>
        </div>
        <div class="clearer"><span></span></div>
    </div>
    <div class="footer">@webteam.<a href="http://www.mnnit.ac.in" title="MNNIT">mnnit</a> Designed And Developed By
        Hemant Kumar Sah (B.Tech ECE 2011) Kedar Panjiyar(CSE 2010-14)
    </div>
</div>
</div>
</body>
</html>