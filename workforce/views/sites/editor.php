<div class="max-w-3xl mx-auto">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h2 class="text-base font-bold text-white mb-1">Configure Client Site & GPS Geofence</h2>
        <p class="text-xs text-slate-400 mb-6">Establish geographic boundaries, post coordinates, and security rules</p>

        <form action="<?= wf_url('sites/store') ?>" method="POST" class="space-y-4">
            <?= wf_csrf_field() ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Client Account *</label>
                    <select name="client_id" required class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                        <?php foreach ($clients as $cl): ?>
                            <option value="<?= $cl['id'] ?>"><?= wf_e($cl['company_name']) ?> (<?= wf_e($cl['client_code']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Managing Branch</label>
                    <select name="branch_id" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                        <?php foreach ($branches as $br): ?>
                            <option value="<?= $br['id'] ?>"><?= wf_e($br['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Site / Complex Name *</label>
                    <input type="text" name="site_name" required placeholder="e.g. Acme Tech Park - Main Gate" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Site Category</label>
                    <select name="site_type" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                        <option value="corporate_office">Corporate Office</option>
                        <option value="commercial_complex">Commercial Complex</option>
                        <option value="hospital">Hospital / Healthcare</option>
                        <option value="warehouse">Warehouse & Logistics</option>
                        <option value="residential">Residential Society</option>
                        <option value="educational">Educational Campus</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Physical Address *</label>
                    <textarea name="address" required rows="2" placeholder="Full postal location..." class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">City</label>
                    <input type="text" name="city" value="New Delhi" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">State</label>
                    <input type="text" name="state" value="Delhi" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Latitude (GPS) *</label>
                    <input type="number" step="0.000001" id="latInput" name="latitude" required value="28.6304" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Longitude (GPS) *</label>
                    <input type="number" step="0.000001" id="lngInput" name="longitude" required value="77.2270" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Geofence Radius (Meters) *</label>
                    <input type="number" name="geofence_radius" required value="75" min="20" max="500" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Site Supervisor / Contact Phone</label>
                    <input type="text" name="contact_phone" placeholder="+91-9811223344" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                <button type="button" onclick="getCurrentLocation()" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs text-slate-300 font-semibold flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-location-crosshairs text-emerald-400"></i>
                    <span>Use Current GPS Device Position</span>
                </button>

                <div class="flex items-center gap-3">
                    <a href="<?= wf_url('sites') ?>" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition">Cancel</a>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold transition shadow-lg shadow-emerald-600/20">Save Site Configuration</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((pos) => {
            document.getElementById('latInput').value = pos.coords.latitude.toFixed(6);
            document.getElementById('lngInput').value = pos.coords.longitude.toFixed(6);
            alert('Location fetched successfully: ' + pos.coords.latitude.toFixed(6) + ', ' + pos.coords.longitude.toFixed(6));
        }, (err) => {
            alert('Error fetching GPS coordinates: ' + err.message);
        });
    }
}
</script>
