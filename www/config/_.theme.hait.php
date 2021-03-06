<?php
/**
 * User: Karel Wintersky
 * Date: 06.01.2019
 */

return [
    /**
     * Название шаблона
     */
    'template_name'     => 'template.hait',
    
    /**
     * Директория с файлами шаблона 
     */
    'template_dir'      => 'template.hait',

    /**
     * Имя редактируемого сайта в админке. 
     * Вынесено в переменную так, как шаблоны админки едины для всех сайтов на движке.
     */
    'root_page_title'   => 'HAIT.OPU.UA',

    /**
     * Включать ли на главную страницу блок с информацией о последнем сборнике?
     * TRUE : включать
     * FALSE: нет
     */
    'default_page:include_last_book'    =>  false,

    /**
     * использовать ли языко-зависимые названия сборников
     * TRUE : везде используются поля title_en, title_ru, title_ua
     * FALSE: все хранится в title_en
     */
    'book:use_lang_depended_title'      =>  true,

    /**
     * Если TRUE - то в списке авторов на странице ?fetch=authors&with=all будут показываться ВСЕ авторы
     * Если FALSE - то только имеющие статьи
     */
    'authors_all:show_without_articles' =>  false
];
 
 
