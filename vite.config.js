import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/property-gallery.css",
                "resources/js/app.js",
                "resources/js/listing-detail.js",
                "resources/js/properties-map.js",
                "resources/js/single-property.js",
                "resources/css/editorjs.css",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        cors: true,
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
});
