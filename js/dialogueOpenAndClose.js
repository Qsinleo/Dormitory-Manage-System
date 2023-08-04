function openDialog(openid) {
    hideDialog();
    document.getElementById("hide-screen").style.display = "block";
    document.getElementById("dialogue").style.display = document.getElementById(openid).style.display = "block";
}

function hideDialog() {
    for (const mod of document.getElementById("dialogue").children[1].children) {
        mod.style.display = "none";
    }
    document.getElementById("dialogue").style.display = document.getElementById("hide-screen").style.display = "none";
}
