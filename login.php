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
    include('/home/agsty/Programming/2sys/index.html');
    echo('\nCould not connect to database! error{new_mysqli_failed}'); //use an error{} flag - this helps to debug
    //AND avoids exposing PHP errors to frontend.
    die();
}

//Include my header file for useful functions.
include('/home/agsty/Programming/2sys/universal.php');

//Load login details (email/key)
$user = $_POST['username'];
$pass = $_POST['pass'];

//Call sanitiser from universal.php
$user = sanitiseStrings($user);
$pass = sanitiseStrings($pass);

//Prepare a statement to withdraw the user's hash (if it exists) before we verify it.
$retrieveHash = $connection->prepare('SELECT `PassHash` FROM `Accounts` WHERE `Username` LIKE ?');
$retrieveHash->bind_param('s', $user);

//Check statement validity
if (!$retrieveHash) {
    include('/home/agsty/Programming/2sys/index.html');
    echo('\nError with prepared statement! error{retrieve_hash_failed}'); //use an error{} flag - this helps to debug
    //AND avoids exposing PHP errors to frontend.
    die();
}

//If statement is valid, we proceed
$retrieveHash->execute();
$retrieveHash->bind_result($hashedPass);
$retrieveHash->fetch();
$retrieveHash->close(); //Close, as we will need another query later in this script (also more secure + save resources)

if ($hashedPass = '') {
    include('/home/agsty/Programming/2sys/index.html');
    echo('\nIncorrect login details!');
    die(); //Die, as this is more likely to be an issue with setup
    //or if the user exists, not a bug or connection issue with phpMA or mySQL. 
    //No need to report an issue with the site using debug techniques (error{FLAG_NAME})
}

//Check the password.
$userVerified = password_verify($pass, $hashedPass);

if (!$userVerified) {
    include('/home/agsty/Programming/2sys/index.html');
    echo('\nIncorrect login details!');
    die(); //Wrong password - unlucky mate! Go try again why don't you
    //We use the exact same 'fail' message in both cases - 
    //this means any MISCREANTS attacking the site can't tell if the username tested exists or not.
    // +1 for security!
}

//At this point in the code, we can tell that the user has:
//A correct username
//AND a correct password.

//We will retrieve their UserID - 
//this should improve the speed of lookup queries 
//compared to string lookups with their username.
$retrieveUserID = $connection->prepare('SELECT `UserID` FROM `Accounts` WHERE `Username` LIKE ?');
$retrieveUserID->bind_param('s', $user);
//This is safe as we have VERIFIED the username AND its password.

if (!$retriveUserID) {
    include('/home/agsty/Programming/2sys/index.html');
    echo('\nError with prepared statement! error{retrieve_hash_failed}'); //use an error{} flag - this helps to debug
    //AND avoids exposing PHP errors to frontend.
    die();
}

$retrieveUserID->execute();
$retrieveUserID->bind_result($userID);
$retrieveUserID->fetch();
$retrieveUserID->close(); //Again, close to save resource and to allow us to query again later.

//Next we will read their 'Access level' - 
//and kick them to the home of whichever group their account belongs to.

$readAccessLevel = $connection->prepare('SELECT `AccessLevel` FROM `Accounts` WHERE `UserID` LIKE ?');
$readAccessLevel->bind_param('i', $userID); //Note - binding an INT this time as that is what UserID is stored as.

if (!$readAccessLevel) {
    include('/home/agsty/Programming/2sys/index.html');
    echo('\nError with prepared statement! error{retrieve_hash_failed}'); //use an error{} flag - this helps to debug
    //AND avoids exposing PHP errors to frontend.
    die();
}

$readAccessLevel->execute();
$readAccessLevel->bind_result($accessPermission);
$readAccessLevel->fetch();
$readAccessLevel->close(); //I don't think this needs explained again.

switch ($accessPermission) {
    case 'Admin':
        header('Location: /home/agsty/Programming/2sys/Admin/panel.php');
        break;
    case 'Parent':
        header('Location: /home/agsty/Programming/2sys/Parents/panel.php');
        break;
    case 'Student':
        header('Location: /home/agsty/Programming/2sys/Students/panel.php');
        break;
    case 'Teacher':
        header('Location: /home/agsty/Programming/2sys/Teachers/panel.php');
        break;
}

//Load login form.
include('/home/agsty/Programming/2sys/index.html');
die(); //Kill script.
?>