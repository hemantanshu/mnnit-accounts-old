<?php
    /*Licensed Under Support Gurukul. http://www.supportgurukul.com */
    ob_start();

    session_start();

    require_once '../include/class.allowance.php';
    require_once '../include/class.upload.php';
    require_once '../include/class.accountInfo.php';
    
    $allowance = new allowance();          
    require_once '../include/class.personalInfo.php';
    require_once '../include/class.employeeInfo.php';
    require_once '../include/class.accountInfo.php';
    require_once '../include/class.department.php';
    $accounts = new accounts();
        
    $personalInfo = new personalInfo();
    $employeeInfo = new employeeInfo();
	$department = new department();
	
    if(!$allowance->checkLogged())
        $allowance->redirect('../');
    
    $upload =& new UPLOAD_FILES();
    if(isset ($_POST)){
        $allowanceId = $_POST['allowanceid'];
        if($_FILES){
          foreach($_FILES as $key => $file){
            $upload->set("name",$file["name"]); // Uploaded file name.
            $upload->set("type",$file["type"]); // Uploaded file type.    
            $upload->set("tmp_name",$file["tmp_name"]); // Uploaded tmp file name.    
            $upload->set("error",$file["error"]); // Uploaded file error.    
            $upload->set("size",$file["size"]); // Uploaded file size.    
            $upload->set("fld_name",$key); // Uploaded file field name.    
            $upload->set("max_file_size",1048976); // Max size allowed for uploaded file in bytes = 1MB.    
            $upload->set("supported_extensions",array("csv" => "application/vnd.ms-excel", "csv" => "text/csv")); // Allowed extensions and types for uploaded file.    
            $upload->set("randon_name",false); // Generate a unique name for uploaded file? bool(true/false).
            $upload->set("replace",true); // Replace existent files or not? bool(true/false).    
            $upload->set("file_perm",0775); // Permission for uploaded file. 0444 (Read only).
            $upload->set("dst_dir",$_SERVER["DOCUMENT_ROOT"]."/accounts/uploads/"); // Destination directory for uploaded files.
            $result = $upload->moveFileToDestination(); // $result = bool (true/false). Succeed or not.  
            
          }
        }
    }else
        $allowance->redirect('./allowance.php');
    
    if($allowance->getAllowanceAccountHead($allowanceId) == '')
            $allowance->redirect('./');

    $details = $allowance->getAllowanceHeadDetails($allowanceId);
    ob_end_flush();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Accounts Section -- File Upload Information</title>
<link rel="stylesheet" type="text/css" href="../include/default.css" media="screen" />
<script type="text/javascript" src="../include/jquery.min.js"></script>
<script type="text/javascript" src="../include/ddaccordion.js"></script>
<script language="javascript" type="text/javascript">
			var currenttime = "<?php
				date_default_timezone_set('Asia/Calcutta');
				print date("F d, Y H:i:s", time())?>"
			var montharray=new Array("January","February","March","April","May","June","July","August","September","October","November","December")
			var serverdate=new Date(currenttime)

			function padlength(what)
			{
				var output=(what.toString().length==1)? "0"+what : what
				return output
			}

			function displaytime()
			{
				serverdate.setSeconds(serverdate.getSeconds()+1)

				var datestring=montharray[serverdate.getMonth()]+" "+padlength(serverdate.getDate())+", "+serverdate.getFullYear()
				var timestring=padlength(serverdate.getHours())+":"+padlength(serverdate.getMinutes())+":"+padlength(serverdate.getSeconds())
				document.getElementById("servertime").innerHTML=datestring+" "+timestring
			}

			window.onload=function()
			{
				setInterval("displaytime()", 1000)
			}

   	</script>

<script type="text/javascript">

ddaccordion.init({
	headerclass: "headerbar", //Shared CSS class name of headers group
	contentclass: "submenu", //Shared CSS class name of contents group
	revealtype: "click", //Reveal content when user clicks or onmouseover the header? Valid value: "click", "clickgo", or "mouseover"
	mouseoverdelay: 200, //if revealtype="mouseover", set delay in milliseconds before header expands onMouseover
	collapseprev: true, //Collapse previous content (so only one open at any time)? true/false
	defaultexpanded: [0], //index of content(s) open by default [index1, index2, etc] [] denotes no content
	onemustopen: true, //Specify whether at least one header should be open always (so never all headers closed)
	animatedefault: false, //Should contents open by default be animated into view?
	persiststate: true, //persist state of opened contents within browser session?
	toggleclass: ["", "selected"], //Two CSS classes to be applied to the header when it's collapsed and expanded, respectively ["class1", "class2"]
	togglehtml: ["", "", ""], //Additional HTML added to the header when it's collapsed and expanded, respectively  ["position", "html1", "html2"] (see docs)
	animatespeed: "normal", //speed of animation: integer in milliseconds (ie: 200), or keywords "fast", "normal", or "slow"
	oninit:function(headers, expandedindices){ //custom code to run when headers have initalized
		//do nothing
	},
	onopenclose:function(header, index, state, isuseractivated){ //custom code to run whenever a header is opened or closed
		//do nothing
	}
})

</script>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>

<body>

<div>
  <div class="top">
    <div class="header">
      <div class="left"><img class="imgright" src="../img/logo.gif" alt="Forest Thistle" height="105px">&nbsp;Accounts Department</div>
      <!--<div class="right">
        <div align="center"> MNNIT <br />
          ALLAHABAD</div>-->
      </div> 


      </div>
    </div>
  </div>
  <div class="container">
    <div class="navigation">
    	<a href="./" target="_parent">Home</a>
        <a href="changePassword.php" target="_parent">Change Password</a>
        <a href="./logout.php" target="_parent">Logout</a>
        <a href="#" target="_parent">&nbsp;&nbsp; &nbsp;Server Time : <span id="servertime"></span></a>
<div class="clearer"><span></span> </div>
    </div>

    <div class="main">
      <div class="contentLarge">
      	<form action="./allowance_directc.php" method="post">
      	<table align="center" border="0" width="100%">
        	<!-- insertion of new departments will be done here -->
            <tr>
            	<td colspan="7" align="center"><font class="error">For Allowance Head : <?php echo $allowance->getAllowanceTypeName($allowanceId);?></font><br /><hr size="1" /><br /></td>
            </tr>
            <tr>
                <td colspan="7" height="10px" align="center">
                        <a href="./allowance_view1.php?id=<?php echo $allowanceId; ?>&type=all" target="_parent">Info </a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_view.php?id=<?php echo $allowanceId; ?>&type=all" target="_parent">Total View </a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_view.php?id=<?php echo $allowanceId; ?>&type=no" target="_parent">Non-Overridden </a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_view.php?id=<?php echo $allowanceId; ?>&type=yes" target="_parent">Over-ridden </a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_direct.php?id=<?php echo $allowanceId; ?>&type=yes" target="_parent">Edit Amount(Direct)</a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_direct1.php?id=<?php echo $allowanceId; ?>&type=yes" target="_parent">Edit Amount(Master-Update)</a>&nbsp;&nbsp;||&nbsp;&nbsp;
                        <a href="./allowance_clear.php?id=<?php echo $allowanceId; ?>" target="_parent">Clear Amount</a>
                </td>
            </tr>
            <tr>
            	<td colspan="7" height="10px"><hr size="1" /></td>
            </tr>
            <tr>
            	<th colspan="7" align="center">Update Master Salary Record &nbsp;&nbsp;&nbsp;<?php echo $details[8] == 'c' ? 'EARNING HEAD' : 'DEDUCTION HEAD' ;?>&nbsp;&nbsp;&nbsp;
                                                <input type="hidden" name="option" value="1" />
                </th>
            </tr>        
            <tr>
            	<td height="5px" colspan="7"><hr size="1" /></td>
            </tr>  
            <tr>
            	<th width="5%">S.N</th>
                <th width="10%">Emp. Code</th>
                <th width="20%">Name</th>
                <th width="20%">Department</th>
                <th width="15%" align="right">S. Amt</th>
                <th width="15%" align="right">C. Amt</th>
                <th width="15%">Amount</th>
            </tr>
            <tr>
            	<td height="10px" colspan="7"><hr size="3" /></td>
            </tr>
            <?php 
                $flag = true;
                if($upload->fail_files_track[0]['error_type'] != 0){
                    $flag = false;
                    echo "
                        <tr>
                            <td colspan=\"7\" align=\"center\"><font color=\"red\"><h1>error -- ".$upload->fail_files_track[0]['msg']."</h1></font></td>
                        </tr>";
                }else{
                    $fileName = $upload->succeed_files_track[0]['destination_directory'].$upload->succeed_files_track[0]['file_name'];                    
                    $fp = fopen($fileName,'r') or die("can't open the file");                    
                    $count = 0;
                    $fileArray = array();
                    while($csv_line = fgetcsv($fp,1024)) {
                        $fileArray[$count] = array();
                        for ($i = 0, $j = count($csv_line); $i < $j; $i++) {
                            array_push($fileArray[$count], $csv_line[$i]);
                        }
                        ++$count;
                    }
                    fclose($fp) or die("can't close file");
                    $i = 0;
                    $j = 0;
                    $rowProblem = array();
                    $error = 0;
                    for ($i=0; $i<$count; ++$i) {
                        $individualEmployeeId = $personalInfo->getEmployeeIdFromCode($fileArray[$i][0]);
                        if($individualEmployeeId == ""){
                            $error++;
                            array_push($rowProblem, ($i+1));
                        }
                        $personalInfo->getEmployeeInformation($individualEmployeeId, true);
                        $amountName = "amount".$i;
                        $employeeIdName = "employeeId".$i;            		
                        $amount = $accounts->getEmployeeSalaryInfo($individualEmployeeId, $allowanceId);
                        ++$j;

                            echo "
                                                    <tr>
                                                            <td align=\"center\">".$j."</td>
                                                            <td align=\"left\">".$personalInfo->getEmployeeCode()."</td>
                                                            <td align=\"left\">".$personalInfo->getName()."</td>
                                                            <td align=\"left\">".$department->getDepartmentName($personalInfo->getDepartment())."</td>
                                                            <td align=\"right\" style=\"padding-right:10px\">". number_format($amount, 2, '.', '')."</td>
                                                            <td align=\"right\" style=\"padding-right:10px\">". number_format($accounts->getAccountSum($individualEmployeeId, $allowanceId), 2, '.', '')."</td>
                                                            <td align=\"right\"><input type=\"text\" name=\"".$amountName."\" value=\"".$fileArray[$i][1]."\" style=\"width:120px\" />
                                                                <input type=\"hidden\" name=\"".$employeeIdName."\" value=\"".$individualEmployeeId."\" /></td>
                                                    </tr>
                                                    <tr>
                                                            <td colspan=\"7\"><hr size=\"1\" /></td>
                                                    </tr>
                                                    <tr>
                                                            <td height=\"5px\"></td>
                                                    </tr>";		
                    }
                }
                
            	
            ?> 
            <tr>
            	<th colspan="4" >Total Number Of Entries In The File</th>
                <th align="left"><?php echo $count; ?></th>
            </tr>
                <tr>
                    <td height="5px"></td>
                </tr>
            <tr>
            	<th colspan="4">Total Successful Entries In The File</th>
                <th  align="left"><?php echo ($count - $error); ?></th>
            </tr>
                <tr>
                    <td height="5px"></td>
                </tr>
            <tr>
            	<th colspan="4">Total Error Entries In The File</th>
                <th  align="left"><?php echo $error; ?></th>
            </tr>
                
            <tr>
            	<td height="10px" colspan="7"><hr /></td>
            </tr>
            
                <?php
                    if($error == 0 && $flag){
                        echo "
                        	<tr>
                            <td colspan=\"7\" align=\"center\"><br />                    
                                <input type=\"hidden\" name=\"allowanceId\" value=\"$allowanceId\" />
                            <input type=\"submit\" name=\"submit\" value=\"Process The Amount For These Employees\" style=\"width:300px\" />
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type=\"button\" onclick=\"window.location='./allowance.php'\" value=\"Return Back To Allowances\" />
                        <br /><br /></td>
                    </tr>";
                    }else{
                        echo "
                            <tr>
                                    <th colspan=\"7\" align=\"center\">Error In Row Nos : ";
                        foreach ($rowProblem as $value)
                            echo "<font color=\"red\">".$value."&nbsp;&nbsp;&nbsp; </font>";
                        echo "<br />Return Back And Correct The File Uploaded.<br /><br /><hr /><br /><br /></th>
                            </tr>";
                    }
                ?>
            	
            
        </table>        
        </form>
      </div>
      
      <div class="clearer"><span></span></div>
    </div>
    <div class="footer">@webteam.<a href="http://www.mnnit.ac.in" title="MNNIT">mnnit</a> Designed And Developed By Hemant Kumar Sah (B.Tech ECE 2011)</div>
  </div>
</div>
</body>
</html>