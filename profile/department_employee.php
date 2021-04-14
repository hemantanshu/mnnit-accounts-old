<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
////error_reporting(0)

require_once '../include/class.employeeInfo.php';
require_once '../include/class.personalInfo.php';

$employeeInfo = new employeeInfo();
$personalInfo = new personalInfo();

$variable = $employeeInfo->getEmployeeIds(true);

$departmentId = $_GET['id'];

$employeeId = array();
foreach ($variable as $value) {
    $personalInfo->getEmployeeInformation($value, true);
    if($personalInfo->getDepartment() == $departmentId)
            array_push($employeeId, $value);
}
?>
<br />
<table align="center" width="100%" border="0">
	<tr>
    	<th>SN</th>
        <th>Employee Code</th>
        <th>Name</th>
        <th>Residence Add.</th>
        <th>Contact No.</th>        
    </tr>
    <?php
        $i = 0;
        foreach ($employeeId as $value){
            ++$i;
            $personalInfo->getEmployeeInformation($value, true);
            echo "
                <tr>
                    <td><font class=\"green\">".$i."</font></td>
                    <td><font class=\"green\">".$personalInfo->getEmployeeCode()."</font></td>
                    <td><font class=\"green\">".$personalInfo->getName()."</font></td>
                    <td><font class=\"green\">".$personalInfo->getTemporarAddress()."</font></td>
                    <td><font class=\"green\">".$personalInfo->getContactNumber()."</font></td>
                </tr>";
        }
    ?>
    
    <tr>
    	<td colspan=\"5\"><hr size=\"1\" /><br /></td>
    </tr>
</table>
