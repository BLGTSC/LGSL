<?php
// Enable Error Reporting for Debugging (Disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // Start PHP Session for user management

require_once 'backend/mock_data.php';
require_once 'backend/counter.php'; // Include the counter script

// Track Visitor
$visitorCount = trackVisitor();

// Simple Router
$page = isset($_GET['p']) ? $_GET['p'] : 'home';
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Handle Logout
if ($page === 'logout') {
    unset($_SESSION['user']);
    header("Location: index.php");
    exit();
}

// Handle Voting
if ($page === 'vote' && $id) {
    $result = voteForServer($id);
    $msgType = $result['success'] ? 'success' : 'error';
    $msg = urlencode($result['message']);
    
    // Redirect back to home with message
    header("Location: index.php?msg_type=$msgType&msg=$msg");
    exit();
}

// Page Title Logic
$pageTitle = "CSX16-SERVER STATS";
if($page === 'server' && $id) {
    $server = getServerById($id);
    if($server) $pageTitle = $server['name'] . " - Stats";
} elseif ($page === 'login') {
    $pageTitle = "Login / Register - CSX16";
} elseif ($page === 'dashboard') {
    $pageTitle = "My Dashboard - CSX16";
} elseif ($page === 'add_server') {
    $pageTitle = "Add New Server - CSX16";
}

// Helper to check auth
$isLoggedIn = isset($_SESSION['user']);
$currentUser = $isLoggedIn ? $_SESSION['user'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    
    <!-- Icons (Lucide implementation for vanilla HTML) -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Chart.js (Replacement for Recharts) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
      body {
        font-family: 'Inter', sans-serif;
        background-color: #0f172a;
        background-image: 
          radial-gradient(at 0% 0%, rgba(56, 189, 248, 0.1) 0px, transparent 50%), 
          radial-gradient(at 100% 0%, rgba(232, 121, 249, 0.1) 0px, transparent 50%), 
          radial-gradient(at 100% 100%, rgba(99, 102, 241, 0.1) 0px, transparent 50%);
        background-attachment: fixed;
        color: #f8fafc;
        margin: 0;
      }
      ::-webkit-scrollbar { width: 8px; }
      ::-webkit-scrollbar-track { background: #0f172a; }
      ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
      ::-webkit-scrollbar-thumb:hover { background: #475569; }

      @keyframes neon-pulse {
        0% { box-shadow: 0 0 0 0 rgba(139, 92, 246, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(139, 92, 246, 0); }
        100% { box-shadow: 0 0 0 0 rgba(139, 92, 246, 0); }
      }
      .btn-pulse { animation: neon-pulse 2s infinite; }
      .btn-pulse-green { animation: neon-pulse-green 2s infinite; }
      
      .glass-panel {
        background: rgba(30, 41, 59, 0.7);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
      }
      
      /* Toast Animation */
      @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
      }
      .toast { animation: slideIn 0.5s ease-out forwards; }
    </style>
</head>
<body class="flex flex-col min-h-screen">

    <!-- NAVBAR -->
    <nav class="glass-panel sticky top-0 z-50 border-b border-slate-700/50 shadow-lg shadow-purple-900/10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
          <a href="index.php" class="flex items-center cursor-pointer group">
            <div class="relative">
              <div class="absolute -inset-1 bg-gradient-to-r from-pink-600 to-purple-600 rounded-full blur opacity-25 group-hover:opacity-75 transition duration-200"></div>
              <i data-lucide="server" class="relative h-9 w-9 text-white"></i>
            </div>
            <span class="ml-3 text-2xl font-black bg-gradient-to-r from-white via-purple-200 to-pink-200 text-transparent bg-clip-text drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)]">
              CSX16<span class="text-purple-400">-</span>SERVER STATS
            </span>
          </a>
          
          <div class="hidden lg:flex items-center space-x-4">
              <a href="index.php" class="text-slate-200 hover:text-white hover:bg-white/10 px-4 py-2 rounded-lg font-bold transition-all border border-transparent hover:border-white/10">
                Server List
              </a>
              <a href="https://csx16.ro" target="_blank" class="group relative px-4 py-2 rounded-lg font-bold text-white transition-all overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-r from-cyan-600 to-blue-600 opacity-80 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative flex items-center">
                  <i data-lucide="message-square" class="w-4 h-4 mr-2"></i> Forum
                </div>
              </a>
              <a href="https://discord.com/channels/1073450734298857534/1337067039914856562" target="_blank" class="group relative px-4 py-2 rounded-lg font-bold text-white transition-all overflow-hidden btn-pulse">
                 <div class="absolute inset-0 bg-[#5865F2] group-hover:bg-[#4752C4] transition-colors"></div>
                 <div class="relative flex items-center">
                   <i data-lucide="message-circle" class="w-4 h-4 mr-2"></i> Discord
                 </div>
              </a>
              
              <div class="h-8 w-px bg-slate-700 mx-2"></div>

              <?php if($isLoggedIn): ?>
                <!-- Logged In State -->
                <div class="flex items-center space-x-4">
                  <div class="flex items-center space-x-2 bg-slate-800/50 py-1 px-3 rounded-full border border-slate-700">
                    <div class="h-8 w-8 rounded-full bg-purple-600 flex items-center justify-center font-bold text-white border-2 border-purple-400">
                        <?= strtoupper(substr($currentUser['username'], 0, 1)) ?>
                    </div>
                    <span class="text-sm font-bold text-purple-200"><?= htmlspecialchars($currentUser['username']) ?></span>
                  </div>
                  
                  <?php if(isset($currentUser['role']) && $currentUser['role'] === 'admin'): ?>
                    <a href="#" class="text-red-400 hover:text-red-300 font-bold px-2">Admin</a>
                  <?php endif; ?>

                   <a href="index.php?p=add_server" 
                      class="bg-gradient-to-r from-emerald-500 to-emerald-700 hover:from-emerald-400 hover:to-emerald-600 text-white px-4 py-2 rounded-lg font-bold flex items-center shadow-lg shadow-emerald-500/20 border border-emerald-400/20">
                    <i data-lucide="plus-circle" class="h-4 w-4 mr-2"></i> Add Server
                  </a>
                  
                  <a href="index.php?p=logout" class="text-slate-400 hover:text-white p-2 hover:bg-red-500/20 rounded-full transition-colors">
                    <i data-lucide="log-out" class="h-5 w-5"></i>
                  </a>
                </div>
              <?php else: ?>
                <!-- Guest State -->
                <a href="index.php?p=login" class="relative px-6 py-2 rounded-lg font-bold text-white shadow-lg overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-r from-violet-600 to-fuchsia-600 group-hover:from-violet-500 group-hover:to-fuchsia-500 transition-colors"></div>
                    <span class="relative flex items-center">Login / Register</span>
                </a>
              <?php endif; ?>
          </div>
        </div>
      </div>
    </nav>

    <!-- Global Toast Notification -->
    <?php if(isset($_GET['msg'])): ?>
    <div class="fixed bottom-5 right-5 toast z-50">
        <div class="flex items-center w-full max-w-xs p-4 rounded-lg shadow-2xl border 
            <?= $_GET['msg_type'] === 'success' ? 'bg-slate-800 text-emerald-400 border-emerald-500/50' : 'bg-slate-800 text-red-400 border-red-500/50' ?>">
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg <?= $_GET['msg_type'] === 'success' ? 'bg-emerald-500/20 text-emerald-500' : 'bg-red-500/20 text-red-500' ?>">
                <i data-lucide="<?= $_GET['msg_type'] === 'success' ? 'check' : 'alert-circle' ?>" class="w-5 h-5"></i>
            </div>
            <div class="ml-3 text-sm font-normal text-white"><?= htmlspecialchars($_GET['msg']) ?></div>
            <button type="button" onclick="this.parentElement.remove()" class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-gray-400 hover:text-white rounded-lg p-1.5 inline-flex h-8 w-8">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    </div>
    <?php endif; ?>

    <!-- MAIN CONTENT -->
    <main class="flex-grow">
        <?php 
        // Simple View Router
        $viewFile = "views/$page.php";
        if (file_exists($viewFile)) {
             if (($page === 'add_server' || $page === 'dashboard') && !$isLoggedIn) {
                 echo "<script>window.location.href='index.php?p=login';</script>";
             } else {
                 include $viewFile;
             }
        } else {
             echo '<div class="p-20 text-center text-xl text-slate-400">Page not found (404)</div>';
        }
        ?>
    </main>

    <!-- FOOTER -->
    <footer class="bg-[#0b1120] py-10 border-t border-slate-800 mt-12 relative overflow-hidden">
        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-full max-w-3xl h-1 bg-gradient-to-r from-transparent via-purple-500 to-transparent blur-sm"></div>
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h3 class="text-xl font-black text-white mb-2 tracking-widest">CSX16-SERVER STATS</h3>
            <p class="text-slate-500 text-sm font-medium mb-6">
               Creat de <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400 font-bold">SILVIU ENACHE</span>
            </p>
            
            <!-- Unique Visitor Counter Badge -->
            <div class="inline-flex items-center bg-slate-900/50 rounded-full px-4 py-1 border border-slate-700/50 shadow-inner">
                <span class="flex h-2 w-2 relative mr-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-xs font-bold text-slate-400 mr-2">UNIQUE VISITORS:</span>
                <span class="text-sm font-mono font-bold text-white tracking-widest"><?= number_format($visitorCount) ?></span>
            </div>
        </div>
    </footer>

    <script>
        // Initialize Icons
        lucide.createIcons();
    </script>
</body>
</html>