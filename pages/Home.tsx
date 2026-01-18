import React, { useEffect, useState } from 'react';
import { GameServer, GameType } from '../types';
import { MockService } from '../services/mockService';
import ServerRow from '../components/ServerRow';
import { Search, Filter, Loader2, Trophy } from 'lucide-react';

interface HomeProps {
    onNavigate: (page: string, id?: string) => void;
}

const Home: React.FC<HomeProps> = ({ onNavigate }) => {
  const [servers, setServers] = useState<GameServer[]>([]);
  const [loading, setLoading] = useState(true);
  const [filterGame, setFilterGame] = useState<string>('All');
  const [searchTerm, setSearchTerm] = useState('');

  const fetchServers = async () => {
    setLoading(true);
    const data = await MockService.getServers();
    setServers(data);
    setLoading(false);
  };

  useEffect(() => {
    fetchServers();
  }, []);

  const handleVote = async (id: string) => {
    const result = await MockService.voteForServer(id);
    if (result.success) {
      alert(result.message);
      // Optimistically update
      setServers(prev => prev.map(s => s.id === id ? { ...s, votes: (result.newVotes || s.votes) } : s).sort((a,b) => b.votes - a.votes));
    } else {
      alert(result.message);
    }
  };

  const filteredServers = servers.filter(s => {
    const matchesGame = filterGame === 'All' || s.game === filterGame;
    const matchesSearch = s.name.toLowerCase().includes(searchTerm.toLowerCase()) || 
                          s.map.toLowerCase().includes(searchTerm.toLowerCase());
    return matchesGame && matchesSearch;
  });

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
      {/* Hero Section */}
      <div className="mb-10 text-center relative">
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-3/4 h-24 bg-purple-500/20 blur-[100px] rounded-full -z-10"></div>
        <h1 className="text-5xl md:text-7xl font-black text-white mb-4 tracking-tight drop-shadow-2xl">
          CSX16 <span className="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-pink-500 to-red-500">RANKING</span>
        </h1>
        <p className="text-slate-300 text-lg md:text-xl font-medium max-w-2xl mx-auto">
          Top servers, real-time stats, and the best community. Vote for your favorite server today!
        </p>
      </div>

      {/* Controls */}
      <div className="glass-panel p-2 rounded-2xl mb-8 flex flex-col md:flex-row gap-2 shadow-xl shadow-purple-900/10">
        <div className="relative w-full">
            <Search className="absolute left-4 top-1/2 transform -translate-y-1/2 text-purple-400 w-5 h-5" />
            <input 
                type="text" 
                placeholder="Find a server or map..." 
                className="w-full bg-slate-900/50 border border-slate-700/50 text-white rounded-xl pl-12 pr-4 py-4 focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all placeholder-slate-500"
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
            />
        </div>

        <div className="relative w-full md:w-64">
            <Filter className="absolute left-4 top-1/2 transform -translate-y-1/2 text-pink-400 w-5 h-5" />
            <select 
                className="w-full bg-slate-900/50 border border-slate-700/50 text-white rounded-xl pl-12 pr-10 py-4 focus:ring-2 focus:ring-pink-500 outline-none appearance-none cursor-pointer"
                value={filterGame}
                onChange={(e) => setFilterGame(e.target.value)}
            >
                <option value="All">All Games</option>
                {Object.values(GameType).map(g => (
                    <option key={g} value={g}>{g}</option>
                ))}
            </select>
        </div>
      </div>

      {/* List */}
      <div className="glass-panel rounded-2xl overflow-hidden shadow-2xl min-h-[400px]">
        {loading ? (
            <div className="flex flex-col items-center justify-center h-80 text-slate-500">
                <Loader2 className="w-12 h-12 animate-spin mb-4 text-purple-500" />
                <p className="font-semibold text-lg">Scanning frequencies...</p>
            </div>
        ) : filteredServers.length > 0 ? (
            <div>
                 {/* Table Header (Desktop only) */}
                <div className="hidden sm:flex bg-slate-950/50 text-purple-300 text-xs font-black uppercase tracking-widest py-4 px-4 border-b border-slate-800">
                    <div className="w-12 text-center">#</div>
                    <div className="w-6"></div>
                    <div className="flex-grow pl-2">Server Details</div>
                    <div className="w-40">Status</div>
                    <div className="w-20 text-center">Votes</div>
                </div>
                {filteredServers.map(server => (
                    <ServerRow 
                        key={server.id} 
                        server={server} 
                        onVote={handleVote}
                        onClick={(id) => onNavigate('server-detail', id)}
                    />
                ))}
            </div>
        ) : (
            <div className="p-12 text-center">
                <Trophy className="w-16 h-16 text-slate-700 mx-auto mb-4" />
                <h3 className="text-xl font-bold text-slate-300">No servers found</h3>
                <p className="text-slate-500">Try adjusting your search filters.</p>
            </div>
        )}
      </div>
    </div>
  );
};

export default Home;