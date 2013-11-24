<DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8">
    <title>Events</title>
        <link href="css/bootstrap.css" media="all" rel="stylesheet" type="text/css">
        <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
        <script src="src/js/bootstrap.js" type="text/javascript"></script>

<?php include_once "Header304.php";
include_once "D2G2variables.php"; ?>
</head>

<body>	
	<form method="POST" action="Messages.php">
    
<p><input type="submit" value="Reset" name="reset"></p>
</form>

<form method="POST" action="Messages.php">
<div class="form-group">
<button type="submit" class="btn btn-primary" value="Show" name="showmessages">Show Messages</button>
  </div>
</form>

<form method="POST" action="Messages.php">
<div class="form-group">
<button type="submit" class="btn btn-primary" value="Send" name="sendmessages">Send Message</button>
  </div>
</form>

<?php
 
//this tells the system that it's no longer just parsing  
//html; it's now parsing PHP
 
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_p4s7", "a57854101", "ug");
 
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
 
 
function printResult($result) { //prints results from a select statement
    echo "<br>Messages<br>";
    echo "<table class=\"table table-bordered\">";
    echo "<tr><th>From</th><th>Message</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
       echo "<tr><td>" . $row[0] . "</td><td>" . $row[2] .  "</td></tr>"; //or just use "echo $row[0]"
 
    }
    echo "</table>";
 
}



if ($db_conn) {


    if (array_key_exists('reset', $_POST)) {	
	
	   echo "<br> dropping table <br>";
       executePlainSQL("Drop table messages cascade constraints");
 
       // Create new table...
       echo "<br> creating new table <br>";
       executePlainSQL("create table messages (aid number(9,0), userid number(9,0), comments varchar2(300), 
       	primary key(aid, userid, comments), foreign key(userid) references users(userid), foreign key(aid) references system_admin(aid))");
       OCICommit($db_conn);
}


if (isset($_POST['showmessages'])){
	$result = executePlainSQL("select * from messages where userid =" . $activeuser . "");
       printResult($result);
}


}
?>

<p> This will be for Messages </p>
</div>
</body>
</html>
