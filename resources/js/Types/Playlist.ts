export interface Track {
    id: string | number;
    name: string;
    albumName: string;
    albumArt: string;
}

export interface Playlist {
    id: string | number;
    coverURL: string;
    name: string;
    tracks: Track[];
}

