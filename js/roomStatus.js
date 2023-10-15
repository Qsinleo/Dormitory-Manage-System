for (const iterator of document.getElementsByClassName("room-status")) {
    switch (iterator.innerText) {
        case "正常":
            iterator.style.backgroundColor = "green";
            break;
        case "正在打扫":
            iterator.style.backgroundColor = "purple";
            break;
        case "正在修复":
            iterator.style.backgroundColor = "blue";
            break;
        default:
            iterator.style.backgroundColor = "red";
            break;
    }
}

function setRoom(number) {
    document.getElementById("roomnum").innerText = document.getElementsByName("room-num")[0].value = number;
    document.getElementById("submit-room").disabled = "";
}

document.getElementsByName("start-time")[0].onchange = document.getElementsByName("end-time")[0].onchange = () => {
    if (new Date(document.getElementsByName("end-time")[0].value) - new Date(document.getElementsByName("start-time")[0].value) < 1) {
        document.getElementsByName("end-time")[0].value = "";
        alert("日期不符合要求：开始日期不能晚于结束日期！");
    }
}
