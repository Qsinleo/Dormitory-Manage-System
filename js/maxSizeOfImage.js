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

hideDialog();