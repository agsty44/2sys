<?php

//Connect to DB using the 'ReadOnly' account
include('/home/agsty/Programming/2sys/readOnlyLogin.php');

//Include my header file for useful functions.
include('/home/agsty/Programming/2sys/universal.php');

//Before doing anything, we should check for already existing login details.
if (isset($_COOKIE['dXNlcm5hbWVDb29raWU=']) && isset($_COOKIE['cGFzc3dvcmRDb29raWU='])) {
    //the user already has login details set, so we should check those first.
    include('/home/agsty/Programming/2sys/retrieveUserInfoCookie.php');
    goToHomePanel(); //Call the function to sent them to the appropriate home page.
}

//Load login details (email/key)
$user = $_POST['username'];
$pass = $_POST['pass'];

//Call sanitiser from universal.php
$user = sanitiseStrings($user);
$pass = sanitiseStrings($pass);

//Now, call the script to:
//1. Verify the user exists and that the password is right.
//2. Retrieve their accessLevel and UID.

include('/home/agsty/Programming/2sys/retrieveUserInfo.php');
goToHomePanel(); //same as the earlier function call, just that this is used if no cookies were found.
//cookies are set by the goToHomePanel function calling establishLoginCookies.
?>