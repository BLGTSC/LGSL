<?php
$user = $_SESSION['user'];
$isSuccess = isset($_GET['success']);

// Get user servers
$allServers = getMockServers();
$myServers = array_filter($allServers, function($s) use ($user) {
    return isset($s['owner_id']) && $s['owner_id'] == $user['id'];
});
$totalServers = count($myServers);
$totalVotes = 0;
foreach($myServers as $s) $totalVotes += $s['votes'];
?>
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-black text-white mb-6">My Dashboard</h1>
    
    <?php if($isSuccess): ?>
    <div class="bg-emerald-500/10 border border-emerald-500/50 text-emerald-400 p-4 rounded-xl mb-6 flex items-center animate-fade-in">
        <i data-lucide="check-circle" class="w-6 h-6 mr-3"></i>
        <span class="font-bold">Server submitted successfully! It appears in the list below.</span>
    </div>
    <?php endif; ?>

    <div class="glass-panel rounded-2xl p-12 text-center border-0 mb-8">
        <div class="bg-slate-800 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 ring-4 ring-purple-500/30">
            <span class="text-4xl font-bold text-purple-400"><?= strtoupper(substr($user['username'], 0, 1)) ?></span>
        </div>
        <h2 class="text-2xl font-bold text-white">Hello, <span class="text-purple-400"><?= htmlspecialchars($user['username']) ?></span>!</h2>
        
        <?php if($totalServers > 0): ?>
             <p class="text-slate-400 mt-2 text-lg">You are managing <strong class="text-white"><?= $totalServers ?></strong> server(s).</p>
             <button onclick="window.location.href='index.php?p=add_server'" class="mt-8 bg-slate-700 hover:bg-slate-600 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg inline-flex items-center">
                <i data-lucide="plus" class="w-5 h-5 mr-2"></i> Add Another Server
            </button>
        <?php else: ?>
            <p class="text-slate-400 mt-2 text-lg">You haven't added any servers yet.</p>
            <button onclick="window.location.href='index.php?p=add_server'" class="mt-8 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg btn-pulse-green inline-flex items-center">
                <i data-lucide="plus-circle" class="w-5 h-5 mr-2"></i> Add Your First Server
            </button>
        <?php endif; ?>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="glass-panel p-6 rounded-2xl">
            <div class="text-slate-400 font-bold text-sm uppercase">Total Servers</div>
            <div class="text-3xl font-black text-white mt-2"><?= $totalServers ?></div>
        </div>
        <div class="glass-panel p-6 rounded-2xl">
            <div class="text-slate-400 font-bold text-sm uppercase">Total Votes</div>
            <div class="text-3xl font-black text-white mt-2"><?= $totalVotes ?></div>
        </div>
        <div class="glass-panel p-6 rounded-2xl">
            <div class="text-slate-400 font-bold text-sm uppercase">Account Status</div>
            <div class="text-emerald-400 font-bold mt-2 flex items-center"><i data-lucide="check-circle" class="w-4 h-4 mr-2"></i> Active</div>
        </div>
    </div>

    <!-- My Servers List -->
    <?php if($totalServers > 0): ?>
    <h3 class="text-xl font-bold text-white mb-4">Your Servers</h3>
    <div class="glass-panel rounded-2xl overflow-hidden">
        <?php foreach($myServers as $server): ?>
            <div class="p-4 border-b border-slate-700/50 last:border-0 flex items-center justify-between hover:bg-slate-800/50 transition-colors">
                <div class="flex items-center">
                     <div class="w-2 h-2 rounded-full mr-3 <?= $server['status'] == 'ONLINE' ? 'bg-emerald-500' : 'bg-red-500' ?>"></div>
                     <div>
                         <div class="font-bold text-white"><?= htmlspecialchars($server['name']) ?></div>
                         <div class="text-xs text-slate-400 font-mono"><?= $server['ip'] ?>:<?= $server['port'] ?></div>
                     </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-center hidden sm:block">
                        <div class="text-xs text-slate-500 font-bold">VOTES</div>
                        <div class="font-bold text-white"><?= $server['votes'] ?></div>
                    </div>
                    <a href="index.php?p=server&id=<?= $server['id'] ?>" class="text-purple-400 hover:text-white px-3 py-1 bg-purple-500/10 rounded-lg text-sm font-bold">View</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>