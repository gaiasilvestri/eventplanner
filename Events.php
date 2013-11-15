<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<link href="CSS Folder/tabMenu.css" rel="stylesheet" type="text/css" />
</head>

<body>	
<div id="navbar">
<div id="holder">

<ul> <!-- unordered list -->


<li><a href="Profile.html">Profile</a></li>
<li><a href="Events.html" id="onlink">Events</a></li>
<li><a href="Groups.html">Groups</a></li>
<li><a href="CreateEvent.html">Create Event</a></li>
<li><a href="Messages.html">Messages</a></li>

</ul>
</div> <!-- end holder div -->
</div>
<p>&nbsp;</p>
<!-- en navbar div -->

<?php 
	//include 'SqlConnect.sql';
	echo "made it to php stuff :)";

	$success = True; //keep track of errors so it redirects the page only if there are no errors
	$db_conn = OCILogon("ora_p4s7", "a57854101", "ug");
	function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
	echo "<br>running ".$cmdstr."<br>";
	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr); //There is a set of comments at the end of the file that describe some of the OCI specific functions and how they work

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

	function printResult($result) { //prints results from a select statement
	echo "<br>Got data from table tab1:<br>";
	echo "<table>";
	echo "<tr><th>ID</th><th>Name</th></tr>";

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<tr><td>" . $row["NID"] . "</td><td>" . $row["NAME"] . "</td></tr>"; //or just use "echo $row[0]" 
	}
	echo "</table>";

}
	$ids = executePlainSQL("select userid from users");
	$result = executePlainSQL("select * from users");
	echo $result;
	$diff = executePlainSQL("select * from users");
	printResult($diff);
?>

</body>
</html>
