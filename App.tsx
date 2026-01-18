import React, { useState } from 'react';
import Navbar from './components/Navbar';
import Home from './pages/Home';
import ServerDetail from './pages/ServerDetail';
import { User } from './types';
import { MockService } from './services/mockService';
import { ArrowRight, Lock, Mail, User as UserIcon, Loader2 } from 'lucide-react';

// --- Simple Inline Pages for Dashboard/Login to save file count but maintain structure ---

const Login: React.FC<{ onLogin: (u: User) => void }> = ({ onLogin }) => {
  const [isRegistering, setIsRegistering] = useState(false);
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [email, setEmail] = useState('');
  const [isLoading, setIsLoading] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!username || !password) return;
    if (isRegistering && !email) return;

    setIsLoading(true);
    // In this mock implementation, login acts as register if user doesn't exist
    const user = await MockService.login(username);
    setIsLoading(false);
    onLogin(user);
  };

  return (
    <div className="flex items-center justify-center min-h-[calc(100vh-80px)] px-4 py-12">
      <div className="w-full max-w-md glass-panel p-8 rounded-2xl shadow-2xl relative overflow-hidden animate-fade-in">
        {/* Background glow effect */}
        <div className="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-violet-500 via-fuchsia-500 to-orange-500"></div>
        
        <h2 className="text-4xl font-black text-white text-center mb-2 tracking-tight">
          {isRegistering ? 'Join the Squad' : 'Welcome Back'}
        </h2>
        <p className="text-slate-400 text-center mb-8 font-medium">
          {isRegistering ? 'Create your profile to manage servers' : 'Login to access your dashboard'}
        </p>
        
        <form onSubmit={handleSubmit} className="space-y-5">
          <div>
            <label className="block text-slate-300 text-sm font-bold mb-2 ml-1">USERNAME</label>
            <div className="relative">
                <UserIcon className="absolute left-4 top-1/2 transform -translate-y-1/2 text-purple-400 w-5 h-5" />
                <input 
                    type="text" 
                    value={username}
                    onChange={e => setUsername(e.target.value)}
                    className="w-full bg-slate-900/50 border border-slate-700 rounded-xl py-3 pl-12 pr-4 text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all placeholder-slate-600"
                    placeholder="Enter username"
                    required
                />
            </div>
          </div>

          {isRegistering && (
            <div className="animate-fade-in-up">
              <label className="block text-slate-300 text-sm font-bold mb-2 ml-1">EMAIL ADDRESS</label>
              <div className="relative">
                  <Mail className="absolute left-4 top-1/2 transform -translate-y-1/2 text-pink-400 w-5 h-5" />
                  <input 
                      type="email" 
                      value={email}
                      onChange={e => setEmail(e.target.value)}
                      className="w-full bg-slate-900/50 border border-slate-700 rounded-xl py-3 pl-12 pr-4 text-white focus:ring-2 focus:ring-pink-500 focus:border-transparent outline-none transition-all placeholder-slate-600"
                      placeholder="name@example.com"
                      required
                  />
              </div>
            </div>
          )}

          <div>
            <label className="block text-slate-300 text-sm font-bold mb-2 ml-1">PASSWORD</label>
            <div className="relative">
                <Lock className="absolute left-4 top-1/2 transform -translate-y-1/2 text-orange-400 w-5 h-5" />
                <input 
                    type="password" 
                    value={password}
                    onChange={e => setPassword(e.target.value)}
                    className="w-full bg-slate-900/50 border border-slate-700 rounded-xl py-3 pl-12 pr-4 text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition-all placeholder-slate-600"
                    placeholder="••••••••"
                    required
                />
            </div>
          </div>

          <button 
            type="submit" 
            disabled={isLoading}
            className={`w-full font-bold text-lg py-4 rounded-xl transition-all flex items-center justify-center mt-4 btn-pulse ${
              isLoading ? 'bg-slate-700 cursor-not-allowed' : 'bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white shadow-lg'
            }`}
          >
            {isLoading ? <Loader2 className="animate-spin w-6 h-6" /> : (isRegistering ? 'CREATE ACCOUNT' : 'LOGIN NOW')} 
            {!isLoading && <ArrowRight className="ml-2 w-5 h-5" />}
          </button>
        </form>

        <div className="mt-8 text-center pt-6 border-t border-slate-700/50">
          <p className="text-slate-400 font-medium">
            {isRegistering ? 'Already have an account?' : "Don't have an account?"}
            <button 
              onClick={() => setIsRegistering(!isRegistering)}
              className="ml-2 text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-fuchsia-400 font-bold hover:brightness-125 transition-all outline-none"
            >
              {isRegistering ? 'Sign In' : 'Register Now'}
            </button>
          </p>
        </div>
      </div>
    </div>
  );
};

const Dashboard: React.FC<{ user: User }> = ({ user }) => {
    return (
        <div className="max-w-7xl mx-auto px-4 py-8">
            <h1 className="text-3xl font-black text-white mb-6">My Dashboard</h1>
            <div className="glass-panel rounded-2xl p-12 text-center border-0">
                <div className="bg-slate-800 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 ring-4 ring-purple-500/30">
                    <UserIcon className="w-12 h-12 text-purple-400" />
                </div>
                <h2 className="text-2xl font-bold text-white">Hello, <span className="text-purple-400">{user.username}</span>!</h2>
                <p className="text-slate-400 mt-2 text-lg">You haven't added any servers yet.</p>
                <button className="mt-8 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg btn-pulse-green">
                    Add Your First Server
                </button>
            </div>
        </div>
    );
}

// --- MAIN APP COMPONENT ---

const App: React.FC = () => {
  const [currentPage, setCurrentPage] = useState<string>('home');
  const [currentServerId, setCurrentServerId] = useState<string | undefined>(undefined);
  const [user, setUser] = useState<User | null>(null);

  const navigate = (page: string, id?: string) => {
    setCurrentPage(page);
    if (id) setCurrentServerId(id);
    window.scrollTo(0, 0);
  };

  const handleLogin = (u: User) => {
    setUser(u);
    navigate('home');
  };

  const handleLogout = () => {
    setUser(null);
    navigate('home');
  };

  return (
    <div className="min-h-screen font-sans selection:bg-fuchsia-500 selection:text-white flex flex-col">
      <Navbar user={user} onLogout={handleLogout} onNavigate={navigate} />
      
      <main className="flex-grow">
        {currentPage === 'home' && <Home onNavigate={navigate} />}
        {currentPage === 'server-detail' && currentServerId && (
            <ServerDetail serverId={currentServerId} onBack={() => navigate('home')} />
        )}
        {currentPage === 'login' && <Login onLogin={handleLogin} />}
        {currentPage === 'dashboard' && user && <Dashboard user={user} />}
        {(currentPage === 'admin' || currentPage === 'add-server') && (
            <div className="p-20 text-center text-slate-400">
                <p className="text-2xl font-bold text-white mb-4">Work in Progress</p>
                <p className="text-xl">This feature is part of the mock.</p>
                <button onClick={() => navigate('home')} className="mt-6 px-6 py-2 bg-slate-800 rounded-lg text-purple-400 font-bold hover:bg-slate-700">Go Home</button>
            </div>
        )}
      </main>

      <footer className="bg-[#0b1120] py-10 border-t border-slate-800 mt-12 relative overflow-hidden">
        {/* Decorative footer glow */}
        <div className="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-full max-w-3xl h-1 bg-gradient-to-r from-transparent via-purple-500 to-transparent blur-sm"></div>
        <div className="max-w-7xl mx-auto px-4 text-center">
            <h3 className="text-xl font-black text-white mb-2 tracking-widest">CSX16-SERVER STATS</h3>
            <p className="text-slate-500 text-sm font-medium">
               Creat de <span className="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-pink-400 font-bold">SILVIU ENACHE</span>
            </p>
        </div>
      </footer>
    </div>
  );
};

export default App;