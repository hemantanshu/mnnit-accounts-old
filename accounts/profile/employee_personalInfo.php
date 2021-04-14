<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
    ////error_reporting(0)

    session_start();

    require_once '../include/class.loginInfo.php';
    require_once '../include/class.personalInfo.php';

    $loggedInfo = new loginInfo();

    if(!$loggedInfo->checkLogged())
            exit (0);

    if(isset ($_GET['id']) && isset ($_GET['type'])){
        $employeeId = $_GET['id'];
        $type = $_GET['type'];
    }else
       exit (0);

    $personalInfo = new personalInfo();
    $personalInfo->getEmployeeInformation($employeeId, true);

    function printMenu($employeeId, $type, $personalInfo){
        echo "
            <table align=\"center\" width=\"100%\" border=\"0\">
                    <tr>
                    <td align=\"center\"><a href=\"#\" onclick=\"loadPHPFile('./employee_personalInfo.php?type=personal&id=".$employeeId."')\">Personal Information</a> || <a href=\"#\" onclick=\"loadPHPFile('employee_personalInfo.php?type=designation&id=".$employeeId."')\">Designation Information</a> || <a href=\"#\" onclick=\"loadPHPFile('employee_personalInfo.php?type=accounts&id=".$employeeId."')\">Accounts Information</a> || <a href=\"#\" onclick=\"loadPHPFile('employee_personalInfo.php?type=allowances&id=".$employeeId."')\">Allowances Information</a></td>
                </tr>
            </table>
            <br /><br />
            <table border=\"1\" align=\"center\" width=\"100%\">
                <tr>
                    <th width=\"5%\">SN</th>
                    <th width=\"40%\">Name</th>
                    <th width=\"8%\">View</th>
                    <th width=\"8%\">Edit</th>
                    <th width=\"8%\">Info</th>
                    <th width=\"8%\">Drop</th>
                </tr>
                <tr>
                    <td align=\"center\"><font class=\"green\">1</font></td>
                    <td align=\"center\" style=\"padding-left:10px\"><a href=\"#\" onclick=\"loadPHPFile('employee_personalInfo.php?type=personal&id=".$employeeId."')\">".$personalInfo->getName()."</a></td>
                    <td align=\"center\"><a href=\"#\"  onclick=\"loadPHPFile('employee_personalInfo.php?type=".$type."&id=".$employeeId."')\"><img src=\"../img/b_props.png\" alt=\"info\" /></a></td>
                    <td align=\"center\"><a href=\"./employee_edit.php?type=".$type."&id=".$employeeId."\" target=\"_parent\"><img src=\"../img/b_edit.png\" alt=\"edit\" /></a></td>
                    <td align=\"center\"><a href=\"#\"  onclick=\"loadPHPFile('employee_info.php?type=".$type."&id=".$employeeId."')\"><img src=\"../img/b_browse.png\" alt=\"info\" /></a></td>
                    <td align=\"center\"><a href=\"./employee_drop.php?id=".$employeeId."\" target=\"_parent\"><img src=\"../img/b_drop.png\" alt=\"delete\" /></a></td>
                </tr>
            </table>";
    }    
    ob_end_flush();
?>
<?php
    if($type == "personal"){
        require_once '../include/class.department.php';
        require_once '../include/class.employeeType.php';
        require_once '../include/class.housing.php';

        $department = new department();
        $employeeType = new employeeType();
        $housing = new housing();

        echo "
            <table align=\"center\" width=\"100%\" border=\"0\">
                <tr>
                    <td align=\"right\" width=\"19%\"><font class=\"error\">Name : </font></td>
                    <td align=\"left\" style=\"padding-left:20px\" width=\"30%\"><font class=\"green\">".ucwords(strtolower($personalInfo->getName()))."</font></td>
                    <td width=\"1%\"></td>
                    <td align=\"right\" width=\"19%\"><font class=\"error\">Employee Code :</font></td>
                    <td align=\"left\" style=\"padding-left:20px\" width=\"30%\"><font class=\"green\">".$personalInfo->getEmployeeId()."</font></td>
                </tr>
                <tr>
                    <td height=\"10px\"></td>
                </tr>
                <tr>
                    <td align=\"right\"><font class=\"error\">Temporary Address :</font></td>
                    <td align=\"left\" style=\"padding-left:20px\"><font class=\"green\">".ucwords(strtolower($personalInfo->getTemporarAddress()))."</font></td>
                    <td></td>
                    <td align=\"right\"><font class=\"error\">Permanent Address :</font></td>
                    <td align=\"left\" style=\"padding-left:20px\"><font class=\"green\">".ucwords(strtolower($personalInfo->getPermanentAddress()))."</font></td>
                </tr>
                <tr>
                    <td height=\"10px\"></td>
                </tr>
                <tr>
                    <td align=\"right\"><font class=\"error\">Blood Grp. :</font></td>
                    <td align=\"left\" style=\"padding-left:20px\"><font class=\"green\">".$personalInfo->getBloodGroup()."</font></td>
                    <td></td>
                    <td align=\"right\"><font class=\"error\">Date Of Birth :</font></td>
                    <td align=\"left\" style=\"padding-left:20px\"><font class=\"green\">".$personalInfo->getDob()."</font></td>
                </tr>
                <tr>
                    <td height=\"10px\"></td>
                </tr>
                <tr>
                    <td align=\"right\"><font class=\"error\">Contact No :</font></td>
                    <td align=\"left\" style=\"padding-left:20px\"><font class=\"green\">".$personalInfo->getContactNumber()."</font></td>
                    <td></td>
                    <td align=\"right\"><font class=\"error\">Type :</font></td>
                    <td align=\"left\" style=\"padding-left:20px\"><font class=\"green\">".$employeeType->getEmployeeTypeName($personalInfo->getEmployeeType())."</font></td>
                </tr>
                <tr>
                    <td height=\"10px\"></td>
                </tr>
                <tr>
                    <td align=\"right\"><font class=\"error\">Department :</font></td>
                    <td align=\"left\" style=\"padding-left:20px\"><font class=\"green\">".$department->getDepartmentName($personalInfo->getDepartment())."</font></td>
                    <td></td>
                    <td align=\"right\"><font class=\"error\">Housing Type :</font></td>
                    <td align=\"left\" style=\"padding-left:20px\"><font class=\"green\">".$housing->getHousingTypeName($personalInfo->getHousingType())."</font></td>
                </tr>
                <tr>
                    <td height=\"50px\"></td>
                </tr>
            </table><br /><br />";
        
            printMenu($employeeId, $type, $personalInfo);
    }
    if($type == "designation"){
        require_once '../include/class.employeeInfo.php';
        require_once '../include/class.designation.php';
	require_once '../include/class.dateDifference.php';


        $designation = new designation();
        $employeeInfo = new employeeInfo();
        $dateDifference = new dateDifference();

        $today = date("Y")."-".date("m")."-".date("d");
        $personalInfo->getEmployeeInformation($employeeId, true);
        echo "
            <table align=\"center\" width=\"100%\" border=\"1\">
                <tr>
                    <th width=\"5%\">SN</th>
                    <th width=\"25%\">Designation Name :</th>
                    <th width=\"25%\">Joining Date</th>
                    <th width=\"25%\">Leaving Date</th>
                    <th width=\"20%\">Tenure</th>
             	</tr>
                <tr>
                	<td colspan=\"5\" height = \"25px\" align=\"center\"><font class=\"error\">Persent Designations :</font></td>
                </tr>";

        $rankId = $employeeInfo->getEmployeeRankIds($employeeId, true);
        $i = 0;
        foreach ($rankId as $value){
            $rankDetails = $employeeInfo->getRankDetails($value, true);
            $dateDifference->getDifference($today, $rankDetails[3]);
            ++$i;
            echo  "
                <tr>
                    <td align=\"center\"><font class=\"green\">".$i."</font></td>
                    <td align=\"left\" style=\"padding-left:20px\"><a href=\"./designation.php\" target=\"_parent\">".$designation->getDesignationTypeName($rankDetails[2], true)."</a></td>
                    <td align=\"center\"><font class=\"green\">".$rankDetails[3]."</font></td>
                    <td align=\"center\"><font class=\"green\">".$rankDetails[4]."</font></td>
                    <td align=\"center\"><font class=\"green\">".$dateDifference->getDays()." Days</font></td>
                </tr>
                <tr>
                	<td colspan=\"5\" height=\"5px\"></td>
                </tr>";
        }
        echo  "
                <tr>
                	<td colspan=\"5\" height=\"15px\"></td>
                </tr>
                <tr>
                	<td height = \"25px\" colspan=\"5\" align=\"center\"><font class=\"error\">Previous Designations :</font></td>
                </tr>";
        $rankId = $employeeInfo->getEmployeeOldRankIds($employeeId);
        $i = 0;
        foreach ($rankId as $value){
            $rankDetails = $employeeInfo->getRankDetails($value, true);
            $dateDifference->getDifference($rankDetails[4], $rankDetails[3]);
            ++$i;
            echo  "
                <tr>
                    <td align=\"center\"><font class=\"green\">".$i."</font></td>
                    <td align=\"left\" style=\"padding-left:20px\"><a href=\"./designation.php\" target=\"_parent\">".$designation->getDesignationTypeName($rankDetails[2], true)."</a></td>
                    <td align=\"center\"><font class=\"green\">".$rankDetails[3]."</font></td>
                    <td align=\"center\"><font class=\"green\">".$rankDetails[4]."</font></td>
                    <td align=\"center\"><font class=\"green\">".$dateDifference->getDays()."</font></td>
                </tr>
                <tr>
                	<td colspan=\"5\" height=\"5px\"></td>
                </tr>";
        }
        if($i == 0){
            echo "
                <tr>
                    <td colspan=\"5\" height=\"15px\" align = \"center\"><font class = \"green\">No Previous Records</td>
                </tr>";
        }
        echo  "
            </table>
            <br /><br />";
            printMenu($employeeId, $type, $personalInfo);
    }
    if($type == "accounts"){
        require_once '../include/class.employeeInfo.php';
		require_once '../include/class.bank.php';
        require_once '../include/class.accountInfo.php';

        $employeeInfo = new employeeInfo();
        $accounts = new accounts();
        $bank = new bank();

        $bankDetails = $employeeInfo->getEmployeeBankAccoutDetails($employeeId, true);

        echo "
            <table border=\"0\" align=\"center\" width=\"100%\">
                <tr>
                <td width=\"30%\" align=\"right\"><font class=\"error\">Basic Salary :</font></td>
                <td width=\"10%\"></td>
                <td width=\"60%\" align=\"left\"><font class=\"green\">INR ".$accounts->getEmployeeBasicSalary($employeeId)."</font></td>
            </tr>
            <tr>
                <td colspan=\"3\" height=\"40px\" align = \"center\"><hr size=\"1\" /><br /><font class = \"error\">Salary Account Information</font><br /><hr size=\"1\" /></td>
            </tr>
            <tr>
                <td align=\"right\"><font class=\"error\">Name Of Bank :</font></td>
                <td></td>
                <td align=\"left\"><font class=\"green\">".$bank->getBankName($bankDetails[3])."</font></td>
            </tr>
            <tr>
                <td height=\"10px\"></td>
            </tr>
            <tr>
                <td align=\"right\"><font class=\"error\">Account Number :</font></td>
                <td></td>
                <td align=\"left\"><font class=\"green\">".$bankDetails[2]."</font></td>
            </tr>
            <tr>
                <td colspan=\"3\" height=\"40px\" align = \"center\"><hr size=\"1\" /><br /><font class = \"error\">Pension Account Information</font><br /><hr size=\"1\" /></td>
            </tr>
            <tr>
                <td align=\"right\"><font class=\"error\">Name Of Bank :</font></td>
                <td></td>
                <td align=\"left\"><font class=\"green\">".$bank->getBankName($bankDetails[5])."</font></td>
            </tr>
            <tr>
                <td height=\"10px\"></td>
            </tr>
            <tr>
                <td align=\"right\"><font class=\"error\">Account Number :</font></td>
                <td></td>
                <td align=\"left\"><font class=\"green\">".$bankDetails[4]."</font></td>
            </tr>
            <tr>
                <td height=\"10px\" colspan = \"3\"><hr size = \"2\" /></td>
            </tr>
        </table>
        <br /><br />";
            printMenu($employeeId, $type, $personalInfo);
    }

    if($type == "allowances"){
        require_once '../include/class.employeeInfo.php';
        require_once '../include/class.allowance.php';

        $allowance = new allowance();
        $employeeInfo = new employeeInfo();

        $personalInfo->getEmployeeInformation($employeeId, true);
        $sessionId = $loggedInfo->getSessionIds();

	echo "
            <table border=\"1\" align=\"center\" width=\"100%\">
                <tr>
                    <th width=\"5%\">SN</th>
                    <th width=\"40%\">Allowance Name</th>
                    <th width=\"25%\">Amount</th>
                    <th width=\"20%\">Type</th>
                </tr>
                <tr>
                    <td colspan=\"4\" height=\"5px\"></td>
                </tr>";
        foreach ($sessionId as $value) {
            $sessionDetails = $loggedInfo->getSessionDetails($value);
            echo "
                <tr>
                    <td colspan=\"4\" align=\"center\"><font class=\"error\">Session Name : ".$sessionDetails[1]."<br />Start Date : ".$sessionDetails[2]." End Date : ".$sessionDetails[3]."</font></td>
                </tr>";
            $masterSalaryId = $employeeInfo->getSessionMasterSalaryIds($employeeId, $value);
            $i = 0;
            foreach ($masterSalaryId as $accountSubhead){
                ++$i;
                $salaryDetails = $employeeInfo->getSalaryIdDetails($accountSubhead, true);
                echo "
                    <tr>
                        <td align=\"center\"><font class=\"green\">".$i."</font></td>
                        <td align=\"left\" style=\"padding-left:10px\"><a href=\"./allowance.php\">".$allowance->getAllowanceTypeName($salaryDetails[4])."</a></td>";
                if($salaryDetails[6] == 'c'){
                    $dependentType = "Credit";
                    echo "<td align=\"center\"><font class=\"green\">INR. ".abs($salaryDetails[5])."</font></td>
                          <td align=\"center\"><font class=\"green\">".$dependentType."</font></td>";
                }
                else{
                    $dependentType = "Debit";
                    echo "<td align=\"center\"><font class=\"error\">INR. ".abs($salaryDetails[5])."</font></td>
                          <td align=\"center\"><font class=\"error\">".$dependentType."</font></td>";
                }
                echo "

                    </tr>
                    <tr>
                        <td colspan=\"4\" height=\"5px\"></td>
                    </tr>";
            }
            echo "
                <tr>
                    <td colspan=\"4\" height=\"15px\"></td>
                </tr>";
        }
      echo "
            </table>
            <br /><br /><br />";
            printMenu($employeeId, $type, $personalInfo);
    }
?>
				