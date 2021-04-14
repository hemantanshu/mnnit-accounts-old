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
              
                ";

    while($i < $count){
        $designationName = "designationName".$i;
        $startdate = "startdate".$i;
        $enddate = "enddate".$i;
        $today = date('d')."-".date('m')."-".date('Y');
        ++$i;
        echo "
            <tr>
                <td colspan=\"3\" align=\"center\"><font class=\"green\">Designation Number :".$i."</font></td>
            </tr>
            <tr>
                <td align=\"right\" width=\"30%\"><font class=\"green\">Designation Name :</font></td>
                <td width=\"10px\"></td>
                <td align=\"left\" width=\"50%\"><select name=\"".$designationName."\" style=\"width:200px\">
                                                    <option value=\"\">None</option> ";
                                                       $designationOptions = $designation->getDesignationIds(true);

                                                        if(is_array($designationOptions)){
                                                            foreach($designationOptions as $value)
                                                                echo "<option value=\"".$value."\">".$designation->getDesignationTypeName($value, true)."</option>";
                                                        }
        echo"                                      </select></td>
            </tr>
            <tr>
                <td align=\"right\"><font class=\"green\">Start Date</font></td>
                <td></td>
                <td align=\"left\"><input type=\"text\" name=\"".$startdate."\" value=\"".$today."\" style=\"width:200px\" /> * Date Form DD-MM-YY</td>
            </tr>
            <tr>
                <td align=\"right\"><font class=\"green\">End Date</font></td>
                <td></td>
                <td align=\"left\"><input type=\"text\" name=\"".$enddate."\" value=\"".$today."\" style=\"width:200px\" /> * Date Form DD-MM-YY</td>
            </tr>
            <tr>
                <td colspan=\"3\" height=\"50px\"><hr size=\"1\" /></td>
            </tr>";
    }
    ob_end_flush();
?>
    
    
