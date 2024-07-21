for (const iterator of document.querySelectorAll("input[type=number]")) {
    function n() {
        if (iterator.value && iterator.max && parseInt(iterator.value) > parseInt(iterator.max)) iterator.value = iterator.max;
        if (iterator.value && iterator.min && parseInt(iterator.value) < parseInt(iterator.min)) iterator.value = iterator.min;
        if (calculateEndTime) calculateEndTime();
    }
    iterator.addEventListener("change", n);
    iterator.addEventListener("keyup", n);
}

for (const iterator of document.querySelectorAll("input[type=date]")) {
    function n() {
        // 转换为Date对象（如果value或min是空的或无效的，则返回Invalid Date）
        if (iterator.getAttribute('min')) {
            // 比较日期
            if (new Date(iterator.value) < new Date(iterator.getAttribute('min'))) {
                iterator.value = iterator.getAttribute('min');
            }
        }
        if (iterator.getAttribute('max')) {
            // 比较日期
            if (new Date(iterator.value) > new Date(iterator.getAttribute('max'))) {
                iterator.value = iterator.getAttribute('max');
            }
        }
    }
    iterator.addEventListener("change", n);
    iterator.addEventListener("keyup", n);
}