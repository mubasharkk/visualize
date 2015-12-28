

$(document).ready(function(){
    $('#flow-map-source').change(function(e){
        console.log($(this).val());
        var form = $('#flow-map-form');
        $('input[type=text]',form).removeAttr('disabled');
        $('input#state-'+$(this).val(),form).attr('disabled','disabled');
    }).change();    
    
    $('#states').change(function(e) {
        var form = $(this).parents("form");
        
        $.post(SITE_URL + 'home/test/', $(form).serialize(), function(response){
            console.log(response);
        });
    });    
    
    $("table").tablesorter({debug: false});
    
    visualize.setUSAMap('flowmap');    
});