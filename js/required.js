for (let i of document.getElementsByTagName("input")) {
    if (i.required) {
        i.parentNode.previousElementSibling.innerHTML = "<span style='color:red;'>*</span>" + i.parentNode.previousElementSibling.innerHTML;
    }
}