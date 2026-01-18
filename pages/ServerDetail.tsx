import React, { useEffect, useState } from 'react';
import { GameServer, ServerStatus } from '../types';
import { MockService } from '../services/mockService';
import { COUNTRY_FLAGS, GAME_IMAGES } from '../constants';
import { AreaChart, Area, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts';
import { ArrowLeft, Share2, Globe, Clock, Shield, Code, BarChart2, Play } from 'lucide-react';

interface ServerDetailProps {
  serverId: string;
  onBack: () => void;
}

const ServerDetail: React.FC<ServerDetailProps> = ({ serverId, onBack }) => {
  const [server, setServer] = useState<GameServer | null>(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState<'stats' | 'widget'>('stats');

  useEffect(() => {
    const load = async () => {
      setLoading(true);
      const data = await MockService.getServerById(serverId);
      setServer(data || null);
      setLoading(false);
    };
    load();
  }, [serverId]);

  if (loading) return <div className="p-20 text-center text-white font-bold text-xl">Loading details...</div>;
  if (!server) return <div className="p-20 text-center text-red-500 font-bold text-xl">Server not found.</div>;

  const isOnline = server.status === ServerStatus.ONLINE;
  const bannerImage = GAME_IMAGES[server.game] || server.bannerUrl;

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 animate-fade-in">
        <button onClick={onBack} className="group flex items-center text-slate-400 hover:text-white mb-8 transition-colors font-medium">
            <div className="bg-slate-800 p-2 rounded-full mr-3 group-hover:bg-purple-600 transition-colors">
              <ArrowLeft className="w-4 h-4" /> 
            </div>
            Back to List
        </button>

        {/* Header Banner */}
        <div className="relative rounded-3xl overflow-hidden shadow-2xl border-2 border-slate-700/50 h-[400px] mb-10 group">
            <img src={bannerImage} alt={server.game} className="w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-1000" />
            
            {/* Gradient Overlay */}
            <div className="absolute inset-0 bg-gradient-to-t from-[#0f172a] via-[#0f172a]/80 to-transparent flex flex-col justify-end p-8 md:p-12">
                <div className="flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <div className="flex items-center space-x-3 mb-4">
                             <span className={`px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest shadow-lg ${isOnline ? 'bg-emerald-500 text-emerald-950 shadow-emerald-500/20' : 'bg-rose-500 text-white'}`}>
                                {server.status}
                             </span>
                             <span className="bg-slate-800/80 backdrop-blur text-purple-200 px-3 py-1 rounded-full text-sm font-bold border border-purple-500/30 flex items-center">
                                {COUNTRY_FLAGS[server.country]} <span className="ml-2 font-mono">{server.ip}:{server.port}</span>
                             </span>
                        </div>
                        <h1 className="text-4xl md:text-6xl font-black text-white mb-4 drop-shadow-xl tracking-tight">{server.name}</h1>
                        <p className="text-slate-300 max-w-2xl text-lg font-medium leading-relaxed">{server.description}</p>
                    </div>
                    
                    <div className="flex items-center space-x-6 glass-panel p-6 rounded-2xl border-t border-white/10">
                        <div className="text-center">
                            <div className="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-b from-orange-300 to-red-500">{server.votes}</div>
                            <div className="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Votes</div>
                        </div>
                        <div className="w-px h-10 bg-slate-700"></div>
                        <div className="text-center">
                            <div className="text-3xl font-black text-white">{server.players} <span className="text-slate-500 text-xl font-bold">/ {server.maxPlayers}</span></div>
                            <div className="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Online</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {/* Content Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {/* Left Column (Stats/Tabs) */}
            <div className="lg:col-span-2 space-y-8">
                {/* Tabs */}
                <div className="border-b-2 border-slate-800">
                    <nav className="-mb-0.5 flex space-x-8">
                        <button
                            onClick={() => setActiveTab('stats')}
                            className={`${activeTab === 'stats' ? 'border-purple-500 text-purple-400' : 'border-transparent text-slate-500 hover:text-slate-300'} whitespace-nowrap py-4 px-2 border-b-4 font-bold text-sm transition-colors`}
                        >
                            <BarChart2 className="w-4 h-4 inline mr-2" /> STATISTICS
                        </button>
                        <button
                            onClick={() => setActiveTab('widget')}
                            className={`${activeTab === 'widget' ? 'border-purple-500 text-purple-400' : 'border-transparent text-slate-500 hover:text-slate-300'} whitespace-nowrap py-4 px-2 border-b-4 font-bold text-sm transition-colors`}
                        >
                            <Code className="w-4 h-4 inline mr-2" /> WIDGETS
                        </button>
                    </nav>
                </div>

                {activeTab === 'stats' && (
                    <div className="glass-panel rounded-2xl p-6">
                        <h3 className="text-xl font-bold text-white mb-8 flex items-center">
                            <span className="w-2 h-8 bg-purple-500 rounded-full mr-3"></span>
                            Player History (24h)
                        </h3>
                        <div className="h-[350px] w-full">
                            <ResponsiveContainer width="100%" height="100%">
                                <AreaChart data={server.history}>
                                    <defs>
                                        <linearGradient id="colorPlayers" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="5%" stopColor="#8b5cf6" stopOpacity={0.6}/>
                                            <stop offset="95%" stopColor="#8b5cf6" stopOpacity={0}/>
                                        </linearGradient>
                                    </defs>
                                    <CartesianGrid strokeDasharray="3 3" stroke="#334155" vertical={false} />
                                    <XAxis dataKey="time" stroke="#94a3b8" tick={{fontSize: 12, fontWeight: 600}} axisLine={false} tickLine={false} />
                                    <YAxis stroke="#94a3b8" tick={{fontSize: 12, fontWeight: 600}} axisLine={false} tickLine={false} />
                                    <Tooltip 
                                        contentStyle={{ backgroundColor: '#0f172a', borderColor: '#4c1d95', borderRadius: '12px', color: '#f8fafc', boxShadow: '0 10px 15px -3px rgba(0, 0, 0, 0.5)' }} 
                                        itemStyle={{ color: '#c4b5fd', fontWeight: 'bold' }}
                                        cursor={{ stroke: '#8b5cf6', strokeWidth: 1 }}
                                    />
                                    <Area type="monotone" dataKey="players" stroke="#8b5cf6" strokeWidth={3} fillOpacity={1} fill="url(#colorPlayers)" />
                                </AreaChart>
                            </ResponsiveContainer>
                        </div>
                    </div>
                )}

                {activeTab === 'widget' && (
                    <div className="glass-panel rounded-2xl p-8 space-y-8">
                        <div>
                            <h3 className="text-lg font-bold text-white mb-3">Forum Signature (BBCode)</h3>
                            <div className="bg-[#0b1120] p-4 rounded-xl border border-slate-800 font-mono text-sm text-emerald-400 select-all cursor-text shadow-inner">
                                [url=https://neolgsl.com/server/{server.id}][img]https://neolgsl.com/api/badge/{server.id}.png[/img][/url]
                            </div>
                        </div>
                        <div>
                            <h3 className="text-lg font-bold text-white mb-3">Website Code (HTML)</h3>
                            <div className="bg-[#0b1120] p-4 rounded-xl border border-slate-800 font-mono text-sm text-blue-400 select-all cursor-text shadow-inner">
                                &lt;a href="https://neolgsl.com/server/{server.id}"&gt;&lt;img src="https://neolgsl.com/api/badge/{server.id}.png" /&gt;&lt;/a&gt;
                            </div>
                        </div>
                        
                        <div className="mt-6 border-t border-slate-700/50 pt-8">
                            <p className="text-sm font-bold text-slate-400 mb-4 uppercase tracking-widest">Preview</p>
                            <div className="inline-flex bg-slate-900 border border-slate-700 rounded-lg p-3 items-center space-x-4 w-full md:w-96 shadow-xl">
                                <div className={`w-1.5 h-12 rounded-full ${isOnline ? 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]' : 'bg-red-500'}`}></div>
                                <div className="flex-1 min-w-0">
                                    <div className="font-bold text-white text-sm truncate">{server.name}</div>
                                    <div className="text-xs text-slate-400 font-mono">{server.ip}:{server.port}</div>
                                </div>
                                <div className="text-right pl-2">
                                    <div className="text-emerald-400 font-black text-sm">{server.players}/{server.maxPlayers}</div>
                                    <div className="text-[9px] text-slate-600 font-bold uppercase">On</div>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </div>

            {/* Right Column (Info) */}
            <div className="space-y-6">
                <div className="glass-panel rounded-2xl p-6 border border-slate-700/50 shadow-xl">
                    <h3 className="text-lg font-black text-white mb-6 border-b border-slate-700 pb-4 tracking-wide">SERVER INFO</h3>
                    <ul className="space-y-5">
                        <li className="flex justify-between items-center">
                            <span className="text-slate-400 flex items-center font-medium text-sm"><Globe className="w-4 h-4 mr-3 text-purple-500"/> CURRENT MAP</span>
                            <span className="text-white font-bold">{server.map}</span>
                        </li>
                        <li className="flex justify-between items-center">
                            <span className="text-slate-400 flex items-center font-medium text-sm"><Shield className="w-4 h-4 mr-3 text-pink-500"/> VERSION</span>
                            <span className="text-white font-bold">{server.version}</span>
                        </li>
                        <li className="flex justify-between items-center">
                            <span className="text-slate-400 flex items-center font-medium text-sm"><Share2 className="w-4 h-4 mr-3 text-orange-500"/> GAME TYPE</span>
                            <span className="text-white font-bold">{server.game}</span>
                        </li>
                        <li className="flex justify-between items-center">
                            <span className="text-slate-400 flex items-center font-medium text-sm"><Clock className="w-4 h-4 mr-3 text-cyan-500"/> LAST CHECK</span>
                            <span className="text-emerald-400 text-sm font-bold bg-emerald-500/10 px-2 py-1 rounded">Just now</span>
                        </li>
                    </ul>
                    
                    <button 
                        className="w-full mt-8 bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-black py-4 px-4 rounded-xl shadow-lg shadow-purple-900/30 transition-all flex justify-center items-center btn-pulse group" 
                        onClick={() => window.open(`steam://connect/${server.ip}:${server.port}`)}
                    >
                        <Play className="w-5 h-5 mr-2 fill-current" /> CONNECT NOW
                    </button>
                </div>
            </div>
        </div>
    </div>
  );
};

export default ServerDetail;