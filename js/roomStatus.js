for (const iterator of document.getElementsByClassName("room-status")) {
    switch (iterator.innerText) {
        case "occupied":
            iterator.style.color = "black";
            iterator.style.backgroundColor = "orange";
            break;
        case "empty":
            iterator.style.color = "black";
            iterator.style.backgroundColor = "green";
            break;
        case "cleaning":
            iterator.style.color = "white";
            iterator.style.backgroundColor = "purple";
            break;
        case "repairing":
            iterator.style.color = "white";
            iterator.style.backgroundColor = "blue";
            break;
        default:
            iterator.style.color = "white";
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
        document.getElementsByName("end-time")[0].value = document.getElementsByName("start-time")[0].value + 1;
    }
}
