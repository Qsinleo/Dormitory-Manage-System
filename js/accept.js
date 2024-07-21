function filtData() {
    function daysDifference(dateString) {
        // 自定义日期字符串解析函数
        function parseDateString(str) {
            const parts = str.split(/\s+/);
            const dateParts = parts[0].split('-');
            const timeParts = parts[1] ? parts[1].split(':') : [0, 0, 0];
            // 注意：JavaScript的月份是从0开始的，所以需要减1
            return new Date(dateParts[0], dateParts[1] - 1, dateParts[2], timeParts[0], timeParts[1], timeParts[2]);
        }
        // 解析传入的日期字符串
        const inputDate = parseDateString(dateString);
        // 获取当前时间
        const now = new Date();
        // 计算时间差（毫秒）
        const diff = Math.abs(now - inputDate);
        // 将时间差转换为天数并向上取整
        // 1天 = 1000毫秒 * 60秒 * 60分钟 * 24小时
        const daysDiff = Math.ceil(diff / (1000 * 60 * 60 * 24));
        return daysDiff;
    }
    let showed = document.querySelectorAll(".data-list tr").length;
    for (const iterator of document.querySelectorAll(".data-list tr")) {
        iterator.style.display = "";
        if ((iterator.querySelector(".id-label").innerText.indexOf(document.getElementById("id-restrict-input").value) == -1) ||
            (iterator.querySelector(".realname-label").innerText.indexOf(document.getElementById("realname-restrict-input").value) == -1) ||
            (iterator.querySelector(".user-id-label").innerText.indexOf(document.getElementById("user-id-restrict-input").value) == -1) ||
            (document.getElementById("type-restrict-select").value != "no-restrict" && iterator.querySelector(".type-label").innerText != document.getElementById("type-restrict-select").value) ||
            (document.getElementById("request-time-restrict-range-start").value && (daysDifference(iterator.querySelector(".request-time-label").innerText) < parseInt(document.getElementById("request-time-restrict-range-start").value) || daysDifference(iterator.querySelector(".request-time-label").innerText) > parseInt(document.getElementById("request-time-restrict-range-start").value) + parseInt(document.getElementById("request-time-restrict-range-length").value) - 1))) {
            iterator.style.display = "none";
            showed--;
        }
    }
    if (!showed) {
        document.querySelector(".no-matched-data-label").style.display = "block";
    } else {
        document.querySelector(".no-matched-data-label").style.display = "none";
    }
    document.getElementById("total-result-label").innerText = showed;
}

{
    for (const iterator2 of document.querySelector(".restrict-table").querySelectorAll("input, select")) {
        iterator2.addEventListener("change", filtData);
        iterator2.addEventListener("keyup", filtData);
    }
}

document.getElementById("clear-restrict-button").addEventListener("click", () => {
    for (const iterator of document.querySelector(".restrict-table").querySelectorAll("input")) {
        iterator.value = "";
    }
    for (const iterator of document.querySelector(".restrict-table").querySelectorAll("select")) {
        iterator.value = "no-restrict";
    }
    filtData();
});

filtData();