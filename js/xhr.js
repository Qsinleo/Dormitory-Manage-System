function sendRequest(params, returnFunc) {
    var xmlhttp;
    if (window.XMLHttpRequest) {
        //  IE7+, Firefox, Chrome, Opera, Safari 浏览器执行代码
        xmlhttp = new XMLHttpRequest();
    }
    else {
        // IE6, IE5 浏览器执行代码
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            if (typeof returnFunc == "function") {
                returnFunc(xmlhttp.responseText);
            }
        }
    }
    xmlhttp.open("POST", `process.php?${params}`, true);
    xmlhttp.send();
}

function loadingShow(method) {
    document.getElementById("loading-info").style.display = method ? "block" : "none";
}