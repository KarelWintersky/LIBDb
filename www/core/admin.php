<?php
require_once('core.php');
require_once('core.db.php');

$SID = session_id();
if(empty($SID)) session_start();
if (!isLogged()) header('Location: /core/');
?>
<html>
<head>
    <title>Административный раздел</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script src="js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".action-is-href").on('click',function(){
                window.location.href = $(this).attr('data-href');
            });
            $(".action-is-href-blank").on('click',function(){
                window.open($(this).attr('data-href'), '_blank');
            });
            $(".action-have-submenu").on('click',function(){
                $('#'+$(this).attr('data-submenu-id') ).toggle(400);
            });
        });
    </script>
    <style>
        table {
            border: 1px dotted blue;
        }
        ul {
            margin-left: 0;
            margin-bottom: 0; margin-top: 0;
            padding: 0;
        }
        ul li {
            list-style-type: none;
            padding-top: 2px;
            padding-bottom: 2px;
        }
        ul li.menu-header {
            text-align: center;
            display: block;
            font-size: large;
            color: #dc143c;
            width: 150px;
        }
        div {
            margin:0;
        }
        /* */
        #footer {
            color: gray;
            background-color: #d3d3d3;
        }

        ol li {
            margin-bottom: 0.5em;
        }

        .hidden {
            display: none;
        }

        /* submenu microengine */
        .have-top-border {
            border-top: 3px navy solid;
            padding-top: 10px;
            margin-top: 10px;
            width: 150px;
        }
        .admin-button-large {
            height: 60px;
            width: 150px;
        }
        .admin-button-small {
            width: 150px;
        }
        .button-have-submenu {
            color: blue;
        }
        .actor-button-sub {
            width: 150px;
        }
        button[disabled] {
            color: gray;
        }

    </style>
</head>
<body>
<div id="header">
    <div style="float:right; margin-right: 1em"><a href="/" target="_blank">Сайт библиотеки</a></div>
</div>
<table width="100%">
    <tr>
        <td width="180">
            <!-- типа меню -->
            <ul>
                <li class="menu-header">
                    Разделы сайта
                </li>
                <li>
                    <button data-href="ref.topicgroups.show.php" class="action-is-href admin-button-large">Группировка<br>разделов</button>
                </li>
                <li>
                    <button data-href="ref.topics.show.php" class="admin-button-large action-is-href">ТЕМАТИЧЕСКИЕ РАЗДЕЛЫ</button>
                </li>
                <li>
                    <button data-href="ref.books.show.php" class="admin-button-large action-is-href">СБОРНИКИ (КНИГИ)</button>
                </li>
                <li>
                    <button data-href="ref.authors.show.php" class="admin-button-large action-is-href">АВТОРЫ</button>
                </li>
                <li>
                    <button data-href="ref.articles.show.php" class="admin-button-large action-is-href">СТАТЬИ</button>
                </li>
                <li>
                    <button data-href="ref.pages.show.php" class="admin-button-large action-is-href">СТАТИЧНЫЕ<br>СТРАНИЦЫ</button>
                </li>
                <li>
                    <button data-href="ref.news.show.php" class="admin-button-large action-is-href">НОВОСТИ</button>
                </li>
                <li>
                    <button data-href="ref.banners.php" class="admin-button-large action-is-href">Баннеры</button>
                </li>
                <li class="have-top-border">
                    <button type="button" class="admin-button-small button-have-submenu action-have-submenu" data-submenu-id="submenu-tools">Maintenance</button>
                    <ul id="submenu-tools" class="hidden submenu-border">
                        <li>
                            <button data-href="ref.users.show.php" class="admin-button-small action-is-href">ПОЛЬЗОВАТЕЛИ</button>
                        </li>
                        <li>
                            <button data-href="list.filestorage.php" class="admin-button-large action-is-href">Filestorage</button>
                        </li>
                        <li class="hidden">
                            <button data-href="/files/storage/" class="admin-button-small action-is-href-blank">/files/storage/</button>
                        </li>
                        <!--
                        <li>
                            <button data-href="list.statistics.php" class="admin-button-small actor-button" disabled>Статистика</button>
                        </li>
                        -->
                        <li>
                            <button data-href="/core/core.filestorage/filestorage.maintenance.recalc.php" class="admin-button-small action-is-href">Recalc filesizes</button>
                        </li>
                        <li class="menu-header">
                            Справочники:
                        </li>
                        <li>
                            <button data-href="ref.abstract.php?ref=ref_selfhood" class="action-is-href admin-button-small">Самость</button>
                        </li>
                    </ul>
                </li>
                <li class="menu-header have-top-border">
                    Управление:
                </li>
                <li>
                    <button data-href="admin.logout.php" class="admin-button action-is-href  admin-button-small">Logout</button>
                </li>
            </ul>
        </td>
        <td valign="top">
            <h1>Краткая справка</h1>
            Она вам нужна? Обратитесь к разработчику :)
            <hr>
            Внимание, старайтесь не загружать фотографии с линейными размерами больше 800*1000 (ширина*высота). <br>
            Если это фотография автора - она все равно будет принудительно уменьшена до размера 150*200<br>
            Если это фотография заглавной страницы сборника - её будет очень неудобно смотреть во всплывающем окне.<br>
            Но, все же, если вы не знаете, как уменьшить размер изображения - загружайте как умеете. Только сообщите разработчику.<br>

            <hr>
            Часто бывает так, что от автора приходит PDF-ка астрономического размера - на несколько
            мегабайт. <br>
            Откуда вообще берется такой размер файла? Чаще всего автор не желает выставлять нормальные настройки сжатия изображений в файле -
            опасаясь потери данных при сжатии. Но что делать <strong>нам</strong> с файлом на 8 мегабайт? <br/>
            Для уменьшения размера файла можно воспользоваться сервисом <a href="http://smallpdf.com/ru/compress-pdf">http://smallpdf.com/ru/compress-pdf</a><br>
            После загрузки исправленного файла крайне желательно открыть подменю "Maintenance" и нажать кнопочку "Recalc filesizes"<br>
        </td>
    </tr>
</table>
<div id="footer">© Karel Wintersky, 2014.</div>

</body>
</html>