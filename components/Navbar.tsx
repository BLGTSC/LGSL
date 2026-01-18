import React from 'react';
import { Menu, User as UserIcon, LogOut, Server, PlusCircle, MessageSquare, MessageCircle } from 'lucide-react';
import { User } from '../types';

interface NavbarProps {
  user: User | null;
  onLogout: () => void;
  onNavigate: (page: string) => void;
}

const Navbar: React.FC<NavbarProps> = ({ user, onLogout, onNavigate }) => {
  const [isOpen, setIsOpen] = React.useState(false);

  return (
    <nav className="glass-panel sticky top-0 z-50 border-b border-slate-700/50 shadow-lg shadow-purple-900/10">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-20">
          <div className="flex items-center cursor-pointer group" onClick={() => onNavigate('home')}>
            <div className="relative">
              <div className="absolute -inset-1 bg-gradient-to-r from-pink-600 to-purple-600 rounded-full blur opacity-25 group-hover:opacity-75 transition duration-200"></div>
              <Server className="relative h-9 w-9 text-white" />
            </div>
            <span className="ml-3 text-2xl font-black bg-gradient-to-r from-white via-purple-200 to-pink-200 text-transparent bg-clip-text drop-shadow-[0_2px_2px_rgba(0,0,0,0.8)]">
              CSX16<span className="text-purple-400">-</span>SERVER STATS
            </span>
          </div>
          
          <div className="hidden lg:flex items-center space-x-4">
              <button onClick={() => onNavigate('home')} className="text-slate-200 hover:text-white hover:bg-white/10 px-4 py-2 rounded-lg font-bold transition-all border border-transparent hover:border-white/10">
                Server List
              </button>

              <a 
                href="https://csx16.ro" 
                target="_blank" 
                rel="noopener noreferrer"
                className="group relative px-4 py-2 rounded-lg font-bold text-white transition-all overflow-hidden"
              >
                <div className="absolute inset-0 bg-gradient-to-r from-cyan-600 to-blue-600 opacity-80 group-hover:opacity-100 transition-opacity"></div>
                <div className="relative flex items-center">
                  <MessageSquare className="w-4 h-4 mr-2" /> Forum
                </div>
              </a>

              <a 
                href="https://discord.com/channels/1073450734298857534/1337067039914856562" 
                target="_blank" 
                rel="noopener noreferrer"
                className="group relative px-4 py-2 rounded-lg font-bold text-white transition-all overflow-hidden btn-pulse"
              >
                 <div className="absolute inset-0 bg-[#5865F2] group-hover:bg-[#4752C4] transition-colors"></div>
                 <div className="relative flex items-center">
                   <MessageCircle className="w-4 h-4 mr-2" /> Discord
                 </div>
              </a>

              <div className="h-8 w-px bg-slate-700 mx-2"></div>

              {user ? (
                <div className="flex items-center space-x-4">
                  <div className="flex items-center space-x-2 bg-slate-800/50 py-1 px-3 rounded-full border border-slate-700">
                    <img src={user.avatarUrl} alt="avatar" className="h-8 w-8 rounded-full border-2 border-purple-500" />
                    <span className="text-sm font-bold text-purple-200">{user.username}</span>
                  </div>
                  
                  {user.role === 'admin' && (
                    <button onClick={() => onNavigate('admin')} className="text-red-400 hover:text-red-300 font-bold px-2">
                      Admin
                    </button>
                  )}

                   <button 
                      onClick={() => onNavigate('add-server')} 
                      className="bg-gradient-to-r from-emerald-500 to-emerald-700 hover:from-emerald-400 hover:to-emerald-600 text-white px-4 py-2 rounded-lg font-bold flex items-center shadow-lg shadow-emerald-500/20 btn-pulse-green border border-emerald-400/20"
                   >
                    <PlusCircle className="h-4 w-4 mr-2" /> Add Server
                  </button>
                  
                  <button onClick={onLogout} className="text-slate-400 hover:text-white p-2 hover:bg-red-500/20 rounded-full transition-colors">
                    <LogOut className="h-5 w-5" />
                  </button>
                </div>
              ) : (
                <button 
                  onClick={() => onNavigate('login')}
                  className="relative px-6 py-2 rounded-lg font-bold text-white shadow-lg overflow-hidden group btn-pulse"
                >
                  <div className="absolute inset-0 bg-gradient-to-r from-violet-600 to-fuchsia-600 group-hover:from-violet-500 group-hover:to-fuchsia-500 transition-colors"></div>
                  <span className="relative flex items-center">
                    Login / Register
                  </span>
                </button>
              )}
          </div>

          <div className="flex lg:hidden">
            <button
              onClick={() => setIsOpen(!isOpen)}
              className="text-slate-300 hover:text-white p-2"
            >
              <Menu className="h-8 w-8" />
            </button>
          </div>
        </div>
      </div>

      {/* Mobile menu */}
      {isOpen && (
        <div className="lg:hidden bg-slate-900 border-t border-slate-800">
          <div className="px-4 pt-4 pb-6 space-y-3">
            <button onClick={() => { onNavigate('home'); setIsOpen(false); }} className="text-slate-300 hover:text-white hover:bg-slate-800 block px-4 py-3 rounded-lg text-lg font-bold w-full text-left">
              Server List
            </button>
            
            <a href="https://csx16.ro" className="bg-gradient-to-r from-cyan-900/50 to-blue-900/50 border border-cyan-700/50 text-cyan-100 block px-4 py-3 rounded-lg text-lg font-bold w-full text-left flex items-center">
               <MessageSquare className="w-5 h-5 mr-3" /> Forum
            </a>

            <a href="https://discord.com/channels/1073450734298857534/1337067039914856562" className="bg-[#5865F2]/20 border border-[#5865F2]/50 text-[#5865F2] block px-4 py-3 rounded-lg text-lg font-bold w-full text-left flex items-center">
               <MessageCircle className="w-5 h-5 mr-3" /> Discord
            </a>

            <div className="border-t border-slate-800 my-2"></div>

             {user ? (
                <>
                  <button onClick={() => { onNavigate('dashboard'); setIsOpen(false); }} className="text-purple-400 block px-4 py-3 font-bold w-full text-left">
                    Dashboard
                  </button>
                  <button onClick={onLogout} className="text-red-400 block px-4 py-3 font-bold w-full text-left">
                     Logout
                  </button>
                </>
             ) : (
                <button onClick={() => { onNavigate('login'); setIsOpen(false); }} className="bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white block px-4 py-3 rounded-lg text-lg font-bold w-full text-center shadow-lg">
                   Login / Register
                </button>
             )}
          </div>
        </div>
      )}
    </nav>
  );
};

export default Navbar;