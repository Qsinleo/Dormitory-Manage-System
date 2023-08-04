const dropBox = document.querySelector("#drop");
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
    const files = e.dataTransfer.files;
    let sizeOfFile = 1024 * 1024 * 5;
    if (files[0].size > sizeOfFile) {
        alert("文件大小不能超出5MB!");
        document.getElementById("dialogue").reset();
    } else if (files[0].type != "image/png" && files[0].type != "image/jpg" && files[0].type != "image/jpeg") {
        alert("文件类型必须是.png、.jpg和.jpeg！");
        document.getElementById("dialogue").reset();
    } else {
        document.getElementById("image").files = files;
    }
}

document.addEventListener('drop', function (e) {
    e.preventDefault()
}, false)
document.addEventListener('dragover', function (e) {
    e.preventDefault()
}, false)