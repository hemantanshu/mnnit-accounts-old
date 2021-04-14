<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
require_once 'class.pending.php';
class bank extends pending {

    public function  __construct() {
        parent::__construct();
    }

    public function getBankIds($flag){  
        if($flag)
            $query = "SELECT id FROM options WHERE field=\"bank\" && status = \"y\" ";
        else
            $query = "SELECT id FROM bakoptions WHERE field=\"bank\" && status = \"y\" ";

        $query = $this->processQuery($query);

        if(mysql_num_rows($query)){
            $ids = array();
            while($result = mysql_fetch_array($query))
                array_push($ids, $result[0]);

            return $ids;
        }
        return false;
    }

    public function getBankName($id){     //the functoin to get the deparment name for a given bank name
        $variable = $this->getValue("name", "options", "id", $id);

        if($variable == ""){
            $variable = $this->getValue("name", "bakoptions", "id", $id);
        }

        return $variable;
    }

    public function setBankName($name){   //the function to set the name of a bank
        $counter = $this->getCounter("bank");
        if($this->isAdmin()){
            $query = "INSERT INTO options (field, id, name, status) VALUES (\"bank\", \"$counter\", \"$name\", \"y\") ";
            if($this->processQuery($query)){
                $pendingId = $this->setPendingWork($counter);
                $this->insertProcess($pendingId, "New Bank <i>".$name."</i> Created");

                return true;
            }
            return false;
        }
        $query = "INSERT INTO bakoptions (field, id, name, status) VALUES (\"bank\", \"$counter\", \"$name\", \"y\") ";
        if($this->processQuery($query)){
            $this->setPendingWork($counter);
            return true;
        }
        return false;
    }

    public function getBankEmployeeCount($id){        //the function that gives the total count of the employees working in a given bank
        $query = "SELECT employeeid FROM bankaccount WHERE salary_bankid = \"$id\" ";
        $query = $this->processQuery($query);

        return mysql_num_rows($query);
    }

    public function checkBankName($name){             //the function to chk for a bank name if it exists already or is in the pending list
        $query = "SELECT id FROM options WHERE field=\"bank\" && name=\"$name\" ";
        $query = $this->processQuery($query);

        if(mysql_num_rows($query))
            return false;

        $query = "SELECT id FROM bakoptions WHERE field=\"bank\" && name=\"$name\" ";
        $query = $this->processQuery($query);

        if(mysql_num_rows($query))
            return false;
            
        return true;
    }

    public function deActivateBankName($id){ //@todo this will check first in whole database where the field is used and if there is still persistant which is still under use then it should give a fatal error showing the list of employees whose details has to be edited
    }

    public function getBankPending(){     //function to get the list of pending banks jobs
        $query = "SELECT id FROM bakoptions WHERE field=\"bank\" ";
        $query = $this->processQuery($query);

        if(mysql_num_rows($query)){
            $ids = array();
            while($result = mysql_fetch_array($query))
                array_push($ids, $result[0]);

            return $ids;
        }
        return false;
    }

}
?>
