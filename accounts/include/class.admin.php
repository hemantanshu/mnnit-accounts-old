<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
require_once 'class.pending.php';
class admin extends pending {

    public function  __construct() {
        parent::__construct();
        if(!$this->isAdmin())
                $this->redirect('./');
    }

    public function createUser($name, $username, $password, $role){
        $counter = $this->getCounter("officer");
        if($role == "admin")
            $sqlQuery = "INSERT INTO officer (id, username, password, name, attempts, type, status, active) VALUES (\"$counter\", \"$username\", \"".md5($password)."\", \"$name\", \"0\", \"a\", \"y\", \"y\" )";
        elseif($role == "supervisor")
            $sqlQuery = "INSERT INTO officer (id, username, password, name, attempts, type, status, active) VALUES (\"$counter\", \"$username\", \"".md5($password)."\", \"$name\", \"0\", \"s\", \"y\", \"y\" )";
        elseif($role == "operator")
            $sqlQuery = "INSERT INTO officer (id, username, password, name, attempts, type, status, active) VALUES (\"$counter\", \"$username\", \"".md5($password)."\", \"$name\", \"0\", \"o\", \"y\", \"y\" )";
		else 
			$sqlQuery = "INSERT INTO officer (id, username, password, name, attempts, type, status, active) VALUES (\"$counter\", \"$username\", \"".md5($password)."\", \"$name\", \"0\", \"l\", \"y\", \"y\" )";
        $this->processQuery($sqlQuery);

        $pendingId = $this->setPendingWork($counter);
        $this->insertProcess($pendingId, "New Officer Created :");
        return true;
    }

    public function checkUserName($username){
        $sqlQuery  = "SELECT name FROM officer WHERE username = \"$username\"";
        $sqlQuery = $this->processQuery($sqlQuery);

        if(mysql_num_rows($sqlQuery))
            return false;
        return true;
    }
    public function unLockUser($id){
        $sqlQuery = "UPDATE officer SET status = \"y\" ,attempts = \"0\" WHERE id = \"$id\" ";
        $this->processQuery($sqlQuery);

        $pendingId = $this->setPendingWork($id);
        $this->insertProcess($pendingId, "Officer <i>".$this->getOfficerNameNotLogged($id)."</i> Unlocked");

        return true;
    }
    public function lockUser($id){
        $sqlQuery = "UPDATE officer SET status = \"\", attempts = \"0\" WHERE id = \"$id\" ";
        $this->processQuery($sqlQuery);

        $pendingId = $this->setPendingWork($id);
        $this->insertProcess($pendingId, "Officer <i>".$this->getOfficerNameNotLogged($id)."</i> Unlocked");

        return true;
    }

    public function updateLoginTimer($time){
        $timer = $this->getCookieTimer();

        $sqlQuery = "UPDATE global SET value = \"$time\" WHERE field = \"logged_timer\" ";
        $this->processQuery($sqlQuery);

        $pendingId = $this->setPendingWork('timer');
        $this->insertProcess($pendingId, "Login Timer Value Changed From <i>".$timer." Secs </i> To <i>".$time." Secs</i>");
    }

    public function updateLoginAttempt($attempt){
        $variable = $this->getMaxAttempt();

        $sqlQuery = "UPDATE global SET value = \"$attempt\" WHERE field = \"login_attempt\" ";
        $this->processQuery($sqlQuery);

        $pendingId = $this->setPendingWork('timer');
        $this->insertProcess($pendingId, "Login Attempt Value Changed From <i>".$variable." Secs </i> To <i>".$attempt." Secs</i>");
    }

    public function getUnLockedOfficerIds(){
        $sqlQuery = "SELECT id FROM officer WHERE type != \"a\" && status = \"y\" ORDER BY name ASC";
        $sqlQuery = $this->processQuery($sqlQuery);

        $variable = array();
        while($result = mysql_fetch_array($sqlQuery))
            array_push($variable, $result[0]);

        return $variable;
    }

    public function getLockedOfficerIds(){
        $sqlQuery = "SELECT id FROM officer WHERE type != \"a\" && status != \"y\" ORDER BY name ASC";
        $sqlQuery = $this->processQuery($sqlQuery);

        $variable = array();
        while($result = mysql_fetch_array($sqlQuery))
            array_push($variable, $result[0]);

        return $variable;
    }   
}
?>
