<?php
//This REQUIRES the variables $user and $pass to be declared!
//DECLARE THE VARIABLES BY PULLING THEM FROM THE COOKIES
//NOTE - use retrieveUserInfo if they are logging in with the form!

//Prepare a statement to withdraw the user's hash (if it exists) before we verify it.
$retrieveHash = $connection->prepare('SELECT `PassHash` FROM `Accounts` WHERE `Username` LIKE ?');
$retrieveHash->bind_param('s', $user);

//Check statement validity
if (!$retrieveHash) {
    include('/home/agsty/Programming/2sys/index.html');
    echo('<br>Error with prepared statement! error{retrieve_hash_failed}'); //use an error{} flag - this helps to debug
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
    unsetCookies(); //This looks to be an error, however every file which calls this script
    //will also have '/home/agsty/Programming/2sys/universal.php' and its functions already included
    //so this function will be available to call.
    echo('<br>Incorrect login details!');
    die(); //Die, as this is more likely to be an issue with setup
    //or if the user exists, not a bug or connection issue with phpMA or mySQL. 
    //No need to report an issue with the site using debug techniques (error{FLAG_NAME})
}

//Check the password. we do NOT use password_verify as the cookie is PREHASHED.
if ($hashedPass != $pass) {
    //something aint quite right with the cookies here, so we are going to CLEAR them
    //and then send the user to login.
    unsetCookies();
    header('Location: /home/agsty/Programming/2sys/index.html');
    die();
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
    echo('<br>Error with prepared statement! error{retrieve_hash_failed}'); //use an error{} flag - this helps to debug
    //AND avoids exposing PHP errors to frontend.
    die();
}

$retrieveUserID->execute();
$retrieveUserID->bind_result($userID);
$retrieveUserID->fetch();
$retrieveUserID->close(); //Again, close to save resource and to allow us to query again later.

//Next we will read their 'Access level'

$readAccessLevel = $connection->prepare('SELECT `AccessLevel` FROM `Accounts` WHERE `UserID` LIKE ?');
$readAccessLevel->bind_param('i', $userID); //Note - binding an INT this time as that is what UserID is stored as.

if (!$readAccessLevel) {
    include('/home/agsty/Programming/2sys/index.html');
    echo('<br>Error with prepared statement! error{retrieve_hash_failed}'); //use an error{} flag - this helps to debug
    //AND avoids exposing PHP errors to frontend.
    die();
}

$readAccessLevel->execute();
$readAccessLevel->bind_result($accessPermission);
$readAccessLevel->fetch();
$readAccessLevel->close(); //I don't think this needs explained again.

//At the end of the scripts, you should have:
//$user - their username.
//$pass - plaintext pass.
//$hashedPass - their hashed pass from DB.
//$userID - their UID.
//$accessPermission - their access level.
?>