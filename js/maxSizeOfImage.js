document.getElementById("image").onchange = () => {
    let sizeOfFile = 1024 * 1024 * 5;
    const item = document.getElementById("image");
    if (item.files[0].size > sizeOfFile) {
        alert("文件大小不能超出5MB!");
        document.getElementById("dialogue").reset();
    } else if (item.files[0].type != "image/png" && files[0].type != "image/jpg" && files[0].type != "image/jpeg") {
        alert("文件类型必须是.png、.jpg和.jpeg！");
        document.getElementById("dialogue").reset();
    } else {
        document.getElementById("image").files = files;
    }
}


for (const each of document.getElementById("dialogue").getElementsByTagName("form")) {
    let reset = document.createElement("input");
    reset.type = "reset";
    let submit = document.createElement("input");
    submit.type = "submit";
    submit.value = "Go!(～￣▽￣)～"
    each.appendChild(reset);
    each.appendChild(submit);
}

hideDialog();