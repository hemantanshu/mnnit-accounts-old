<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */

require_once 'class.pending.php';
require_once 'class.personalInfo.php';
require_once 'class.housing.php';
require_once 'class.employeeType.php';
require_once 'class.designation.php';
require_once 'class.employeeInfo.php';
require_once 'class.department.php';
require_once 'class.allowance.php';
require_once 'class.bank.php';
require_once 'class.accountInfo.php';
require_once 'class.salutation.php';



class editPersonalInfo extends pending {

    private $allowance;
    private $designation;
    private $housing;
    private $employeeType;
    private $department;
    private $employeeInfo;
    private $accountClass;
    private $salutation;

    public function  __construct() {
        parent::__construct();
        $this->department = new department();
        $this->designation = new designation();
        $this->allowance = new allowance();
        $this->housing = new housing();
        $this->employeeType = new employeeType();
        $this->employeeInfo =  new employeeInfo();
        $this->accountClass = new accounts();
        $this->salutation = new salutation();
    }

    public function isEditable($id){       

        if($this->isWorkInPendingStatus($id)){
                if(!$this->isPendingEditable($id))
                        return false;
        }
        return true;
    }

    public function setPersonalInfo($empcode, $salutation, $name, $padd, $tadd, $contact, $dob, $bgrp, $housing, $type, $dept){
        $counter = $this->getCounter("employee");
        if($this->isAdmin()){
            $sqlQuery = "INSERT INTO employee (id, employeeid, salutation, name, padd, tadd, bgrp, dob, contact, department, housing, type) VALUES (\"$counter\", \"$empcode\", \"$salutation\", \"$name\", \"$padd\", \"$tadd\", \"$bgrp\", \"$dob\", \"$contact\", \"$dept\", \"$housing\", \"$type\") ";
            $this->processQuery($sqlQuery);
            
            $sqlQuery = "INSERT INTO employeelogin (id, username, password, attempts, status, active)  VALUES (\"$counter\", \"$empcode\", \"1\", \"0\", \"y\", \"y\")";
            $this->processQuery($sqlQuery);

            $pendingId = $this->setPendingWork($counter);
            $this->insertProcess($pendingId, "New Employee <i>".$name."</i> Created");
            return $counter;
        }else{
            $sqlQuery = "INSERT INTO bakemployee (id, employeeid, salutation, name, padd, tadd, bgrp, dob, contact, department, housing, type) VALUES (\"$counter\", \"$empcode\", \"$salutation\", \"$name\", \"$padd\", \"$tadd\", \"$bgrp\", \"$dob\", \"$contact\", \"$dept\", \"$housing\", \"$type\") ";
            $this->processQuery($sqlQuery);

            $this->setPendingWork($counter);
            return $counter;
        }
        return false;
    }

    public function updatePersonalInfo($details){
        $personalInfo = new personalInfo();
        $personalInfo->getEmployeeInformation($details[0], false);
		
        if($personalInfo->getName() == ""){
            $sqlQuery = "INSERT INTO bakemployee (SELECT * FROM employee WHERE id = \"$details[0]\")";
            $this->processQuery($sqlQuery);
        }
        $personalInfo->getEmployeeInformation($details[0], true);
        $status = false;

        if($personalInfo->getEmployeeCode() != $details[1]){
            $status = true;
            $update .= "Employee Code Changed From <i>".$personalInfo->getEmployeeCode()."</i> To <i>".$details[1]."</i>";
        }
        if($personalInfo->getName() != $details[2]){
            $status = true;
            $update .= "Name Changed From <i>".$personalInfo->getName()."</i> To <i>".$details[2]."</i>";
        }
        if($personalInfo->getPermanentAddress() != $details[3]){
            $status = true;
            $update .= "Permanent Address Changed From <i>".$personalInfo->getPermanentAddress()."</i> To <i>".$details[3]."</i>";
        }
        if($personalInfo->getTemporarAddress() != $details[4]){
            $status = true;
            $update .= "Temporary Address Changed From <i>".$personalInfo->getTemporarAddress()."</i> To <i>".$details[4]."</i>";
        }
        if($personalInfo->getBloodGroup() != $details[5]){
            $status = true;
            $update .= "Blood Group Changed From <i>".$personalInfo->getBloodGroup()."</i> To <i>".$details[5]."</i>";
        }
        if($personalInfo->getDob() != $details[6]){
            $status = true;
            $update .= "DOB Changed From <i>".$personalInfo->getDob()."</i> To <i>".$details[6]."</i>";
        }
        if($personalInfo->getContactNumber() != $details[7]){
            $status = true;
            $update .= "Contact Number Changed From <i>".$personalInfo->getContactNumber()."</i> To <i>".$details[7]."</i>";
        }
        if($personalInfo->getDepartment() != $details[8]){
            $status = true;
            $update .= "Department Changed From <i>".$this->department->getDepartmentName($personalInfo->getHousingType())."</i> To <i>".$this->department->getDepartmentName($details[8])."</i>";
        }
        if($personalInfo->getHousingType() != $details[9]){
            $status = true;
            $update .= "Housing Type Changed From <i>".$this->housing->getHousingTypeName($personalInfo->getHousingType())."</i> To <i>".$this->housing->getHousingTypeName($details[9])."</i>";
        }
        if($personalInfo->getEmployeeType() != $details[10]){
            $status = true;
            $update .= "Name Changed From <i>".$this->employeeType->getEmployeeTypeName($personalInfo->getEmployeeType())."</i> To <i>".$this->employeeType->getEmployeeTypeName($details[10])."</i>";
        }
        if($personalInfo->getSalutationId() != $details[11]){
            $status = true;
            $update .= "Salutation Changed From <i>".$this->salutation->getSalutationName($personalInfo->getSalutationId())."</i> To <i>".$this->salutation->getSalutationName($details[11])."</i>";
        }
        
        if($status){
            $sqlQuery = "UPDATE bakemployee SET employeeid = \"$details[1]\", salutation=\"$details[11]\", name = \"$details[2]\", padd = \"$details[3]\", tadd = \"$details[4]\", bgrp = \"$details[5]\", dob = \"$details[6]\", contact = \"$details[7]\", department = \"$details[8]\", housing = \"$details[9]\", type = \"$details[10]\" WHERE id = \"$details[0]\" ";
            $this->processQuery($sqlQuery);
        }

        if($this->isAdmin()){           
            
            $sqlQuery = "DELETE FROM employee WHERE id = \"$details[0]\" ";
            $this->processQuery($sqlQuery);

            $sqlQuery = "INSERT INTO employee (SELECT * FROM bakemployee WHERE id = \"$details[0]\")";
            $this->processQuery($sqlQuery);

            $sqlQuery = "DELETE FROM bakemployee WHERE id = \"$details[0]\" ";
            $this->processQuery($sqlQuery);
            
            $sqlQuery = "SELECT * FROM employeelogin WHERE id = \"$details[0]\" "; //setting the employee login details
            $sqlQuery = $this->processQuery($sqlQuery);
            if (mysql_num_rows($sqlQuery)){
            	$sqlQuery = "UPDATE employeelogin SET username = \"$details[1]\" WHERE id = \"$details[0]\" ";
            	$this->processQuery($sqlQuery);
            }else{
            	$sqlQuery = "INSERT INTO employeelogin (id, username, password, attempts, status, active)  VALUES (\"$details[0]\", \"$details[1]\", \"1\", \"0\", \"y\", \"y\")";
            	$this->processQuery($sqlQuery);
            }            
            $status = $this->setPendingWork($details[0]);
            if($personalInfo->getName() == ""){
                $this->insertProcess($status, "New Employee <i>".$details[2]."</i> Registered");
            }else{
                $this->insertProcess($status, "Employee Information Changed : ".$update);
            }
            return true;
        }else{ //the logged user is not admin            
            $this->setPendingWork($details[0]);
            return true;
        }

    }
    public function setEmployeeDesignation($details){
        $counter = $this->getCounter("ranks");

        if($this->isAdmin()){            
            $sqlQuery = "INSERT INTO ranks (id, employeeid, designation, sdate, edate) VALUES (\"$counter\", \"$details[0]\", \"$details[1]\", \"$details[2]\", \"$details[3]\") ";
            $this->processQuery($sqlQuery);          

            $pendingId = $this->setPendingWork($counter);
            $this->insertProcess($pendingId, "New Designation <i>".$this->designation->getDesignationTypeName($details[1], true)."</i> Assigned");
            return true;
        }else{
            $sqlQuery = "INSERT INTO bakranks (id, employeeid, designation, sdate, edate) VALUES (\"$counter\", \"$details[0]\", \"$details[1]\", \"$details[2]\", \"$details[3]\") ";
            $this->processQuery($sqlQuery);

            $pendingId = $this->setPendingWork($counter);
            return true;
        }
    }

    public function updateEmployeeDesignation($details){        
        $rankDetails = $this->employeeInfo->getRankDetails($details[0], true);
        if($rankDetails[1] == "")
            $update = false;
        
        $status = false;
        
        if($rankDetails[1] != $details[1] && $rankDetails[1] != "")
            $this->redirect('./employee.php');

        if($rankDetails[2] != $details[2] && $rankDetails[1] != ""){
            $status = true;            
            $update .= "Designation Type Changed From <i>".$this->designation->getDesignationTypeName($rankDetails[2], true)."</i> To <i>".$this->designation->getDesignationTypeName($details[2], true)."</i>";
        }
        if($rankDetails[3] != $details[3] && $rankDetails[1] != ""){
            $status = true;
            $update .= "Start Date Changed From <i>".$rankDetails[3]."</i> To <i>".$details[3]."</i>";
        }
        if($rankDetails[4] != $details[4] && $rankDetails[1] != ""){
            $status = true;
            $update .= "End Date Changed From <i>".$rankDetails[4]."</i> To <i>".$details[4]."</i>";
        }
        $rankDetails = $this->employeeInfo->getRankDetails($details[0], false);
        if($rankDetails[0] == ""){
            $sqlQuery = "INSERT INTO bakranks (SELECT * FROM ranks WHERE id = \"$details[0]\")";
            $this->processQuery($sqlQuery);
        }
        
        if($status || $rankDetails[1] == ""){
            $sqlQuery = "UPDATE bakranks SET designation = \"$details[2]\", sdate = \"$details[3]\", edate = \"$details[4]\" WHERE id = \"$details[0]\" ";
            $this->processQuery($sqlQuery);
        }
        if($this->isAdmin()){
            $sqlQuery = "DELETE FROM ranks WHERE id = \"$details[0]\" ";
            $this->processQuery($sqlQuery);

            $sqlQuery = "INSERT INTO ranks (SELECT * FROM bakranks WHERE id = \"$details[0]\")";
            $this->processQuery($sqlQuery);

            $sqlQuery = "DELETE FROM bakranks WHERE id = \"$details[0]\" ";
            $this->processQuery($sqlQuery);


            $status = $this->setPendingWork($details[0]);
            if(!$update){
                $this->insertProcess($status, "New Designation <i>".$this->designation->getDesignationTypeName($details[2], true)."</i> Registered");
            }else{
                $this->insertProcess($status, "Employee Designation Info Changed : ".$update);
            }
            return true;
        }else{ //the logged user is not admin
            $this->setPendingWork($details[0]);
            return true;
        }
    }

    public function deActivateSessionMasterSalary($employeeId){
        $sqlQuery = "UPDATE mastersalary SET active = \"\" WHERE employeeId = \"$employeeId\" ";
        $this->processQuery($sqlQuery);

        return true;
    }   

    public function setMasterAccountDetails($employeeId, $details){    	
        $status = false;
        $overridden = "";
        
        if($details[1] > 0)
            $pendingId = 'c';
        else
            $pendingId = 'd';

        $sessionId = $this->getCurrentSession();
        $counter = $this->getCounter("masterSalary");

        $amountValue = $this->accountClass->getAccountSum($employeeId, $details[0]);
        if(($amountValue > $details[1] && ($amountValue - $details[1]) > 1) || ($amountValue < $details[1] && ($details[1] - $amountValue) > 1))
            $overridden = 'y';

        if($this->isAdmin()){
            $sqlQuery = "SELECT did FROM mastersalary WHERE employeeid = \"$employeeId\" && sessionid = \"$sessionId\" && active = \"y\" ";
            $sqlQuery = $this->processArray($sqlQuery);
            if($sqlQuery[0] != ""){
                $pendingCounter = $sqlQuery[0];
                $status = true;
            }
            else
                $pendingCounter = $this->getCounter("masterSalaryDependency");            

            $sqlQuery = "INSERT INTO mastersalary (id, did, employeeid, sessionid, allowanceid, amount, type, overridden, active) VALUES (\"$counter\", \"$pendingCounter\", \"$employeeId\", \"$sessionId\", \"$details[0]\", \"".abs($details[1])."\", \"$pendingId\", \"$overridden\", \"y\")";
            $this->processQuery($sqlQuery);

            if(!$status){
                $pendingId = $this->setPendingWork($pendingCounter);
                $_SESSION['pendingId'] = $pendingId;
                $this->insertProcess($pendingId, "New Salary Subtype Defined ----Type <i>".$this->allowance->getAllowanceTypeName($details[0])."</i> Magnitude <i>".$details[1]."</i> ");
            }
            else{
                $pendingId = $_SESSION['pendingId'];
                unset($_SESSION['pendingId']);
                $log = $this->getPendingLogIdInfo($pendingId);
                $this->dropProcessLog($pendingId);
                $this->insertProcess($pendingId, $log[6]." ---- Type <i>".$this->allowance->getAllowanceTypeName($details[0])."</i> Magnitude <i>".$details[1]."</i> ");
            }
            return true;

        }else{            
            $sqlQuery = "SELECT did FROM bakmastersalary WHERE employeeid = \"$employeeId\" && sessionid = \"$sessionId\" ";
            $sqlQuery = $this->processQuery($sqlQuery);
            if(mysql_num_rows($sqlQuery)){
                $sqlQuery = mysql_fetch_array($sqlQuery);
                $pendingCounter = $sqlQuery[0];
                $status = true;
            }else{
                $pendingCounter = $this->getCounter("masterSalaryDependency");
            }
            $sqlQuery = "INSERT INTO bakmastersalary (id, did, employeeid, sessionid, allowanceid, amount, type, overridden, active) VALUES (\"$counter\", \"$pendingCounter\", \"$employeeId\", \"$sessionId\", \"$details[0]\", \"".abs($details[1])."\", \"$pendingId\", \"$overridden\", \"y\")";
            $this->processQuery($sqlQuery);

            $this->setPendingWork($pendingCounter);
            return true;
        }
        return false;

    }

    public function setEmployeeBankInfo($details){
        $counter = $this->getCounter("employeeBank");
        if($this->isAdmin()){
            $bank = new bank();
            
            $sqlQuery = "INSERT INTO bankaccount (id, employeeid, salary_accountno, salary_bankid, pension_accountno, pension_bankid) VALUES (\"$counter\", \"$details[0]\", \"$details[1]\", \"$details[2]\", \"$details[3]\", \"$details[4]\")";
            $this->processQuery($sqlQuery);

            $pendingId = $this->setPendingWork($counter);
            $this->insertProcess($pendingId, "Bank Account Info Created : Salary Account No :<i>".$details[1]."</i> Bank Name :<i>".$bank->getBankName($details[2])."</i> Pension Account No. <i>".$details[3]."</i>Bank Name :<i>".$bank->getBankName($details[4])."</i>");

            $counter = $this->getCounter("basic");
            $sqlQuery = "INSERT INTO basic (id, employeeid, amount) VALUES (\"$counter\", \"$details[0]\", \"$details[5]\") ";
            $this->processQuery($sqlQuery);

            $pendingId = $this->setPendingWork($counter);
            $this->insertProcess($pendingId, "Basic Salary Set : Amount : <i>".$details[5]."</i>");

            return true;
        }else{
            $sqlQuery = "INSERT INTO bakbankaccount (id, employeeid, salary_accountno, salary_bankid, pension_accountno, pension_bankid) VALUES (\"$counter\", \"$details[0]\", \"$details[1]\", \"$details[2]\", \"$details[3]\", \"$details[4]\")";
            $this->processQuery($sqlQuery);
            $this->setPendingWork($counter);

            $counter = $this->getCounter("basic");
            $sqlQuery = "INSERT INTO bakbasic (id, employeeid, amount) VALUES (\"$counter\", \"$details[0]\", \"$details[5]\") ";
            $this->processQuery($sqlQuery);
            $this->setPendingWork($counter);

            return true;
        }
    }

    public function updateBankInformation($employeeid, $details){        
        $variable = array();

        $bank = new bank();
        $employeeInfo = new employeeInfo();

        $variable[0] = $employeeInfo->getEmployeeBankAccoutDetails($employeeid, true);
        $status = false;

        if($details[0] != $variable[0][2]){
            $status = true;
            $update .= "Salary Account No From <i>".$variable[0][2]."</i> To <i>".$details[0]."</i>";
        }

        if($details[1] != $variable[0][3]){
            $status = true;
            $update .= "Salary Account Bank From <i>".$bank->getBankName($variable[0][3])."</i> To <i>".$bank->getBankName($details[1])."</i>";
        }

        if($details[2] != $variable[0][4]){
            $status = true;
            $update .= "Pension Account No From <i>".$variable[0][4]."</i> To <i>".$details[2]."</i>";
        }

        if($details[3] != $variable[0][5]){
            $status = true;
            $update .= "Pension Account Bank From <i>".$bank->getBankName($variable[0][5])."</i> To <i>".$bank->getBankName($details[3])."</i>";
        }
        
        if($status){
            $variable[1] = $employeeInfo->getEmployeeBankAccoutDetails($employeeId, false);
            if($this->isAdmin()){ //the logged in individual is an admin privileged user
                if($variable[1][0] != ""){ //there is pending job in this respect

                    $sqlQuery = "UPDATE bakbankaccount SET salary_accountno = \"$details[0]\", salary_bankid = \"$details[1]\", pension_accountno=\"$details[2]\", pension_bankid = \"$details[3]\" WHERE id = \"".$variable[1][0]."\" ";
                    $this->processQuery($sqlQuery);

                    if($variable[0][0] != ""){
                        $sqlQuery = "DELETE FROM bankaccount WHERE id = \"".$variable[1][0]."\" ";
                        $this->processQuery($sqlQuery);
                    }

                    $sqlQuery = "INSERT INTO bankaccount (SELECT * FROM bakbankaccount WHERE id = \"".$variable[1][0]."\" )";
                    $this->processQuery($sqlQuery);
                    
                    $sqlQuery = "DELETE FROM bakbankaccount WHERE id = \"".$variable[1][0]."\"";
                    $this->processQuery($sqlQuery);

                    $pendingId = $this->setPendingWork($variable[1][0]);
                    $this->insertProcess($pendingId, $update);

                }else{ //there is no pending job earlier
                    if($variable[0][0] != ""){ //there is already an entry in the table
                        $sqlQuery = "UPDATE bankaccount SET salary_accountno = \"$details[0]\", salary_bankid = \"$details[1]\", pension_accountno=\"$details[2]\", pension_bankid = \"$details[3]\" WHERE id = \"".$variable[0][0]."\" ";
                        $this->processQuery($sqlQuery);

                        $pendingId = $this->setPendingWork($variable[0][0]);
                        $this->insertProcess($pendingId, $update);

                    }else{ //there is no entry in the database
                        $counter = $this->getCounter('employeeBank');

                        $sqlQuery = "INSERT INTO bankaccount (id, employeeid, salary_accountno, salary_bankid, pension_accountno, pension_bankid) VALUES (\"$counter\", \"$employeeid\", \"$details[0]\", \"$details[1]\", \"$details[2]\", \"$details[3]\")";
                        echo $sqlQuery;
                        $this->processQuery($sqlQuery);

                        $pendingId = $this->setPendingWork($counter);
                        $this->insertProcess($pendingId, "New entry of Bank done");

                    }
                }
            }else{ //the logged individual is not an admin
                if($variable[1][0] != ""){ //there is a pending entry in this respect
                    $sqlQuery = "UPDATE bakbankaccount SET salary_accountno = \"$details[0]\", salary_bankid = \"$details[1]\", pension_accountno=\"$details[2]\", pension_bankid = \"$details[3]\" WHERE id = \"".$variable[1][0]."\" ";
                    $this->processQuery($sqlQuery);

                    $this->setPendingWork($variable[1][0]);

                }else{ //there is no pending entry
                    if($variable[0][0] != ""){ //there exists a master entry for this employee bank info
                        $sqlQuery = "INSERT INTO bakbankaccount (SELECT * FROM bankaccount WHERE id = \"".$variable[0][0]."\" )";
                        $this->processQuery($sqlQuery);

                        $this->setPendingWork($variable[0][0]);

                    }else{ //new entry has to be made for the given details
                        $counter = $this->getCounter('employeeBank');

                        $sqlQuery = "INSERT INTO bakbankaccount (id, employeeid, salary_accountno, salary_bankid, pension_accountno, pension_bankid) VALUES (\"$counter\", \"$employeeId\", \"$details[0]\", \"$details[1]\", \"$details[2]\", \"$details[3]\")";
                        $this->processQuery($sqlQuery);

                        $this->setPendingWork($counter);
                    }
                }
            }
        }
        return;        
    }

    public function updateEmployeeBasicSalary($employeeId, $basic){
        $variable = array();

        $employeeInfo = new employeeInfo();
        $variable[0] = $employeeInfo->getEmployeeBasicSalaryDetails($employeeId, true);
        $status = false;
        
        if($variable[0][2] != $basic){
            $status = true;
            $update = "Basic Salary Changed From <i>".$variable[0][2]."</i> To <i>".$basic."</i>";
        }

        if($this->isAdmin() && $status){
            $variable[1] = $employeeInfo->getEmployeeBasicSalaryDetails($employeeId, true);
            if($variable[1][0] == ""){
                $counter = $this->getCounter("basic");
                $sqlQuery = "INSERT INTO basic (id, employeeid, amount) VALUES (\"$counter\", \"$employeeId\", \"$basic\") ";
                $status = false;
            }else{
                $sqlQuery = "UPDATE basic SET amount = \"$basic\" WHERE employeeid = \"$employeeId\" ";
            }
            $this->processQuery($sqlQuery);

            $sqlQuery = "DELETE FROM bakbasic WHERE employeeid = \"$employeeId\" ";
            $this->processQuery($sqlQuery);
			
            $this->accountClass->updateEmployeeBasicComponent($employeeId);            
            
            if(!$status){
                $pendingId = $this->setPendingWork($counter);
                $this->insertProcess($pendingId, "Basic Salary Set : Amount : <i>".$basic."</i>");
                return true;
            }
            $pendingId = $this->setPendingWork($variable[0][0]);
            $this->insertProcess($pendingId, $update);
            return true;
        }elseif($status){
            $variable[1] = $employeeInfo->getEmployeeBasicSalaryDetails($employeeId, true);
            if($variable[1][0] == ""){
                $variable[1] = $employeeInfo->getEmployeeBasicSalaryDetails($employeeId, false);
                if($variable[1][0] == ""){
                    $counter = $this->getCounter("basic");
                    $sqlQuery = "INSERT INTO bakbasic (id, employeeid, amount) VALUES (\"$counter\", \"$employeeId\", \"$basic\") ";
                    $status = false;
                }
            }else{
                $sqlQuery = "INSERT INTO bakbasic (SELECT * FROM basic WHERE employeeid = \"$employeeId\")";
            }
            $this->processQuery($sqlQuery);

            $sqlQuery = "UPDATE bakbasic SET amount = \"$basic\" WHERE employeeid = \"$employeeId\" ";
            $this->processQuery($sqlQuery);

            if($status)
                $this->setPendingWork($variable[0][0]);
            else
                $this->setPendingWork($counter);
            return true;
        }else{
            return false;
        }
    }

    public function updateMasterAccountDetails($employeeId, $details){
        $sessionId = $this->getCurrentSession();

        if($details[6] > 0)
            $variable = 'c';
        else
            $variable = 'd';

        if($details[5] == 0)
            return;

        $sqlQuery = "UPDATE bakmastersalary SET amount = \"$details[5]\", type = \"$variable\" WHERE id = \"$details[0]\" ";
        $this->processQuery($sqlQuery);

        if($this->isAdmin()){
            $salary = array();

            $sqlQuery = "UPDATE mastersalary SET active = \"\" WHERE employeeid = \"".$details[2]."\" && did != \"$details[1]\"";
            $this->processQuery($sqlQuery);

            $sqlQuery = "INSERT INTO mastersalary (SELECT * FROM bakmastersalary WHERE id = \"$details[0]\")";
            $this->processQuery($sqlQuery);

            $sqlQuery = "DELETE FROM bakmastersalary WHERE id = \"$details[0]\"";
            $this->processQuery($sqlQuery);

            $sqlQuery = "SELECT id FROM bakmastersalary WHERE did = \"$details[1]\" ";
            $sqlQuery = $this->processQuery($sqlQuery);

            if(mysql_num_rows($sqlQuery)){
                $_SESSION['update'] .= "New Allowance Name <i>".$this->allowance->getAllowanceTypeName($details[4])."</i> Magnitude <i>".$details[5]."</i>";
            }else{
                $update = $_SESSION['update']."New Allowance Name <i>".$this->allowance->getAllowanceTypeName($details[4])."</i> Magnitude <i>".$details[5]."</i>";
                $pendingId = $this->setPendingWork($details[1]);
                $this->insertProcess($pendingId, $update);
            }
        }else{
            $this->setPendingWork($details[1]);            
        }        
    }
    
}
?>
