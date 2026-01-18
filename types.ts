export enum GameType {
  CS2 = 'Counter-Strike 2',
  MINECRAFT = 'Minecraft',
  RUST = 'Rust',
  GTAV = 'GTA V / FiveM',
  VALHEIM = 'Valheim',
  ARK = 'ARK: Survival Evolved'
}

export enum ServerStatus {
  ONLINE = 'ONLINE',
  OFFLINE = 'OFFLINE',
  MAINTENANCE = 'MAINTENANCE'
}

export interface User {
  id: string;
  username: string;
  email: string;
  role: 'admin' | 'user';
  avatarUrl?: string;
}

export interface GameServer {
  id: string;
  name: string;
  ip: string;
  port: number;
  game: GameType;
  map: string;
  players: number;
  maxPlayers: number;
  status: ServerStatus;
  votes: number;
  rank: number;
  ownerId: string;
  description: string;
  bannerUrl: string;
  version: string;
  country: string;
  history: { time: string; players: number }[]; // For charts
}

export interface WidgetConfig {
  theme: 'dark' | 'light';
  width: number;
  showMap: boolean;
}