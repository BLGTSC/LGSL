import { GameType } from './types';

export const GAME_IMAGES: Record<GameType, string> = {
  [GameType.CS2]: 'https://picsum.photos/id/1/800/400', 
  [GameType.MINECRAFT]: 'https://picsum.photos/id/2/800/400',
  [GameType.RUST]: 'https://picsum.photos/id/3/800/400',
  [GameType.GTAV]: 'https://picsum.photos/id/4/800/400',
  [GameType.VALHEIM]: 'https://picsum.photos/id/5/800/400',
  [GameType.ARK]: 'https://picsum.photos/id/6/800/400',
};

// Map some placeholders specifically for visual variety in the mock
export const MOCK_BANNERS = [
  'https://picsum.photos/seed/server1/800/300',
  'https://picsum.photos/seed/server2/800/300',
  'https://picsum.photos/seed/server3/800/300',
  'https://picsum.photos/seed/server4/800/300',
];

export const COUNTRY_FLAGS: Record<string, string> = {
  'RO': '🇷🇴',
  'US': '🇺🇸',
  'DE': '🇩🇪',
  'FR': '🇫🇷',
  'UK': '🇬🇧',
};
