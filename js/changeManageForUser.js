var roomlist;

function remove(arr, item) {
    for (var i = arr.length - 1; i >= 0; i--) {
        if (arr[i] == item) {
            arr.splice(i, 1);
        }
    }
    return arr;
}

function delRoom(obj) {
    remove(roomlist, obj.previousSibling.data);
    obj.parentNode.parentNode.removeChild(obj.parentNode);
    document.getElementById("room-data").value = roomlist.join(",");
    document.getElementById("submit").disabled = "";
}

function querymanage() {
    document.getElementById("manage-parts").innerText = "加载中";
    document.getElementById("submit").disabled = "disabled";
    document.getElementById("room-number-add").disabled = "disabled";
    document.getElementById("manage-parts").style.color = "initial";
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
            document.getElementById("room-number-add").disabled = "";
            if (xmlhttp.responseText == "null") {
                roomlist = [];
                document.getElementById("room-data").value = [];
                document.getElementById("manage-parts").innerHTML = "<i>无管理</i>";
            } else {
                roomlist = xmlhttp.responseText.slice(0, xmlhttp.responseText.length - 1).split(",");
                document.getElementById("room-data").value = roomlist.join(",");
                document.getElementById("manage-parts").innerHTML = "";
                for (const iterator of roomlist) {
                    let newRoom = document.createElement("li");
                    newRoom.innerHTML = iterator + "<button onclick='delRoom(this);' type='button'>删除</button>";
                    document.getElementById("manage-parts").appendChild(newRoom);
                }
            }
        }
    }
    xmlhttp.open("POST", "proceed.php?type=querymanage&id=" + document.getElementsByName("setid")[0].value, true);
    xmlhttp.send();
}

document.getElementById("room-number-add").onkeydown = () => {
    document.getElementById("add-room").disabled = "disabled";
    document.getElementById("room-info").style.color = "initial";
    document.getElementById("room-info").innerText = "等待失焦……";
}

document.getElementById("room-number-add").onblur = () => {
    document.getElementById("room-info").innerText = "加载中";
    document.getElementById("add-room").disabled = "disabled";
    document.getElementById("room-info").style.color = "initial";
    if (document.getElementById("room-number-add").value.length == 0) {
        document.getElementById("room-info").innerText = "房间不能为空！";
        document.getElementById("room-info").style.color = "red";
    } else if (roomlist.indexOf(document.getElementById("room-number-add").value) != -1) {
        document.getElementById("room-info").innerText = "房间重复或不存在！";
        document.getElementById("room-info").style.color = "red";
    } else {
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
                if (xmlhttp.responseText == "true") {
                    document.getElementById("room-info").innerText = "房间存在";
                    document.getElementById("room-info").style.color = "green";
                    document.getElementById("add-room").disabled = "";
                } else {
                    document.getElementById("room-info").innerText = "房间重复或不存在！";
                    document.getElementById("room-info").style.color = "red";
                }
            }
        }
        xmlhttp.open("POST", "proceed.php?type=queryroom&roomnumber=" + document.getElementById("room-number-add").value, true);
        xmlhttp.send();
    }
}

function addRoom() {
    if (document.getElementById("manage-parts").innerText == "无管理") {
        document.getElementById("manage-parts").innerText = "";
    }
    let newRoom = document.createElement("li");
    newRoom.innerHTML = document.getElementById("room-number-add").value + "<button onclick='delRoom(this);' type='button'>删除</button>"
    document.getElementById("manage-parts").appendChild(newRoom);
    roomlist.push(document.getElementById("room-number-add").value);
    document.getElementById("room-data").value = roomlist.join(",");
    console.log(document.getElementById("room-data").value);
    document.getElementById("add-room").disabled = "disabled";
    document.getElementById("submit").disabled = "";
}