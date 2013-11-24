<DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8">
    <title>Events</title>
        <link href="css/bootstrap.css" media="all" rel="stylesheet" type="text/css">
        <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
        <script src="src/js/bootstrap.js" type="text/javascript"></script>


</head>

<body>
	<form method="POST" action="adminPage.php">
    
<p><input type="submit" value="Reset" name="reset"></p>
</form>

<form method="POST" action="adminPage.php">
<div class="form-group">
	<input type="text" name="sender">
	<input type="text" name="receiver">
	<input type="text" name="messages">
<button type="submit" class="btn btn-primary" value="sendmessage" name="sendmessage">Send Message</button>
  </div>
</form>


	<?php

$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_p4s7", "a57854101", "ug");
$arr = array();

	function printResult($result) { //prints results from a select statement
	global $arr;
    echo "<table class=\"table table-bordered\">";
    echo "<tr><th>Title</th><th>Description</th><th>Creator ID</th><th>Delete Event</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
    	$test = (string)$row[0];
    	array_push($arr, $test);
       echo "<tr><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[7] . "</td><td> 
       <form method=\"POST\" action=\"adminPage.php\">
 		<div class=\"form-group\">
 			<input type=\"hidden\" value=\"" . $row[0] . "\" name=\"" . $row[0] . "\" >
			<button type=\"submit\" class=\"btn btn-default\" name=\"" . $row[0] . "\">Delete</button>
  			</div>
</form> </td></tr>";


    }
    echo "</table>";

}

function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
    //echo "<br>running ".$cmdstr."<br>";
    global $db_conn, $success;
    $statement = OCIParse($db_conn, $cmdstr); //There is a set of comments at the end of the file that describe some of the OCI specific functions and how they work
    //echo "the value of the parsed string is:";
    
 
    if (!$statement) {
       echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
       $e = OCI_Error($db_conn); // For OCIParse errors pass the      
       // connection handle
       echo htmlentities($e['message']);
       $success = False;
    }
 
    $r = OCIExecute($statement, OCI_DEFAULT);
    if (!$r) {
       echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
       $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
       echo htmlentities($e['message']);
       $success = False;
    } else {
 
    }
    return $statement;
 
}

function executeBoundSQL($cmdstr, $list) {
    /* Sometimes a same statement will be excuted for severl times, only
     the value of variables need to be changed.
     In this case you don't need to create the statement several times;  
     using bind variables can make the statement be shared and just  
     parsed once. This is also very useful in protecting against SQL injection. See example code below for     how this functions is used */
 
    global $db_conn, $success;
    $statement = OCIParse($db_conn, $cmdstr);
 
    if (!$statement) {
       echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
       $e = OCI_Error($db_conn);
       echo htmlentities($e['message']);
       $success = False;
    }
 
    foreach ($list as $tuple) {
       foreach ($tuple as $bind => $val) {
          //echo $val;
          //echo "<br>".$bind."<br>";
          OCIBindByName($statement, $bind, $val);
          unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
 
       }
       $r = OCIExecute($statement, OCI_DEFAULT);
       if (!$r) {
          echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
          $e = OCI_Error($statement); // For OCIExecute errors pass the statementhandle
          echo htmlentities($e['message']);
          echo "<br>";
          $success = False;
       }
    }
    }

if ($db_conn) {
	
	$result = executePlainSQL("select * from event");
    printResult($result);

/*
executePlainSQL("insert into system_admin values (000000001, 'masterdbadmin102', 'dbmaster@gmail.com')");
executePlainSQL("insert into system_admin values (000000002, 'youshallnotpass20', 'sportstar202@gmail.com')");
executePlainSQL("insert into system_admin values (000000003, '101tlemeire2', 'sysadmindbs@gmail.com')");
executePlainSQL("insert into system_admin values (000000004, 'l1l1l1e0r', 'helpfulboom@gmail.com')");
executePlainSQL("insert into system_admin values (000000005, '12345oooo9', 'susie.crabgrass@hotmail.ca')");
*/

    if (array_key_exists('reset', $_POST)) {
       // Drop old table...
       echo "<br> dropping table <br>";
       executePlainSQL("Drop table system_admin cascade constraints");
 
       // Create new table...
       echo "<br> creating new table <br>";
       executePlainSQL("create table system_admin (aid number (9,0), apswd varchar2(30), aemail varchar2(30), primary key (aid))");
       OCICommit($db_conn);
 
    }

    if (array_key_exists('sendmessage', $_POST)) {
   
   	$tuple = array (
             ":bind1" => $_POST['sender'],
             ":bind2" => $_POST['receiver'],
             ":bind3" => $_POST['messages'],
          );
          $alltuples = array (
             $tuple
          );

       executeBoundSQL("insert into messages values (:bind1, :bind2, :bind3)", $alltuples);
       OCICommit($db_conn);
 
    }
		

print_r($arr);

$i = 0;
foreach($arr as $ar){
	
	if (array_key_exists($ar, $_POST)) {

          $deletes = $_POST[$ar];
          echo $deletes;
          executePlainSQL("delete from event where eid = '" . $deletes . "'");
          OCICommit($db_conn);
 
       }
       $i = $i + 1;
}
}




?>
