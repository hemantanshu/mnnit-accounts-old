<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */

require_once 'class.pending.php';
require_once 'class.employeeInfo.php';

class employeePending extends pending {
    private $employeeInfo;

    public function  __construct() {
        parent::__construct();
        $this->employeeInfo = new employeeInfo();
    }   

    public function isEmployeePersonalInformationInPendingStatus($employeeId){
        if($this->isWorkInPendingStatus($employeeId))
                return $employeeId;
        else
            return false;
    }

    public function isEmployeeDesignationInPendingStatus($employeeId){       

        $variable = $this->employeeInfo->getEmployeeRankIds($employeeId, true);        
        foreach ($variable as $value) {
            if($this->isWorkInPendingStatus($value))
               return $value;
        }
        
        $variable = $this->employeeInfo->getEmployeeRankIds($employeeId, false);
        foreach ($variable as $value) {
            if($this->isWorkInPendingStatus($value))
               return $value;
        }
        return false;
    }

    public function isEmployeeBankInformationInPendingStatus($employeeId){
        $variable = array();

        $variable[0] = $this->employeeInfo->getEmployeeBankAccoutDetails($employeeId, true);

        if($this->isWorkInPendingStatus($variable[0][0]))
                return $variable[0][0];
        

        $variable[0] = $this->employeeInfo->getEmployeeBankAccoutDetails($employeeId, false);
        if($this->isWorkInPendingStatus($variable[0][0]))
                return $variable[0][0];

        $variable[0] = $this->employeeInfo->getEmployeeBasicSalaryDetails($employeeId, true);

        if($this->isWorkInPendingStatus($variable[0][0]))
                return $variable[0][0];


        $variable[0] = $this->employeeInfo->getEmployeeBasicSalaryDetails($employeeId, false);
        if($this->isWorkInPendingStatus($variable[0][0]))
                return $variable[0][0];


        
        return false;
    }

    public function isEmployeeMasterSalaryInPendingStatus($employeeId){
        $variable = array();

        $masterSalaryIds = $this->employeeInfo->getMasterSalaryId($employeeId, true);
        foreach ($masterSalaryIds as $value){
            $variable[0] = $this->employeeInfo->getSalaryIdDetails($value, true);
            if($this->isWorkInPendingStatus($variable[0][1]))
                    return $variable[0][1];

            $variable[0] = $this->employeeInfo->getSalaryIdDetails($value, false);
            if($this->isWorkInPendingStatus($variable[0][1]))
                    return $variable[0][1];
        }
        
        $masterSalaryIds = $this->employeeInfo->getMasterSalaryId($employeeId, false);
        foreach ($masterSalaryIds as $value){
            $variable[0] = $this->employeeInfo->getSalaryIdDetails($value, true);
            if($this->isWorkInPendingStatus($variable[0][1]))
                    return $variable[0][1];

            $variable[0] = $this->employeeInfo->getSalaryIdDetails($value, false);
            if($this->isWorkInPendingStatus($variable[0][1]))
                    return $variable[0][1];
        }
        return false;
    }

    public function isEmployeeSalaryInPendingStatus($employeeId){        
        $variable = array();

        $sqlQuery = "SELECT did FROM baksalary WHERE employeeid = \"$employeeId\" && month = \"$this->currentMonth\" LIMIT 1";
        $sqlQuery = $this->processQuery($sqlQuery);

        if(mysql_num_rows($sqlQuery)){
            $sqlQuery = mysql_fetch_array($sqlQuery);
            return $sqlQuery[0];
        }else
            return false;
    }

    public function isEmployeeSalaryProcessed($employeeId){        
        $variable = array();

        $sqlQuery = "SELECT did FROM salary WHERE employeeid = \"$employeeId\" && month = \"$this->currentMonth\" LIMIT 1";
        $sqlQuery = $this->processQuery($sqlQuery);

        if(mysql_num_rows($sqlQuery)){
            $sqlQuery = mysql_fetch_array($sqlQuery);
            return $sqlQuery[0];
        }else
            return false;
    }


}
?>
