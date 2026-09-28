document.addEventListener('error', function (event) {
    const image = event.target;

    if (!(image instanceof HTMLImageElement) || !image.dataset.fallbackImage) {
        return;
    }

    const fallback = image.dataset.fallbackImage;
    if (image.src !== fallback) {
        image.src = fallback;
    }
}, true);

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('img[data-fallback-image]').forEach(function (image) {
        if (image.complete && image.naturalWidth === 0) {
            image.src = image.dataset.fallbackImage;
        }
    });
});