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
                <form method ="POST" action="LoginPageTest.php">
                        <p> <input type="submit" value="Reset" name="reset"></p>
                </form>
                <br>
                <br>
<div class="row">
  <div class="col-md-6">
                  <h1> Log In </h1>

                  <FORM NAME="fomLogin" Method="POST" Action="mainPage.php">
                          <div class="form-group">
                                    <label class=".sr-only" for="InputUsername1">Username</label>
                                    <input type="text" class="form-control" id="InputUsername1" placeholder="Enter Username">
                          </div>
                          <div>
                            <label class=".sr-only" for="InputPassword1">Password</label>
                            <input type="password" class="form-control" id="InputPassword1" placeholder="Password">
                          </div>
                          <br>
                          <br>
                        <button type="submit" class="btn btn-primary">Sign in</button>
                </FORM>
        </div>

        <div class="col-md-6">
                <h1> Sign Up </h1>
                <FORM NAME="fomSign" Method="POST" Action="LoginPageTest.php">

                        <div class="form-group">

                        <div>
                                    <label class=".sr-only" for="SignUserId1">UserID</label>
                                    <input type="text" class="form-control" id="SignUserId1" name="userid" placeholder="Choose UID">
                          </div>

                        <div>
                                    <label class=".sr-only" for="SignUsername1">Username</label>
                                    <input type="text" class="form-control" id="SignUsername1" name="username" placeholder="Choose Username">
                          </div>


                          <div>
                            <label class=".sr-only" for="SignEmail1">Email</label>
                            <input type="email" class="form-control" id="SignEmail1" name="uemail" placeholder="Enter Email">
                          </div>

                          <div>
                            <label class=".sr-only" for="SignPhone1">Phone Number</label>
                            <input type="text" class="form-control" id="SignPhone1" name="uphone" placeholder="Enter Phone Number">
                          </div>

                          <div>
                            <label class=".sr-only" for="SignPass1">Choose a password</label>
                            <input type="password" class="form-control" id="SignPhone1" name="upswd" placeholder="Enter Password">
                          </div>

                          
                          <br>
                          <br>
                        <button type="submit" class="btn btn-primary" name="insertsubmit">Sign Up</button>
                </FORM>
        </div>
        </div>
</div>
                <br>
                <br>
                <br>
                <br>
                <br>
                <form name="adminone" method="POST" action="adminlogin.php">
                        <button type="submit" class="btn btn-default">Admin Log In</button>
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
    echo "the value of the parsed string is:";
    var_dump($statment);
 
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
    echo "<br>Got data from table users:<br>";
    echo "<table>";
    echo "<tr><th>UserID</th><th>UserName</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
       echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>"; //or just use "echo $row[0]"
       echo "the value of row in the loop is:";
       var_dump($row);  
    }
    echo "the value of row after the loop is:";
    var_dump($row);
    echo "</table>";
 
}
 
// Connect Oracle...
if ($db_conn) {
 
    if (array_key_exists('reset', $_POST)) {
       // Drop old table...
       echo "<br> dropping table <br>";
       executePlainSQL("Drop table users cascade constraints");
 
       // Create new table...
       echo "<br> creating new table <br>";
       executePlainSQL("create table users (userid number(9,0), username varchar2(30), upswd varchar2(30), uemail varchar2(30),uphone varchar2(30), unique (username),
        primary key (userid))");
       OCICommit($db_conn);
 
    } else
       if (array_key_exists('insertsubmit', $_POST)) {
          //Getting the values from user and insert data into the table
          $tuple = array (
             ":bind1" => $_POST['userid'],
             ":bind2" => $_POST['username'],
             ":bind3" => $_POST['upswd'],
             ":bind4" => $_POST['uemail'],
             ":bind5" => $_POST['uphone'],
          );
          $alltuples = array (
             $tuple
          );
          executeBoundSQL("insert into users values (:bind1, :bind2, :bind3, :bind4, :bind5)", $alltuples);
          OCICommit($db_conn);
 
       } else
          if (array_key_exists('updatesubmit', $_POST)) {
             // Update tuple using data from user
             $tuple = array (
                ":bind1" => $_POST['oldName'],
                ":bind2" => $_POST['newName']
             );
             $alltuples = array (
                $tuple
             );
             executeBoundSQL("update users set username=:bind2 where username=:bind1", $alltuples);
             OCICommit($db_conn);
 
          } else
             if (array_key_exists('dostuff', $_POST)) {
                // Insert data into table...
                executePlainSQL("insert into users values (000000001, 'Frank')");
                // Inserting data into table using bound variables
                $list1 = array (
                   ":bind1" => 600000000,
                   ":bind2" => "All"
                );
                $list2 = array (
                   ":bind1" => 700000000,
                   ":bind2" => "John"
                );
                $allrows = array (
                   $list1,
                   $list2
                );
                executeBoundSQL("insert into users values (:bind1, :bind2)", $allrows); //the function takes a list of lists
                // Update data...
                //executePlainSQL("update tab1 set nid=10 where nid=2");
                // Delete data...
                //executePlainSQL("delete from tab1 where nid=1");
                OCICommit($db_conn);
             }
 
    if ($_POST && $success) {
       //POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
       header("location: LoginPageTest.php");
    } else {
       // Select data...
       $result = executePlainSQL("select * from users");
       printResult($result);
       echo "did we do it?";
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

