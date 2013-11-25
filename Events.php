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

<p>If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>
<form method="POST" action="Events.php">
    
<p><input type="submit" value="Reset" name="reset"></p>
</form>
 


<form method="POST" action="Events.php">
<div class="form-group">
<button type="submit" class="btn btn-primary" value="Main Create" name="showcreate">Create Event View</button>
  </div>
</form>

<form method="POST" action="Events.php">
<div class="form-group">
<button type="submit" class="btn btn-primary" value="Main Show" name="showevents">Show all Events</button>
  </div>
</form>

<form method="POST" action="Events.php">
<div class="form-group">
<button type="submit" class="btn btn-primary" value="Main Show" name="showmyevents">Show all Events I Created</button>
  </div>
</form>

<form method="POST" action="Events.php">
<div class="form-group">
<button type="submit" class="btn btn-primary" value="Main Show" name="showattending">Show all Events I'm Attending</button>
  </div>
</form>

<form method="POST" action="Events.php">
<div class="form-group">
<button type="submit" class="btn btn-primary" value="Main Search" name="searchevents">Search Events</button>
  </div>
</form>
    

<!-- create a form to pass the values. See below for how to  
get the values-->  
<!-- 
<p> Update the name by inserting the old and new values below: </p>
<p><font size="2"> Old Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  
New Name</font></p>-->
<!--<form method="POST" action="EventsHardCode.php">-->
<!--refresh page when submit
 
   <p><input type="text" name="oldName" size="6"><input type="text" name="newName"  
size="18">-->
<!--define two variables to pass the value-->
<!-- 
<input type="submit" value="update" name="updatesubmit"></p>
<input type="submit" value="run hardcoded queries" name="dostuff"></p>
</form>
-->
<?php
 
//this tells the system that it's no longer just parsing  
//html; it's now parsing PHP
 
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = OCILogon("ora_p4s7", "a57854101", "ug");
$arr = array();
 
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
// <button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal">
//  Launch demo modal
//</button>

function fill_the_array($arrayNew, $result){
	global $arr;
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		array_push($arr, $row[7]);
	}
}

function printRatings($numReviews, $minReviews, $avgReviews, $maxReviews){

			 echo "<table class=\"table table-bordered\">";
    echo "<tr><th>Number of Reviews</th><th>Min Rating</th><th>Average Rating</th><th>Maximum Rating</th></tr>";
    while (($row = OCI_Fetch_Array($numReviews, OCI_BOTH)) && ($row1 = OCI_Fetch_Array($minReviews, OCI_BOTH)) && ($row2 = OCI_Fetch_Array($avgReviews, OCI_BOTH)) && ($row3 = OCI_Fetch_Array($maxReviews, OCI_BOTH))) {
       echo "<tr><td>" . $row[0] . "</td><td>" . $row1[0] . "</td><td>" . $row2[0] . "</td><td>" . $row3[0] . "</td></tr>"; //or just use "echo $row[0]"

    }
    echo "</table>";

}


function printAttends($attend){
echo "<table class=\"table table-bordered\">";
    echo "<tr><th>Title</th><th>Description</th><th>Start</th><th>End</th><th>Building</th><th>Creator</th><th>Write Review?</th></tr>";
    while ($row = OCI_Fetch_Array($attend, OCI_BOTH)) {
       echo "<tr>
<td>" . $row[0] . "</td>
<td>" . $row[1] . "</td>
<td>" . $row[2] . "</td>
<td>" . $row[3] . "</td>
<td>" . $row[4] . "</td>
<td>" . $row[5] . "</td>
<td> 
       <form method=\"POST\" action=\"StillneedToImplement.php\">
 		<div class=\"form-group\">
			<button type=\"submit\" class=\"btn btn-default\" name =\"writereview\"value=\"" . $row[6] . "\">Write</button>
 </div>
</form> </td>
</tr>";


}
}

function printReviews($review){
	 echo "<table class=\"table table-bordered\">";
    echo "<tr><th>Comments</th><th>Rating</th><th>User</th></tr>";
    while ($row = OCI_Fetch_Array($review, OCI_BOTH)) {
       echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td></tr>"; //or just use "echo $row[0]"

    }
    echo "</table>";

}

function printResult($result) { //prints results from a select statement
    	global $arr;
	echo "<table class=\"table table-bordered\">";
    echo "<tr><th>Title</th><th>Description</th><th>Start</th><th>End</th><th>Address</th><th>Building</th><th>Creator</th><th>Attend?</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
    	array_push($arr, $row[7]);
       echo "<tr>
<td>
<form method=\"POST\" action=\"Events.php\">
<div class=\"form-group\">
<button class=\"btn btn-link\" name=\"" . $row[7] . "\">"
   . $row[0] ."
</button>
</div>
</form>
</td>
<td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><td>" . $row[4] . "</td><td>" . $row[5] . "</td><td>" . $row[6] . "</td>
<td> 
       <form method=\"POST\" action=\"Events.php\">
 		<div class=\"form-group\">
			<button type=\"submit\" class=\"btn btn-default\" name =\"attend\"value=\"" . $row[7] . "\">Attend</button>
  			</div>
</form> </td>
</tr>"; //or just use "echo $row[0]"

    }
    echo "</table>";

}
function printResultMine($result) { //prints results from the join statement (show all my events)
    	global $arr;
    echo "<table class=\"table table-bordered\">";
    echo "<tr><th>Title</th><th>Description</th><th>Start</th><th>End</th><th>Address</th><th>Building</th><th>Creator</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
    	array_push($arr, $row[7]);
       echo "<tr><td>
<form method=\"POST\" action=\"Events.php\">
<div class=\"form-group\">
<button class=\"btn btn-link\" name=\"" . $row[7] . "\">"
   . $row[0] ."
</button>
</div>
</form>
</td>

<td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><td>" . $row[4] . "</td><td>" . $row[5] . "</td><td>" . $row[6] . "</td></tr>"; //or just use "echo $row[0]"

    }
    echo "</table>";

}
function printResultT($result) { //prints results from a select statement
	    	global $arr;
    echo "<table class=\"table table-bordered\">";
    echo "<tr><th>Title</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
    	array_push($arr, $row[1]);
       echo "<tr><td>
<form method=\"POST\" action=\"Events.php\">
<div class=\"form-group\">
<button class=\"btn btn-link\" name=\"" . $row[1] . "\">"
   . $row[0] ."
</button>
</div>
</form>
</td>

</tr>"; //or just use "echo $row[0]"

    }
    echo "</table>";

}
function printResultD($result) { //prints results from a select statement
	    	global $arr;
    echo "<table class=\"table table-bordered\">";
    echo "<tr><th>Description</th></tr>";
    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
    	array_push($arr, $row[1]);
       echo "<tr><td>
<form method=\"POST\" action=\"Events.php\">
<div class=\"form-group\">
<button class=\"btn btn-link\" name=\"" . $row[1] . "\">"
   . $row[0] ."
</button>
</div>
</form>
</td>

</tr>"; //or just use "echo $row[0]"

    }
    echo "</table>";

}
//WRITE NEW PRINT RESULT FOR ISSETJOIN
 
// Connect Oracle...
if ($db_conn) {

$i = 0;

$resultFill = executePlainSQL("select etitle, edescription, startTime, end, street_address, building, u.username, e.eid from event e, users u where u.userid = e.userid");
fill_the_array($arr, $resultFill);
foreach($arr as $ar){
	
	if (array_key_exists($ar, $_POST)) {
          //executePlainSQL("delete from event where eid = " . $ar);
			$reviews = executePlainSQL("select r.comments, r.rating, u.username from review_submit r, users u, event e where u.userid = r.userid AND r.eid = e.eid AND e.eid = " . $ar);
			printReviews($reviews);
          OCICommit($db_conn);
			//header("location:adminPage.php");
			
			echo "<form method=\"POST\" action=\"Events.php\">
<div class=\"form-group\">
<input type =\"hidden\" name=\"EID\" value=" . $ar . ">
<button type=\"submit\" class=\"btn btn-primary\" value=\"STATS\" name=\"stats\">STATS</button>
  </div>
</form>";

       }
       $i = $i + 1;
}

    if (array_key_exists('stats', $_POST)) {
		echo "Made it in";
		$temp = $_POST['EID'];
		echo "$temp";
		
		
		$numReviews = executePlainSQL("select count(*) from review_submit where eid = " . $_POST['EID']);
		$minReviews = executePlainSQL("select min(rating) from review_submit where eid = " . $_POST['EID']);
		$avgReviews = executePlainSQL("select AVG(rating) from review_submit where eid = " . $_POST['EID']);
		$maxReviews = executePlainSQL("select max(rating) from review_submit where eid = " . $_POST['EID']);

	printRatings($numReviews, $minReviews, $avgReviews, $maxReviews);


}

    if (array_key_exists('showattending', $_POST)) {
      echo "attending";
		$attendingview = executePlainSQL("select e.etitle, e.edescription, e.starttime, e.end, e.building, u.username, e.eid from event e, users u, attends a where u.userid = e.userid AND a.userid = " . $activeuser . " AND a.eid = e.eid");
		printAttends($attendingview);

}


if (array_key_exists('attend', $_POST)) {
    echo $activeuser;
    $attendeid = $_POST['attend'];
		echo $attendeid;

		$attendevent = executePlainSQL("insert into attends values (" . $activeuser . "," . $attendeid . ")");
    OCICommit($db_conn);

		if(!$success){
		echo"Success was false in attend button";
	echo " NEED TO IMPLEMENT WHAT TO DO IF CAN'T ATTEND EVENT";

}else{

echo "I think you are now attending the event! great job!";
}

}
 
    if (array_key_exists('reset', $_POST)) {
       // Drop old table...
       echo "<br> dropping table <br>";
       executePlainSQL("Drop table event cascade constraints");
 
       // Create new table...
       echo "<br> creating new table <br>";
       executePlainSQL("create table event (eid number(9,0), etitle varchar2(300), edescription varchar2(400), 
     startTime DATE, end DATE, street_address varchar2(100), building varchar2(100), userid number(9,0),
     primary key (eid),
     foreign key (userid) references users(userid))");
       OCICommit($db_conn);
 
    } else
       if (array_key_exists('insertsubmit', $_POST)) {
          //Getting the values from user and insert data into the table
          $tuple = array (
             ":bind1" => $_POST['eid'],
             ":bind2" => $_POST['etitle'],
             ":bind3" => $_POST['edescription'],
             ":bind4" => $_POST['startTime)'],
             ":bind5" => $_POST['end'],
             ":bind6" => $_POST['street_address'],
             ":bind7" => $_POST['building'],
             ":bind8" => $_POST['userid'],
          );
          $alltuples = array (
             $tuple
          );
          executeBoundSQL("insert into event values (:bind1, :bind2, :bind3, TO_DATE(:bind4, 'DD-MON-YY HH:MIPM'), TO_DATE(:bind5, 'DD-MON-YY HH:MIPM'), :bind6, :bind7, :bind8)", $alltuples);
          OCICommit($db_conn);
 
       } else
			if (array_key_exists('searchtitle', $_POST)){
			
			$term = $_POST['title'];

			$result1 = executePlainSQL("select etitle, edescription, startTime, end, street_address, building, u.username from event e, users u where etitle like '%" . $term . "%' AND u.userid = e.userid");
			printResult($result1);

} else
if (array_key_exists('showtitle', $_POST)){
			
			$term = $_POST['title'];

			$result1 = executePlainSQL("select etitle, eid from event where etitle like '%" . $term . "%'");
			printResultT($result1);

}else
if (array_key_exists('showtitle1', $_POST)){
			
			$term = $_POST['desc'];

			$result1 = executePlainSQL("select etitle, eid from event where edescription like '%" . $term . "%'");
			printResultT($result1);

} else
if (array_key_exists('showtitle2', $_POST)){
			
			$term = $_POST['location'];

			$result1 = executePlainSQL("select etitle, eid from event where building like '%" . $term . "%'");
			printResultT($result1);

}else
if (array_key_exists('showdescription', $_POST)){
			
			$term = $_POST['title'];

			$result1 = executePlainSQL("select edescription, eid from event where etitle like '%" . $term . "%'");
			printResultD($result1);

}else
if (array_key_exists('showdescription1', $_POST)){
			
			$term = $_POST['desc'];

			$result1 = executePlainSQL("select edescription, eid from event where edescription like '%" . $term . "%'");
			printResultD($result1);

}else
if (array_key_exists('showdescription2', $_POST)){
			
			$term = $_POST['location'];

			$result1 = executePlainSQL("select edescription, eid from event where building like '%" . $term . "%'");
			printResultD($result1);

}else
if (array_key_exists('searchdescription', $_POST)){
			
			$term = $_POST['desc'];

			$result1 = executePlainSQL("select etitle, edescription, startTime, end, street_address, building, u.username from event e, users u where edescription like '%" . $term . "%' AND u.userid = e.userid");
			printResult($result1);

} else

if (array_key_exists('searchlocation', $_POST)){
			
			$term = $_POST['location'];

			$result1 = executePlainSQL("select etitle, edescription, startTime, end, street_address, building, u.username from event e, users u where building like '%" . $term . "%' AND u.userid = e.userid");
			printResult($result1);

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
             executeBoundSQL("update event set etitle=:bind2 where etitle=:bind1", $alltuples);
             OCICommit($db_conn);
 
          } else
             if (array_key_exists('dostuff', $_POST)) {
                // Insert data into table...
              executePlainSQL("insert into event values (000000001, 'Computation And Sustainability: Beyond Green IT - FLS Talk By Alan Mackworth, UBC/CS', 
                'Alan Mackworth, Professor and Canada Research Chair in AI, UBC Computer Science', 
                TO_DATE('30-AUG-13 3:30PM', 'DD-MON-YY HH:MIPM'), 
                TO_DATE('30-AUG-13 6:30PM','DD-MON-YY HH:MIPM'), 
                '110-6245 Agronomy Rd.', 
                'Hugh Dempster Pavilion', 000000001)");
              executePlainSQL("insert into event values (000000002, 'UBC FilmSoc Wicker Man Beer Garden', 
                'Wonder why weve been counting down to Cagemas? Well, for our beer garden of course.', 
                TO_DATE('13-FEB-13 12:00AM','DD-MON-YY HH:MIAM'), 
                TO_DATE('13-FEB-13 1:30PM','DD-MON-YY HH:MIPM'), 
                '130-6138 Student Union Blvd', 
                'Student Union Building', 000000003)");
              executePlainSQL("insert into event values (000000003, 'Career Fair 2014', 'Join us for the chance to interface with your future masters!', 
                TO_DATE('24-SEP-13 1:30PM','DD-MON-YY HH:MIPM'),
                TO_DATE('24-SEP-13 5:30PM','DD-MON-YY HH:MIPM'), 
                '1137 Alumni Ave',
                'Life Sciences Building', 000000001)");
              executePlainSQL("insert into event values (000000004, 'COGS Meet the Profs!', 'Join us for a chance to try to network with your fellow students and professors!', 
                TO_DATE('04-OCT-13 10:30AM','DD-MON-YY HH:MIAM'), 
                TO_DATE('04-OCT-13 11:30AM','DD-MON-YY HH:MIAM'), 
                '6371 Crescent Rd', 
                'Leo and Thea Koerner Graduate Student Centre', 000000002)");
              executePlainSQL("insert into event values (000000005, 'CPSC 304 group meeting', 'Doug, Dane, Greg, and Gaia are meeting to work on project', 
                TO_DATE('02-NOV-13 6:30PM','DD-MON-YY HH:MIPM'), 
                TO_DATE('02-NOV-13 8:30PM','DD-MON-YY HH:MIPM'), 
                '2366 Main Mall', 
                'ICICS', 000000004)");
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
                executeBoundSQL("insert into event values (:bind1, :bind2)", $allrows); //the function takes a list of lists
                // Update data...
                //executePlainSQL("update tab1 set nid=10 where nid=2");
                // Delete data...
                //executePlainSQL("delete from tab1 where nid=1");*/
                OCICommit($db_conn);
             }
 
    if ($_POST && !$success) {
       //POST-REDIRECT-GET -- See http://en.wikipedia.org/wiki/Post/Redirect/Get
       //header("location: failed.php");
		echo "I FAILED AT THE BOTTOM";
    } else {
       // Select data...
       //$result = executePlainSQL("select eid, etitle, u.username from event e, users u where u.userid = e.userid");
       //printResult($result);
       //echo "did we do it?";
    }

if (isset($_POST['searchevents'])){
echo "Search Titles";
echo "<form method=\"POST\" action=\"Events.php\">
<div class=\"form-group\">
    <label class=\"sr-only\" for=\"Event Title\">Event Title</label>
    <input type=\"text\" class=\"form-control\" id=\"Event Title\" placeholder=\"Event Title\" name=\"title\">
<button type=\"submit\" class=\"btn btn-primary\" value=\"searchtitle\" name=\"searchtitle\">Show All</button>
<button type=\"submit\" class=\"btn btn-primary\" value=\"showtitle\" name=\"showtitle\">Show Titles</button>
<button type=\"submit\" class=\"btn btn-primary\" value=\"showdescription\" name=\"showdescription\">Show Description</button>
	<p></p>
	<label class=\"sr-only\" for=\"Event Description\">Event Description</label>
    <input type=\"text\" class=\"form-control\" id=\"Event Description\" placeholder=\"Event Description\" name=\"desc\">
<button type=\"submit\" class=\"btn btn-primary\" value=\"searchdescription\" name=\"searchdescription\">Show All</button>
<button type=\"submit\" class=\"btn btn-primary\" value=\"showtitle1\" name=\"showtitle1\">Show Titles</button>
<button type=\"submit\" class=\"btn btn-primary\" value=\"showdescription1\" name=\"showdescription1\">Show Description</button>
	<p></p>
	<label class=\"sr-only\" for=\"Event Location\">Event Location</label>
    <input type=\"text\" class=\"form-control\" id=\"Event Building\" placeholder=\"Event Building\" name=\"location\">
<button type=\"submit\" class=\"btn btn-primary\" value=\"searchlocation\" name=\"searchlocation\">Show All</button>
<button type=\"submit\" class=\"btn btn-primary\" value=\"showtitle2\" name=\"showtitle2\">Show Titles</button>
<button type=\"submit\" class=\"btn btn-primary\" value=\"showdescription2\" name=\"showdescription2\">Show Description</button>
  </div>

</form>";


}

if (isset($_POST['showevents'])){
	$result = executePlainSQL("select etitle, edescription, startTime, end, street_address, building, u.username, e.eid from event e, users u where u.userid = e.userid");
       printResult($result);
}
if (isset($_POST['showmyevents'])){
	$result = executePlainSQL("select etitle, edescription, startTime, end, street_address, building, u.username, e.eid from event e, users u where u.userid =" . $activeuser . " AND u.userid = e.userid");
       printResultMine($result);
}	
if (isset($_POST['showcreate'])){

	

	echo "<form method=\"POST\" action=\"Events.php\">
<!--refresh page when submit-->
<p>Insert values into event below:</p>
 <!--watch out for START TIME AND END TIME MUST BE WORKED OUT BETTER!!! -->
<div class=\"form-group\">
    <label class=\"sr-only\" for=\"Event ID\">Event ID</label>
    <input type=\"text\" class=\"form-control\" id=\"Event ID\" placeholder=\"Event ID\" name=\"eid\">
  </div>
<div class=\"form-group\">
    <label class=\"sr-only\" for=\"Event Title\">Event Title</label>
    <input type=\"text\" class=\"form-control\" id=\"Event Title\" placeholder=\"Event Title\" name=\"etitle\">
  </div>
<div class=\"form-group\">
    <label class=\"sr-only\" for=\"Event Desc\">Event Desc</label>
    <input type=\"text\" class=\"form-control\" id=\"Event Desc\" placeholder=\"Event Description\" name=\"edescription\">
  </div>
<div class=\"form-group\">
    <label class=\"sr-only\" for=\"Event Start\">Event Start</label>
    <input type=\"text\" class=\"form-control\" id=\"Event Start\" placeholder=\"Start DD-MON-YY HH:MIPM\" name=\"startTime\">
  </div>
<div class=\"form-group\">
    <label class=\"sr-only\" for=\"Event End\">Event End</label>
    <input type=\"text\" class=\"form-control\" id=\"Event End\" placeholder=\"End DD-MON-YY HH:MIPM\" name=\"end\">
  </div>
<div class=\"form-group\">
    <label class=\"sr-only\" for=\"Street\">Street</label>
    <input type=\"text\" class=\"form-control\" id=\"Street\" placeholder=\"Street Address\" name=\"street_address\">
  </div>
<div class=\"form-group\">
    <label class=\"sr-only\" for=\"Building\">Building</label>
    <input type=\"text\" class=\"form-control\" id=\"Building\" placeholder=\"Building\" name=\"building\">
  </div>
<div class=\"form-group\">
    <label class=\"sr-only\" for=\"\">Creator</label>
    <input type=\"text\" class=\"form-control\" id=\"Creator\" placeholder=\"Creator(user)\" name=\"userid\">
  </div><br>
<button type=\"submit\" class=\"btn btn-primary\" value=\"insert\" name=\"insertsubmit\">Create Event</button>
</form>";
	}
 
    //Commit to save changes...
    OCILogoff($db_conn);
} else {
    echo "cannot connect";
    $e = OCI_Error(); // For OCILogon errors pass no handle
    echo htmlentities($e['message']);
}
?>

</html>

