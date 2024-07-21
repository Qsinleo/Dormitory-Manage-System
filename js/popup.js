
function openPopup(type) {
    for (const iterator of document.getElementById("popup-content").children) {
        iterator.style.display = "none";
        if (iterator.dataset.type == type) {
            iterator.style.display = "";
        }
    }
    document.getElementById("popup-bg").style.display = "block";
    document.getElementById("popup-title").innerText = type;
}

function closePopup() {
    document.getElementById("popup-bg").style.display = "none";
}


for (const iterator of document.getElementsByClassName("form-controller")) {
    const reseter = document.createElement("input");
    reseter.type = "reset";
    const submitter = document.createElement("input");
    submitter.type = "submit";
    submitter.value = "提交 ➔";
    if (iterator.dataset.needreset != "false") {
        iterator.append(reseter);
    }
    iterator.append(submitter);
}
