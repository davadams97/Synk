export interface Track {
    id: string | number;
    name: string;
    artist: string;
    albumName: string;
    albumArt: string;
    href: string;
    year?: number;
    duration?: number; // in milliseconds
    genre?: string;
    explicit?: boolean;
    popularity?: number;
}

export interface Playlist {
    id: string | number;
    coverURL: string;
    name: string;
    isSelected: boolean;
    tracks: Track[];
    trackCount?: number;
}

