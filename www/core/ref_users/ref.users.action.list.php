﻿<?php

$link = ConnectDB();

$ref_name = IsSet($_GET['ref']) ? $_GET['ref'] : 'users';
$ref_prompt = IsSet($_GET["prompt"]) ? ($_GET["prompt"]) : 'Работа с пользователем';

$query = "SELECT * FROM $ref_name WHERE deleted=0";
$res = mysql_query($query); // or die("Невозможно получить содержимое справочника! ".$ref_name);
$ref_numrows = @mysql_num_rows($res) ;

if ($ref_numrows > 0) {
    for ($i=0; $i < $ref_numrows; $i++)
    {
        $ref_record = mysql_fetch_assoc($res);
        $ref_list[$ref_record['id']] = $ref_record;
    }
} else {
    $ref_message = 'Пока не ввели ни одного пользователя!';
}

CloseDB($link);
?>
<table border="1" width="100%">
<tr>
    <th width="5%">№</th>
    <th>Ф.И.О.</th>
    <th>E-Mail</th>
    <th>Телефон</th>
    <th>Уровень<br>доступа</th>
    <th>Login</th>
    <th width="10%">Управление</th>
</tr>
    <?php
    if ($ref_numrows > 0) {
    foreach ($ref_list as $r_id => $r_value)
    {
        $row = $r_value;
        $is_disabled = ($row['permissions'] == 255) ? ' disabled' : '';
        echo <<<REF_ANYROW
<tr>
<td>{$row['id']}</td>
<td>{$row['name']}</td>
<td>{$row['email']}</td>
<td>{$row['phone']}</td>
<td>{$row['permissions']}</td>
<td>{$row['login']}</td>
<td class="centred_cell"><button class="edit_button" name="{$row['id']}" $is_disabled>Edit</button></td>
</tr>
REF_ANYROW;
        }
    } else {
        echo <<<REF_NUMROWS_ZERO
<tr><td colspan="7">$ref_message</td></tr>
REF_NUMROWS_ZERO;
    }

    ?>

</table>
