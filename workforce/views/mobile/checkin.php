<div class="space-y-4">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xl">
        <h2 class="text-sm font-bold text-white mb-1">4-Layer Biometric & Geofence Check-In</h2>
        <p class="text-xs text-slate-400">Verifying GPS Location, Dynamic QR Scan, and Live Camera Selfie</p>
    </div>

    <!-- Telemetry Status Panel -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xl space-y-4 text-xs">
        <!-- Layer 1: GPS Geofence Status -->
        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-950 border border-slate-800">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-location-crosshairs text-emerald-400 text-base"></i>
                <div>
                    <div class="font-bold text-white">1. GPS Geofence Location</div>
                    <div id="gpsStatusText" class="text-[10px] text-slate-400">Acquiring satellite lock...</div>
                </div>
            </div>
            <span id="gpsBadge" class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-500/20 text-amber-400">Checking</span>
        </div>

        <!-- Layer 2: Dynamic QR Scan Token -->
        <div class="p-3 rounded-2xl bg-slate-950 border border-slate-800 space-y-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-qrcode text-blue-400 text-base"></i>
                    <div>
                        <div class="font-bold text-white">2. Site Terminal Dynamic QR</div>
                        <div class="text-[10px] text-slate-400">Scan 30s token from site screen or enter token</div>
                    </div>
                </div>
                <span id="qrBadge" class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-800 text-slate-400">Optional</span>
            </div>
            <input type="text" id="qrTokenInput" placeholder="Paste or scan Dynamic QR token..." class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-800 text-white text-[11px] font-mono focus:border-emerald-500 focus:outline-none">
        </div>

        <!-- Layer 3: Live Selfie Stream -->
        <div class="p-3 rounded-2xl bg-slate-950 border border-slate-800 space-y-2">
            <div class="font-bold text-white flex items-center gap-2">
                <i class="fa-solid fa-camera text-purple-400"></i>
                <span>3. Live Selfie & Liveness Verification</span>
            </div>
            <div class="relative w-full h-48 bg-slate-900 rounded-2xl overflow-hidden border border-slate-800 flex items-center justify-center">
                <video id="webcamVideo" autoplay playsinline class="w-full h-full object-cover"></video>
                <canvas id="selfieCanvas" class="hidden"></canvas>
                <div id="cameraPlaceholder" class="absolute inset-0 flex flex-col items-center justify-center text-slate-500">
                    <i class="fa-solid fa-camera text-2xl mb-1"></i>
                    <span class="text-[10px]">Tap to initialize camera</span>
                </div>
            </div>
            <button type="button" onclick="initCamera()" class="w-full py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 font-semibold text-xs border border-slate-800 transition">
                <i class="fa-solid fa-video mr-1 text-emerald-400"></i> Initialize Live Camera
            </button>
        </div>

        <!-- Submit Button -->
        <button type="button" id="submitCheckInBtn" onclick="submit4LayerCheckIn()" class="w-full py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs tracking-wider shadow-lg shadow-emerald-600/30 flex items-center justify-center gap-2 transition">
            <i class="fa-solid fa-shield-check text-sm"></i>
            <span>SUBMIT VERIFIED CHECK-IN</span>
        </button>
    </div>
</div>

<script>
let userLat = <?= (float)($deployment['latitude'] ?? 28.6304) ?>;
let userLng = <?= (float)($deployment['longitude'] ?? 77.2270) ?>;
let userAccuracy = 15;
let videoStream = null;

// Initialize Geolocation
document.addEventListener('DOMContentLoaded', () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((pos) => {
            userLat = pos.coords.latitude;
            userLng = pos.coords.longitude;
            userAccuracy = pos.coords.accuracy;
            document.getElementById('gpsStatusText').innerText = 'GPS Locked: (' + userLat.toFixed(4) + ', ' + userLng.toFixed(4) + ') ±' + Math.round(userAccuracy) + 'm';
            document.getElementById('gpsBadge').className = 'px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-400';
            document.getElementById('gpsBadge').innerText = 'LOCKED';
        }, (err) => {
            document.getElementById('gpsStatusText').innerText = 'GPS default post coordinates active';
            document.getElementById('gpsBadge').innerText = 'POST GPS';
        }, { enableHighAccuracy: true });
    }
});

function initCamera() {
    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false })
        .then(stream => {
            videoStream = stream;
            const video = document.getElementById('webcamVideo');
            video.srcObject = stream;
            document.getElementById('cameraPlaceholder').classList.add('hidden');
        })
        .catch(err => {
            alert('Camera permission required for biometric verification: ' + err.message);
        });
}

function captureSelfie() {
    const video = document.getElementById('webcamVideo');
    const canvas = document.getElementById('selfieCanvas');
    if (videoStream && video.videoWidth > 0) {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        return canvas.toDataURL('image/jpeg', 0.8);
    }
    return null;
}

function submit4LayerCheckIn() {
    const btn = document.getElementById('submitCheckInBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Verifying 4 Layers...</span>';

    const selfieBase64 = captureSelfie();
    const qrToken = document.getElementById('qrTokenInput').value;

    const payload = {
        employee_id: <?= (int)($user['employee_id'] ?? 1) ?>,
        site_id: <?= (int)($deployment['site_id'] ?? 1) ?>,
        shift_id: <?= (int)($deployment['shift_id'] ?? 1) ?>,
        latitude: userLat,
        longitude: userLng,
        accuracy: userAccuracy,
        qr_token: qrToken,
        selfie_base64: selfieBase64,
        device_id: 'MOBILE_PWA_' + navigator.userAgent.substring(0, 20)
    };

    fetch('<?= wf_url('api/attendance/check-in') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-shield-check text-sm"></i> <span>SUBMIT VERIFIED CHECK-IN</span>';
        if (res.success) {
            alert('✅ ' + res.message + '\nVerification Score: ' + res.data.verification_score + '%');
            window.location.href = '<?= wf_url('mobile') ?>';
        } else {
            alert('❌ Check-In Verification Failed:\n' + res.message);
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-shield-check text-sm"></i> <span>SUBMIT VERIFIED CHECK-IN</span>';
        alert('Network or verification error: ' + err.message);
    });
}
</script>
