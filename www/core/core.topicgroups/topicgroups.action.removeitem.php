<?php
require_once('../core.php');
require_once('../core.db.php');
require_once('../core.kwt.php');

$SID = session_id();
if(empty($SID)) session_start();
if (!isLogged()) header('Location: /core/');

if (!IsSet($_GET['ref_name'])) {
    $result['error'] = 1; $result['message'] = 'Unknown caller!'; print(json_encode($result)); exit();
} else {
    $table = 'topicgroups';
    $id = $_GET["id"];
    $link = ConnectDB();

    $q = "DELETE FROM {$table} WHERE id = {$id} ";
    if ($r = mysql_query($q)) {
            $result["error"] = 0;
            $result['message'] = 'Группа тематических разделов удалена из базы данных!';
        } else {
            $result["error"] = 1;
            $result['message'] = 'Ошибка удаления группы тематических разделов из базы данных!';
        }
    CloseDB($link);
    };

print(json_encode($result));
?>