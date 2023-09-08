const switcher=() => {
    document.body.classList.toggle("dark");
    if(body.classList.contains('dark')){
        modeText.innerText="Dark mode"
    }else{
        modeText.innerText="Light mode"
    }
};
const media = window.matchMedia("(prefers-color-scheme:dark)");

// 判断是否为暗主题
if (media.matchs) {
  // 匹配到暗主题
  switcher();
} else {
  // 没有匹配到暗主题
  switcher();
}

// 上面操作只会在页面加载时才会生效，因此，需要给media添加事件监听器
media.addEventListener("change", (e) => {
  if (e.matches) {
    switcher();
  } else {
    switcher();
  }
});
