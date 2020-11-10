// ZOOM BROWSER
// Monitoring screen resolution or zoom level changes
// https://developer.mozilla.org/en-US/docs/Web/API/Window/devicePixelRatio

let zoomBox = document.querySelector(".zoom-browser");
let pixelRatioBox = document.querySelector(".pixel-ratio");

let mqString = `(resolution: ${window.devicePixelRatio}dppx)`;

const updatePixelRatio = () => {
    // let zoom = Math.round(window.devicePixelRatio * 100);
    // let zoom = window.outerWidth / window.document.documentElement.clientWidth;
    // let zoom = window.visualViewport.scale;
    // let zoomString = Math.round(window.devicePixelRatio * 100);

    // alert('availWidth:' + window.screen.availWidth);
    // alert('availHeight:' + window.screen.availHeight);
    // 1094x575

    // alert('width:' + window.screen.width);
    // alert('height:' + window.screen.height);
    // 1094x615

    let pr = window.devicePixelRatio;
    let prString = (pr * 100).toFixed(0);
    pixelRatioBox.innerText = `Pixel ratio: ${prString}% (${pr.toFixed(2)})`;

    // if (window.screen.width == 1094 & window.screen.height == 615 & window.devicePixelRatio == 1.25) {
    // if (window.screen.width == 1280 & window.screen.height == 720 & window.devicePixelRatio == 1.5) {
    if (window.devicePixelRatio == 1.25) {
        document.body.setAttribute("id", "zoomBrowser");
        zoomBox.innerText = `Zoom browser: 67%`;
    } else {
        document.body.removeAttribute("id");
        zoomBox.innerText = `Zoom browser: 100%`;
    }
}
updatePixelRatio();
matchMedia(mqString).addListener(updatePixelRatio);
