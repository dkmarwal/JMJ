<div class="space-y-6">
    <!-- Header -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-950 text-slate-400 border border-slate-800"><?= wf_e($site['site_code']) ?></span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20"><?= wf_e($site['status']) ?></span>
            </div>
            <h2 class="text-xl font-bold text-white"><?= wf_e($site['site_name']) ?></h2>
            <p class="text-xs text-emerald-400 font-semibold mt-1"><?= wf_e($site['client_name']) ?> &bull; <?= wf_e($site['address']) ?>, <?= wf_e($site['city']) ?></p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" onclick="showLiveQR(<?= $site['id'] ?>, '<?= wf_e($site['site_name']) ?>')" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition shadow-lg shadow-emerald-600/20">
                <i class="fa-solid fa-qrcode"></i>
                <span>Open Dynamic QR Terminal</span>
            </button>
        </div>
    </div>

    <!-- Map & Specifications -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Site Interactive Geofence Map -->
        <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Live Geofence Boundary Map</h3>
            <div id="siteMap" class="h-64 rounded-xl overflow-hidden border border-slate-800"></div>
        </div>

        <!-- Coordinates & Details -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Security Parameters</h3>
            <div class="text-xs space-y-3 text-slate-300">
                <div>
                    <span class="text-slate-500 block">GPS Coordinates:</span>
                    <span class="font-mono text-white text-sm"><?= $site['latitude'] ?>, <?= $site['longitude'] ?></span>
                </div>
                <div>
                    <span class="text-slate-500 block">Enforced Geofence Radius:</span>
                    <span class="font-bold text-emerald-400 text-base"><?= $site['geofence_radius'] ?> meters</span>
                </div>
                <div>
                    <span class="text-slate-500 block">Emergency Police / Fire Post:</span>
                    <span class="font-bold text-white"><?= wf_e($site['emergency_contact'] ?? '112') ?></span>
                </div>
                <div class="pt-3 border-t border-slate-800">
                    <span class="text-slate-500 block mb-1">Post Standing Orders:</span>
                    <p class="text-slate-400 leading-relaxed bg-slate-950 p-2.5 rounded-xl border border-slate-800/80"><?= wf_e($site['instructions'] ?? 'Standard security and access control procedures apply.') ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkpoints & Zones -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Checkpoints -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Patrol Checkpoints (<?= count($checkpoints) ?>)</h3>
            <div class="space-y-2">
                <?php foreach ($checkpoints as $cp): ?>
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-bold text-white"><?= wf_e($cp['checkpoint_name']) ?></div>
                            <div class="text-[10px] text-slate-500 font-mono"><?= wf_e($cp['checkpoint_code']) ?> &bull; Token: <?= wf_e($cp['qr_token']) ?></div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">QR Active</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Shift Schedules -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-xl">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Operating Shifts (<?= count($shifts) ?>)</h3>
            <div class="space-y-2">
                <?php foreach ($shifts as $sh): ?>
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-between text-xs">
                        <div>
                            <div class="font-bold text-white"><?= wf_e($sh['name']) ?></div>
                            <div class="text-[10px] text-emerald-400 font-mono font-semibold"><?= wf_format_time($sh['start_time']) ?> &mdash; <?= wf_format_time($sh['end_time']) ?></div>
                        </div>
                        <div class="text-right">
                            <span class="text-white font-bold"><?= $sh['required_guards'] ?> Guards</span>
                            <div class="text-[10px] text-slate-500"><?= $sh['required_cleaners'] ?> Cleaners</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Dynamic QR Terminal Modal -->
<div id="qrModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-sm w-full text-center shadow-2xl">
        <h3 id="modalSiteName" class="text-sm font-bold text-white mb-1">Dynamic Site QR Terminal</h3>
        <p class="text-xs text-slate-400 mb-4">Refreshes cryptographically every 30 seconds</p>
        
        <div class="p-4 bg-white rounded-2xl inline-block mx-auto mb-4 shadow-inner">
            <img id="qrImage" src="" alt="Dynamic QR Code" class="w-48 h-48 mx-auto">
        </div>

        <div class="text-xs text-slate-400 mb-4">
            Token Expires in: <span id="countdown" class="font-bold text-emerald-400 text-sm">30</span>s
        </div>

        <button type="button" onclick="closeQRModal()" class="w-full py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs transition">Close Terminal</button>
    </div>
</div>

<script>
// Initialize Leaflet Map for Site
document.addEventListener('DOMContentLoaded', () => {
    const lat = <?= (float)$site['latitude'] ?>;
    const lng = <?= (float)$site['longitude'] ?>;
    const radius = <?= (int)$site['geofence_radius'] ?>;

    const map = L.map('siteMap').setView([lat, lng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map).bindPopup("<b><?= wf_e($site['site_name']) ?></b>").openPopup();
    L.circle([lat, lng], {
        color: '#10b981',
        fillColor: '#10b981',
        fillOpacity: 0.2,
        radius: radius
    }).addTo(map);
});

let qrInterval;
let countdownInterval;

function showLiveQR(siteId, siteName) {
    document.getElementById('modalSiteName').innerText = siteName + ' - Dynamic Check-In QR';
    document.getElementById('qrModal').classList.remove('hidden');
    fetchQR(siteId);
    qrInterval = setInterval(() => fetchQR(siteId), 30000);
}

function fetchQR(siteId) {
    fetch('<?= wf_url('api/sites/') ?>' + siteId + '/dynamic-qr')
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                const token = res.data.token;
                document.getElementById('qrImage').src = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(token);
                startCountdown(res.data.expires_in);
            }
        });
}

function startCountdown(seconds) {
    let rem = seconds;
    clearInterval(countdownInterval);
    document.getElementById('countdown').innerText = rem;
    countdownInterval = setInterval(() => {
        rem--;
        if (rem < 0) rem = 0;
        document.getElementById('countdown').innerText = rem;
    }, 1000);
}

function closeQRModal() {
    document.getElementById('qrModal').classList.add('hidden');
    clearInterval(qrInterval);
    clearInterval(countdownInterval);
}
</script>
