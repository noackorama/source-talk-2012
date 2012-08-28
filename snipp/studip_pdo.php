<?php
$db = DbManager::get();

$rs = $db->query("SELECT * FROM auth_user_md5 LIMIT 1");
$data = $rs->fetch(PDO::FETCH_ASSOC);

$rs = $db->query("SELECT * FROM auth_user_md5 LIMIT 10");
$data = $rs->fetchAll(PDO::FETCH_ASSOC);

$rs = $db->query("SELECT user_id as first,auth_user_md5.* FROM auth_user_md5 LIMIT 10");
$data = $rs->fetchGrouped();

$rs = $db->query("SELECT user_id, username FROM auth_user_md5 LIMIT 10");
$data = $rs->fetchGrouped(PDO::FETCH_COLUMN);

$ok = $db->exec("DELETE FROM auth_user_md5 WHERE username = " . $db->quote('test'));

$st = $db->prepare("SELECT * FROM auth_user_md5 WHERE username IN(?) ORDER BY ? LIMIT ?");
$st->bindValue(1, array('elmar','anoack','suchi'), StudipPDO::PARAM_ARRAY);
$st->bindValue(2, 'Nachname', StudipPDO::PARAM_COLUMN);
$st->bindValue(3, 5, StudipPDO::PARAM_INT);
$st->execute();
$data = $st->fetchAll(PDO::FETCH_ASSOC);
