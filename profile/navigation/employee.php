<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    echo "<div class=\"urbangreymenu\">";
    echo "
        <h3 class=\"headerbar\"><a href=\"./employee.php\">Employee Master</a></h3>
            <ul class=\"submenu\">
                <li><a href=\"./employee_new.php\">New Employee Registration</a></li>
                <li><a href=\"./employee.php\">View/Edit Employee Type</a></li>                
                <li><a href=\"./employee_pending.php\">Pending Employee Job</a></li>
            </ul>";

    include './navigation/index.php';
    echo "
        </div>";
?>

