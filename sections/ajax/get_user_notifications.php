<?php

$Skip = [];
$Skip[] = db_string($_GET['skip']);

$NotificationsManager = new NotificationsManager($LoggedUser['ID'], $Skip);
json_die('success', $NotificationsManager->get_notifications());
