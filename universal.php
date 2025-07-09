<?php

//This file stores a set of univeral functions for use throughout 2sys. This includes:
//Data sanitisers (more TBA)

function sanitiseStrings($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>