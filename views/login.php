<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $isRegister = isset($_POST['register_mode']);

    // MOCK AUTHENTICATION (Simulating database check)
    // In production, use password_verify() and SQL queries
    if ($username && $password) {
        // FIXED: Use crc32 to generate a consistent ID based on the username.
        // This ensures that if you log out and back in as 'admin', you get the SAME ID,
        // and can still see your servers.
        $consistentId = crc32($username);

        $_SESSION['user'] = [
            'id' => $consistentId, 
            'username' => htmlspecialchars($username),
            'email' => strtolower($username) . '@example.com',
            'role' => ($username === 'admin') ? 'admin' : 'user',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Redirect to dashboard
        echo "<script>window.location.href='index.php?p=dashboard';</script>";
        exit;
    }
}
?>

<div class="flex items-center justify-center min-h-[calc(100vh-80px)] px-4 py-12">
    <div class="w-full max-w-md glass-panel p-8 rounded-2xl shadow-2xl relative overflow-hidden animate-fade-in">
        <!-- Background glow effect -->
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-violet-500 via-fuchsia-500 to-orange-500"></div>
        
        <h2 id="formTitle" class="text-4xl font-black text-white text-center mb-2 tracking-tight">
          Welcome Back
        </h2>
        <p id="formSubtitle" class="text-slate-400 text-center mb-8 font-medium">
          Login to access your dashboard
        </p>
        
        <form method="POST" action="" class="space-y-5">
            <div>
                <label class="block text-slate-300 text-sm font-bold mb-2 ml-1">USERNAME</label>
                <div class="relative">
                    <i data-lucide="user" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-purple-400 w-5 h-5"></i>
                    <input 
                        type="text" 
                        name="username"
                        class="w-full bg-slate-900/50 border border-slate-700 rounded-xl py-3 pl-12 pr-4 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all placeholder-slate-600"
                        placeholder="Enter username"
                        required
                    />
                </div>
            </div>

            <!-- Email field, hidden by default, shown via JS for registration -->
            <div id="emailField" class="hidden">
              <label class="block text-slate-300 text-sm font-bold mb-2 ml-1">EMAIL ADDRESS</label>
              <div class="relative">
                  <i data-lucide="mail" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-pink-400 w-5 h-5"></i>
                  <input 
                      type="email" 
                      name="email"
                      class="w-full bg-slate-900/50 border border-slate-700 rounded-xl py-3 pl-12 pr-4 text-white focus:ring-2 focus:ring-pink-500 focus:border-transparent outline-none transition-all placeholder-slate-600"
                      placeholder="name@example.com"
                  />
              </div>
            </div>

            <div>
                <label class="block text-slate-300 text-sm font-bold mb-2 ml-1">PASSWORD</label>
                <div class="relative">
                    <i data-lucide="lock" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-orange-400 w-5 h-5"></i>
                    <input 
                        type="password" 
                        name="password"
                        class="w-full bg-slate-900/50 border border-slate-700 rounded-xl py-3 pl-12 pr-4 text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition-all placeholder-slate-600"
                        placeholder="••••••••"
                        required
                    />
                </div>
            </div>

            <input type="hidden" name="register_mode" id="registerMode" value="0">

            <button 
                type="submit" 
                class="w-full font-bold text-lg py-4 rounded-xl transition-all flex items-center justify-center mt-4 btn-pulse bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white shadow-lg"
            >
                <span id="submitBtnText">LOGIN NOW</span>
                <i data-lucide="arrow-right" class="ml-2 w-5 h-5"></i>
            </button>
        </form>

        <div class="mt-8 text-center pt-6 border-t border-slate-700/50">
          <p class="text-slate-400 font-medium">
            <span id="toggleText">Don't have an account?</span>
            <button 
              type="button"
              onclick="toggleMode()"
              class="ml-2 text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-fuchsia-400 font-bold hover:brightness-125 transition-all outline-none"
            >
              <span id="toggleBtn">Register Now</span>
            </button>
          </p>
        </div>
    </div>
</div>

<script>
    function toggleMode() {
        const emailField = document.getElementById('emailField');
        const formTitle = document.getElementById('formTitle');
        const formSubtitle = document.getElementById('formSubtitle');
        const submitBtnText = document.getElementById('submitBtnText');
        const toggleText = document.getElementById('toggleText');
        const toggleBtn = document.getElementById('toggleBtn');
        const registerModeInput = document.getElementById('registerMode');

        if (emailField.classList.contains('hidden')) {
            // Switch to Register
            emailField.classList.remove('hidden');
            formTitle.textContent = 'Join the Squad';
            formSubtitle.textContent = 'Create your profile to manage servers';
            submitBtnText.textContent = 'CREATE ACCOUNT';
            toggleText.textContent = 'Already have an account?';
            toggleBtn.textContent = 'Sign In';
            registerModeInput.value = "1";
        } else {
            // Switch to Login
            emailField.classList.add('hidden');
            formTitle.textContent = 'Welcome Back';
            formSubtitle.textContent = 'Login to access your dashboard';
            submitBtnText.textContent = 'LOGIN NOW';
            toggleText.textContent = "Don't have an account?";
            toggleBtn.textContent = 'Register Now';
            registerModeInput.value = "0";
        }
    }
</script>