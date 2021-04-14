<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();

    session_start();

    require_once '../include/class.designation.php';
    $designation = new designation();

    if(!$designation->checkLogged())
        $designation->redirect('../');


    if(isset ($_GET['value'])){
        $count = $_GET['value'];
    }else
        exit(0);

    

    $i = 0;
    
	echo "<table width=\"100%\" align=\"center\" border=\"1\">
              <tr>
                  <th width=\"10%\">SN</th>
                  <th width=\"40%\">Value</th>
                  <th width=\"50%\">Dependent</th>
                  
              </tr>
                ";
        
    while($i < $count){
        $dependentName = "dependentName".$i;
        $dependentValue  = "designationValue".$i;
        ++$i;
        echo "
                <tr>
                    <td height=\"10px\" colspan=\"4\"></td>
                </tr>
                <tr>
                    <td align=\"center\">".$i."</td>
                    <td align=\"center\"><input type=\"text\" name=\"".$dependentValue."\" value=\"\" style=\"width:200px\" /></td>
                    <td align=\"center\">
                                    <select name=\"".$dependentName."\" style=\"width:200px\">
                                        <option value=\"\">None</option>";

                                           $designationOptions = $designation->getDesignationOptions();
                                            
                                            if(is_array($designationOptions)){
                                                foreach($designationOptions as $value)
                                                    echo "<option value=\"".$value."\">".$designation->getAllowanceTypeName($value)."</option>";
                                            }
       echo "
                                    </select></td>
                        
                </tr>";



    }
	echo "</table>";



    ob_end_flush();
?>
