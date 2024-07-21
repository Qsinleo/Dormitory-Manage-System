for (let index = 0; index < document.getElementsByClassName("tabs").length; index++) {
    const element = document.getElementsByClassName("tabs")[index];
    element.addEventListener("click", () => {
        for (let index2 = 0; index2 < document.getElementsByClassName("tabs").length; index2++) {
            if (index2 != index && document.getElementsByClassName("tabs")[index2].classList.contains("active")) {
                document.getElementsByClassName("tabs")[index2].classList.add("inactive");
                document.getElementsByClassName("tabs")[index2].classList.remove("active");
            }
        }
        if (document.getElementsByClassName("tabs")[index].classList.contains("inactive")) {
            document.getElementsByClassName("tabs")[index].classList.add("active");
            document.getElementsByClassName("tabs")[index].classList.remove("inactive");
        }
        for (const iterator of document.getElementById("main-form-container").children) {
            iterator.style.display = "none";
        }
        document.getElementById("main-form-container").children[index].style.display = "block";
    });
}

document.getElementById("email-input").onkeyup = () => {
    document.getElementById("register-submit").disabled = true;
    if (document.getElementById("email-input").value) {
        document.getElementById("email-is-existed").innerText = "🕒加载中";
        sendRequest(`type=query-email&email=${encodeURIComponent(document.getElementById("email-input").value)}`, (res) => {
            if (res == "true") {
                document.getElementById("email-is-existed").innerText = "✔️邮箱可用";
                document.getElementById("register-submit").disabled = false;
            } else {
                console.log(res);
                document.getElementById("email-is-existed").innerText = "❌邮箱已存在！";
            }
        });
    } else {
        document.getElementById("email-is-existed").innerText = "❌邮箱不能为空！";
    }
}

document.getElementById("register-password").onkeyup = document.getElementById("password-retype").onkeyup = () => {
    if (document.getElementById("register-password").value.length == 0) {
        document.getElementById("password-info").innerText = "";
    } else if (document.getElementById("register-password").value.length < 8) {
        document.getElementById("password-info").innerText = "❌密码少于8位！";
    } else if (document.getElementById("password-retype").value != document.getElementById("register-password").value) {
        document.getElementById("password-info").innerText = "❌密码不同！";
    } else {
        document.getElementById("register-submit").disabled = false;
        document.getElementById("password-info").innerText = "✔️密码验证完毕";
        return;
    }
    document.getElementById("register-submit").disabled = true;
}
