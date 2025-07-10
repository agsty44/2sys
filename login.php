<?php

//Connect to DB using the 'ReadOnly' account
include('/home/agsty/Programming/2sys/readOnlyLogin.php');

//Include my header file for useful functions.
include('/home/agsty/Programming/2sys/universal.php');

//Before doing anything, we should check for already existing login details.
if (isset($_COOKIE['dXNlcm5hbWVDb29raWU=']) && isset($_COOKIE['cGFzc3dvcmRDb29raWU='])) {
    //the user already has login details set, so we should check those first.
    include('/home/agsty/Programming/2sys/retrieveUserInfoCookie.php');
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

switch ($accessPermission) {
    case 'Admin':
        header('Location: /home/agsty/Programming/2sys/Admin/panel.php');
        establishLoginCookies();
        die();
    case 'Parent':
        header('Location: /home/agsty/Programming/2sys/Parents/panel.php');
        establishLoginCookies();
        die();
    case 'Student':
        header('Location: /home/agsty/Programming/2sys/Students/panel.php');
        establishLoginCookies();
        die();
    case 'Teacher':
        header('Location: /home/agsty/Programming/2sys/Teachers/panel.php');
        establishLoginCookies();
        die();
    default:
        header('Locations: /home/agsty/Programming/2sys/index.html'); //Something is wrong here, so we should send them back to login.
        //We also dont set cookies as this user is bugged.
        echo('A problem occured with your account. Contact the admins. error{access_level_not_real}');
        die();
}

?>