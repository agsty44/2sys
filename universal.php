<?php

//This file stores a set of univeral functions for use throughout 2sys. This includes:
//Data sanitisers (more TBA)

function sanitiseStrings($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function establishLoginCookies() {
    global $user, $hashedPass;

    //At this point, we should also commit the user's username and password to their cookies, 
    //ofc hashing the password for security.

    setcookie('dXNlcm5hbWVDb29raWU', $user, time() + (86400 * 7), '/');
    //that is 'usernameCookie' in b64.

    //set the hashed pass here (we already have it:)
    setcookie('cGFzc3dvcmRDb29raWU', $hashedPass, time() + (86400 * 7), '/');
    //cookie name is 'passwordCookie' in b64
    //yes i know that's nowhere near secure but it beats storing it under the name 'passwordCookie'
}

function unsetCookies() {
    setcookie('dXNlcm5hbWVDb29raWU', '', time() -1, '/');
    setcookie('cGFzc3dvcmRDb29raWU', '', time() -1, '/');
}

function goToHomePanel() {
    global $accessPermission;

    switch ($accessPermission) {
        case 'Admin':
            header('Location: http://localhost//Admin/panel.php');
            establishLoginCookies();
            die();
        case 'Parent':
            header('Location: http://localhost//Parents/panel.php');
            establishLoginCookies();
            die();
        case 'Student':
            header('Location: http://localhost//Students/panel.php');
            establishLoginCookies();
            die();
        case 'Teacher':
            header('Location: http://localhost//Teachers/panel.php');
            establishLoginCookies(); //Either set for first time OR refresh cookies.
            die();
        default:
            header('Location: http://localhost/index.html'); //Something is wrong here, so we should send them back to login.
            echo('A problem occured with your account. Contact the admins. error{access_level_not_real}');
            die();
}
}
?>