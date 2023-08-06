document.getElementById("new-password-retype").onkeyup = () => {
    const obj = document.getElementsByName("new-password")[0].parentNode.parentNode.parentNode.parentNode.parentNode.querySelector("input[type=\"submit\"]");
    obj.disabled = "disabled";
    obj.previousElementSibling.onclick = () => {
        obj.disabled = "";
        document.getElementById("password-info").innerText =
            document.getElementById("password-info2").innerText =
            document.getElementById("password-info3").innerText = ""
    }
    document.getElementById("password-info2").style.color = "initial";
    if (document.getElementsByName("new-password")[0].value != document.getElementById("new-password-retype").value) {
        document.getElementById("password-info2").style.color = "red";
        obj.disabled = "disabled";
        document.getElementById("password-info2").innerText = "密码不一致";
    } else if (document.getElementsByName("new-password")[0].value.length < 8) {
        document.getElementById("password-info2").style.color = "red";
        obj.disabled = "disabled";
        document.getElementById("password-info2").innerText = "密码少于8位";
    } else {
        document.getElementById("password-info2").style.color = "green";
        obj.disabled = "";
        document.getElementById("password-info2").innerText = "密码一致";
    }
}


document.getElementsByName("new-password")[0].onkeyup = () => {
    const obj = document.getElementsByName("new-password")[0].parentNode.parentNode.parentNode.parentNode.parentNode.querySelector("input[type=\"submit\"]");
    obj.disabled = "disabled";
    obj.previousElementSibling.onclick = () => {
        obj.disabled = "";
        document.getElementById("password-info").innerText =
            document.getElementById("password-info2").innerText =
            document.getElementById("password-info3").innerText = ""
    }
    document.getElementById("password-info3").style.color = "initial";
    if (sha1(document.getElementsByName("new-password")[0].value) == document.getElementById("old-password").getAttribute("key")) {
        document.getElementById("password-info3").style.color = "red";
        obj.disabled = "disabled";
        document.getElementById("password-info3").innerText = "新密码不能与旧密码相同";
    } else {
        document.getElementById("password-info3").style.color = "green";
        obj.disabled = "";
        document.getElementById("password-info3").innerText = "密码可用";
    }
}