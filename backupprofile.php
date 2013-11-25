<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8">
    <title>Profile</title>
        <link href="css/bootstrap.css" media="all" rel="stylesheet" type="text/css">
        <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
        <script src="src/js/bootstrap.js" type="text/javascript"></script>

<?php include_once "Header304.php";
 include_once ('D2G2variables.php');
 ?>

</head>

<body>

<div class="jumbotron">
  <h1> Welcome, <?php echo $activeusername; ?> </h1>
  <p> Email: <?php echo $activeuseremail; ?>  </p>
  <p> Phone: <?php echo $activeuserphone; ?> </p><br> 
<p> Change your username, if you want - no pressure </p>
<form method="POST" action="Profile.php">
   <p><input type="text" name="oldName" disabled="disabled"value="<?php echo $activeusername; ?>"size="6"><input type="text" name="newName"  
size="18">
<input type="submit" value="Change" name="updatesubmit"></p>
</form>
  <p><a class="btn btn-primary btn-lg" role="button">Learn more</a></p>
</div>



<?php

//this tells the system that it's no longer just parsing  
//html; it's now parsing PHP
 
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_p4s7", "a57854101", "ug");

//$potentialnewusername;

 
function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
    //echo "<br>running ".$cmdstr."<br>";
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
 
 
// Connect Oracle...
if ($db_conn) {
 
          if (array_key_exists('updatesubmit', $_POST)) {
		$potentialnewusername=$_POST['newName'];
		$result1= executePlainSql("select distinct username from users where username='$potentialnewusername'");
		if(!$row = OCI_Fetch_Array($result1, OCI_BOTH)){
		echo "In here?";
		executePlainSQL("update users set username='" . $_POST['newName'] ."' where userid=" . $activeuser ."");
		echo "after";
	file_put_contents('D2G2variables.php', '<?php $activeuser=' . $activeuser . '; $activeusername="' . $potentialnewusername . '"; $activeuseremail="' . $activeuseremail . '"; $activeuserphone=' . $activeuserphone . '; ?>');

             OCICommit($db_conn);
		header("location:Profile.php");
		echo "Have to echo here";
 		}else{
echo "New username is already in use, please re-enter a different one";
}
          } 
 
    if ($_POST && !$success) {
       //POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
       header("location: failed.php");
    } else {
      
    }

if (isset($_POST['showmyusername'])){
	echo $activeusername;
}

    //Commit to save changes...
    OCILogoff($db_conn);
} else {
    echo "cannot connect";
    $e = OCI_Error(); // For OCILogon errors pass no handle
    echo htmlentities($e['message']);
}

?>



</body>
</html>
