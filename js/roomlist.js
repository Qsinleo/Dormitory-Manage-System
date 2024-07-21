for (const iterator of document.getElementsByClassName("status-label")) {
    switch (iterator.innerText) {
        case "正常":
            iterator.style.backgroundColor = "green";
            break;
        case "正在打扫":
            iterator.style.backgroundColor = "purple";
            break;
        case "正在维修":
            iterator.style.backgroundColor = "blue";
            break;
        default:
            iterator.style.backgroundColor = "orangered";
            break;
    }
}

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
    if (!document.querySelectorAll(".data-list tr")) {
        document.querySelector(".no-matched-data-label").style.display = "block";
        return;
    }
    let showed = document.querySelectorAll(".data-list tr").length;
    for (const iterator of document.querySelectorAll(".data-list tr")) {
        iterator.style.display = "";
        if ((iterator.querySelector(".number-label").innerText.indexOf(document.getElementById("number-restrict-input").value) == -1) ||
            (document.getElementById("status-restrict-select").value != "no-restrict" && iterator.querySelector(".status-label").innerText != document.getElementById("status-restrict-select").value) ||
            (document.getElementById("last-update-restrict-range-start").value && (daysDifference(iterator.querySelector(".last-update-label").innerText) < parseInt(document.getElementById("last-update-restrict-range-start").value) || daysDifference(iterator.querySelector(".last-update-label").innerText) > parseInt(document.getElementById("last-update-restrict-range-start").value) + parseInt(document.getElementById("last-update-restrict-range-length").value) - 1)) ||
            (document.getElementById("added-time-restrict-range-start").value && (daysDifference(iterator.querySelector(".added-time-label").innerText) < parseInt(document.getElementById("added-time-restrict-range-start").value) || daysDifference(iterator.querySelector(".added-time-label").innerText) > parseInt(document.getElementById("added-time-restrict-range-start").value) + parseInt(document.getElementById("added-time-restrict-range-length").value) - 1))) {
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

function setRoomToLiveIn(number) {
    document.getElementById("chosen-room-number-label").innerText = document.getElementById("room-number-input").value = number;
    document.getElementById("submit-request-checkin").disabled = false;
}

filtData();

function calculateEndTime() {
    function getNowFormatDate(date) {
        year = date.getFullYear(), //获取完整的年份(4位)
            month = date.getMonth() + 1, //获取当前月份(0-11,0代表1月)
            strDate = date.getDate() // 获取当前日(1-31)
        if (month < 10) month = `0${month}` // 如果月份是个位数，在前面补0
        if (strDate < 10) strDate = `0${strDate}` // 如果日是个位数，在前面补0

        return `${year}-${month}-${strDate}`
    }
    if (document.getElementById("book-start-time-input").value && document.getElementById("book-during-time-input").value) {
        let newDate = new Date(document.getElementById("book-start-time-input").value);
        newDate.setDate(newDate.getDate() + parseInt(document.getElementById("book-during-time-input").value))
        document.getElementById("end-book-time-label").innerText = getNowFormatDate(newDate);
    } else {
        document.getElementById("end-book-time-label").innerText = "----/--/--";
    }

}
if (document.getElementById("book-start-time-input")) {
    document.getElementById("book-start-time-input").onchange =
        document.getElementById("book-during-time-input").onchange =
        document.getElementById("book-during-time-input").onkeyup = calculateEndTime;
    calculateEndTime();
    document.getElementById("submit-request-checkin").previousElementSibling.onclick = () => {
        document.getElementById("end-book-time-label").innerText = "----/--/--";
    }
}

document.getElementById("included-room-search-input").onkeyup =
    document.getElementById("included-room-search-input").onchange = () => {
        if (document.getElementById("included-room-search-input").value) {
            document.getElementById("included-rooms-searchbox").innerHTML = "<div><i>搜索中……</i></div>";
            sendRequest(`type=query-room-number&room-number=${encodeURIComponent(document.getElementById("included-room-search-input").value)}`, (res) => {
                document.getElementById("included-rooms-searchbox").innerHTML = "";
                if (JSON.parse(res).length > 0) {
                    for (const iterator of JSON.parse(res)) {
                        let obj = document.createElement("div");
                        obj.innerHTML = `<b>${iterator.number}</b>
                        <button onclick="transferResult('${iterator.number}',0)">新增区域➔</button>
                        <button onclick="transferResult('${iterator.number}',1)">更改区域➔</button>`;
                        document.getElementById("included-rooms-searchbox").appendChild(obj);
                    }
                } else {
                    document.getElementById("included-rooms-searchbox").innerHTML = "<div><i>无搜索结果</i></div>";
                }
            });
        } else {
            document.getElementById("included-rooms-searchbox").innerHTML = "<div><i>无搜索结果</i></div>";
        }

    }

function transferResult(number, area) {
    let obj = document.createElement("span");
    obj.ondblclick = () => {
        obj.remove();
    }
    obj.innerText = number;
    if (area == 0) {
        for (const iterator of document.getElementById("adding-included-room-list").children) {
            if (iterator.innerText == number) return;
        }
        document.getElementById("adding-included-room-list").append(obj);
    } else {
        for (const iterator of document.getElementById("modifying-included-room-list").children) {
            if (iterator.innerText == number) return;
        }
        document.getElementById("modifying-included-room-list").append(obj);
    }
}

function generateData(area) {
    let obj = [];
    if (area == 0) {
        for (const iterator of document.getElementById("adding-included-room-list").children) {
            obj.push(iterator.innerText);
        }
        document.getElementById("adding-area-includes-rooms-meta").value = obj.join(',');
    } else {
        for (const iterator of document.getElementById("modifying-included-room-list").children) {
            obj.push(iterator.innerText);
        }
        document.getElementById("modifying-area-includes-rooms-meta").value = obj.join(',');
        console.log(document.getElementById("modifying-area-includes-rooms-meta").value);
    }
    return true;
}

function modifyArea(name) {
    loadingShow(true);
    sendRequest(`type=query-area-includes&name=${name}`, (res) => {
        loadingShow(false);
        document.getElementById("modifying-area-origin-name").innerText = name;
        document.getElementById("modifying-included-room-list").innerHTML = "";
        document.getElementById("new-area-name-input").value = name;
        document.getElementById("origin-area-name-meta").value = name;
        if (JSON.parse(res).length > 0) {
            for (const iterator of JSON.parse(res)) {
                let obj = document.createElement("span");
                obj.ondblclick = () => {
                    obj.remove();
                }
                obj.innerText = iterator;
                document.getElementById("modifying-included-room-list").append(obj);
            }
        }
        document.getElementById("modify-area-button").disabled = false;
    })
}

document.getElementById("reset-modify-area-button").onclick = () => {
    if (document.getElementById("origin-area-name-meta").value) {
        modifyArea(document.getElementById("origin-area-name-meta").value);
    }
}

function modifyRoom(number) {
    loadingShow(true);
    sendRequest(`type=query-room-detail-info&number=${encodeURIComponent(number)}`, (res) => {
        var res = JSON.parse(res);
        for (const iterator of document.getElementsByClassName("modifying-room-number-meta")) {
            iterator.value = number;
        }
        document.getElementById("modifying-room-status-select").value = res.status;
        document.getElementById("modifying-room-location-input").value = res.location;
        document.getElementById("current-room-living-label").innerText = res.people.length;
        document.getElementById("max-room-living-label").innerText =
            document.getElementById("max-living-people-input").value = res.max;
        if (res.people.length > 0) {
            document.getElementById("room-living-people-list").innerHTML = "";
            for (const iterator of res.people) {
                let obj = document.createElement("div");
                obj.innerHTML = `${iterator.realname}<span class='lighter smaller'>(ID:${iterator.id})</span> <code>${iterator.fromdate}</code>➔<code>${iterator.todate}</code>
            <form action="process.php" method="post" class="inline">
                <input type="hidden" name="type" value="remove-living-person" />
                <input type="hidden" name="id" value="${iterator.id}" />
                <input type="submit" value="取消入住" class="dangerous" />
            </form>
            `;
                document.getElementById("room-living-people-list").append(obj);
            }
        } else {
            document.getElementById("room-living-people-list").innerHTML = "<div><i>没有居住人</i></div>";
        }
        if (res.usertype != "system-admin") {
            document.getElementById("delete-room-button").disabled = true;
        } else {
            document.getElementById("delete-room-button").display = false;
        }
        openPopup("房间设置");
        loadingShow(false);
    })
}

document.getElementById("refresh-room-info-button").addEventListener("click", () => {
    modifyRoom(document.getElementsByClassName("modifying-room-number-meta")[0].value);
});