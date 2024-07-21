
{
    const submitter = document.getElementById("change-email-form").querySelector("input[type=submit]");
    document.getElementById("email-input").onkeyup = () => {
        submitter.disabled = true;
        if (document.getElementById("email-input").value) {
            document.getElementById("email-is-existed").style.color = "";
            document.getElementById("email-is-existed").innerText = "🕒加载中";
            sendRequest(`type=query-email&email=${encodeURIComponent(document.getElementById("email-input").value)}`, (res) => {
                if (res == "true") {
                    document.getElementById("email-is-existed").innerText = "✅邮箱可用";
                    document.getElementById("email-is-existed").style.color = "green";
                    submitter.disabled = false;
                } else {
                    console.log(res);
                    document.getElementById("email-is-existed").innerText = "❌邮箱已存在！";
                    document.getElementById("email-is-existed").style.color = "red";
                }
            });
        } else {
            document.getElementById("email-is-existed").innerText = "❌邮箱不能为空！";
            document.getElementById("email-is-existed").style.color = "red";
        }
    }

    document.getElementById("change-email-form").querySelector("input[type=reset]").onclick = () => {
        submitter.disabled = true;
        document.getElementById("email-is-existed").innerText = "❌邮箱已存在！";
        document.getElementById("email-is-existed").style.color = "red";
    }
}
{

    const dropBox = document.querySelector("#drop-image-box");
    dropBox.addEventListener("dragenter", dragEnter, false);
    dropBox.addEventListener("dragover", dragOver, false);
    dropBox.addEventListener("drop", drop, false);

    function dragEnter(e) {
        e.stopPropagation();
        e.preventDefault();
    }

    function dragOver(e) {
        e.stopPropagation();
        e.preventDefault();
    }

    function drop(e) {
        // 当文件拖拽到dropBox区域时,可以在该事件取到files
        verify(e.dataTransfer.files);
    }

    document.addEventListener('drop', function (e) {
        e.preventDefault();
    }, false);
    document.addEventListener('dragover', function (e) {
        e.preventDefault();
    }, false);

    document.getElementById("upload-image-input").onchange = () => {
        verify(document.getElementById("upload-image-input").files);
    }

    document.getElementById("change-header-form").querySelector("input[type=reset]").onclick = () => {
        document.getElementById("uploaded-image-status-label").innerText = "等待上传……";
        document.getElementById("change-header-form").querySelector("input[type=submit]").disabled = false;
    }

    function verify(files) {
        if (files) {
            let sizeOfFile = 1024 * 1024;
            if (files[0].size > sizeOfFile) {
                document.getElementById("uploaded-image-status-label").innerText = "❌文件大小不能超出1MB!";
            } else if (files[0].type != "image/png" && files[0].type != "image/jpg" && files[0].type != "image/jpeg") {
                document.getElementById("uploaded-image-status-label").innerText = "❌文件类型必须是.png、.jpg或.jpeg！";
            } else {
                document.getElementById("uploaded-image-status-label").innerText = "✔️文件可以上传";
                document.getElementById("change-header-form").querySelector("input[type=submit]").disabled = false;
                document.getElementById("upload-image-input").files = files;
                return;
            }
            document.getElementById("change-header-form").querySelector("input[type=submit]").disabled = true;
        } else {
            document.getElementById("uploaded-image-status-label").innerText = "等待上传……";
            document.getElementById("change-header-form").querySelector("input[type=submit]").disabled = false;
        }
    }
}

{
    const obj = document.getElementById("change-password-form").querySelector("input[type=submit]");
    document.getElementById("new-password-input").onkeyup =
        document.getElementById("new-password-retype").onkeyup = () => {
            if (document.getElementById("new-password-input").value.length < 8) {
                document.getElementById("new-password-info").innerText = "❌密码少于8位";
            } else if (document.getElementById("new-password-input").value != document.getElementById("new-password-retype").value) {
                document.getElementById("new-password-info").innerText = "❌密码不一致";
            } else {
                obj.disabled = false;
                document.getElementById("new-password-info").innerText = "✔️密码一致";
                return;
            }
            obj.disabled = true;
        }
    document.getElementById("change-password-form").querySelector("input[type=reset]").onclick = () => {
        obj.disabled = false;
        document.getElementById("new-password-info").innerText = "";
    }
}

function getUserManage() {
    if (document.getElementById("area-name-select")) {
        loadingShow(true);
        sendRequest(`type=query-user-info&id=${document.getElementById("change-id-meta").value}`, (res) => {
            var res = JSON.parse(res);
            document.getElementById("area-name-select").innerHTML = "";
            let obj = document.createElement("option");
            obj.value = "[null]";
            obj.innerText = `(无管理)`;
            document.getElementById("area-name-select").dataset.origin = res.managepart === null ? "[null]" : res.managepart;
            document.getElementById("area-name-select").append(obj);
            for (const iterator of res.allparts) {
                obj = document.createElement("option");
                obj.value = iterator.name;
                obj.innerText = `${iterator.name} (房间数${iterator.length == 0 ? 0 : iterator.includes.split(',').length})`;
                document.getElementById("area-name-select").append(obj);
            }
            document.getElementById("area-name-select").value = res.managepart === null ? "[null]" : res.managepart;
            loadingShow(false);
            openPopup("更改管理");
        });
    } else {
        openPopup("更改管理");
    }
}
if (document.getElementById("reload-user-manage-change")) {
    document.getElementById("reload-user-manage-change").addEventListener("click", () => {
        getUserManage();
    });
}
if (document.getElementById("area-name-select")) {
    document.getElementById("area-name-select").addEventListener("change", () => {
        if (document.getElementById("area-name-select").value == document.getElementById("area-name-select").dataset.origin) {
            document.getElementById("submit-user-manage-change").disabled = true;
        } else {
            document.getElementById("submit-user-manage-change").disabled = false;
        }
    })
}
