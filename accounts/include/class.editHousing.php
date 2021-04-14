<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */

require_once 'class.housing.php';
class editHousing extends housing {


    public function  __construct() {
        parent::__construct();
    }

    public function isEditable($id){

        if(!$this->getHousingTypeName($id))   //checking if there is a Housing with this id either in pending or in new one
                return false;

        if($this->isWorkInPendingStatus($id)){
                if(!$this->isPendingEditable($id))
                        return false;
        }
        return true;
    }

    public function getPendingHousingInfo($id){
        static $sqlQuery;
        static $variable;

        $sqlQuery = "SELECT name, value, status FROM bakoptions WHERE id = \"$id\" ";
        if($sqlQuery = $this->processArray($sqlQuery)){
            $variable = array();

            array_push($variable, $sqlQuery['name']);
            array_push($variable, $sqlQuery['value']);
            array_push($variable, $sqlQuery['status']);

            return $variable;
        }

        return false;
    }



    public function updateHousingInfo($id, $name, $value){
       $housingType = $this->getHousingTypeName($id);
       $housingValue = $this->getHouseTypeValue($id);

       $sqlQuery = "SELECT status FROM bakoptions WHERE id = \"$id\" ";
       $sqlQuery = $this->processQuery($sqlQuery);

       if(mysql_num_rows($sqlQuery)){    //the option is already in the pending status
           if($this->isAdmin()){
               $pendingId = $this->setPendingWork($id);

               
               $sqlQuery = "UPDATE bakoptions SET name = \"$name\", value = \"$value\" WHERE id = \"$id\" && field = \"housing\" ";
               $this->processQuery($sqlQuery);

               $sqlQuery = "SELECT name FROM options WHERE id = \"$id\" ";
               $sqlQuery = $this->processQuery($sqlQuery);

               if(mysql_num_rows($sqlQuery)){
                   $sqlQuery = "DELETE FROM options WHERE id = \"$id\" ";
                   $this->processQuery($sqlQuery);                   

                   if($housingType != $name && $housingValue != $value)
                        $this->insertProcess($pendingId, "Housing Type Name Changed From <i>".$housingType."</i> to <i>".$name."</i> <br />Value Changed From <i>".$housingValue."</i> to <i>".$value."</i>");
                   else{
                       if($housingType != $name)
                            $this->insertProcess($pendingId, "Housing Type Name Changed From <i>".$housingType."</i> to <i>".$name."</i> ");
                       if($housingValue != $value)
                           $this->insertProcess($pendingId, "Housing Type Value Changed From <i>".$housingValue."</i> to <i>".$value."</i> ");
                    }
               }
               else
                    $this->insertProcess($pendingId, "New Housing Type <i>".$name."</i> Formed ");

               $sqlQuery = "INSERT INTO options (SELECT * FROM bakoptions WHERE id=\"$id\" && field = \"housing\" )";
               $this->processQuery($sqlQuery);

               $sqlQuery = "DELETE FROM bakoptions WHERE id = \"$id\" ";
               $this->processQuery($sqlQuery);

                return true;
           }
            else{
                $sqlQuery = "UPDATE bakoptions SET name = \"$name\", value = \"$value\" WHERE id = \"$id\" ";
                $this->processQuery($sqlQuery);

                $this->setPendingWork($id);
                return true;
           }
       }else{                           //the option is not in the pending list

           if($this->isAdmin()){
               $sqlQuery = "UPDATE options SET name = \"$name\", value = \"$value\" WHERE id = \"$id\" && field = \"housing\" ";
               $this->processQuery($sqlQuery);
               $pendingId = $this->setPendingWork($id);

               if($housingType != $name && $housingValue != $value)
                    $this->insertProcess($pendingId, "Housing Type Name Changed From <i>".$housingType."</i> to <i>".$name."</i> <br />Value Changed From <i>".$housingValue."</i> to <i>".$value."</i>");
               else{
                   if($housingType != $name)
                        $this->insertProcess($pendingId, "Housing Type Name Changed From <i>".$housingType."</i> to <i>".$name."</i> ");
                   if($housingValue != $value)
                       $this->insertProcess($pendingId, "Housing Type Value Changed From <i>".$housingValue."</i> to <i>".$value."</i> ");
                }
                return true;

           }else{
               $sqlQuery = "INSERT INTO bakoptions (field, id, name, value, status) VALUES (\"housing\", \"$id\", \"$name\", \"$value\", \"y\" )";
               $this->processQuery($sqlQuery);

               $this->setPendingWork($id);
               return true;
           }

        }
    }

}
?>
