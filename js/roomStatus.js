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