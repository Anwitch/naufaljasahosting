/**
 * map.js
 * Tanggung Jawab: Inisialisasi peta Leaflet dasar dan base layer.
 */

export const initMap = (containerId) => {
    // Pusat peta default (Universitas Tanjungpura, Pontianak)
    const map = L.map(containerId, {
        zoomControl: false
    }).setView([-0.0583, 109.3448], 15);

    // Pindahkan zoom control ke kanan bawah
    L.control.zoom({
        position: 'bottomright'
    }).addTo(map);

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const tileUrl = isDark 
        ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
        : 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png';

    // Tile Layer Premium (CartoDB Dark Matter / Light All)
    const tileLayer = L.tileLayer(tileUrl, {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);

    // Dynamic theme updater for tile layer
    const observer = new MutationObserver(() => {
        const dark = document.documentElement.getAttribute('data-theme') === 'dark';
        tileLayer.setUrl(dark 
            ? 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png'
            : 'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png'
        );
    });
    observer.observe(document.documentElement, { attributes: true });

    return map;
};
