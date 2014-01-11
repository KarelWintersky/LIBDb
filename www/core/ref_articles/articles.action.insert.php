<?php
$result['message'] = '';
$result['error'] = 0;

if (!IsSet($_POST['caller'])) {
    $result['error'] = 1; $result['message'] .= 'Unknown caller!'; print(json_encode($result)); exit();
}

$link = ConnectDB();

$q = array(
    'udc' => mysql_escape_string($_POST['udc']),
    'title_eng' => mysql_escape_string($_POST['title_eng']),
    'title_rus' => mysql_escape_string($_POST['title_rus']),
    'title_ukr' => mysql_escape_string($_POST['title_ukr']),
    'abstract_eng' => mysql_escape_string($_POST['abstract_eng']),
    'abstract_rus' => mysql_escape_string($_POST['abstract_rus']),
    'abstract_ukr' => mysql_escape_string($_POST['abstract_ukr']),
    'keywords_eng' => mysql_escape_string($_POST['keywords_eng']),
    'keywords_rus' => mysql_escape_string($_POST['keywords_rus']),
    'keywords_ukr' => mysql_escape_string($_POST['keywords_ukr']),
    'refs' => mysql_escape_string($_POST['refs']),
    'book' => mysql_escape_string($_POST['book']),
    'add_date' => mysql_escape_string($_POST['add_date']),
    'topic' => mysql_escape_string($_POST['topic']),
    'pages' => mysql_escape_string($_POST['pages'])
);

// теперь нам нужно вставить данные в БАЗУ (пока что с учетом вставки файла в БЛОБ)
$qstr = MakeInsert($q,'articles');
$res = mysql_query($qstr, $link) or Die("Невозможно вставить данные в базу  ".$qstr);
$new_id = mysql_insert_id() or Die("Не удалось получить id последней добавленной записи!");

if (IsSet($_FILES)) {
    $pdf_username = $_FILES['pdffile']['name'];
    $pdf_filesize = $_FILES['pdffile']['size'];

    $tmp_name = ($_SERVER['REMOTE_ADDR']==="127.0.0.1") ? str_replace('\\','\\\\',$_FILES['pdffile']['tmp_name']) : $_FILES['pdffile']['tmp_name'];

    $blobdata = mysql_escape_string(floadpdf($tmp_name));

    $q = "INSERT INTO `pdfdata` (`content`,`username`,`tempname`,`filesize`,`articleid`)
    VALUES ('$blobdata','$pdf_username','$tmp_name','$pdf_filesize' , '$new_id')";

    mysql_query($q, $link) or Die("Death on $q");
    $pdf_id = mysql_insert_id() or Die("Не удалось получить id последней добавленной записи!");

    $q = "UPDATE articles SET pdfid=$pdf_id  WHERE id=$new_id";
    mysql_query($q, $link) or Die("Death on $q");
} else {
    $result['error'] = 1;
    $result['message'] .= "Не выбран файл для загрузки или ошибка передачи данных! <br>\r\n";
}

// потом обновить кросс-таблицу
// в едите нужно удалить старые значения, потом добавить новые
if (IsSet($_POST['authors'])) {
    $authors = $_POST['authors'];
    foreach ($authors as $n => $author) {
        $qa = "INSERT INTO cross_aa (author,article) VALUES ($author, $new_id)";
        mysql_query($qa , $link) or Die('error at '.$qa);
    }
} else {
    $result['error'] = 1;
    $result['message'] .= "Не указаны авторы!<br>\r\n";
}

CloseDB($link);

if ($result['error'] == 0) {
    $override = array(
        'time' => 10,
        'target' => '../ref.articles.show.php',
        'buttonmessage' => 'Вернуться к списку статей',
        'message' => 'Статья добавлена'
    );
} else {
    $override = array(
        'time' => 10,
        'target' => '../ref.articles.show.php',
        'buttonmessage' => 'Вернуться к списку статей',
        'message' => $result['message']
    );
}
$tpl = new kwt('../ref.all.timed.callback.tpl');
$tpl->override($override);
$tpl->out();

?>