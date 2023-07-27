for (let i of document.getElementsByTagName("input")) {
    if (i.required) {
        i.parentNode.previousElementSibling.innerHTML = "<span class='required'>*</span>" + i.parentNode.previousElementSibling.innerHTML;
    }
}