<DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8">
    <title>Login</title>
        <link href="css/bootstrap.css" media="all" rel="stylesheet" type="text/css">
        <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
        <script src="src/js/bootstrap.js" type="text/javascript"></script>
</head>
<body>


<?php


$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_p4s7", "a57854101", "ug");

$checkuser;
$checkusername;
$checkuseremail;
$checkuserphone;


// username and password sent from form 
$myusername=$_POST['myusername']; 
$mypassword=$_POST['mypassword']; 

function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
    //echo "<br>running ".$cmdstr."<br>";
    global $db_conn, $success;
    $statement = OCIParse($db_conn, $cmdstr); //There is a set of comments at the end of the file that describe some of the OCI specific functions and how they work
    //echo "the value of the parsed string is:";
    //var_dump($statment);
 
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

// To protect MySQL injection (more detail about MySQL injection)

$result= executePlainSQL("select * from users where username='$myusername' and upswd='$mypassword'");


// Mysql_num_row is counting table row
$count=countRows($result);



function countRows($result) { //prints results from a select statement
        global $checkuser, $checkusername, $checkuseremail, $checkuserphone;
    $i=0;
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
       $i = $i + 1;
       $checkuser = $row[0];
       $checkusername = $row[1];
	   $checkuseremail = $row[3];
       $checkuserphone = $row[4];

    }
    return $i;
 
}

// If result matched $myusername and $mypassword, table row must be 1 row
if($count==1){

// Register $myusername, $mypassword and redirect to file "login_success.php"

file_put_contents('D2G2variables.php', '<?php $activeuser=' . $checkuser . '; $activeusername="' . $checkusername . '"; $activeuseremail="' . $checkuseremail . '"; $activeuserphone=' . $checkuserphone . '; ?>');
header("location:Profile.php");

}
else {
echo "Wrong Username or Password";

echo "<FORM NAME=\"fomLogin\"  Action=\"LoginPageTestHardCode.php\">
                          <div class=\"form-group\">
            
                        <button type=\"submit\" class=\"btn btn-primary\">Try Again</button>
                        </div>
                </FORM>";

}
?>

</body>
</html>
