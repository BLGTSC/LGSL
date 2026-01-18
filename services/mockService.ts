import { GameServer, GameType, ServerStatus, User } from '../types';
import { MOCK_BANNERS } from '../constants';

// --- MOCK DATA ---

const MOCK_USERS: User[] = [
  { id: 'u1', username: 'AdminUser', email: 'admin@neolgsl.com', role: 'admin', avatarUrl: 'https://picsum.photos/id/64/100/100' },
  { id: 'u2', username: 'GamerOne', email: 'gamer@test.com', role: 'user', avatarUrl: 'https://picsum.photos/id/65/100/100' },
];

let MOCK_SERVERS: GameServer[] = [
  {
    id: 's1',
    name: 'Romania Elite CS2 Public',
    ip: '89.123.45.67',
    port: 27015,
    game: GameType.CS2,
    map: 'de_mirage',
    players: 24,
    maxPlayers: 32,
    status: ServerStatus.ONLINE,
    votes: 1450,
    rank: 1,
    ownerId: 'u1',
    description: 'The best public server in Romania. 128 tick (simulated), no lag, active admins.',
    bannerUrl: MOCK_BANNERS[0],
    version: '1.39.5.4',
    country: 'RO',
    history: Array.from({ length: 12 }, (_, i) => ({ time: `${i * 2}:00`, players: Math.floor(Math.random() * 32) })),
  },
  {
    id: 's2',
    name: 'Minecraft Survival Hardcore',
    ip: 'play.mc-hardcore.ro',
    port: 25565,
    game: GameType.MINECRAFT,
    map: 'world',
    players: 156,
    maxPlayers: 500,
    status: ServerStatus.ONLINE,
    votes: 1205,
    rank: 2,
    ownerId: 'u2',
    description: 'Pure vanilla survival. No plugins, just you and the world.',
    bannerUrl: MOCK_BANNERS[1],
    version: '1.20.4',
    country: 'RO',
    history: Array.from({ length: 12 }, (_, i) => ({ time: `${i * 2}:00`, players: 100 + Math.floor(Math.random() * 100) })),
  },
  {
    id: 's3',
    name: 'Rustified EU Main',
    ip: '145.23.12.11',
    port: 28015,
    game: GameType.RUST,
    map: 'Procedural Map',
    players: 0,
    maxPlayers: 200,
    status: ServerStatus.OFFLINE,
    votes: 890,
    rank: 3,
    ownerId: 'u1',
    description: 'Weekly wipes, active PvP.',
    bannerUrl: MOCK_BANNERS[2],
    version: '2345',
    country: 'DE',
    history: Array.from({ length: 12 }, (_, i) => ({ time: `${i * 2}:00`, players: 0 })),
  },
  {
    id: 's4',
    name: 'Los Santos Roleplay RO',
    ip: 'fivem.ls-rp.ro',
    port: 30120,
    game: GameType.GTAV,
    map: 'Los Santos',
    players: 64,
    maxPlayers: 64,
    status: ServerStatus.ONLINE,
    votes: 650,
    rank: 4,
    ownerId: 'u2',
    description: 'Serious RP server. Whitelist only.',
    bannerUrl: MOCK_BANNERS[3],
    version: 'FiveM',
    country: 'RO',
    history: Array.from({ length: 12 }, (_, i) => ({ time: `${i * 2}:00`, players: 60 + Math.floor(Math.random() * 4) })),
  },
];

// --- SERVICE SIMULATION ---

export const MockService = {
  getServers: async (): Promise<GameServer[]> => {
    // Simulate network delay
    await new Promise(resolve => setTimeout(resolve, 600));
    // Sort by votes desc
    return [...MOCK_SERVERS].sort((a, b) => b.votes - a.votes).map((s, idx) => ({ ...s, rank: idx + 1 }));
  },

  getServerById: async (id: string): Promise<GameServer | undefined> => {
    await new Promise(resolve => setTimeout(resolve, 300));
    return MOCK_SERVERS.find(s => s.id === id);
  },

  voteForServer: async (serverId: string): Promise<{ success: boolean; message: string; newVotes?: number }> => {
    await new Promise(resolve => setTimeout(resolve, 400));
    
    // Check local storage for cooldown
    const lastVote = localStorage.getItem(`neolgsl_vote_${serverId}`);
    const now = Date.now();
    
    if (lastVote && now - parseInt(lastVote) < 24 * 60 * 60 * 1000) {
        return { success: false, message: 'You can only vote once every 24 hours per server.' };
    }

    const serverIndex = MOCK_SERVERS.findIndex(s => s.id === serverId);
    if (serverIndex !== -1) {
      MOCK_SERVERS[serverIndex].votes += 1;
      localStorage.setItem(`neolgsl_vote_${serverId}`, now.toString());
      return { success: true, message: 'Vote registered successfully!', newVotes: MOCK_SERVERS[serverIndex].votes };
    }
    
    return { success: false, message: 'Server not found.' };
  },

  addServer: async (serverData: Partial<GameServer>, user: User): Promise<GameServer> => {
    await new Promise(resolve => setTimeout(resolve, 800));
    const newServer: GameServer = {
      id: `s${Date.now()}`,
      name: serverData.name || 'Unknown Server',
      ip: serverData.ip || '127.0.0.1',
      port: serverData.port || 27015,
      game: serverData.game || GameType.CS2,
      map: 'loading...',
      players: 0,
      maxPlayers: 32,
      status: ServerStatus.ONLINE, // Assume online for demo
      votes: 0,
      rank: MOCK_SERVERS.length + 1,
      ownerId: user.id,
      description: serverData.description || '',
      bannerUrl: MOCK_BANNERS[Math.floor(Math.random() * MOCK_BANNERS.length)],
      version: '1.0',
      country: 'RO',
      history: []
    };
    MOCK_SERVERS.push(newServer);
    return newServer;
  },

  deleteServer: async (id: string): Promise<void> => {
    await new Promise(resolve => setTimeout(resolve, 500));
    MOCK_SERVERS = MOCK_SERVERS.filter(s => s.id !== id);
  },

  login: async (username: string): Promise<User> => {
    await new Promise(resolve => setTimeout(resolve, 500));
    const user = MOCK_USERS.find(u => u.username.toLowerCase() === username.toLowerCase());
    if (user) return user;
    // Auto register for demo
    return {
        id: `u${Date.now()}`,
        username,
        email: `${username}@demo.com`,
        role: 'user',
        avatarUrl: `https://ui-avatars.com/api/?name=${username}`
    };
  }
};
