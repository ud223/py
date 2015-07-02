function setLanguage(language) {
    localStorage.setItem("language", language);

    location.reload();
}


// JavaScript Document
function loadjscssfile(filename,filetype){

    if(filetype == "js"){
        var fileref = document.createElement('script');
        fileref.setAttribute("type","text/javascript");
        fileref.setAttribute("src",filename);
    }else if(filetype == "css"){

        var fileref = document.createElement('link');
        fileref.setAttribute("rel","stylesheet");
        fileref.setAttribute("type","text/css");
        fileref.setAttribute("href",filename);
    }
    if(typeof fileref != "undefined"){
        document.getElementsByTagName("head")[0].appendChild(fileref);
    }

}

$(document).ready(function() {
    var language = localStorage.getItem("language");

    if (language == "en") {
        $(".cn").hide();
        $(".en").show();
        $(".en-select").addClass("selected");

        loadjscssfile("/css/py_global_en.css","css");
    }
    else {
        $(".en").hide();
        $(".cn").show();
        $(".cn-select").addClass("selected");
    }
});