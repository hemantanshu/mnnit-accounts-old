<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();

    session_start();

    require_once '../include/class.allowance.php';
    $allowance = new allowance();

    if(!$allowance->checkLogged())
        $allowance->redirect('../');


        $count = $_GET['value'];
       

    $i = 1;
	echo "<table width=\"100%\" align=\"center\" border=\"1\">    ";
    while($i < $count){
        $dependentName = "dependentName".$i;
        $dependentType = "allowanceType".$i;
        $dependentValue  = "allowanceValue".$i;
        ++$i;
        echo "
                <tr>
					<td height=\"10px\" colspan=\"4\"></td>
				</tr>
				<tr>
                    <td width=\"5%\" align=\"center\">".$i."</td>
                    <td width=\"20%\" align=\"center\"><input type=\"text\" name=\"".$dependentValue."\" value=\"\" style=\"width:200px\" /></td>
                    <td width=\"50%\" align=\"center\">
                                    <select name=\"".$dependentName."\" style=\"width:200px\">
                                        <option value=\"\">None</option>";
                                        
                                            $allowanceOptions = $allowance->getAllowanceOptions();
                                            if(is_array($allowanceOptions)){
                                                foreach($allowanceOptions as $value)
                                                    echo "<option value=\"".$value."\">".$allowance->getAllowanceTypeName($value)."</option>";
                                            }
                                        
       echo "
                                    </select></td>
                            <td width=\"25%\" align=\"center\"><select name=\"".$dependentType."\" style=\"width:200px\">
                                        <option value=\"c\">Credit</option>
                                        <option value=\"d\">Debit</option>
                                    </select></td>
                </tr>";

				
       
    }
	echo "</table>";



    ob_end_flush();
?>
