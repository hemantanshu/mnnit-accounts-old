<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */
////error_reporting(0);
require_once 'class.pending.php';
require_once 'class.allowance.php';
require_once 'class.housing.php';
require_once 'class.personalInfo.php';
require_once 'class.designation.php';
require_once 'class.employeeInfo.php';
require_once 'class.accountHead.php';
require_once 'class.loan.php';
require_once 'class.gpftotal.php';

class accounts extends pending
{
    private $allowance;
    private $personalInfo;
    private $housing;
    private $designation;
    private $employeeInfo;
    public $salaryHead;
    public $salaryAmount;
    private $loan;
    private $gpfTotal;
    private $accountHead;

    public function __construct ()
    {
        parent::__construct();

        $this->allowance = new allowance();
        $this->personalInfo = new personalInfo();
        $this->housing = new housing();
        $this->designation = new designation();
        $this->employeeInfo = new employeeInfo();
        $this->loan = new loan();
        $this->gpfTotal = new gpfTotal();
        $this->accountHead = new accountHead();
    }

    public function getTotalMonthDays ()
    {
        $even = array(1, 3, 5, 7, 8, 10, 12);
        $odd = array(4, 6, 9, 11);
        $nonodd = array(2);

        $month = date('m');
        if ( in_array($month, $even) )
            return '31';
        elseif ( in_array($month, $odd) )
            return '30';
        else {
            if ( date('Y') % 4 == 0 )
                return '29';
            else
                return '28';
        }
    }

    public function getEmployeeBasicSalary ($employeeId, $type)
    {
        if ( $type == 'total' ) {
            $sqlQuery = "SELECT sum(amount) FROM salaryadditions WHERE type=\"c\" && allowanceid = \"ACT1\" && employeeid = \"$employeeId\" && month=\"$this->currentMonth\"";
            $sqlQuery = $this->processArray($sqlQuery);
            $sum = $sqlQuery[0];

            $sqlQuery = "SELECT sum(amount) FROM salaryadditions WHERE type=\"d\" && allowanceid = \"ACT1\" && employeeid = \"$employeeId\" && month=\"$this->currentMonth\"";
            $sqlQuery = $this->processArray($sqlQuery);
            $sum = $sum - $sqlQuery[0];

            return ( $sum + $this->getValue("amount", "basic", "employeeid", $employeeId) == "" ? $this->getValue("amount", "bakbasic", "employeeid", $employeeId) : $this->getValue("amount", "basic", "employeeid", $employeeId) );
        }

        return $this->getValue("amount", "basic", "employeeid", $employeeId) == "" ? $this->getValue("amount", "bakbasic", "employeeid", $employeeId) : $this->getValue("amount", "basic", "employeeid", $employeeId);
    }

    public function getAccountSum ($employeeId, $accountId)
    {
        $variable = array();

        if ( $accountId == "ACT1" )
            return $this->getEmployeeBasicSalary($employeeId);

        $variable[2] = $this->getValue("value", "subheads", "allowanceid", $accountId);
        if ( $variable[2] == "" && $accountId != "" ) {
            $variable[2] = $this->getValue("id", "options", "field", $accountId);
            if ( $variable[2] == "" )
                return 0;
            else {
                $this->personalInfo->getEmployeeInformation($employeeId, true);
                $variable[2] = $this->personalInfo->getHousingType();

                return $this->housing->getHouseTypeValue($variable[2]);
            }
        }

        $variable[0] = $this->allowance->getAllowanceDependentIds($accountId, true);

        if ( sizeof($variable[0]) == 1 ) {  //the account type has only one dependency
            $value = $this->allowance->getAllowanceDependentDetails($variable[0][0], true);
            if ( $value[3] == "" ) {
                if ( $value[4] == 'c' )
                    return $value[2];
                else
                    return ( 0 - $value[2] );
            } else {
                if ( $value[4] == 'c' )
                    return ( $value[2] * .01 * abs($this->getAccountSum($employeeId, $value[3])) );
                else
                    return ( 0 - $value[2] * .01 * abs($this->getAccountSum($employeeId, $value[3])) );
            }
        }

        $total = 0;
        foreach ( $variable[0] as $value ) {  //the account type has multiple dependency
            $variable[1] = $this->allowance->getAllowanceDependentDetails($value, true);
            if ( $variable[1][3] == "" ) {
                if ( $variable[1][4] == 'c' )
                    $total += $variable[1][2];
                else
                    $total -= $variable[1][2];
            } else {
                if ( $variable[1][4] == 'c' )
                    $total += ( $variable[1][2] * .01 * abs($this->getAccountSum($employeeId, $variable[1][3])) );
                else
                    $total -= ( $variable[1][2] * .01 * abs($this->getAccountSum($employeeId, $variable[1][3])) );
            }
        }

        return $total;
    }

    public function getEmployeeSalaryInfo ($employeeId, $allowanceId)
    {
        $sqlQuery = "SELECT amount, type FROM mastersalary WHERE employeeid = \"$employeeId\" && allowanceid = \"$allowanceId\" && active = \"y\" ";
        $sqlQuery = $this->processQuery($sqlQuery);
        if ( mysql_num_rows($sqlQuery) ) {
            $result = mysql_fetch_array($sqlQuery);
            if ( $result['type'] == 'c' )
                return $result['amount'];
            else {
                $variable = 0 - $result['amount'];

                return $variable;
            }
        }

        return false;
    }


    public function getRankBenefitTotal ($employeeId, $dependentId)
    {
        $variable = array();

        $variable[0] = $this->designation->getDesignationDependentDetails($dependentId, true);

        if ( $variable[0][3] == "" )
            return $variable[0][2];
        else
            return $variable[0][2] * 0.01 * abs($this->getAccountSum($employeeId, $variable[0][3]));
    }

    public function getTotalSalary ($employeeId)
    {
        $total = 0;
        $sessionId = $this->designation->getCurrentSession();
        $salaryId = $this->employeeInfo->getSessionMasterSalaryIds($employeeId, $sessionId);
        foreach ( $salaryId as $value ) {
            $details = $this->employeeInfo->getSalaryIdDetails($value, true);
            if ( $details[6] == 'c' )
                $total += $details[5];
            else
                $total -= $details[5];
        }
        $total += $this->getEmployeeBasicSalary($employeeId);

        //******************************************************************************************//
        // CALCULATION OF SALARY THAT IS NOT IN THE MASTER TABLE BUT HAS TO BE PROCESSED//        
        $sqlQuery = "SELECT * FROM salaryadditions WHERE employeeid = \"$employeeId\" && month = \"$this->currentMonth\"";
        $sqlQuery = $this->processQuery($sqlQuery);
        while ( $result = mysql_fetch_row($sqlQuery) ) {
            if ( $result[4] == 'c' )
                $total += $result[3];
            else
                $total -= $result[3];
        }

        //******************************************************************************************//
        return $total;
    }

    public function processEmployeeMonthlySalary ($employeeId, $days, $bankTransfer)
    {
        $monthId = $this->currentMonth;
        $totalMonthDays = $this->getTotalMonthDays();

        $processingId = $this->getCounter("salaryProcessing");
        $counter = $this->getCounter("salary");

        $sessionId = $this->designation->getCurrentSession();
        $salaryId = $this->employeeInfo->getMasterSalaryId($employeeId, true);

        $basicSalary = $this->getEmployeeBasicSalary($employeeId);

        $basicSalary *= $days / $totalMonthDays;
        if ( $this->allowance->isAllowanceRoundable('ACT1') )
            $basicSalary = $this->roundAmount($basicSalary);

        $sqlQuery = "INSERT INTO baksalary (id, did, employeeid, allowanceid, accounthead, amount, type, month) VALUES (\"$counter\", \"$processingId\", \"$employeeId\", \"ACT1\", \"ACH1\", \"$basicSalary\", \"c\", \"$monthId\") ";
        $this->processQuery($sqlQuery);

        foreach ( $salaryId as $value ) { //inserting the master salary frame into the current month salary            
            $details = $this->employeeInfo->getSalaryIdDetails($value, true);
            $accountHead = $this->allowance->getAllowanceAccountHead($details[4]);

            $details[5] *= $days / $totalMonthDays;
            if ( $this->allowance->isAllowanceRoundable($details[4]) )
                $details[5] = $this->roundAmount($details[5]);

            $counter = $this->getCounter("salary");
            $sqlQuery = "INSERT INTO baksalary (id, did, employeeid, allowanceid, accounthead, amount, type, month) VALUES (\"$counter\", \"$processingId\", \"$employeeId\", \"$details[4]\", \"$accountHead\", \"$details[5]\", \"$details[6]\", \"$monthId\") ";
            $this->processQuery($sqlQuery);
        }

        $this->employeeInfo->preserveEmployeeInformation($employeeId, false, $bankTransfer); //preserving the employee information
        /****************************************************************************************************/
        //processing the General Loan Installment        
        $data = $this->loan->processEmployeeLoanInstallment($employeeId, false);
        $i = 0;
        while ( true && $data ) {
            if ( $data[ $i ][0] == "" )
                break;

            $amount = $data[ $i ][1];
            $allowanceId = $data[ $i ][0];
            if ( $amount == 0 )
                continue;

            $counter = $this->getCounter("salary");
            $accountHead = $this->allowance->getAllowanceAccountHead($data[ $i ][0]);

            $sqlQuery = "INSERT INTO baksalary (id, did, employeeid, allowanceid, accounthead, amount, type, month) VALUES (\"$counter\", \"$processingId\", \"$employeeId\", \"$allowanceId\", \"$accountHead\", \"$amount\", \"d\", \"$this->currentMonth\") ";
            $this->processQuery($sqlQuery);
            ++$i;
        }
        //****************************************************************************************************//

        //****************************************************************************************************//	
        //processing the GPF Loan Installment
        $amount = $this->gpfTotal->processEmployeGPFLoanInstallment($employeeId, false);
        if ( $amount ) {
            $counter = $this->getCounter("salary");
            $accountHead = $this->allowance->getAllowanceAccountHead("ACT33");

            $sqlQuery = "INSERT INTO baksalary (id, did, employeeid, allowanceid, accounthead, amount, type, month) VALUES (\"$counter\", \"$processingId\", \"$employeeId\", \"ACT33\", \"$accountHead\", \"$amount\", \"d\", \"$this->currentMonth\") ";
            $this->processQuery($sqlQuery);
        }
        //****************************************************************************************************//

        //****************************************************************************************************************************//
        //INCLUDING THE SALARY THAT IS NOT PART OF THE MASTER SALARY BUT HAS BEEN ENFORCED UPON IT. additional salary component
        $extraSalaryId = array();
        $sqlQuery = "SELECT id FROM salaryadditions WHERE employeeid = \"$employeeId\" && month = \"$this->currentMonth\" ";
        $sqlQuery = $this->processQuery($sqlQuery);

        while ( $result = mysql_fetch_row($sqlQuery) ) {
            array_push($extraSalaryId, $result[0]);
        }
        foreach ( $extraSalaryId as $value ) {
            $sqlQuery = "SELECT * FROM salaryadditions WHERE id = \"$value\" ";
            $details = $this->processArray($sqlQuery);


            $sqlQuery = "SELECT amount, type FROM baksalary WHERE employeeid = \"$employeeId\" && allowanceid = \"$details[2]\" && month = \"$monthId\" ";
            $sqlQuery = $this->processQuery($sqlQuery);

            if ( mysql_num_rows($sqlQuery) ) {
                $sqlQuery = mysql_fetch_array($sqlQuery);
                $salaryAmount = $sqlQuery['type'] == 'c' ? $sqlQuery['amount'] : ( 0 - $sqlQuery['amount'] );
                $additionAmount = $details[4] == 'c' ? $details[3] : ( 0 - $details[3] );
                if ( $this->allowance->isAllowanceRoundable($details[2]) )
                    $additionAmount = $this->roundAmount($additionAmount);

                $salaryAmount += $additionAmount;
                $type = $salaryAmount < 0 ? 'd' : 'c';
                $salaryAmount = abs($salaryAmount);

                $sqlQuery = "UPDATE baksalary SET amount = \"$salaryAmount\", type = \"$type\" WHERE employeeid = \"$employeeId\" && allowanceid = \"$details[2]\" && month = \"$monthId\" ";
                $this->processQuery($sqlQuery);
            } else {
                $counter = $this->getCounter("salary");
                $accountHead = $this->allowance->getAllowanceAccountHead($details[2]);

                $sqlQuery = "INSERT INTO baksalary (id, did, employeeid, allowanceid, accounthead, amount, type, month) VALUES (\"$counter\", \"$processingId\", \"$employeeId\", \"$details[2]\", \"$accountHead\", \"$details[3]\", \"$details[4]\", \"$monthId\") ";
                $this->processQuery($sqlQuery);
            }
        }
        //****************************************************************************************************************************//
        if ( $this->isAdmin() ) {
            $sqlQuery = "INSERT INTO salary (SELECT * FROM baksalary WHERE did = \"$processingId\")";
            $this->processQuery($sqlQuery);

            $sqlQuery = "DELETE FROM baksalary WHERE did = \"$processingId\" ";
            $this->processQuery($sqlQuery);

            $this->gpfTotal->processEmployeeGpfTotal($employeeId); //inserting the gpf amount & advance recovery in the total gpf balance
            $this->employeeInfo->preserveEmployeeInformation($employeeId, true);    //preserving the employee current month information

            $this->loan->processEmployeeLoanInstallment($employeeId, true); //processing the loan installment
            $this->gpfTotal->processEmployeGPFLoanInstallment($employeeId, true); //processing the gpf loan installment

            $this->processCollegeContribution($employeeId);

            $pendingId = $this->setPendingWork($processingId);
            $this->insertProcess($pendingId, "Salary Processed For Employee");

            return true;
        } else {
            $this->setPendingWork($processingId);

            return true;
        }
    }


    public function preserveAccountHead ()
    {
        $month = $this->currentMonth;
        $sqlQuery = "SELECT name FROM salaryaccounthead WHERE month = \"$month\"";
        $sqlQuery = $this->processQuery($sqlQuery);
        if ( mysql_num_rows($sqlQuery) )
            return true;

        $accountHeadIds = $this->accountHead->getAccountHeadIds(true);
        foreach ( $accountHeadIds as $values ) {
            $id = $this->getCounter('salaryHeadPreserve');
            $name = $this->accountHead->getAccountHeadName($values);
            $sqlQuery = "INSERT INTO salaryaccounthead (id, accounthead, name, month) VALUES (\"$id\", \"$values\", \"$name\", \"$month\")";
            $this->processQuery($sqlQuery);
        }
    }

    public function updateProcessedSalary ($employeeId)
    {
        $sqlQuery = "SELECT did FROM baksalary WHERE employeeid = \"$employeeId\" LIMIT 1";
        $sqlQuery = $this->processQuery($sqlQuery);
        if ( mysql_num_rows($sqlQuery) ) {
            $sqlQuery = mysql_fetch_array($sqlQuery);
            $processingId = $sqlQuery[0];

            if ( $this->isAdmin() ) {
                $sqlQuery = "INSERT INTO salary (SELECT * FROM baksalary WHERE did = \"$processingId\")";
                $this->processQuery($sqlQuery);

                $sqlQuery = "DELETE FROM baksalary WHERE did = \"$processingId\" ";
                $this->processQuery($sqlQuery);

                $pendingId = $this->setPendingWork($processingId);
                $this->insertProcess($pendingId, "Salary Of The Employee Processed");

                $this->loan->processEmployeeLoanInstallment($employeeId, true);
                $this->gpfTotal->processEmployeGPFLoanInstallment($employeeId, true);
                $this->gpfTotal->processEmployeeGpfTotal($employeeId);
                $this->employeeInfo->preserveEmployeeInformation($employeeId, true);
                $this->processCollegeContribution($employeeId);

                return true;
            } else {
                $this->setPendingWork($processingId);

                return true;
            }
        }
    }


    public function getSalaryProcessingIdSum ($processingId)
    {
        $sqlQuery = "SELECT amount, type FROM salary WHERE did = \"$processingId\" ";
        $sqlQuery = $this->processQuery($sqlQuery);
        if ( !mysql_num_rows($sqlQuery) ) {
            $sqlQuery = "SELECT amount, type FROM baksalary WHERE did = \"$processingId\" ";
            $sqlQuery = $this->processQuery($sqlQuery);
        }
        $total = 0;
        while ( $result = mysql_fetch_array($sqlQuery) ) {
            if ( $result['type'] == 'c' )
                $total += $result['amount'];
            else
                $total -= $result['amount'];
        }

        return $total;
    }

    public function getProcessedSalarySum ($employeeId, $month)
    {
        $sqlQuery = "SELECT SUM(amount) FROM salary WHERE employeeid = \"$employeeId\" && month = \"$month\" && type = \"c\" ";
        $sqlQuery = $this->processArray($sqlQuery);
        $sum = $sqlQuery[0];

        $sqlQuery = "SELECT SUM(amount) FROM salary WHERE employeeid = \"$employeeId\" && month = \"$month\" && type = \"d\" ";
        $sqlQuery = $this->processArray($sqlQuery);
        $sum -= $sqlQuery[0];

        return $sum;
    }

    public function getNPSContribution ($employeeId, $month)
    {
        $arr = array();

        $sqlQuery = "SELECT SUM(amount) FROM npstotal WHERE employeeid = \"$employeeId\" && month = \"$month\" && flag = \"m\" ";
        $sqlQuery = $this->processArray($sqlQuery);
        $arr[0] = $sqlQuery[0];

        $sqlQuery = "SELECT SUM(amount) FROM npstotal WHERE employeeid = \"$employeeId\" && month = \"$month\" && flag = \"c\" ";
        $sqlQuery = $this->processArray($sqlQuery);
        $arr[1] = $sqlQuery[0];

        return $arr;
    }

    public function getSalaryProcessingIdIDS ($processingId)
    {
        $sqlQuery = "SELECT id FROM salary WHERE did = \"$processingId\"";
        $sqlQuery = $this->processQuery($sqlQuery);

        if ( !mysql_num_rows($sqlQuery) ) {
            $sqlQuery = "SELECT id FROM baksalary WHERE did = \"$processingId\"";
            $sqlQuery = $this->processQuery($sqlQuery);
        }
        $details = array();
        while ( $result = mysql_fetch_array($sqlQuery) )
            array_push($details, $result[0]);

        return $details;
    }

    public function getSalaryIdDetails ($id)
    {
        $sqlQuery = "SELECT * FROM salary WHERE id = \"$id\"";
        $sqlQuery = $this->processQuery($sqlQuery);

        if ( !mysql_num_rows($sqlQuery) ) {
            $sqlQuery = "SELECT * FROM baksalary WHERE id = \"$id\"";
            $sqlQuery = $this->processQuery($sqlQuery);
        }
        $details = mysql_fetch_array($sqlQuery);

        return $details;
    }

    public function getSalaryReceiptIds ($employeeId, $date)
    {
        $sqlQuery = "SELECT id FROM salary WHERE employeeid = \"$employeeId\" && month = \"$date\" ORDER BY type, allowanceid ASC";
        $sqlQuery = $this->processQuery($sqlQuery);

        if ( !mysql_num_rows($sqlQuery) ) {
            $sqlQuery = "SELECT id FROM baksalary WHERE employeeid = \"$employeeId\" && month = \"$date\" ORDER BY type, allowanceid ASC";
            $sqlQuery = $this->processQuery($sqlQuery);
        }
        $details = array();
        while ( $result = mysql_fetch_array($sqlQuery) )
            array_push($details, $result[0]);

        return $details;
    }

    public function getSalaryProcessingIds ($date)
    {
        $sqlQuery = "SELECT distinct(did) FROM salary, bankaccount WHERE month = \"$date\" && salary.employeeid=bankaccount.employeeid ORDER BY  CAST(bankaccount.salary_accountno AS SIGNED) ASC";
        $sqlQuery = $this->processQuery($sqlQuery);

        if ( !mysql_num_rows($sqlQuery) ) {
            $sqlQuery = "SELECT distinct(did) FROM baksalary WHERE month = \"$date\" ";
            $sqlQuery = $this->processQuery($sqlQuery);
        }
        $details = array();
        while ( $result = mysql_fetch_array($sqlQuery) )
            array_push($details, $result[0]);

        return $details;
    }

    public function updateMasterSalaryAllowanceData ($allowanceId)
    {
        $session = $this->getCurrentSession();

        $sqlQuery = "SELECT employeeId FROM mastersalary WHERE allowanceid = \"$allowanceId\" && active = \"y\" && overridden = \"\" ";
        $sqlQuery = $this->processQuery($sqlQuery);

        while ( $result = mysql_fetch_array($sqlQuery) ) {
            $employeeId = $result[0];

            $amountValue = $this->getAccountSum($employeeId, $allowanceId);
            if ( $amountValue == 0 )
                continue;

            if ( $amountValue > 0 )
                $type = 'c';
            else
                $type = 'd';

            $amountValue = $this->roundAmount(abs($amountValue));

            $masterCounter = $this->getCounter('masterSalary');
            $dependentCounter = $this->getCounter('masterSalaryDependency');

            $query = "UPDATE mastersalary SET active = \"\" WHERE employeeid = \"$employeeId\" && allowanceid = \"$allowanceId\" ";
            $this->processQuery($query);

            $query = "INSERT INTO mastersalary (id, did, employeeid, sessionid, allowanceid, amount, type, overridden, active) VALUES (\"$masterCounter\", \"$dependentCounter\", \"$employeeId\", \"$session\", \"$allowanceId\", \"$amountValue\", \"$type\", \"\", \"y\")";
            $this->processQuery($query);
        }

    }

    private function processCollegeContribution ($employeeId)
    {
        $allowanceId = $this->allowance->getCollegeContributionAllowanceIds();
        foreach ( $allowanceId as $value ) {
            $sqlQuery = "SELECT amount FROM salary WHERE allowanceid = \"$value\" && month = \"$this->currentMonth\" && employeeid = \"$employeeId\" && type = \"d\" ";
            $amount = $this->processArray($sqlQuery);

            if ( $amount[0] == "" || $amount[0] == 0 )
                continue;

            if ( $value != "ACT23" ) {
                $contributionAmount = abs($this->getAccountSum($employeeId, $value));
                $contributionAmount = $contributionAmount > $amount[0] ? $amount[0] : $contributionAmount;

                if ( $contributionAmount != 0 && $value == "ACT21" ) {
                    $counter = $this->getCounter("cpfTotal");
                    $sqlQuery = "INSERT INTO cpftotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"" . $amount[0] . "\", \"$this->currentMonth\", \"m\")";
                    $this->processQuery($sqlQuery);

                    $counter = $this->getCounter("cpfTotal");
                    $sqlQuery = "INSERT INTO cpftotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"" . $contributionAmount . "\", \"$this->currentMonth\", \"c\")";
                    $this->processQuery($sqlQuery);


                }
            } else {
                $contributionAmount = $amount[0] * 1.4;
                if ( $contributionAmount != 0 ) {
                    $counter = $this->getCounter("npsTotal");
                    $sqlQuery = "INSERT INTO npstotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"" . $amount[0] . "\", \"$this->currentMonth\", \"m\")";
                    $this->processQuery($sqlQuery);

                    $counter = $this->getCounter("npsTotal");
                    $sqlQuery = "INSERT INTO npstotal (id, employeeid, amount, month, flag) VALUES (\"$counter\", \"$employeeId\", \"" . $contributionAmount . "\", \"$this->currentMonth\", \"c\")";
                    $this->processQuery($sqlQuery);
                }
            }
            $contributionAmount = $this->roundAmount($contributionAmount);

            $counter = $this->getCounter('collegeContribution');
            $accountHead = $this->allowance->getAllowanceAccountHead($value);

            $sqlQuery = "INSERT INTO collegecontribution (id, employeeid, allowanceid, accountheadid, amount, month) VALUES (\"$counter\", \"$employeeId\", \"$value\", \"$accountHead\", \"$contributionAmount\", \"$this->currentMonth\")";
            $this->processQuery($sqlQuery);
        }
    }

    public function updateEmployeeBasicComponent ($employeeId)
    {
        $sqlQuery = "SELECT allowanceid FROM subheads WHERE dependent = \"ACT1\" ";
        $sqlQuery = $this->processQuery($sqlQuery);
        $completeIds = array();
        while ( $result = mysql_fetch_array($sqlQuery) )
            array_push($completeIds, $result[0]);
        foreach ( $completeIds as $id ) {
            $amount = $this->getAccountSum($employeeId, $id);
            if ( $amount == 0 )
                continue;
            $type = $amount > 0 ? 'c' : 'd';
            $sqlQuery = "UPDATE mastersalary SET amount = \"" . abs($amount) . "\", type = \"$type\" WHERE employeeid = \"$employeeId\" && allowanceid = \"$id\" && overridden = \"\" && active = \"y\" ";
            $this->processQuery($sqlQuery);
        }
    }

    public function roundAmount ($amount)
    {
        $variable = (int) $amount;

        if ( $amount > 0 ) {
            if ( $amount - $variable >= .5 )
                return $variable + 1;
            else
                return $variable;
        } else {
            if ( $variable - $amount >= .5 )
                return $variable - 1;
            else
                return $variable;
        }
    }
}

?>
