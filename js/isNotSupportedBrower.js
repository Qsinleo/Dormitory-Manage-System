
function getBrowserVersion() {
    var userAgent = window.navigator.userAgent;

    // IE
    if (/MSIE|Trident/.test(userAgent)) {
        window.location.href = 'error_browser.html'; //遇到IE 就放弃
    }

    // Chrome
    if (/Chrome\/(\d+)/.test(userAgent)) {
        var chromeVersion = parseInt(RegExp.$1);
        var minimumChromeVersion = 70; // 设置最低支持的版本

        if (chromeVersion < minimumChromeVersion) {
            window.location.href = 'error_browser.html';
        }
    }

    // Firefox
    if (/Firefox\/(\d+)/.test(userAgent)) {
        var firefoxVersion = parseInt(RegExp.$1);
        var minimumFirefoxVersion = 60; // 设置最低支持的版本

        if (firefoxVersion < minimumFirefoxVersion) {
            window.location.href = 'error_browser.html';
        }
    }

    // Safari
    if (/Safari\/(\d+)/.test(userAgent)) {
        var safariVersion = parseInt(RegExp.$1);
        var minimumSafariVersion = 12; // 设置最低支持的版本
        if (safariVersion < minimumSafariVersion) {
            window.location.href = 'error_browser.html';
        }
    }

    // Edge
    if (/Edg\/(\d+)/.test(userAgent)) {
        var edgeVersion = parseInt(RegExp.$1);
        var minimumEdgeVersion = 80; // 设置最低支持的版本

        if (edgeVersion < minimumEdgeVersion) {
            window.location.href = 'error_browser.html';
        }
    }

    // Opera
    if (/OPR\/(\d+)/.test(userAgent)) {
        var operaVersion = parseInt(RegExp.$1);
        var minimumOperaVersion = 60; // 设置最低支持的版本

        if (operaVersion < minimumOperaVersion) {
            window.location.href = 'error_browser.html';
        }
    }
}

window.onload = function() {
    getBrowserVersion();

};