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
    echo "<tr><th>Group Name</th><th>Description</th><th>Created On</th><th>Manager</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
       echo "<tr><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><td>" . $row[4] . "</td></tr>"; //or just use "echo $row[0]"
 
    }
    echo "</table>";
 
}
function printResultMine($result) { //prints results from the join statement (show all my events)
    echo "<table class=\"table table-bordered\">";
    echo "<tr><th>Title</th><th>Description</th><th>Since</th><th>Creator</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
       echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td></tr>"; //or just use "echo $row[0]"

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
       header("location: failed.php");
    } else {
       // Select data...
       //$result = executePlainSQL("select * from groups");
       //printResult($result);
       // echo "did we do it?";
    }
if (isset($_POST['showgroups'])){
	$result = executePlainSQL("select gid, gname, description, since, u.username from groups g, users u where u.userid = g.manager");
       printResult($result);
} 
if (isset($_POST['showmygroups'])){
	$result = executePlainSQL("select gname, description, since, u.username from groups g, users u where u.userid =" . $activeuser . " AND u.userid = g.manager");
       printResultMine($result);
}
if (isset($_POST['showgroupcreate'])){



	echo "<form method=\"POST\" action=\"Groups.php\">
<!--refresh page when submit-->
<p>Insert values into event below:</p>
<div class=\"form-group\">
    <label class=\"sr-only\" for=\"Group ID\">Group ID</label>
    <input type=\"text\" class=\"form-control\" id=\"Group ID\" placeholder=\"Group ID\" name=\"gid\">
  </div>
<div class=\"form-group\">
    <label class=\"sr-only\" for=\"Group Name\">Group Name</label>
    <input type=\"text\" class=\"form-control\" id=\"Group Name\" placeholder=\"Group Name\" name=\"gname\">
  </div>
<div class=\"form-group\">
    <label class=\"sr-only\" for=\"Group Description\">EGroup Description</label>
    <input type=\"text\" class=\"form-control\" id=\"Group Description\" placeholder=\"Group Description\" name=\"description\">
  </div>
<div class=\"form-group\">
    <label class=\"sr-only\" for=\"Manager\">Your ID</label>
    <input type=\"text\" class=\"form-control\" id=\"Manager\" placeholder=\"Manager\" name=\"manager\">
  </div>
<button type=\"submit\" class=\"btn btn-primary\" value=\"insert\" name=\"insertsubmit\">Create Group</button>


</form>";
	}
//TODO:

    //Commit to save changes...
    OCILogoff($db_conn);
} else {
    echo "cannot connect";
    $e = OCI_Error(); // For OCILogon errors pass no handle
    echo htmlentities($e['message']);
}
