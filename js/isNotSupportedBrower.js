// 获取用户代理字符串
var userAgent = navigator.userAgent;

// 判断浏览器版本
function getBrowserVersion() {
  var version = null;

  if (/MSIE (\d+\.\d+);/.test(userAgent)) {
    // 匹配IE浏览器
    version = parseFloat(RegExp.$1);
  } else if (/Firefox\/([\d.]+)/.test(userAgent)) {
    // 匹配Firefox浏览器
    version = parseFloat(RegExp.$1);
  } else if (/Chrome\/([\d.]+)/.test(userAgent)) {
    // 匹配Chrome浏览器
    version = parseFloat(RegExp.$1);
  } else if (/Version\/([\d.]+).*Safari/.test(userAgent)) {
    // 匹配Safari浏览器
    version = parseFloat(RegExp.$1);
  } else if (/Opera\/([\d.]+)/.test(userAgent)) {
    // 匹配Opera浏览器
    version = parseFloat(RegExp.$1);
  }
  return version;
}
// 判断浏览器内核
function getBrowserEngine() {
  var engine = null;

  if (/Trident\/([\d.]+)/.test(userAgent)) {
    // 匹配Trident内核（IE浏览器）
    engine = "Trident";
  } else if (/Gecko\/([\d.]+)/.test(userAgent)) {
    // 匹配Gecko内核（Firefox浏览器）
    engine = "Gecko";
  } else if (/AppleWebKit\/([\d.]+)/.test(userAgent)) {
    // 匹配Webkit内核（Chrome、Safari浏览器）
    engine = "Webkit";
  } else if (/Presto\/([\d.]+)/.test(userAgent)) {
    // 匹配Presto内核（Opera浏览器）
    engine = "Presto";
  }
  return engine;
}

// 测试代码
var browserVersion = getBrowserVersion();
var browserEngine = getBrowserEngine();

if (
  (browserEngine == "Trident" && browserVersion < 9) ||
  (browserEngine == "Gecko" && browserVersion < 3.5) ||
  (browserEngine == "Webkit" && browserEngine < 3) ||
  (browserEngine == "Presto" && browserVersion < 10)
) {
    document.getElementById("notSupportedBrowerAlert").style.display="";
}

// console.log("浏览器版本: " + browserVersion);
// console.log("浏览器内核: " + browserEngine);

var buttonCanel=document.getElementById("NSBA-canel");
// console.log(buttonCanel);
buttonCanel.onclick=() => {
  document.getElementById("notSupportedBrowerAlert").style.display="none";
}