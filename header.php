<?php

$con = mysqli_connect("72.44.78.124", "dms", password: "dms", database: "dms-data");
session_start();
if (!array_key_exists("loginid", $_SESSION)) {
    $_SESSION["loginid"] = null;
}
if (!array_key_exists("message", $_SESSION)) {
    $_SESSION["message"] = null;
}
if (!is_null($_SESSION["loginid"])) {
    $userinfo = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM `users` where id=" . $_SESSION["loginid"]));
    if ($userinfo["actived"] == 0) {
        $usertype = "inactived";
    } else {
        $usertype = $userinfo["accessment"];
    }
} else {
    $usertype = null;
}

if (!is_null($_SESSION["message"])) {
    echo <<<EOF
    <div id="message-bar-no-conflicting" style="opacity:1;">
    <div style='box-shadow:3px 3px 3px grey;
    padding:10px;
    margin:5px;
    border-left:7px cyan solid;
    border-radius:5px;
    display:inline-block;
    width:calc(100% - 85px);
    background-color:white;
    '>
    EOF; //CSS样式
    echo $_SESSION["message"];
    echo <<<EOF
    </div>
    <button style='
    display:inline-block;
    width:30px;
    height:42px;
    margin:5px;
    color:white;
    background-color:grey;
    border:none;
    border-radius:5px;
    font-size:xx-large;
    vertical-align:top;
    box-shadow:3px 3px 3px lightgrey;
    ' onclick='clearTimeout(fadeOutMessage);fadeOut(document.getElementById("message-bar-no-conflicting"),40);'>×</button>
    </div>
    <script>
    function fadeOut(element,text,speed){
        if(element.style.opacity !=0){
            var speed = speed || 30 ;
            var num = 10;
            var st = setInterval(function(){
                num--;
                element.style.opacity = num / 10 ;
                    if(num<=0)  {
                        clearInterval(st);
                        element.style.display = "none";
                    }
            },speed);
        }
    
    }
    var fadeOutMessage = setTimeout(function (){
        fadeOut(document.getElementById("message-bar-no-conflicting"),40)
    },5000);
    </script>
    EOF;
    unset($_SESSION["message"]);
}
