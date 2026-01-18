<?php
// Handle form submission
$error = '';
$games = getGameTypes();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect data
    $name = $_POST['name'] ?? '';
    $ip = $_POST['ip'] ?? '';
    $port = $_POST['port'] ?? '';
    $game = $_POST['game'] ?? '';
    $desc = $_POST['description'] ?? '';
    $banner = $_POST['banner_url'] ?? '';

    // Basic Validation
    if (!$name || !$ip || !$port || !$game) {
       $error = "Please fill in all required fields (Name, IP, Port, Game).";
    } elseif (!is_numeric($port)) {
       $error = "Port must be a number.";
    } else {
       // Create Server Object
       $newServer = [
            'name' => htmlspecialchars($name),
            'ip' => htmlspecialchars($ip),
            'port' => intval($port),
            'game' => htmlspecialchars($game),
            'map' => 'unknown', 
            'players' => 0,
            'max_players' => 32,
            'status' => 'ONLINE', // Optimistic status
            'votes' => 0,
            'description' => htmlspecialchars($desc),
            'banner_url' => $banner ?: 'https://picsum.photos/seed/'.rand(100,999).'/800/300',
            'version' => '1.0',
            'country' => 'RO',
            'owner_id' => isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0,
            'player_list' => []
       ];

       // Save using the new persistent function
       addServerToJson($newServer);
       
       echo "<script>window.location.href='index.php?p=dashboard&success=1';</script>";
       exit;
    }
}
?>

<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="glass-panel p-8 md:p-12 rounded-2xl shadow-2xl relative overflow-hidden animate-fade-in">
        <!-- Background decorative elements -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-purple-500/10 rounded-full blur-3xl"></div>

        <div class="relative">
            <h2 class="text-3xl md:text-4xl font-black text-white mb-2">Add New Server</h2>
            <p class="text-slate-400 mb-8 text-lg">Publish your server to the community listing.</p>

            <?php if($error): ?>
                <div class="bg-red-500/10 border border-red-500/50 text-red-400 p-4 rounded-xl mb-6 flex items-center">
                    <i data-lucide="alert-circle" class="w-5 h-5 mr-3 flex-shrink-0"></i>
                    <span class="font-bold"><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-6">
                <!-- Row 1: Name & Game -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-slate-300 text-sm font-bold mb-2 ml-1">SERVER NAME *</label>
                        <div class="relative">
                             <i data-lucide="type" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-purple-400 w-5 h-5"></i>
                             <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" 
                                class="w-full bg-slate-900/50 border border-slate-700 rounded-xl py-3 pl-12 pr-4 text-white focus:ring-2 focus:ring-purple-500 outline-none transition-all placeholder-slate-600"
                                placeholder="e.g. Best CS2 Server" required />
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-300 text-sm font-bold mb-2 ml-1">GAME TYPE *</label>
                        <div class="relative">
                             <i data-lucide="gamepad-2" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-pink-400 w-5 h-5"></i>
                             <select name="game" class="w-full bg-slate-900/50 border border-slate-700 rounded-xl py-3 pl-12 pr-4 text-white focus:ring-2 focus:ring-pink-500 outline-none transition-all appearance-none cursor-pointer">
                                <option value="" disabled selected>Select a game...</option>
                                <?php foreach($games as $g): ?>
                                    <option value="<?= $g ?>" <?= (isset($_POST['game']) && $_POST['game'] === $g) ? 'selected' : '' ?>><?= $g ?></option>
                                <?php endforeach; ?>
                             </select>
                             <i data-lucide="chevron-down" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-500 w-4 h-4 pointer-events-none"></i>
                        </div>
                    </div>
                </div>

                <!-- Row 2: IP & Port -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-slate-300 text-sm font-bold mb-2 ml-1">IP ADDRESS / HOSTNAME *</label>
                        <div class="relative">
                             <i data-lucide="globe" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-cyan-400 w-5 h-5"></i>
                             <input type="text" name="ip" value="<?= htmlspecialchars($_POST['ip'] ?? '') ?>" 
                                class="w-full bg-slate-900/50 border border-slate-700 rounded-xl py-3 pl-12 pr-4 text-white focus:ring-2 focus:ring-cyan-500 outline-none transition-all placeholder-slate-600"
                                placeholder="e.g. 192.168.1.1 or play.myserver.com" required />
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-300 text-sm font-bold mb-2 ml-1">PORT *</label>
                        <div class="relative">
                             <i data-lucide="hash" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-orange-400 w-5 h-5"></i>
                             <input type="number" name="port" value="<?= htmlspecialchars($_POST['port'] ?? '27015') ?>" 
                                class="w-full bg-slate-900/50 border border-slate-700 rounded-xl py-3 pl-12 pr-4 text-white focus:ring-2 focus:ring-orange-500 outline-none transition-all placeholder-slate-600"
                                placeholder="27015" required />
                        </div>
                    </div>
                </div>

                <!-- Banner -->
                <div>
                    <label class="block text-slate-300 text-sm font-bold mb-2 ml-1">BANNER URL (Optional)</label>
                    <div class="relative">
                         <i data-lucide="image" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-emerald-400 w-5 h-5"></i>
                         <input type="url" name="banner_url" value="<?= htmlspecialchars($_POST['banner_url'] ?? '') ?>" 
                            class="w-full bg-slate-900/50 border border-slate-700 rounded-xl py-3 pl-12 pr-4 text-white focus:ring-2 focus:ring-emerald-500 outline-none transition-all placeholder-slate-600"
                            placeholder="https://imgur.com/..." />
                    </div>
                    <p class="text-xs text-slate-500 mt-1 ml-1">Recommended size: 800x300px. Leave empty for default.</p>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-slate-300 text-sm font-bold mb-2 ml-1">DESCRIPTION</label>
                    <div class="relative">
                         <textarea name="description" rows="4" 
                            class="w-full bg-slate-900/50 border border-slate-700 rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-purple-500 outline-none transition-all placeholder-slate-600 resize-none"
                            placeholder="Tell players why they should join your server..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Submit -->
                <div class="pt-4 flex items-center justify-end space-x-4">
                    <button type="button" onclick="window.history.back()" class="px-6 py-3 font-bold text-slate-400 hover:text-white transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg btn-pulse-green flex items-center">
                        <i data-lucide="plus-circle" class="w-5 h-5 mr-2"></i> Publish Server
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>