<?php
//Define DB connection.
$server = 'localhost';
$SQLuser = 'ReadOnly';
$SQLpass = 'localpass';
$db = '2sys_main';

//Establish DB connection.
$connection = new mysqli($server, $SQLuser, $SQLpass, $db);

//Check the connection.
if ($connection->connect_error) {
    include('http://localhost/index.html');
    echo('<br>Could not connect to database! error{new_mysqli_failed}'); //use an error{} flag - this helps to debug
    //AND avoids exposing PHP errors to frontend.
    die();
}
?>