import React from 'react';
import { GameServer, ServerStatus } from '../types';
import { COUNTRY_FLAGS } from '../constants';
import { Trophy, Copy } from 'lucide-react';

interface ServerRowProps {
  server: GameServer;
  onVote: (id: string) => void;
  onClick: (id: string) => void;
}

const ServerRow: React.FC<ServerRowProps> = ({ server, onVote, onClick }) => {
  const isOnline = server.status === ServerStatus.ONLINE;
  const fillPercentage = Math.min((server.players / server.maxPlayers) * 100, 100);

  const copyIp = (e: React.MouseEvent) => {
    e.stopPropagation();
    navigator.clipboard.writeText(`${server.ip}:${server.port}`);
    alert('IP Copied to clipboard!'); 
  };

  const handleVote = (e: React.MouseEvent) => {
    e.stopPropagation();
    onVote(server.id);
  };

  return (
    <div 
      onClick={() => onClick(server.id)}
      className="group relative bg-slate-900/40 hover:bg-slate-800/60 border-b border-slate-700/50 last:border-0 transition-all cursor-pointer backdrop-blur-sm"
    >
      {/* Hover Gradient Border effect via pseudo element */}
      <div className="absolute inset-0 border-l-4 border-transparent group-hover:border-purple-500 transition-all duration-300"></div>

      <div className="flex flex-col sm:flex-row items-center p-4 gap-4">
        {/* Rank */}
        <div className="flex-shrink-0 w-12 flex justify-center">
          <div className={`
            w-10 h-10 rounded-lg flex items-center justify-center font-black text-lg shadow-lg
            ${server.rank === 1 ? 'bg-gradient-to-br from-yellow-300 to-yellow-600 text-yellow-950 ring-2 ring-yellow-400/50' : 
              server.rank === 2 ? 'bg-gradient-to-br from-slate-200 to-slate-400 text-slate-800 ring-2 ring-slate-400/50' : 
              server.rank === 3 ? 'bg-gradient-to-br from-orange-300 to-orange-600 text-orange-950 ring-2 ring-orange-400/50' : 
              'bg-slate-800 text-slate-500 border border-slate-700'}
          `}>
            {server.rank}
          </div>
        </div>

        {/* Status */}
        <div className="flex-shrink-0">
          <div className={`relative w-4 h-4 rounded-full ${isOnline ? 'bg-emerald-500' : 'bg-red-500'}`}>
            {isOnline && <div className="absolute inset-0 rounded-full bg-emerald-400 animate-ping opacity-75"></div>}
          </div>
        </div>

        {/* Info */}
        <div className="flex-grow min-w-0 text-center sm:text-left w-full sm:w-auto">
          <div className="flex items-center justify-center sm:justify-start space-x-2">
            <span className="text-xl shadow-sm" title={`Country: ${server.country}`}>{COUNTRY_FLAGS[server.country] || '🌐'}</span>
            <h3 className="text-lg font-bold text-slate-100 truncate group-hover:text-transparent group-hover:bg-clip-text group-hover:bg-gradient-to-r group-hover:from-purple-400 group-hover:to-pink-400 transition-all">
              {server.name}
            </h3>
          </div>
          <div className="text-sm flex flex-wrap justify-center sm:justify-start gap-2 mt-1.5">
            <span className="bg-slate-800/80 px-2 py-0.5 rounded text-xs font-semibold text-cyan-400 border border-cyan-900/30">
              {server.game}
            </span>
            <span className="group/ip flex items-center cursor-copy text-slate-400 hover:text-white transition-colors" onClick={copyIp}>
              <span className="font-mono">{server.ip}:{server.port}</span>
              <Copy className="w-3 h-3 ml-1 opacity-0 group-hover/ip:opacity-100 transition-opacity" />
            </span>
            <span className="text-slate-600">|</span>
            <span className="text-purple-300">{server.map}</span>
          </div>
        </div>

        {/* Players */}
        <div className="w-full sm:w-40 flex-shrink-0">
            <div className="flex justify-between text-xs font-bold text-slate-400 mb-1.5">
                <span>POPULATION</span>
                <span className={isOnline ? 'text-white' : 'text-slate-600'}>{server.players}/{server.maxPlayers}</span>
            </div>
            <div className="w-full bg-slate-800 rounded-full h-3 overflow-hidden border border-slate-700">
                <div 
                    className={`h-full rounded-full shadow-[0_0_10px_rgba(0,0,0,0.5)] ${isOnline ? 'bg-gradient-to-r from-blue-500 to-purple-500' : 'bg-slate-600'}`} 
                    style={{ width: `${fillPercentage}%` }}
                />
            </div>
        </div>

        {/* Vote Button */}
        <div className="flex-shrink-0 flex items-center space-x-3">
             <div className="text-center hidden sm:block">
                 <div className="text-lg font-black text-white">{server.votes}</div>
             </div>
             <button 
                onClick={handleVote}
                className="btn-pulse-red bg-gradient-to-br from-orange-500 to-red-600 hover:from-orange-400 hover:to-red-500 text-white p-2.5 rounded-xl shadow-lg border border-red-400/20 transform active:scale-95 transition-all group/vote"
             >
                <Trophy className="w-5 h-5 group-hover/vote:rotate-12 transition-transform" />
             </button>
        </div>
      </div>
    </div>
  );
};

export default ServerRow;