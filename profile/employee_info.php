<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();
    ////error_reporting(0)
    session_start();

    require_once '../include/class.personalInfo.php';
    require_once '../include/class.employeeInfo.php';
    
    if(isset ($_GET['id']) && isset ($_GET['type'])){
        $employeeId = $_GET['id'];
        $type = $_GET['type'];
    }else
       exit (0);
    
    $personalInfo = new personalInfo();
    $employeeInfo = new employeeInfo();

    $personalInfo->getEmployeeInformation($employeeId, true);

    function printMenu($employeeId, $type, $personalInfo, $id){
        require_once '../include/class.pending.php';
        $pending = new pending();
        if(!$pending->checkLogged())
                exit (0);

        $pendingId = array();
        
        foreach($id as $value){
            $variable = $pending->getPendingLogIds($value);
            foreach ($variable as $newValue)
                array_push($pendingId, $newValue);
        }       

        echo "
            <table align=\"center\" border=\"1\" bordercolor=\"#008000\" width=\"100%\">
                <tr>
                    <th width=\"5%\">SN</th>
                    <th width=\"31%\">Operator</th>
                    <th width=\"31%\">Supervisor</th>
                    <th width=\"32%\">Admin</th>
                </tr>";

        $i = 1;
        foreach ($pendingId as $value){
            $logInfo = $pending->getPendingLogIdInfo($value);
            echo "

                <tr>
                    <td align=\"center\" rowspan=\"3\"><font class=\"error\">".$i."</font></td>
                    <td align=\"center\"><font class=\"display\">".$logInfo[0]."</font></td>
                    <td align=\"center\"><font class=\"display\">".$logInfo[1]."</font></td>
                    <td align=\"center\"><font class=\"display\">".$logInfo[2]."</font></td>
                </tr>
                <tr>
                    <td align=\"center\"><font class=\"display\">".$pending->getOfficerNameNotLogged($logInfo[3])."</font></td>
                    <td align=\"center\"><font class=\"display\">".$pending->getOfficerNameNotLogged($logInfo[4])."</font></td>
                    <td align=\"center\"><font class=\"display\">".$pending->getOfficerNameNotLogged($logInfo[5])."</font></td>
                </tr>
                <tr>
                    <td align=\"center\" colspan=\"3\"><font color=\"green\">".$logInfo[6]."</font></td>
                </tr>
                <tr>
                    <td colspan=\"4\" height=\"5px\"><hr size=\"3\" color=\"#FF0000\" /></td>
                </tr>
                ";
            ++$i;
        }
        echo "
            </table>
            <br /><br />
            <table align=\"center\" width=\"100%\" border=\"0\">
                    <tr>
                    <td align=\"center\"><a href=\"#\" onclick=\"loadPHPFile('./employee_info.php?type=personal&id=".$employeeId."')\">Personal Log Informationn</a> || <a href=\"#\" onclick=\"loadPHPFile('employee_info.php?type=designation&id=".$employeeId."')\">Designation Log Informationn</a> || <a href=\"#\" onclick=\"loadPHPFile('employee_info.php?type=accounts&id=".$employeeId."')\">Accounts Log Informationn</a> || <a href=\"#\" onclick=\"loadPHPFile('employee_info.php?type=allowances&id=".$employeeId."')\">Allowances Log Informationn</a></td>
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
        echo "
            <table align=\"center\" width=\"100%\" border=\"1\">
                <tr>
                    <td align=\"center\" width=\"30px\"><font class=\"error\">Employee Personal Log Information</font></td>
                </tr>
            </table>";
        $pendingId = array();
        array_push($pendingId, $employeeId);
        printMenu($employeeId, $type, $personalInfo, $pendingId);
    }
    if($type == "designation"){
        echo "
            <table align=\"center\" width=\"100%\" border=\"1\">
                <tr>
                    <td align=\"center\" width=\"30px\"><font class=\"error\">Employee Designation Log Information</font></td>
                </tr>
            </table>";
        $pendingId = array();
        
        $variable = $employeeInfo->getEmployeeRankIds($employeeId, true);
        foreach ($variable as $value)
            array_push($pendingId, $value);
        
        $variable = $employeeInfo->getEmployeeOldRankIds($employeeId);
        foreach ($variable as $value)
            array_push($pendingId, $value);

        printMenu($employeeId, $type, $personalInfo, $pendingId);
    }
    if($type == "accounts"){
        echo "
            <table align=\"center\" width=\"100%\" border=\"1\">
                <tr>
                    <td align=\"center\" width=\"30px\"><font class=\"error\">Employee Accounts Log Information</font></td>
                </tr>
            </table>";
        $pendingId = array();

        $variable = $employeeInfo->getEmployeeBankAccoutDetails($employeeId, true);
        array_push($pendingId, $variable[0]);
        
        $variable = $employeeInfo->getEmployeeBasicSalaryDetails($employeeId, true);
        array_push($pendingId, $variable[0]);
        
        printMenu($employeeId, $type, $personalInfo, $pendingId);
    }

    if($type == "allowances"){
        echo "
            <table align=\"center\" width=\"100%\" border=\"1\">
                <tr>
                    <td align=\"center\" width=\"30px\"><font class=\"error\">Employee Accounts Allowance Log Information</font></td>
                </tr>
            </table>";
        $pendingId = array();

        $variable = $employeeInfo->getMasterSalaryId($employeeId, true);
        foreach ($variable as $value){
            $salaryDetails = $employeeInfo->getSalaryIdDetails($value, true);
            if(!in_array($salaryDetails[1], $pendingId))
                array_push($pendingId, $salaryDetails[1]);
        }


        printMenu($employeeId, $type, $personalInfo, $pendingId);
    }
?>
				