<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Digital Identity Card - <?= wf_e($employee['employee_code']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            body { background: white !important; padding: 0 !important; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-slate-950 min-h-screen flex flex-col items-center justify-center p-6 text-slate-100 font-sans">
    <div class="no-print mb-6 flex items-center gap-3">
        <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs flex items-center gap-2 shadow-lg shadow-emerald-600/30 transition">
            <i class="fa-solid fa-print"></i>
            <span>Print Official ID Card</span>
        </button>
        <a href="<?= wf_url('staff/view?id=' . $employee['id']) ?>" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold transition">Back to Profile</a>
    </div>

    <!-- Official ID Card (Front & Back Layout) -->
    <div class="w-[360px] bg-slate-900 border-2 border-emerald-500/40 rounded-3xl overflow-hidden shadow-2xl relative text-slate-100">
        <!-- Top Banner -->
        <div class="bg-gradient-to-r from-amber-600 via-slate-900 to-slate-950 p-4 text-center border-b border-amber-500/30 flex flex-col items-center">
            <img src="<?= wf_url('assets/images/logo.png') ?>" alt="JMJ Logo" class="w-14 h-14 rounded-full border-2 border-amber-400 object-cover mb-1.5 shadow-lg bg-slate-950">
            <div class="font-black text-sm tracking-wider uppercase text-white"><?= wf_e($company['name'] ?? 'JMJ Enterprise Solutions') ?></div>
            <div class="text-[9px] text-amber-300 tracking-widest uppercase font-bold">PSARA License: <?= wf_e($company['psara_license_no'] ?? 'PSARA/DL/2016/9821') ?></div>
        </div>

        <!-- Middle Body -->
        <div class="p-6 text-center space-y-4">
            <!-- Photo Frame -->
            <div class="w-24 h-24 mx-auto rounded-2xl bg-emerald-950/60 border-2 border-emerald-500/60 flex items-center justify-center text-emerald-400 font-bold text-3xl shadow-lg shadow-emerald-500/10">
                <?= strtoupper(substr($employee['first_name'], 0, 1) . substr($employee['last_name'], 0, 1)) ?>
            </div>

            <div>
                <h3 class="text-base font-bold text-white"><?= wf_e($employee['first_name'] . ' ' . $employee['last_name']) ?></h3>
                <div class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                    <?= wf_e($employee['designation']) ?>
                </div>
            </div>

            <div class="bg-slate-950 p-3.5 rounded-2xl border border-slate-800 text-xs text-left space-y-1.5 font-medium">
                <div class="flex justify-between"><span class="text-slate-500">Employee Code:</span> <strong class="font-mono text-emerald-400"><?= wf_e($employee['employee_code']) ?></strong></div>
                <div class="flex justify-between"><span class="text-slate-500">Mobile:</span> <span class="font-mono text-white"><?= wf_e($employee['phone']) ?></span></div>
                <div class="flex justify-between"><span class="text-slate-500">Emergency:</span> <span class="font-mono text-slate-300"><?= wf_e($employee['emergency_phone'] ?? '100') ?></span></div>
                <div class="flex justify-between"><span class="text-slate-500">Joining Date:</span> <span><?= wf_format_date($employee['joining_date']) ?></span></div>
            </div>

            <!-- Verification QR Code -->
            <div class="pt-2">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?= urlencode('JMJ-VERIFY:' . $employee['employee_code'] . ':' . $employee['id']) ?>" 
                     alt="Staff Verification QR" class="w-16 h-16 mx-auto rounded-lg bg-white p-1">
                <div class="text-[9px] text-slate-500 uppercase tracking-wider mt-1.5 font-bold">Scan to Verify Credentials</div>
            </div>
        </div>

        <!-- Footer Strip -->
        <div class="bg-slate-950 p-3 text-center border-t border-slate-800 text-[9px] text-slate-500">
            <div>Emergency Ops Command: <strong>18008890832</strong></div>
            <div>250, Sant Nagar, East of Kailash, New Delhi - 110065</div>
        </div>
    </div>
</body>
</html>
