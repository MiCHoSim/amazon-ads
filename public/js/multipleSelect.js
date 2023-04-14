$(document).ready(function(){
    $(".mul-select").select2({
        placeholder: "select", //placeholder
        tags: true,
        tokenSeparators: ['/',',',';'," "]
    });
})