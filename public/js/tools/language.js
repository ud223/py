function setLanguage(language) {
    localStorage.setItem("language", language);

    location.reload();
}

$(document).ready(function() {
    var language = localStorage.getItem("language");

    if (language == "en") {
        $(".cn").hide();
        $(".en").show();
    }
    else {
        $(".en").hide();
        $(".cn").show();
    }
});