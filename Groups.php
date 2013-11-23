<!--Test Oracle file for UBC CPSC304 2011 Winter Term 2
  Created by Jiemin Zhang
  Modified by Simona Radu
  This file shows the very basics of how to execute PHP commands
  on Oracle.   
  specifically, it will drop a table, create a table, insert values
  update values, and then query for values
 
  IF YOU HAVE A TABLE CALLED "tab1" IT WILL BE DESTROYED
 
  The script assumes you already have a server set up
  All OCI commands are commands to the Oracle libraries
  To get the file to work, you must place it somewhere where your
  Apache server can run it, and you must rename it to have a ".php"
  extension.  You must also change the username and password on the  
  OCILogon below to be your ORACLE username and password -->
 <DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8">
    <title>Groups</title>
        <link href="css/bootstrap.css" media="all" rel="stylesheet" type="text/css">
        <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
        <script src="src/js/bootstrap.js" type="text/javascript"></script>
<?php include_once "Header304.php"; ?>
</head>

<body>

<p>If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>
<form method="POST" action="Groups.php">
    
<p><input type="submit" value="Reset" name="reset"></p>
</form>

<form method="POST" class="form-inline" role="form" action="Groups.php">
  <div class="form-group">
    <label class="sr-only" for="Group ID">Group Id</label>
    <input type="text" class="form-control" id="Group ID" placeholder="Group ID" name="gid">
  </div>
  <div class="form-group">
    <label class="sr-only" for="Group Name">Group Name</label>
    <input type="text" class="form-control" id="Group Name" placeholder="Group Name" name="gname">
  </div>
  <div class="form-group">
    <label class="sr-only" for="Description">Description</label>
    <input type="text" class="form-control" id="Description" placeholder="Description" name="description">
  </div>

  <div class="form-group">
    <label class="sr-only" for="Manager">Your ID</label>
    <input type="text" class="form-control" id="Manager" placeholder="Manager" name="manager">
  </div>

  <button type="submit" class="btn btn-default" name="insertsubmit">Create Group</button>
  <input type="hidden" name="since" value="<?php echo date("d-M-y"); ?>">

</form>
 
<!--  <p>Insert values into groups below:</p>
<p><font size="2"> Number&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  
Name</font></p>
<form method="POST" action="Groups.php">
<!refresh page when submit-->

 <!--
<p>
<input type="text" name="gid" size="9">
<input type="text" name="gname">
<input type="text" name="description">
<input type="text" name="since">
<input type="text" name="manager">
define two variables to pass the value-->
   <!-- 
<input type="submit" value="Create Group" name="insertsubmit"></p>
</form>
 create a form to pass the values. See below for how to  
get the values-->  
 
<p> Update the name by inserting the old and new values below: </p>
<p><font size="2"> Old Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  
New Name</font></p>
<form method="POST" action="Groups.php">
<!--refresh page when submit-->
 
   <p><input type="text" name="oldName" size="6"><input type="text" name="newName"  
size="18">
<!--define two variables to pass the value-->
    
<input type="submit" value="update" name="updatesubmit"></p>
<input type="submit" value="run hardcoded queries" name="dostuff"></p>
</form>
 </body>
 </html>
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
    echo "<br>Groups<br>";
    echo "<table class=\"table table-bordered\">";
    echo "<tr><th>Group Name</th><th>Description</th><th>Created On</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
       echo "<tr><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td></tr>"; //or just use "echo $row[0]"
 
    }
    echo "</table>";
 
}



 
// Connect Oracle...
if ($db_conn) {
 
    if (array_key_exists('reset', $_POST)) {
       // Drop old table...
       echo "<br> dropping table <br>";
       executePlainSQL("Drop table groups cascade constraints");

 
       // Create new table...
       echo "<br> creating new table <br>";
       executePlainSQL("create table groups (gid number, gname varchar2(30), description varchar2(300), 
     since DATE, manager number(9,0),primary key (gid), foreign key (manager) references users(userid))");
   

       OCICommit($db_conn);
 
    } else
       if (array_key_exists('insertsubmit', $_POST)) {
          //Getting the values from user and insert data into the table
          $tuple = array (
             ":bind1" => $_POST['gid'],
             ":bind2" => $_POST['gname'],
             ":bind3" => $_POST['description'],
             ":bind4" => $_POST['since'],
             ":bind5" => $_POST['manager'],
          );
          $alltuples = array (
             $tuple
          );
          executeBoundSQL("insert into groups values (:bind1, :bind2, :bind3, :bind4, :bind5)", $alltuples);
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
             executeBoundSQL("update groups set gname=:bind2 where gname=:bind1", $alltuples);
             OCICommit($db_conn);
 
          } else
                  if (array_key_exists('dostuff', $_POST)) {
                // Insert data into table...
                executePlainSQL("insert into groups values (100000000,'Origami Lovers','A groups for anyone who loves folding paper',
                TO_DATE('23-OCT-12 10:30AM','DD-MON-YY HH:MIAM'),
                000000003)");
                executePlainSQL("insert into groups values (200000000,'Basketball Rec','Rec team for amateur basketball games',
                TO_DATE('05-JAN-11 10:30AM','DD-MON-YY HH:MIAM'),
                000000003)");
                executePlainSQL("insert into groups values (300000000,'Cooking club','We meet once a week and make awesome food',
                TO_DATE('23-MAR-09 8:30AM','DD-MON-YY HH:MIAM'),
                000000001)");
                executePlainSQL("insert into groups values (400000000,'Graduating class of 2014','A group for all students graduating in 2014',
                TO_DATE('25-APR-11 10:30AM','DD-MON-YY HH:MIAM'),
                000000005)");
                executePlainSQL("insert into groups values (500000000,'Chess club','We play chess every friday night of the week',
                TO_DATE('17-DEC-13 10:30AM','DD-MON-YY HH:MIAM'),
                000000005)");
                // Inserting data into table using bound variables
                /*$list1 = array (
                   ":bind1" => 6,
                   ":bind2" => "All"
                );
                $list2 = array (
                   ":bind1" => 7,
                   ":bind2" => "John"
                );
                $allrows = array (
                   $list1,
                   $list2
                );
                executeBoundSQL("insert into groups values (:bind1, :bind2)", $allrows); //the function takes a list of lists
                // Update data...
                //executePlainSQL("update tab1 set nid=10 where nid=2");
                // Delete data...
                //executePlainSQL("delete from tab1 where nid=1");*/
                OCICommit($db_conn);
             }
 
    if ($_POST && !$success) {
       //POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
       header("location: Groups.php");
    } else {
       // Select data...
       $result = executePlainSQL("select * from groups");
       printResult($result);
       // echo "did we do it?";
    }
 
    //Commit to save changes...
    OCILogoff($db_conn);
} else {
    echo "cannot connect";
    $e = OCI_Error(); // For OCILogon errors pass no handle
    echo htmlentities($e['message']);
}

