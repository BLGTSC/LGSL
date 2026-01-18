<?php
$id = isset($_GET['id']) ? $_GET['id'] : null;
$server = getServerById($id);

if(!$server) {
    echo '<div class="p-20 text-center text-red-500 font-bold text-xl">Server not found.</div>';
    return;
}

$isOnline = $server['status'] === 'ONLINE';
$playerList = isset($server['player_list']) ? $server['player_list'] : [];
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 animate-fade-in">
    <a href="index.php" class="inline-flex items-center text-slate-400 hover:text-white mb-8 transition-colors font-medium">
        <div class="bg-slate-800 p-2 rounded-full mr-3 hover:bg-purple-600 transition-colors">
          <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </div>
        Back to List
    </a>

    <!-- Header Banner -->
    <div class="relative rounded-3xl overflow-hidden shadow-2xl border-2 border-slate-700/50 h-[400px] mb-10 group">
        <img src="<?= $server['banner_url'] ?>" alt="Banner" class="w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-1000">
        
        <div class="absolute inset-0 bg-gradient-to-t from-[#0f172a] via-[#0f172a]/80 to-transparent flex flex-col justify-end p-8 md:p-12">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                         <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest shadow-lg <?= $isOnline ? 'bg-emerald-500 text-emerald-950 shadow-emerald-500/20' : 'bg-rose-500 text-white' ?>">
                            <?= $server['status'] ?>
                         </span>
                         <span class="bg-slate-800/80 backdrop-blur text-purple-200 px-3 py-1 rounded-full text-sm font-bold border border-purple-500/30 flex items-center">
                            <?= getCountryFlag($server['country']) ?> <span class="ml-2 font-mono"><?= $server['ip'] ?>:<?= $server['port'] ?></span>
                         </span>
                    </div>
                    <h1 class="text-4xl md:text-6xl font-black text-white mb-4 drop-shadow-xl tracking-tight"><?= $server['name'] ?></h1>
                    <p class="text-slate-300 max-w-2xl text-lg font-medium leading-relaxed"><?= $server['description'] ?></p>
                </div>
                
                <div class="flex items-center space-x-6 glass-panel p-6 rounded-2xl border-t border-white/10">
                    <div class="text-center">
                        <div class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-b from-orange-300 to-red-500"><?= $server['votes'] ?></div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Votes</div>
                        <a href="index.php?p=vote&id=<?= $server['id'] ?>" class="text-xs text-orange-400 hover:text-white underline mt-1 block">Vote Now</a>
                    </div>
                    <div class="w-px h-10 bg-slate-700"></div>
                    <div class="text-center">
                        <div class="text-3xl font-black text-white"><?= $server['players'] ?> <span class="text-slate-500 text-xl font-bold">/ <?= $server['max_players'] ?></span></div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Online</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- PLAYERS TABLE -->
            <div class="glass-panel rounded-2xl p-6">
                 <h3 class="text-xl font-bold text-white mb-6 flex items-center">
                    <span class="w-2 h-8 bg-emerald-500 rounded-full mr-3"></span>
                    Live Players
                    <span class="ml-2 bg-slate-800 text-slate-400 text-xs px-2 py-1 rounded-md"><?= count($playerList) ?> online</span>
                </h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs text-slate-500 uppercase border-b border-slate-700/50">
                                <th class="py-3 pl-4">#</th>
                                <th class="py-3">Player Name</th>
                                <th class="py-3 text-right">Score</th>
                                <th class="py-3 pr-4 text-right">Time Played</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <?php if(empty($playerList)): ?>
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-slate-500 italic">
                                        No players visible or query hidden by server.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($playerList as $idx => $player): ?>
                                <tr class="border-b border-slate-800/50 hover:bg-slate-800/30 transition-colors">
                                    <td class="py-3 pl-4 font-mono text-slate-600"><?= $idx + 1 ?></td>
                                    <td class="py-3 font-bold text-slate-200"><?= htmlspecialchars($player['name']) ?></td>
                                    <td class="py-3 text-right font-mono text-emerald-400"><?= $player['score'] ?></td>
                                    <td class="py-3 pr-4 text-right text-slate-400"><?= $player['time'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Stats Chart -->
            <div class="glass-panel rounded-2xl p-6">
                <h3 class="text-xl font-bold text-white mb-8 flex items-center">
                    <span class="w-2 h-8 bg-purple-500 rounded-full mr-3"></span>
                    History (24h)
                </h3>
                <div class="h-[300px] w-full relative">
                    <canvas id="playersChart"></canvas>
                </div>
            </div>

            <!-- Widgets -->
            <div class="glass-panel rounded-2xl p-8 space-y-8">
                <div>
                    <h3 class="text-lg font-bold text-white mb-3">Forum Signature</h3>
                    <div class="bg-[#0b1120] p-4 rounded-xl border border-slate-800 font-mono text-sm text-emerald-400 select-all cursor-text shadow-inner break-all">
                        [url=https://csx16.ro/servers/?p=server&id=<?= $server['id'] ?>][img]https://csx16.ro/servers/api/badge.php?id=<?= $server['id'] ?>[/img][/url]
                    </div>
                </div>
                <div class="mt-4">
                     <p class="text-sm text-slate-400 mb-2 font-bold uppercase">Preview:</p>
                     <!-- Added timestamp to force refresh image -->
                     <img src="api/badge.php?id=<?= $server['id'] ?>&t=<?= time() ?>" alt="Server Badge" class="rounded-lg shadow-xl border border-slate-700">
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <div class="glass-panel rounded-2xl p-6 border border-slate-700/50 shadow-xl">
                <h3 class="text-lg font-black text-white mb-6 border-b border-slate-700 pb-4 tracking-wide">SERVER INFO</h3>
                <ul class="space-y-5">
                    <li class="flex justify-between items-center">
                        <span class="text-slate-400 flex items-center font-medium text-sm"><i data-lucide="globe" class="w-4 h-4 mr-3 text-purple-500"></i> MAP</span>
                        <span class="text-white font-bold"><?= $server['map'] ?></span>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-slate-400 flex items-center font-medium text-sm"><i data-lucide="shield" class="w-4 h-4 mr-3 text-pink-500"></i> VERSION</span>
                        <span class="text-white font-bold"><?= $server['version'] ?></span>
                    </li>
                    <li class="flex justify-between items-center">
                        <span class="text-slate-400 flex items-center font-medium text-sm"><i data-lucide="share-2" class="w-4 h-4 mr-3 text-orange-500"></i> GAME</span>
                        <span class="text-white font-bold"><?= $server['game'] ?></span>
                    </li>
                </ul>
                
                <a href="steam://connect/<?= $server['ip'] ?>:<?= $server['port'] ?>" 
                   class="w-full mt-8 bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-black py-4 px-4 rounded-xl shadow-lg shadow-purple-900/30 transition-all flex justify-center items-center btn-pulse">
                    <i data-lucide="play" class="w-5 h-5 mr-2"></i> CONNECT NOW
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Mock Chart Data initialization
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('playersChart').getContext('2d');
    
    // Gradient
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(139, 92, 246, 0.5)');   
    gradient.addColorStop(1, 'rgba(139, 92, 246, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
            datasets: [{
                label: 'Players Online',
                data: [12, 5, 2, 18, 25, 30, 28], // Mock data - in real app, fetch from history table
                borderColor: '#8b5cf6',
                backgroundColor: gradient,
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#8b5cf6',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.1)' },
                    ticks: { color: '#94a3b8' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8' }
                }
            }
        }
    });
});
</script>