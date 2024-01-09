export interface Track {
    id: string | number;
    name: string;
    albumName: string;
}

export interface Playlist {
    id: string | number;
    name: string;
    tracks: Track[];
}

