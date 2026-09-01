<div class="max-w-3xl mx-auto">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h2 class="text-base font-bold text-white mb-1">Conduct Field Officer Site Audit</h2>
        <p class="text-xs text-slate-400 mb-6">Inspect staff turnout, guard post cleanliness, registers, and equipment</p>

        <form action="<?= wf_url('audits/store') ?>" method="POST" class="space-y-4">
            <?= wf_csrf_field() ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Inspected Client Site *</label>
                    <select name="site_id" required class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                        <?php foreach ($sites as $st): ?>
                            <option value="<?= $st['id'] ?>"><?= wf_e($st['site_name']) ?> (<?= wf_e($st['client_name']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Audit Type</label>
                    <select name="audit_type" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none">
                        <option value="regular">Scheduled Daylight Audit</option>
                        <option value="surprise_night">Surprise Night Patrol Audit</option>
                        <option value="client_complaint">Incident Follow-Up Audit</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Physical Guards Count Present</label>
                    <input type="number" name="guards_present" value="4" min="0" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none font-mono">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Uniform & Turnout Rating (%)</label>
                    <input type="number" name="uniform_compliance_score" value="95" min="0" max="100" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none font-mono">
                </div>
            </div>

            <!-- Checkboxes -->
            <div class="pt-3 border-t border-slate-800 space-y-2">
                <label class="flex items-center gap-2 text-xs text-slate-300">
                    <input type="checkbox" name="equipment_status_ok" value="1" checked class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-emerald-600 focus:ring-emerald-500">
                    <span>Security Equipment, Torches, Whistles & Metal Detectors Operational</span>
                </label>
                <label class="flex items-center gap-2 text-xs text-slate-300">
                    <input type="checkbox" name="registers_updated" value="1" checked class="w-4 h-4 rounded bg-slate-950 border-slate-800 text-emerald-600 focus:ring-emerald-500">
                    <span>Visitor, Vehicle & Key Issue Registers up-to-date and countersigned</span>
                </label>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">Client Feedback & Field Observations</label>
                <textarea name="client_feedback" rows="3" placeholder="Notes from client facility manager or site supervisor..." class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-white text-xs focus:border-emerald-500 focus:outline-none"></textarea>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <a href="<?= wf_url('audits') ?>" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold">Cancel</a>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-lg shadow-emerald-600/20">Submit Audit Record</button>
            </div>
        </form>
    </div>
</div>
