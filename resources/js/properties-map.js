import L from "leaflet";
import "leaflet/dist/leaflet.css";

const defaultCenter = [20.91445, -100.74412];

const currencyFormatter = new Intl.NumberFormat("es-MX", {
    maximumFractionDigits: 0,
});

const escapeHtml = (value) =>
    String(value ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");

const formatPrice = (property) => {
    if (!property.price) {
        return "Precio a consultar";
    }

    return `${property.currency ?? "USD"} $${currencyFormatter.format(property.price)}`;
};

const formatMeta = (property) => {
    const parts = [];

    if (property.bedrooms) {
        parts.push(`${property.bedrooms} recámaras`);
    }

    if (property.bathrooms) {
        parts.push(`${property.bathrooms} baños`);
    }

    if (property.construction_meters) {
        parts.push(`${property.construction_meters} m²`);
    }

    return parts;
};

const popupMarkup = (property) => {
    const location = [property.neighborhood, property.city].filter(Boolean).join(" · ");
    const image = property.image
        ? `<div class="h-32 overflow-hidden rounded-2xl bg-zinc-100"><img src="${escapeHtml(property.image)}" alt="${escapeHtml(property.name)}" class="h-full w-full object-cover"></div>`
        : "";

    return `
        <a href="${escapeHtml(property.detail_url)}" class="block w-[280px] space-y-3 rounded-3xl bg-white p-1 text-zinc-900">
            ${image}
            <div class="space-y-2 px-1 pb-1">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="truncate text-base font-semibold">${escapeHtml(property.name)}</p>
                        <p class="text-xs text-zinc-500">${escapeHtml(location || "San Miguel de Allende")}</p>
                    </div>
                    ${
                        property.status
                            ? `<span class="shrink-0 rounded-full bg-amber-100 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-amber-700">${escapeHtml(property.status)}</span>`
                            : ""
                    }
                </div>
                <p class="text-sm font-bold text-amber-700">${escapeHtml(formatPrice(property))}</p>
                <div class="flex flex-wrap gap-2 text-xs text-zinc-600">
                    ${formatMeta(property)
                        .map(
                            (item) =>
                                `<span class="rounded-full bg-zinc-100 px-2 py-1">${escapeHtml(item)}</span>`,
                        )
                        .join("")}
                </div>
                <span class="inline-flex items-center gap-2 text-sm font-semibold text-amber-700">
                    Ver más información
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            </div>
        </a>
    `;
};

document.addEventListener("DOMContentLoaded", () => {
    const mapElement = document.querySelector("#properties-map");
    if (!mapElement) {
        return;
    }

    const properties = Array.isArray(window.propertiesMapData)
        ? window.propertiesMapData
        : [];

    const map = L.map(mapElement, {
        center: defaultCenter,
        zoom: 13,
        scrollWheelZoom: true,
    });

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution:
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(map);

    const icon = L.icon({
        iconUrl: new URL("leaflet/dist/images/marker-icon.png", import.meta.url).toString(),
        iconRetinaUrl: new URL(
            "leaflet/dist/images/marker-icon-2x.png",
            import.meta.url,
        ).toString(),
        shadowUrl: new URL("leaflet/dist/images/marker-shadow.png", import.meta.url).toString(),
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41],
    });

    if (!properties.length) {
        return;
    }

    const bounds = L.latLngBounds([]);

    properties.forEach((property) => {
        const marker = L.marker([property.latitude, property.longitude], { icon }).addTo(map);

        marker.bindPopup(popupMarkup(property), {
            className: "properties-map-popup",
            closeButton: true,
            maxWidth: 320,
        });

        bounds.extend([property.latitude, property.longitude]);
    });

    if (bounds.isValid()) {
        map.fitBounds(bounds, { padding: [40, 40] });
    }
});
