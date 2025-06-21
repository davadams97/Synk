# YTMusicProxy

YTMusicProxy is an HTTP wrapper around the [davadams97/youtubemusicapi](https://github.com/davadams97/youtubemusicapi) library. It provides a RESTful API interface for YouTube Music operations, making it easy to integrate YouTube Music functionality into web applications.

## Features

- **Playlist Management**: Create, read, update, and delete playlists
- **Search Functionality**: Search for songs, albums, artists, and playlists
- **User Management**: Retrieve user information and channel details
- **Authentication**: Bearer token-based authentication
- **RESTful API**: Clean, predictable API endpoints

## API Endpoints

### Playlists

- `GET /v1/playlists` - Get user's library playlists
- `POST /v1/playlists` - Create a new playlist
- `GET /v1/playlists/<playlist_id>` - Get specific playlist details
- `DELETE /v1/playlists/<playlist_id>` - Delete a playlist
- `POST /v1/playlists/<playlist_id>` - Add items to a playlist

### Search

- `GET /v1/search` - Search for content (songs, albums, artists, playlists)

### Users

- `GET /v1/users/<channel_id>` - Get user information

## Prerequisites

- Python 3.8 or higher
- pip (Python package installer)
- YouTube Music account with authentication

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd ytmusicproxy
   ```

2. **Create a virtual environment (recommended)**
   ```bash
   python -m venv venv
   source venv/bin/activate  # On Windows: venv\Scripts\activate
   ```

3. **Install dependencies**
   ```bash
   pip install -r requirements.txt
   ```

## Development

### Starting the development server

```bash
flask run
```

The server will start on `http://localhost:5000` by default.

### Using Gunicorn (Production)

```bash
gunicorn app:app
```

### Environment Variables

You can configure the following environment variables:

- `FLASK_ENV` - Set to `development` for debug mode
- `PORT` - Port number (default: 5000)

## API Usage

### Authentication

All API endpoints require a bearer token in the Authorization header:

```
Authorization: Bearer <your_youtube_music_token>
```

## Development Notes

### Motivation

`YTMusicProxy` is a simple Flask server that provides APIs for the `ytmusicapi` library. It was created to support the Synk project's YouTube Music integration needs.

### Current Status

**Note**: This is currently work in progress, therefore use the APIs with caution. It is currently only being used for a personal project.

### Future Improvements

- Add comprehensive error handling
- Implement rate limiting
- Add API documentation with Swagger/OpenAPI
- Add unit tests
- Implement caching for better performance

## License

This project is open-sourced software licensed under the [MIT license](LICENSE.md).


