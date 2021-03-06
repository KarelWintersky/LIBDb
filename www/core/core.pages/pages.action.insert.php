<?php
define('__ACCESS_MODE__', 'admin');
require_once '../__required.php'; // $mysqli_link

$ref_name = 'staticpages';

$dataset = array(
    'alias'         => mysqli_real_escape_string($mysqli_link, $_POST['alias'] ?? ''),
    'comment'       => mysqli_real_escape_string($mysqli_link, $_POST['comment'] ?? ''),
    'title_en'      => mysqli_real_escape_string($mysqli_link, $_POST['title_en'] ?? ''),
    'title_ru'      => mysqli_real_escape_string($mysqli_link, $_POST['title_ru'] ?? ''),
    'title_ua'      => mysqli_real_escape_string($mysqli_link, $_POST['title_ua'] ?? ''),
    'content_en'    => mysqli_real_escape_string($mysqli_link, $_POST['content_en'] ?? ''),
    'content_ru'    => mysqli_real_escape_string($mysqli_link, $_POST['content_ru'] ?? ''),
    'content_ua'    => mysqli_real_escape_string($mysqli_link, $_POST['content_ua'] ?? ''),
);
$query = MakeInsert($dataset, 'staticpages');

if ($res = mysqli_query($mysqli_link, $query)) {
    $result['message'] = $query;
    $result['error'] = 0;
    $record_id = mysqli_insert_id($mysqli_link);
    kwLogger::logEvent('Add', 'pages', $record_id, "Static page added, id = {$record_id}");
}
else {
    Die("Unable to insert data to DB!  ".$query);
}


if (isAjaxCall()) {
    print(json_encode($result));
} else {
    if ($result['error'] == 0) {

        $template_dir = '$/core/_templates';
        $template_file = "ref.all_timed_callback.html";

        $template_data = array(
            'time'          => Config::get('callback_timeout') ?? 15,
            'target'        => '../list.pages.show.php',
            'button_text'   => 'Вернуться к списку страниц',
            'message'       => 'Статическая страница добавлена'
        );
        echo websun_parse_template_path($template_data, $template_file, $template_dir);
    }
}