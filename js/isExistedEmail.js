document.getElementsByName("email")[0].onkeyup = () => {
    document.getElementById("submit").disabled = "disabled";
    document.getElementById("is-existed").style.color = "initial";
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
        document.getElementById("is-existed").innerText = "加载中";
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            if (xmlhttp.responseText == "true") {
                document.getElementById("is-existed").innerText = "邮箱可用";
                document.getElementById("is-existed").style.color = "green";
                document.getElementById("submit").disabled = "";
            } else {
                console.log(xmlhttp.responseText);
                document.getElementById("is-existed").innerText = "邮箱已存在！";
                document.getElementById("is-existed").style.color = "red";
            }
        }
    }
    xmlhttp.open("POST", "proceed.php?type=queryemail&email=" + document.getElementsByName("email")[0].value, true);
    xmlhttp.send();
}