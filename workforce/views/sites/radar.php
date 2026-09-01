<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                <h2 class="text-lg font-bold text-white tracking-tight">Live Operations Radar & Geofence Map</h2>
            </div>
            <p class="text-xs text-slate-400">Real-time geospatial tracking of client complexes, active guard posts, and staffing coverage</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 text-xs text-slate-300 font-mono">
                <i class="fa-solid fa-satellite text-emerald-400 mr-1.5"></i> <?= count($sites) ?> Sites Tracked
            </span>
        </div>
    </div>

    <!-- Full Width Radar Map Container -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-4 shadow-2xl relative overflow-hidden">
        <div id="radarMap" class="w-full h-[600px] rounded-xl overflow-hidden border border-slate-800"></div>

        <!-- Radar Overlay Legend -->
        <div class="absolute bottom-8 left-8 bg-slate-900/90 backdrop-blur-md border border-slate-800 p-4 rounded-2xl shadow-xl z-[1000] text-xs space-y-2">
            <div class="font-bold text-white mb-1">Radar Legend</div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                <span class="text-slate-300">Full Staffing Deployed</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                <span class="text-slate-300">Under-Strength Warning</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                <span class="text-slate-300">SOS Emergency Alert</span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Center initially on Delhi NCR
    const map = L.map('radarMap').setView([28.6139, 77.2090], 11);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    fetch('<?= wf_url('api/radar/live-sites') ?>')
        .then(r => r.json())
        .then(res => {
            if (res.success && res.data.sites) {
                const bounds = [];
                res.data.sites.forEach(st => {
                    const lat = parseFloat(st.latitude);
                    const lng = parseFloat(st.longitude);
                    const rad = parseInt(st.geofence_radius) || 75;

                    if (!isNaN(lat) && !isNaN(lng)) {
                        bounds.push([lat, lng]);

                        const isFull = (parseInt(st.live_present_count) >= parseInt(st.required_guards_count));
                        const markerColor = isFull ? '#10b981' : '#f59e0b';

                        L.circle([lat, lng], {
                            color: markerColor,
                            fillColor: markerColor,
                            fillOpacity: 0.25,
                            radius: rad
                        }).addTo(map);

                        const popupHtml = `
                            <div style="font-family: sans-serif; font-size: 12px; min-width: 180px;">
                                <strong style="font-size: 13px; color: #0f172a;">${st.site_name}</strong><br>
                                <span style="color: #64748b;">${st.client_name}</span><br>
                                <hr style="margin: 6px 0; border: 0; border-top: 1px solid #e2e8f0;">
                                <strong>Active On Duty:</strong> ${st.live_present_count} Guards<br>
                                <strong>Geofence Radius:</strong> ${st.geofence_radius}m<br>
                                <a href="<?= wf_url('sites/view?id=') ?>${st.id}" style="display: inline-block; margin-top: 6px; color: #16a34a; font-weight: bold; text-decoration: none;">View Site Command &rarr;</a>
                            </div>
                        `;

                        L.marker([lat, lng]).addTo(map).bindPopup(popupHtml);
                    }
                });

                if (bounds.length > 0) {
                    map.fitBounds(bounds, { padding: [50, 50] });
                }
            }
        });
});
</script>
