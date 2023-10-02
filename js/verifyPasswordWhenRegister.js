
document.getElementsByName("password")[0].onkeyup = document.getElementById("password-retype").onkeyup = () => {
    verify();
}

function verify() {
    document.getElementById("submit").disabled = "disabled";
    document.getElementById("is-long").style.color = "initial";
    document.getElementById("is-same").style.color = "initial";
    if (document.getElementsByName("password")[0].value.length == 0) {
        document.getElementById("is-long").innerText = "";
    } else if (document.getElementById("password-retype").value != document.getElementsByName("password")[0].value) {
        document.getElementById("is-same").style.color = "red";
        document.getElementById("submit").disabled = "disabled";
        document.getElementById("is-same").innerText = "密码不同！";
    } else {
        document.getElementById("is-same").style.color = "green";
        document.getElementById("submit").disabled = "";
        document.getElementById("is-same").innerText = "√";
    }
    if (document.getElementsByName("password")[0].value.length == 0) {
        document.getElementById("is-long").innerText = "";
    } else if (document.getElementsByName("password")[0].value.length < 8) {
        document.getElementById("is-long").style.color = "red";
        document.getElementById("submit").disabled = "disabled";
        document.getElementById("is-long").innerText = "密码少于8位！";
    } else {
        document.getElementById("is-long").style.color = "green";
        document.getElementById("submit").disabled = "";
        document.getElementById("is-long").innerText = "√";
    }
}