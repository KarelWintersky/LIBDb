var booksList = preloadOptionsList('ajax.php?actor=get_books_as_optionlist&lang=ru');

BuildSelector('book', booksList, 0);

url = "ajax.php?actor=load_articles_by_query&lang=ru&topic="/*plus_topic_id*/;

// если хэш установлен - нужно загрузить статьи согласно выбранным позициям
// тут нам лишний if не нужен, мы на старте загружаем все статьи
wlh = (window.location.hash).substr(1);
$("#articles_list").empty().load(url+'&'+wlh); // передача лишнего & запросу не вредит.

$("#button-show-withselection").on('click',function(){
    query = "";
    query+="&book="+$('select[name="book"]').val();
    $("#articles_list").empty().load(url+query);
});
$("#button-reset-selection").on('click',function(){
    $('select[name="book"]').val(0);
    setHashBySelectors(); // сброс хэша!
});
$("#button-show-all").on('click',function(){
    $("#articles_list").empty().load(url);
});

$('#articles_list').on('click','.more_info',function(){
    location.href = '?fetch=articles&with=info&lang=ru&id='+$(this).attr('name');
});