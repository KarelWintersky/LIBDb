<?php
require_once('core/core.php');
require_once('core/core.db.php');
require_once('core/core.kwt.php');
require_once('frontend.php');

$site_language = FE_GetSiteLanguage(); // на самом деле получаем из куки
$tpl_path = 'tpl';

// init defaults fields and variables
$content = '';
$jscripts = '';
$override = array( // template override array
);

// load default template, based on language
$tpl_index = new kwt($tpl_path."/index.{$site_language}.html"); // файлы шаблонов различны для разных языков + файл переводов
$tpl_index->config('<!--{','}-->');
$tpl_index->contentstart();

$override = array();

$link = ConnectDB();

$override['rubrics'] = FE_PrintTopics(DB_LoadTopics($site_language),$site_language);
$override['books'] = FE_PrintBooks(DB_LoadBooks($site_language),$site_language);

// Main switch

$fetch = isset($_GET['fetch']) ? $_GET['fetch'] : '';
$with = isset($_GET['with']) ? $_GET['with'] : '';

switch ($fetch) {
    case 'authors' : {
        /* секция обработки авторов - информация или список */
        switch ($with) {
            case 'info' : {
                /*расширенная информация по автору + список его статей + фото */
                $filename = $tpl_path.'/fetch=authors/with=info/f_authors+w_info.'.$site_language;
                $id = $_GET['id'];

                $inner_html = new kwt($filename.".html");

                $air = DB_LoadAuthorInformation_ById($id, $site_language);

                $a_articles = FE_PrintArticles_ByAuthor(DB_LoadArticles_ByAuthor($id, $site_language), $site_language);

                $inner_html_override = array(
                    'author_publications'   => $a_articles,
                    'author_name'           => $air['author_name'],
                    'author_title'          => $air['author_title'],
                    'author_email'          => $air['author_email'],
                    'author_workplace'      => $air['author_workplace'],
                    'author_bio'            => $air['author_bio'],
                    'author_photo_id'       => $air['author_photo_id'],
                    'author_photo_link'     => ($air['author_photo_id'] == -1) ? "/images/no_photo.png" : "core/getimage.php?id={$air['author_photo_id']}"
                );

                $inner_html->override($inner_html_override);
                $inner_html->contentstart();

                $content = $inner_html->getcontent();

                $inner_js = new kwt($filename.".js");

                $inner_js->override( array(
                    "author_is_es" => ($air['author_is_es'])==1 ? 'block' : 'none' )
                );
                $inner_js->contentstart();
                $jscripts = $inner_js->getcontent();

                break;
            } // end case info
            case 'all' : {
                // список ВСЕХ авторов - для поисковых систем
                // фио, титул, email -> link to author page
                $filename = $tpl_path.'/fetch=authors/with=all/f_authors+w_all.'.$site_language;

                $all_authors_list = FE_PrintAuthors_PlainList(DB_LoadAuthors_ByLetter('', $site_language, 'no'), $site_language);

                $inner_html = new kwt($filename.".html");
                $inner_html->override( array (
                    'all_authors_list' => $all_authors_list
                ));
                $inner_html->contentstart();
                $content = $inner_html->getcontent();

                $inner_js = new kwt($filename.".js");
                $inner_js->contentstart();
                $jscripts = $inner_js->getcontent();
                break;
            } // end case all
            //@todo: редколлегию переработать
            case 'estuff': {
                // список ВСЕХ авторов в редколлегии - для поисковых систем
                // фио, титул, email -> link to author page
                $filename = $tpl_path.'/fetch=authors/with=estuff/f_authors+w_estuff.'.$site_language;

                $all_authors_plainlist = FE_PrintAuthors_PlainList(DB_LoadAuthors_ByLetter('0',$site_language, 'yes'), $site_language);

                // вообще-то порядок (в смысле запрашиваемый selfhood) хорошо бы дергать из справочника, но...

                $inner_html = new kwt($filename.".html");
                $inner_html->override( array (
                    // 'es_authors_list' => $all_authors_plainlist,
                    // главный редактор = 5
                    'estuff_main_editor' => FE_PrintAuthors_EStuffList(DB_LoadAuthors_ByLetter('0',$site_language, 'yes', 5), $site_language),
                    // замглавного редактора = 4
                    'estuff_main_subeditors' => FE_PrintAuthors_EStuffList(DB_LoadAuthors_ByLetter('0',$site_language, 'yes', 4), $site_language),
                    // редакционная коллегия = 3
                    'estuff_local_editors' => FE_PrintAuthors_EStuffList(DB_LoadAuthors_ByLetter('0',$site_language, 'yes', 3), $site_language),
                    // международная редакционная коллегия = 1
                    'estuff_remote_editors' => FE_PrintAuthors_EStuffList(DB_LoadAuthors_ByLetter('0',$site_language, 'yes', 1), $site_language),
                    // редакторы = 6
                    'estuff_simple_editors' => FE_PrintAuthors_EStuffList(DB_LoadAuthors_ByLetter('0',$site_language, 'yes', 6), $site_language),
                ));
                $inner_html->contentstart();
                $content = $inner_html->getcontent();

                $inner_js = new kwt($filename.".js");
                $inner_js->contentstart();
                $jscripts = $inner_js->getcontent();

                break;
            } // end case estuff
            case 'list' : {

                $filename = $tpl_path.'/fetch=authors/with=list/f_authors+w_list.'.$site_language;

                $inner_html = new kwt($filename.".html");
                $inner_html->contentstart();
                $content = $inner_html->getcontent();

                $inner_js = new kwt($filename.".js");
                $inner_js->contentstart();
                $jscripts = $inner_js->getcontent();

                break;
            } // end case list
        }; // end $with authors switch
        break;
    }
    case 'articles': {
        /* секция вывода статей по критерию поиска, информации по статье */
        switch ($with) {
            case 'extended': {
                $filename = $tpl_path.'/fetch=articles/with=extended/f_articles+w_extended.'.$site_language;

                $inner_html = new kwt($filename.'.html');
                // $inner_html->override( array ());
                $inner_html->contentstart();
                $content = $inner_html->getcontent();

                $inner_js = new kwt($filename.'.js');
                // $inner_js->override( array() );
                $inner_js->contentstart();
                $jscripts = $inner_js->getcontent();

                break;
            } // end case extended
            case 'topic': {
                $filename = $tpl_path.'/fetch=articles/with=topic/f_articles+w_topic.'.$site_language;

                $inner_html = new kwt($filename.'.html');
                // $inner_html->override( array ());
                $inner_html->contentstart();
                $content = $inner_html->getcontent();

                $inner_js = new kwt($filename.'.js');

                $inner_js->config('/*','*/');
                $inner_js->override( array( "plus_topic_id" => "+".$_GET['id'] ) );
                $inner_js->contentstart();
                $jscripts = $inner_js->getcontent();

                break;
            } // end case topic
            case 'book' : {
                $filename = $tpl_path.'/fetch=articles/with=book/f_articles+w_book.'.$site_language;

                $inner_html = new kwt($filename.'.html');
                // load extended book fields by ID -- @todo: move to function !!!
                $book_row = mysql_fetch_assoc(mysql_query("SELECT file_cover, file_title, file_toc FROM books WHERE id={$_GET['id']}"));

                $inner_html->override( array (
                    'file_cover' => $book_row['file_cover'],
                    'file_title' => $book_row['file_title'],
                    'file_toc' => $book_row['file_toc']
                ));
                $inner_html->contentstart();
                $content = $inner_html->getcontent();

                $inner_js = new kwt($filename.'.js');

                $inner_js->config('/*','*/');
                $inner_js->override( array( "plus_book_id" => "+".$_GET['id'] ) );
                $inner_js->contentstart();
                $jscripts = $inner_js->getcontent();

                break;
            } // end case book
            case 'info' : {
                $id = $_GET['id'];
                $filename = $tpl_path.'/fetch=articles/with=info/f_articles+w_info.'.$site_language;

                $inner_html = new kwt($filename.'.html');

                $article_info = DB_LoadArticleInformation_ById($id, $site_language);
                $article_authors = FE_PrintAuthors_ByArticle(DB_LoadAuthors_ByArticle($id, $site_language, 'yes'), $site_language);

                $inner_html->override( array (
                    'article-title' => $article_info['title_'.$site_language],
                    'article-abstract' => $article_info['abstract_'.$site_language],
                    'article-authors-list' => $article_authors, // список авторов, писавших статью
                    'article-keywords' => $article_info['keywords_'.$site_language],
                    'article-book-title' => $article_info['btitle'],
                    'article-book-year' => $article_info['byear'],
                    'article-pdfid' => $article_info['pdfid'],
                    'article-refs' => $article_info['refs_'.$site_language]
                ));
                $override['meta_keywords'] = $article_info['keywords_'.$site_language]; // GLOBAL KEYWORDS

                $inner_html->contentstart();
                $content = $inner_html->getcontent();

                $inner_js = new kwt($filename.'.js');

                $inner_js->config('/*','*/');
                $inner_js->override( array( "plus_book_id" => "+".$_GET['id'] ) );
                $inner_js->contentstart();
                $jscripts = $inner_js->getcontent();

                break;
            } // end case info
            case 'all' : {
                // список ВСЕХ СТАТЕЙ - для поисковых систем
                // фио, титул, email -> link to author page
                $filename = $tpl_path.'/fetch=articles/with=all/f_articles+w_all.'.$site_language;
                $inner_html = new kwt($filename.".html");

                $inner_html->override( array (
                    'all_articles_list' => FE_PrintArticlesList_Simple(DB_LoadArticlesByQuery(array(), $site_language, 'no') ,$lang)
                ));
                $inner_html->contentstart();
                $content = $inner_html->getcontent();

                $inner_js = new kwt($filename.".js");
                $inner_js->contentstart();
                $jscripts = $inner_js->getcontent();

                break;
            }// end case all
        } // end $with articles switch
        break;
    }
    case 'page' : {
        /* секция вывода статических или условно-статических страниц */
        $page_alias = ($with === '') ? 'default' : $with;
        // если никакую страницу не запрашиваем - выводим контент страницы с алиасом DEFAULT
        $content = FE_GetStaticPage($page_alias, $site_language);

        break;
    }
    case 'news': {
        /* секция вывода новостей */
        break;
    }
    default: {
        // default page
        $page_alias = 'about';
        $content = FE_GetStaticPage($page_alias, $site_language);
        // это статическая страница "о журнале" + свидетельство + список статей в последнем выпуске
        // + ? последняя новость
    }
}; // end $fetch all switch




$fetch = isset($_GET['fetch']) ? $_GET['fetch'] : '';
$with = isset($_GET['with']) ? $_GET['with'] : '';

$override['content_jquery'] = $jscripts;
$override['content'] = $content;

// $override['debug'] = print_r($_COOKIE, true);

$tpl_index->override($override);
$tpl_index->contentstart(); // если есть вложенные темплейты, этот вызов обязателен!!!!!!
$tpl_index->out();

?>