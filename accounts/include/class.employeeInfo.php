<?php
/*Licensed Under Support Gurukul. http://www.supportgurukul.com */

require_once 'class.sqlFunctions.php';
require_once 'class.designation.php';

class employeeInfo extends sqlFunction
{
    private $designation;

    public function __construct ()
    {
        parent::__construct();

        $this->designation = new designation();
    }

    public function getEmployeeIds ($flag, $report)
    {
        if ( func_num_args() > 1 ) {
            if ( $report == 'salaryProcess' )
                $sqlQuery = "SELECT id FROM employee WHERE id NOT IN (SELECT employeeid FROM blocksalary WHERE smonth <= \"$this->currentMonth\" && (emonth >= \"$this->currentMonth\" || emonth = \"\")) ";
            elseif ( $report == 'salary' )
                $sqlQuery = "SELECT distinct(employeeid) FROM salary WHERE month = \"$this->currentMonth\" ";
            elseif ( $report == 'block' )
                $sqlQuery = "SELECT employeeid FROM blocksalary WHERE (emonth >= '$this->currentMonth' || emonth = \"\") ";
            elseif ( $report == "bank" )
                $sqlQuery = "SELECT a.employeeid employeeid FROM salaryemployeehead a, employee b WHERE a.employeeid = b.id and month = \"" . func_get_arg(2) . "\" order by b.name asc";
            elseif ( $report == "all" )
                $sqlQuery = "SELECT id FROM employee ORDER BY name ASC";
            elseif ( $report == "NPS" )
                $sqlQuery = "select distinct(employeeid) employeeid from npstotal where month = \"" . func_get_arg(2) . "\" ";
            elseif ( $report == "REPORT" )
                $sqlQuery = "SELECT id FROM employee ORDER BY department,type, name ASC";
            else
                $sqlQuery = "SELECT id FROM employee ORDER BY name ASC";
        } else {
            if ( $flag )
                $sqlQuery = "SELECT id FROM employee ORDER BY name ASC";
            else
                $sqlQuery = "SELECT id FROM bakemployee ORDER BY name ASC ";
        }
        $sqlQuery = $this->processQuery($sqlQuery);
        $variable = array();
        while ( $result = mysql_fetch_array($sqlQuery) )
            array_push($variable, $result[0]);

        return $variable;
    }

    public function getEmployeeRankIds ($employeeId, $flag)
    {

        if ( $flag )
            $sqlQuery = "SELECT id FROM ranks WHERE employeeid = \"$employeeId\" && edate = \"0000-00-00\" ORDER BY sdate DESC ";
        else
            $sqlQuery = "SELECT id FROM bakranks WHERE employeeid = \"$employeeId\" ORDER BY sdate DESC ";

        $sqlQuery = $this->processQuery($sqlQuery);
        $variable = array();
        while ( $result = mysql_fetch_array($sqlQuery) )
            array_push($variable, $result[0]);

        return $variable;
    }

    public function getEmployeeOldRankIds ($employeeId)
    {
        $sqlQuery = "SELECT id FROM ranks WHERE employeeid = \"$employeeId\" && edate != \"0000-00-00\"  ORDER BY sdate DESC ";
        $sqlQuery = $this->processQuery($sqlQuery);
        $variable = array();
        while ( $result = mysql_fetch_array($sqlQuery) )
            array_push($variable, $result[0]);

        return $variable;

    }

    public function getEmployeeDesignationIds ($employeeId, $flag)
    {
        if ( $flag )
            $sqlQuery = "SELECT designation FROM ranks WHERE employeeid = \"$employeeId\" && edate = \"0000-00-00\"  ORDER BY sdate DESC ";
        else
            $sqlQuery = "SELECT designation FROM bakranks WHERE employeeid = \"$employeeId\" ORDER BY sdate DESC ";

        $sqlQuery = $this->processQuery($sqlQuery);
        $variable = array();
        while ( $result = mysql_fetch_array($sqlQuery) )
            array_push($variable, $result[0]);

        return $variable;
    }

    public function getRankDetails ($id, $flag)
    {
        if ( $flag )
            $sqlQuery = "SELECT * FROM ranks WHERE id = \"$id\" ";
        else
            $sqlQuery = "SELECT * FROM bakranks WHERE id = \"$id\" ";

        $sqlQuery = $this->processArray($sqlQuery);

        return $sqlQuery;
    }

    public function getMasterSalaryId ($employeeId, $flag)
    {
        if ( $flag )
            $sqlQuery = "SELECT DISTINCT(id) FROM mastersalary WHERE employeeid = \"$employeeId\" && active = \"y\" ORDER BY type ASC ";
        else
            $sqlQuery = "SELECT DISTINCT(id) FROM bakmastersalary WHERE employeeid = \"$employeeId\" && active = \"y\" ";

        $sqlQuery = $this->processQuery($sqlQuery);
        $variable = array();
        while ( $result = mysql_fetch_array($sqlQuery) )
            array_push($variable, $result[0]);

        return $variable;
    }

    public function getSessionMasterSalaryIds ($employeeId, $sessionId)
    {
        $sqlQuery = "SELECT id FROM mastersalary WHERE employeeid = \"$employeeId\" && sessionid = \"$sessionId\" && active = \"y\" ORDER BY type ASC";
        $sqlQuery = $this->processQuery($sqlQuery);

        $variable = array();
        while ( $result = mysql_fetch_array($sqlQuery) )
            array_push($variable, $result[0]);

        if ( sizeof($variable) )
            return $variable;

        return false;
    }

    public function getSalaryIdDetails ($id, $flag)
    {
        if ( $flag )
            $sqlQuery = "SELECT * FROM mastersalary WHERE id = \"$id\" ";
        else
            $sqlQuery = "SELECT * FROM bakmastersalary WHERE id = \"$id\" ";

        $sqlQuery = $this->processArray($sqlQuery);

        return $sqlQuery;
    }

    public function getEmployeeBankAccoutDetails ($employeeId, $flag)
    {
        if ( $flag )
            $sqlQuery = "SELECT * FROM bankaccount WHERE employeeid = \"$employeeId\" ";
        else
            $sqlQuery = "SELECT * FROM bakbankaccount WHERE employeeid = \"$employeeId\" ";

        $sqlQuery = $this->processArray($sqlQuery);

        return $sqlQuery;
    }

    public function getReservedEmployeeBankAccountDetails ($employeeId, $month)
    {
        $sqlQuery = "SELECT bank, accountno FROM salaryemployeehead WHERE employeeid = \"$employeeId\" && month = \"$month\" ";
        $sqlQuery = $this->processArray($sqlQuery);

        return $sqlQuery;
    }

    public function getEmployeeBasicSalaryDetails ($employeeId, $flag)
    {
        if ( $flag )
            $sqlQuery = "SELECT * FROM basic WHERE employeeid = \"$employeeId\" ";
        else
            $sqlQuery = "SELECT * FROM bakbasic WHERE employeeid = \"$employeeId\" ";

        $sqlQuery = $this->processArray($sqlQuery);

        return $sqlQuery;
    }

    public function preserveEmployeeInformation ($employeeId, $flag)
    {
        if ( $flag ) {
            $sqlQuery = "INSERT INTO salaryemployeehead (SELECT * FROM baksalaryemployeehead WHERE employeeid = \"$employeeId\" && month = \"$this->currentMonth\")";
            $this->processQuery($sqlQuery);

            $sqlQuery = "DELETE FROM baksalaryemployeehead WHERE employeeid = \"$employeeId\"  ";
            $this->processQuery($sqlQuery);
        } else {
            $sqlQuery = "SELECT salutation, department, type, salary_bankid, salary_accountno, tadd FROM bankaccount, employee WHERE bankaccount.employeeid = employee.id && employee.id = \"$employeeId\"";
            $sqlQuery = $this->processArray($sqlQuery);

            $department = $sqlQuery['department'];
            $type = $sqlQuery['type'];
            if ( func_get_arg(2) == "1" ) {
                $bank = $sqlQuery['salary_bankid'];
                $accountno = $sqlQuery['salary_accountno'];
            }
            $salutation = $sqlQuery['salutation'];
            $id = $this->getCounter('salaryEmployeeHead');
            $house = $sqlQuery['tadd'];

            $sqlQuery = "INSERT INTO baksalaryemployeehead (id, employeeid, salutation, department, type, bank, accountno, month, house) VALUES (\"$id\", \"$employeeId\", \"$salutation\", \"$department\", \"$type\", \"$bank\", \"$accountno\", \"$this->currentMonth\", \"$house\")";
            $this->processQuery($sqlQuery);
        }

        return;
    }

    public function changeEmployeePassword ($employeeId, $password)
    {
        $var = md5($password);
        $sqlQuery = "UPDATE employeelogin SET password = \"$var\" WHERE id = \"$employeeId\" ";
        $this->processQuery($sqlQuery);

        return true;
    }

    public function getActiveInactiveLoginEmployeeIds ($flag)
    {
        if ( $flag )
            $sqlQuery = "SELECT id FROM employeelogin WHERE active = \"y\" ";
        else
            $sqlQuery = "SELECT id FROM employeelogin WHERE active = \"\" ";
        $sqlQuery = $this->processQuery($sqlQuery);
        $variable = array();
        while ( $result = mysql_fetch_array($sqlQuery) )
            array_push($variable, $result[0]);

        return $variable;
    }

    public function blockUnblockEmployeeLogin ($employeeId, $flag)
    {
        if ( $flag )
            $sqlQuery = "UPDATE employeelogin SET status = \"y\", active = \"y\", attempts = \"0\" WHERE id = \"$employeeId\" ";
        else
            $sqlQuery = "UPDATE employeelogin SET status = \"\", active = \"\", attempts = \"0\" WHERE id = \"$employeeId\" ";

        $this->processQuery($sqlQuery);
    }
}

?>
