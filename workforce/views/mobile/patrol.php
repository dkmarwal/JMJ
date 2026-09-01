<div class="space-y-4">
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xl">
        <h2 class="text-sm font-bold text-white mb-1">Guard Tour & Patrol Checkpoints</h2>
        <p class="text-xs text-slate-400">Scan physical QR tokens sequentially along the perimeter</p>
    </div>

    <!-- Active Route Details -->
    <?php if (!empty($routes)): ?>
        <?php foreach ($routes as $rt): ?>
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 shadow-xl space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-white"><?= wf_e($rt['name']) ?></span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-500/20 text-blue-400"><?= $rt['estimated_minutes'] ?> mins</span>
                </div>
                <p class="text-xs text-slate-400"><?= wf_e($rt['description']) ?></p>

                <!-- Checkpoint Scan Simulation Input -->
                <div class="pt-3 border-t border-slate-800 space-y-2">
                    <label class="block text-[10px] uppercase font-bold text-slate-400">Scan or Enter Checkpoint QR Token</label>
                    <div class="flex gap-2">
                        <input type="text" id="cpTokenInput_<?= $rt['id'] ?>" placeholder="e.g. JMJ-CP-ABC-A-001" class="flex-1 px-3 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs font-mono focus:border-emerald-500 focus:outline-none">
                        <button type="button" onclick="submitCheckpointScan(<?= $rt['id'] ?>)" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition">
                            Scan
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 text-center text-xs text-slate-500">
            No active patrol routes configured for your assigned site post.
        </div>
    <?php endif; ?>
</div>

<script>
function submitCheckpointScan(routeId) {
    const token = document.getElementById('cpTokenInput_' + routeId).value;
    if (!token) {
        alert('Please enter or scan a checkpoint QR token.');
        return;
    }

    navigator.geolocation.getCurrentPosition((pos) => {
        fetch('<?= wf_url('api/patrols/scan') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                tour_id: 1,
                qr_token: token,
                latitude: pos.coords.latitude,
                longitude: pos.coords.longitude
            })
        }).then(r => r.json()).then(res => {
            if (res.success) {
                alert('✅ ' + res.message);
                document.getElementById('cpTokenInput_' + routeId).value = '';
            } else {
                alert('❌ ' + res.message);
            }
        });
    }, () => {
        alert('Checkpoint scanned with default coordinates.');
    });
}
</script>
