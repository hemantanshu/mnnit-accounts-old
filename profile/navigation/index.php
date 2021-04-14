<?php
////error_reporting(0)
if ( !isset ($loggedInfo) ) {
    require_once '../include/class.loginInfo.php';
    $loggedInfo = new loginInfo();
}
echo "
    <h3 class=\"headerbar\"><a href=\"#\">Report Generation</a></h3>
        <ul class=\"submenu\">
            <li><a href=\"./salary_slip.php\">View Employee Pay Slip</a></li>
            <li><a href=\"./salary_vpayc.php\">View Consolidated Report</a></li>  
            <li><a href=\"./salary_vpay.php\">View Pay Bill</a></li>                      
            <li><a href=\"./salary_vbank.php\">View Bank Slip</a></li>           
            <li><a href=\"./report_gpf.php\">View Total Fund Balance</a></li>
			 <li><a href=\"./report_fund.php\">Print Annual Fund</a></li>
            <li><a href=\"./report_fund_list.php\">Print LIST of Annual Fund</a></li>            
            <li><a href=\"./salary_gpf.php\">Employee Fund Statement</a></li>
            <li><a href=\"./report_mhead.php\">Monthly Head Report</a></li>            
            <li><a href=\"./report_emolument.php\">View Employee Emoluments</a></li>                        
            <li><a href=\"./report_employeeac.php\">Employee AccountHead Report</a></li>
            <li><a href=\"./report_employeeal.php\">Employee Allowance Report</a></li>
            <li><a href=\"./report_accounthead.php\">View AccountHead Report</a></li>
            <li><a href=\"./report_allowance.php\">View Allowance Report</a></li>
            <li><a href=\"./report_maccountheads.php\">Monthly AccountHead Report</a></li>            
            <li><a href=\"./report_mallowances.php\">Monthly Allowance Report</a></li> 
            <li><a href=\"./report_eannual.php\">Employee Annual Summary</a></li>
			<li><a href=\"./report_quarter.php\">Quarterwise Report</a></li>   
			<li><a href=\"./report_nps_contribution.php\">NPS Contribution Report</a></li>           
        </ul>";
echo "
    <h3 class=\"headerbar\"><a href=\"#\">Loan Report</a></h3>
        <ul class=\"submenu\">
            <li><a href=\"./report_gsanction.php\">Monthly GPF Sanction</a></li>
            <li><a href=\"./report_mgpfl.php\">Monthly GPF Recovery </a></li>  
            <li><a href=\"./report_gactive.php\">Active GPF Loan</a></li>                      
            <li><a href=\"./report_gstatement.php\">GPF Loan Statement</a></li>           
            <li><a href=\"./#\">----------------------------------------</a></li>
            <li><a href=\"./report_lsanction.php\">Monthly Loan Sanction</a></li>            
            <li><a href=\"./report_mloan.php\">Monthly Loan Recovery</a></li>
            <li><a href=\"./report_lactive.php\">Active Loan Report</a></li>            
            <li><a href=\"./report_lstatement.php\">Loan Statement Report</a></li>
        </ul>";
echo "
    <h3 class=\"headerbar\"><a href=\"#\">Account Operations </a></h3>
        <ul class=\"submenu\">
            <li><a href=\"./process_msalary.php\">Process Monthly Salary</a></li>
            <li><a href=\"./salary_rollback.php\">RollBack Processed Salary</a></li>
             <li><a href=\"./salary_fakeslip.php\">Employee Fake Slip</a></li>             
            <li><a href=\"./salary_block.php\">Block Employee Salary</a></li>            
            <li><a href=\"./salary_unblock.php\">UnBlock Employee Salary</a></li>          
            <li><a href=\"./remarks.php\">Employee Remarks Module</a></li>
            <li><a href=\"./salary_extra.php\">Additional Payment Module</a></li>			                        
            <li><a href=\"./salary_process.php\">Employee MasterSalary Info</a></li>           
            <li><a href=\"./process_interest.php\">Process Fund Interest</a></li>
            <li><a href=\"./process_uinterest.php\">Rollback Fund Interest</a></li>
            <li><a href=\"./direct_fund.php\">Direct GPF/NPS/CPF Addition</a></li>
        </ul>";

echo "
    <h3 class=\"headerbar\"><a href=\"#\">Pending Operations</a></h3>
        <ul class=\"submenu\">
            <li><a href=\"./salary_pending.php\">Pending Monthly Salary</a></li>
            <li><a href=\"./salary_rollbackp.php\">Pending Salary RollBack</a></li>
            <li><a href=\"./salary_blockp.php\">Pending Block Employee</a></li>
            <li><a href=\"./salary_unblockp.php\">Pending UnBlock Employee</a></li>                        
            <li><a href=\"./remarks_pending.php\">Pending Remarks Module</a></li>
            <li><a href=\"./salary_extrap.php\">Pending Additional Payment</a></li>

        </ul>";
if ( $loggedInfo->isAdmin() )
    echo "
        <h3 class=\"headerbar\"><a href=\"#\">Global Management</a></h3>
            <ul class=\"submenu\">
                <li><a href=\"./create_user.php\">Create User</a></li>
                <li><a href=\"./change_globals.php\">Change Globals</a></li>
                <li><a href=\"./lock_user.php\">Lock/Unlock User</a></li>
                <li><a href=\"./employee_password.php\">Employee Password</a></li>
                <li><a href=\"./employee_block.php\">Block/Unblock Employee Login</a></li>
                <li><a href=\"./fiscal_year.php\">New Financial Year</a></li>
            </ul>";
echo "
    <h3 class=\"headerbar\"><a href=\"#\">Master Record</a></h3>
        <ul class=\"submenu\">
            <li><a href=\"./housing.php\">Housing Master</a></li>
            <li><a href=\"./salutation.php\">Salutation Master</a></li>
            <li><a href=\"./bank.php\">Bank Master</a></li>
            <li><a href=\"./department.php\">Departments Master</a></li>
            <li><a href=\"./employeetype.php\">Employee Type Master</a></li>
			<li><a href=\"./accountHead.php\">Account Head Master</a></li>			
            <li><a href=\"./allowance.php\">Allowances/Deduction Master</a></li>
            <li><a href=\"./designation.php\">Designation Master</a></li>
            <li><a href=\"./employee.php\">Employee Master</a></li>            
        </ul>

";
?>

