<style>
    @keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            transform: rotate(0deg);
        }

        100% {
            -webkit-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }

    #loading-info {
        display: none;
        user-select: none;
        z-index: 500;
        text-align: center;
        position: fixed;
        background-color: rgba(128, 128, 128, 0.2);
        left: 0;
        top: 0;
        right: 0;
        bottom: 0;
        width: 100vw;
        height: 100vh;
    }

    #loading-info>div {
        position: fixed;
        right: 40px;
        bottom: 30px;
        border-radius: 5px;
        padding: 10px;
        width: fit-content;
        height: fit-content;
    }

    #loading-info>div>div:first-child {
        display: inline-block;
        width: 80px;
        height: 80px;
        margin: auto;
        vertical-align: middle;
        background: transparent;
        border-top: 8px solid #009688;
        border-right: 8px solid transparent;
        border-radius: 50%;
        -webkit-animation: 1s spin linear infinite;
        animation: 1s spin linear infinite;
    }

    #loading-info>div>div:nth-child(2) {
        display: inline-block;
        margin-left: 15px;
        font-size: 36px;

    }
</style>

<div id="loading-info">
    <div>
        <div></div>
        <div>
            <b>加载中</b>
        </div>
    </div>
</div>