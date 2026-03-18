document.addEventListener("DOMContentLoaded", () => {
    const gallery = document.querySelector("[data-listing-gallery]");

    if (!gallery) {
        return;
    }

    const mainImage = gallery.querySelector("[data-gallery-main]");
    const thumbnails = Array.from(
        gallery.querySelectorAll("[data-gallery-thumb]"),
    );
    const prevButton = gallery.querySelector("[data-gallery-prev]");
    const nextButton = gallery.querySelector("[data-gallery-next]");

    if (!mainImage || thumbnails.length === 0) {
        return;
    }

    let activeIndex = thumbnails.findIndex(
        (thumbnail) => thumbnail.dataset.active === "true",
    );

    if (activeIndex < 0) {
        activeIndex = 0;
    }

    const setActiveImage = (index) => {
        const safeIndex =
            ((index % thumbnails.length) + thumbnails.length) % thumbnails.length;
        activeIndex = safeIndex;

        const activeThumbnail = thumbnails[safeIndex];
        const nextSource = activeThumbnail.dataset.gallerySrc;

        if (nextSource) {
            mainImage.src = nextSource;
        }

        thumbnails.forEach((thumbnail, thumbnailIndex) => {
            thumbnail.dataset.active =
                thumbnailIndex === safeIndex ? "true" : "false";
        });
    };

    thumbnails.forEach((thumbnail, index) => {
        thumbnail.addEventListener("click", () => {
            setActiveImage(index);
        });
    });

    prevButton?.addEventListener("click", () => {
        setActiveImage(activeIndex - 1);
    });

    nextButton?.addEventListener("click", () => {
        setActiveImage(activeIndex + 1);
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "ArrowLeft") {
            setActiveImage(activeIndex - 1);
        }

        if (event.key === "ArrowRight") {
            setActiveImage(activeIndex + 1);
        }
    });
});
