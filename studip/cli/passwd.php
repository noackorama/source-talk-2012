#!/usr/bin/php
<?php

include 'studip_cli_env.inc.php';

echo "Please enter username:";
$username = trim(fgets(STDIN));
echo "Please enter new password:";
$newpass = trim(fgets(STDIN));
echo "Please re-enter new password:";
$newpass2 = trim(fgets(STDIN));
if ($newpass !== $newpass2) {
    trigger_error("Passwords do not match", E_USER_ERROR);
}
$db = DbManager::get();
$rs = $db->query("SELECT * FROM auth_user_md5 WHERE username = " . $db->quote($username));
$user = $rs->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    trigger_error("User not found", E_USER_ERROR);
}
$st = $db->prepare("UPDATE auth_user_md5 SET password = MD5(?) LIMIT 1");
$st->execute(array($newpass));
if ($st->rowCount()) {
    echo "Password for {$user['username']} changed.\n";
    exit(0);
} else {
    trigger_error("Unable to change password.", E_USER_ERROR);
}
