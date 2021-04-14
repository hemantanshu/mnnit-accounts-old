<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();

    session_start();

    require_once '../include/class.allowance.php';
    $allowance = new allowance();

    if(!$allowance->checkLogged())
        $allowance->redirect('../');

    if(isset ($_GET['id'])){
        $allowanceId = $_GET['id'];
        if(!$allowance->getAllowanceTypeName($allowanceId))
            $allowance->palert("No allowance With This Information Exists ", "./allowance.php");
    }else
        $allowance->redirect("./allowance.php");



    ob_end_flush();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>

<body>

<table align="center" border="0" width="100%">
			<tr>
                            <td align="center"><h2 style="color:#F00">Information On : <?php echo $allowance->getAllowanceTypeName($allowanceId); ?></h2></td>
            </tr>
            <tr>
            	<td height="10px"><hr size="1" /></td>
            </tr>
            <tr>
            	<td align="center" width="100%">
                	<table align="center" border="1"  width="100%">

                        <tr>
                        	<th width="5%">SN</th>
                            <th width="31%">Operator</th>
                            <th width="31%">Supervisor</th>
                            <th width="32%">Admin</th>
                        </tr>
                        <?php
                            $logId = $allowance->getPendingLogIds($allowanceId);
                            if(is_array($logId) && sizeof($logId)){
                                $i = 1;
                                foreach ($logId as $value){
                                    $logInfo = $allowance->getPendingLogIdInfo($value);
                                    echo "
                                        <tr>
                                            <td align=\"center\" rowspan=\"3\"><font class=\"error\">".$i."</font></td>
                                            <td align=\"center\"><font class=\"display\">".$logInfo[0]."</font></td>
                                            <td align=\"center\"><font class=\"display\">".$logInfo[1]."</font></td>
                                            <td align=\"center\"><font class=\"display\">".$logInfo[2]."</font></td>
                                        </tr>
                                        <tr>
                                            <td align=\"center\"><font class=\"display\">".$allowance->getOfficerNameNotLogged($logInfo[3])."</font></td>
                                            <td align=\"center\"><font class=\"display\">".$allowance->getOfficerNameNotLogged($logInfo[4])."</font></td>
                                            <td align=\"center\"><font class=\"display\">".$allowance->getOfficerNameNotLogged($logInfo[5])."</font></td>
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
                            }
                        ?>
                    </table>
                </td>
            </tr>
        </table>
</body>
</html>