<?php
$servers = getMockServers();
?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Hero Section -->
    <div class="mb-10 text-center relative">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-3/4 h-24 bg-purple-500/20 blur-[100px] rounded-full -z-10"></div>
        <h1 class="text-5xl md:text-7xl font-black text-white mb-4 tracking-tight drop-shadow-2xl">
          CSX16 <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-pink-500 to-red-500">RANKING</span>
        </h1>
        <p class="text-slate-300 text-lg md:text-xl font-medium max-w-2xl mx-auto">
          Top servers, real-time stats, and the best community. Vote for your favorite server today!
        </p>
    </div>

    <!-- Search / Filter (Visual only for now) -->
    <div class="glass-panel p-2 rounded-2xl mb-8 flex flex-col md:flex-row gap-2 shadow-xl shadow-purple-900/10">
        <div class="relative w-full">
            <i data-lucide="search" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-purple-400 w-5 h-5"></i>
            <input type="text" placeholder="Find a server or map..." class="w-full bg-slate-900/50 border border-slate-700/50 text-white rounded-xl pl-12 pr-4 py-4 focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all placeholder-slate-500">
        </div>
    </div>

    <!-- Server List -->
    <div class="glass-panel rounded-2xl overflow-hidden shadow-2xl min-h-[400px]">
        
        <?php if(empty($servers)): ?>
             <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="bg-slate-800 p-6 rounded-full mb-4">
                    <i data-lucide="server-off" class="w-12 h-12 text-slate-500"></i>
                </div>
                <h3 class="text-2xl font-bold text-white mb-2">No Servers Listed Yet</h3>
                <p class="text-slate-400 max-w-md mb-8">Be the first to add a server to the list!</p>
                <a href="index.php?p=add_server" class="bg-gradient-to-r from-emerald-500 to-emerald-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg hover:brightness-110 transition-all">
                    Add Server
                </a>
             </div>
        <?php else: ?>

        <!-- Header -->
        <div class="hidden sm:flex bg-slate-950/50 text-purple-300 text-xs font-black uppercase tracking-widest py-4 px-4 border-b border-slate-800">
            <div class="w-12 text-center">#</div>
            <div class="w-6"></div>
            <div class="flex-grow pl-2">Server Details</div>
            <div class="w-40">Status</div>
            <div class="w-20 text-center">Votes</div>
        </div>

        <?php foreach($servers as $server): 
            $isOnline = $server['status'] === 'ONLINE';
            $maxPlayers = $server['max_players'] > 0 ? $server['max_players'] : 32;
            $fillPercentage = min(($server['players'] / $maxPlayers) * 100, 100);
            
            // Rank Color Logic
            $rankClass = 'bg-slate-800 text-slate-500 border border-slate-700';
            if($server['rank'] == 1) $rankClass = 'bg-gradient-to-br from-yellow-300 to-yellow-600 text-yellow-950 ring-2 ring-yellow-400/50';
            if($server['rank'] == 2) $rankClass = 'bg-gradient-to-br from-slate-200 to-slate-400 text-slate-800 ring-2 ring-slate-400/50';
            if($server['rank'] == 3) $rankClass = 'bg-gradient-to-br from-orange-300 to-orange-600 text-orange-950 ring-2 ring-orange-400/50';
        ?>
            <div onclick="window.location.href='index.php?p=server&id=<?= $server['id'] ?>'" 
                 class="group relative bg-slate-900/40 hover:bg-slate-800/60 border-b border-slate-700/50 last:border-0 transition-all cursor-pointer backdrop-blur-sm">
                
                <div class="absolute inset-0 border-l-4 border-transparent group-hover:border-purple-500 transition-all duration-300"></div>

                <div class="flex flex-col sm:flex-row items-center p-4 gap-4">
                    <!-- Rank -->
                    <div class="flex-shrink-0 w-12 flex justify-center">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center font-black text-lg shadow-lg <?= $rankClass ?>">
                            <?= $server['rank'] ?>
                        </div>
                    </div>

                    <!-- Status Dot -->
                    <div class="flex-shrink-0">
                        <div class="relative w-4 h-4 rounded-full <?= $isOnline ? 'bg-emerald-500' : 'bg-red-500' ?>">
                            <?php if($isOnline): ?>
                                <div class="absolute inset-0 rounded-full bg-emerald-400 animate-ping opacity-75"></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="flex-grow min-w-0 text-center sm:text-left w-full sm:w-auto">
                        <div class="flex items-center justify-center sm:justify-start space-x-2">
                            <span class="text-xl shadow-sm"><?= getCountryFlag($server['country']) ?></span>
                            <h3 class="text-lg font-bold text-slate-100 truncate group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-purple-400 group-hover:to-pink-400 transition-all">
                                <?= $server['name'] ?>
                            </h3>
                        </div>
                        <div class="text-sm flex flex-wrap justify-center sm:justify-start gap-2 mt-1.5">
                            <span class="bg-slate-800/80 px-2 py-0.5 rounded text-xs font-semibold text-cyan-400 border border-cyan-900/30"><?= $server['game'] ?></span>
                            <span class="font-mono text-slate-400"><?= $server['ip'] ?>:<?= $server['port'] ?></span>
                            <span class="text-slate-600">|</span>
                            <span class="text-purple-300"><?= $server['map'] ?></span>
                        </div>
                    </div>

                    <!-- Players -->
                    <div class="w-full sm:w-40 flex-shrink-0">
                        <div class="flex justify-between text-xs font-bold text-slate-400 mb-1.5">
                            <span>POPULATION</span>
                            <span class="<?= $isOnline ? 'text-white' : 'text-slate-600' ?>"><?= $server['players'] ?>/<?= $server['max_players'] ?></span>
                        </div>
                        <div class="w-full bg-slate-800 rounded-full h-3 overflow-hidden border border-slate-700">
                            <div class="h-full rounded-full shadow-[0_0_10px_rgba(0,0,0,0.5)] <?= $isOnline ? 'bg-gradient-to-r from-blue-500 to-purple-500' : 'bg-slate-600' ?>" 
                                 style="width: <?= $fillPercentage ?>%"></div>
                        </div>
                    </div>

                    <!-- Vote -->
                    <div class="flex-shrink-0 flex items-center space-x-3">
                        <div class="text-center hidden sm:block">
                            <div class="text-lg font-black text-white"><?= $server['votes'] ?></div>
                        </div>
                        <a href="index.php?p=vote&id=<?= $server['id'] ?>" 
                           onclick="event.stopPropagation();"
                           class="bg-gradient-to-br from-orange-500 to-red-600 hover:from-orange-400 hover:to-red-500 text-white p-2.5 rounded-xl shadow-lg border border-red-400/20 transform active:scale-95 transition-all">
                            <i data-lucide="trophy" class="w-5 h-5"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>