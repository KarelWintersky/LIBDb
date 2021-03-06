/* articles__extended */
var siteLanguage = '&lang=ua';
var topicsList = preloadOptionsList('ajax.php?actor=get_topics_as_optgroup_list'+siteLanguage);
var booksList = preloadOptionsList('ajax.php?actor=get_books_as_optionlist_extended'+siteLanguage);
var lettersList = preloadOptionsList('ajax.php?actor=get_letters_as_optionlist'+siteLanguage);

BuildSelector('letter', lettersList, 'ANY', 0);
BuildSelectorExtended('book', booksList, 'ANY', 0);
BuildSelectorExtended('topic', topicsList, 'ANY', 0);

url_extended = "ajax.php?actor=load_articles_by_query"+siteLanguage;
url_expert = "ajax.php?actor=load_articles_expert_search"+siteLanguage;

// если хэш установлен - нужно загрузить статьи согласно выбранным позициям
wlh = (window.location.hash).substr(1);
if (wlh !== '') {
    $("#articles_list").empty().load(url_extended + '&' + wlh);
}

$("#button-show-withselection").on('click',function(){
    var query = '', url= '';
    if ($('#button-toggle-search').attr('data-searchmode') == 'extended')
    {
        query+="&topic="+$('select[name="topic"]').val();
        query+="&book="+$('select[name="book"]').val();
        query+="&letter="+$('select[name="letter"]').val();
        url = url_extended;
    } else {
        query+= "&expert_name=" + $('input[name="expert_name"]').val();
        query+= "&expert_udc=" + $('input[name="expert_udc"]').val();
        query+= "&expert_add_date=" + $('input[name="expert_year"]').val();
        // kill first, last and duplicated spaces, then replace '+' to escaped %2b and replace 'space' to '+'
        query+= "&expert_keywords=" + ($('input[name="expert_keywords"]').val()).fulltrim().replace(/\+/g,"%2B").replace(/ /g,'+');
        // query+= "&expert_abstract=" + $('input[name="expert_abstract"]').val();
        url = url_expert;
    }
    $("#articles_list").empty().load(url + query);
});
$("#button-reset-selection").on('click',function(){
    $('select[name="letter"]').val(0);
    $('select[name="topic"]').val(0);
    $('select[name="book"]').val(0);
    $('input[name="expert_name"]').val('');
    $('input[name="expert_udc"]').val('');
    $('input[name="expert_year"]').val('');
    $('input[name="expert_keywords"]').val('');
    $('input[name="expert_abstract"]').val('');
    setHashBySelectors(); // сброс хэша!
});
$("#button-show-all").on('click',function(){
    $("#articles_list").empty().load(url_extended);
});

$('#articles_list').on('click','.more_info',function(){
    location.href = '?fetch=articles&with=info&id='+$(this).attr('name')+siteLanguage;
});

// onload (disabled in HTML template)
// $('#expert_search').hide();

// extended/expert toggle
$("#button-toggle-search").on('click',function(){
    $(this).attr('data-searchmode', $(this).attr('data-searchmode') == 'expert' ? 'extended' : 'expert' );
    $(this).html( ($(this).attr('data-searchmode') == 'expert') ? '<<< LESS Search criteria' : 'MORE Search criteria >>>' );
    $('#extended_search').toggle();
    $('#expert_search').toggle();
});
/* articles__extended */