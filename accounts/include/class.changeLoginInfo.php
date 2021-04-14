<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
require_once 'class.loginInfo.php';
class changeLoginInfo extends loginInfo {
    
    private $officer;

    public function  __construct() {
        parent::__construct();

        $this->officer = $this->checkLogged();

    }

    

    public function changePrivilege($privilege){
        static $query;

        if($privilege == "accountant")
            $query = "UPDATE officer SET admin=\"\", superintendent=\"\" WHERE username \"$this->officer\" ";
        if($privilege == "Supretendent")
            $query = "UPDATE officer SET admin=\"\", superintendent=\"y\" WHERE username \"$this->officer\" ";
        if($privilege == "admin")
            $query = "UPDATE officer SET admin=\"y\", superintendent=\"y\" WHERE username \"$this->officer\" ";

        if($this->processQuery($query))
                return true;

        return false;
    }

    public function unlockOfficer($username){
        static $query;

        $query = "UPDATE officer SET status = \"y\" WHERE username = \"$username\" ";
        $this->processQuery($query);
    }
}
?>
