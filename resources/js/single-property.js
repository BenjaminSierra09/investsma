import { Swiper } from "swiper";
import { Navigation, Pagination, Zoom } from "swiper/modules";
import PhotoSwipeLightbox from "photoswipe/lightbox";
import PhotoSwipe from "photoswipe";
import L from "leaflet";

// Import Swiper styles
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import "swiper/css/zoom";

// Import PhotoSwipe styles
import "photoswipe/style.css";
import "leaflet/dist/leaflet.css";

const LAT =
    typeof window !== "undefined" && window.latitude != null
        ? window.latitude
        : 20.915283;
const LNG =
    typeof window !== "undefined" && window.longitude != null
        ? window.longitude
        : -100.74407;

document.addEventListener("DOMContentLoaded", function () {
    const galleryEl = document.querySelector("#property-gallery");
    if (!galleryEl) return;

    // Simple and effective lazy loading manager
    class LazyLoadManager {
        constructor() {
            this.observer = null;
            this.init();
        }

        init() {
            // Create intersection observer
            this.observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            this.loadImage(entry.target);
                        }
                    });
                },
                {
                    threshold: 0.1,
                    rootMargin: "200px 0px",
                }
            );
        }

        loadImage(img) {
            const src = img.dataset.src;
            if (!src) return;

            // Show loading indicator
            const preloader = img.parentElement.querySelector(
                ".swiper-lazy-preloader"
            );
            if (preloader) {
                preloader.style.display = "block";
            }

            // Create new image and load it
            const newImg = new Image();
            newImg.onload = () => {
                // Replace placeholder with actual image
                img.src = src;
                img.classList.add("loaded");

                // Hide loading indicator
                if (preloader) {
                    preloader.style.display = "none";
                }

                // Fade in effect
                img.style.opacity = "1";

                // Stop observing this image
                this.observer.unobserve(img);
            };

            newImg.onerror = () => {
                // Hide loading indicator on error
                if (preloader) {
                    preloader.style.display = "none";
                }

                // Add error class
                img.classList.add("error");
                this.observer.unobserve(img);
            };

            // Start loading
            newImg.src = src;
        }

        observe(img) {
            if (this.observer) {
                this.observer.observe(img);
            }
        }
    }

    // Initialize lazy loading manager
    const lazyLoadManager = new LazyLoadManager();

    // Hide loading overlay once DOM is loaded
    window.addEventListener("load", function () {
        const loadingOverlay = document.querySelector(".gallery-loading");
        if (loadingOverlay) {
            loadingOverlay.style.opacity = "0";
            setTimeout(() => {
                loadingOverlay.style.display = "none";
            }, 300);
        }
    });

    // Initialize main swiper
    const mainSwiper = new Swiper(".gallery-main", {
        modules: [Navigation, Pagination, Zoom],
        spaceBetween: 10,
        loop: false,
        preloadImages: false,
        lazy: true,
        zoom: {
            maxRatio: 3,
            minRatio: 1,
        },
        navigation: {
            nextEl: ".main-next",
            prevEl: ".main-prev",
        },
        pagination: {
            el: ".main-pagination",
            type: "bullets",
            clickable: true,
        },
        on: {
            init: function () {
                // Initialize lazy loading for main images
                const mainImages = document.querySelectorAll(
                    ".gallery-main img[data-src]"
                );
                mainImages.forEach((img) => {
                    lazyLoadManager.observe(img);
                });

                // Update counter
                updateImageCounter(this.realIndex);
            },
            slideChange: function () {
                updateImageCounter(this.realIndex);

                // Preload adjacent images
                const currentIndex = this.realIndex;
                const totalSlides = this.slides.length;

                // Preload next few images
                for (let i = 1; i <= 3; i++) {
                    const nextIndex = (currentIndex + i) % totalSlides;
                    const nextSlide = this.slides[nextIndex];
                    if (nextSlide) {
                        const nextImg =
                            nextSlide.querySelector("img[data-src]");
                        if (nextImg) {
                            lazyLoadManager.observe(nextImg);
                        }
                    }
                }
            },
        },
    });

    // Initialize PhotoSwipe lightbox
    const lightbox = new PhotoSwipeLightbox({
        gallery: "#property-gallery",
        children: ".pswp-gallery-item",
        showHideAnimationType: "zoom",
        initialZoomLevel: "fit",
        secondaryZoomLevel: 1.5,
        maxZoomLevel: 3,
        pswpModule: PhotoSwipe,
        padding: { top: 20, bottom: 40, left: 100, right: 100 },
        bgOpacity: 0.9,
    });

    // Add custom UI elements to PhotoSwipe
    lightbox.on("uiRegister", function () {
        lightbox.pswp.ui.registerElement({
            name: "download-button",
            order: 8,
            isButton: true,
            html: `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>`,
            onInit: (el, pswp) => {
                el.setAttribute("title", "Download image");
                el.onclick = () => {
                    const link = document.createElement("a");
                    link.href = pswp.currSlide.data.src;
                    link.download = pswp.currSlide.data.alt || "property-image";
                    link.click();
                };
            },
        });
    });

    lightbox.init();

    // Update image counter
    function updateImageCounter(index) {
        const counter = document.querySelector(".image-counter");
        const totalImages =
            Number(galleryEl.dataset.total || 0) ||
            document.querySelectorAll(".gallery-main .swiper-slide").length;
        if (counter) {
            const current = Math.min(index + 1, totalImages || index + 1);
            counter.textContent = `${current} / ${totalImages}`;
        }
    }

    // Fullscreen functionality
    document
        .querySelector(".fullscreen-btn")
        ?.addEventListener("click", function () {
            const currentSlide = mainSwiper.slides[mainSwiper.activeIndex];
            const link = currentSlide.querySelector(".pswp-gallery-item");
            if (link) {
                link.click();
            }
        });

    // Keyboard navigation
    document.addEventListener("keydown", function (e) {
        if (e.key === "ArrowLeft") {
            mainSwiper.slidePrev();
        } else if (e.key === "ArrowRight") {
            mainSwiper.slideNext();
        } else if (e.key === "Escape") {
            if (lightbox.pswp && lightbox.pswp.isOpen) {
                lightbox.pswp.close();
            }
        }
    });
});

// Leaflet map setup
function setupLeaflet() {
    const mapTarget = document.querySelector("#property-map");
    if (!mapTarget) return;

    const map = L.map(mapTarget, {
        center: [LAT, LNG],
        zoom: 14,
        zoomControl: true,
        scrollWheelZoom: true,
    });

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution:
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(map);

    const icon = L.icon({
        iconUrl: new URL(
            "leaflet/dist/images/marker-icon.png",
            import.meta.url
        ).toString(),
        iconRetinaUrl: new URL(
            "leaflet/dist/images/marker-icon-2x.png",
            import.meta.url
        ).toString(),
        shadowUrl: new URL(
            "leaflet/dist/images/marker-shadow.png",
            import.meta.url
        ).toString(),
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41],
    });

    L.marker([LAT, LNG], { icon }).addTo(map);
}

setupLeaflet();
