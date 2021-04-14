<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    session_start();

    require_once '../include/class.loggedInfo.php';
    session_destroy();
    $loggedInfo = new loggedIn();

    $loggedInfo->redirect("./");

?>
