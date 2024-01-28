from flask import Flask, request, abort

# Third-party imports
from ytmusicapi import YTMusic

app = Flask(__name__)

@app.get("/v1/playlists")
def get_playlists():
    if 'authorization' not in request.headers:
        abort(401, description="You are not authorized to view this content")

    bearer_token = request.headers['authorization']
    yt_music = YTMusic({'authorization': bearer_token})
    return yt_music.get_library_playlists()
@app.post("/v1/playlists")
def create_playlist():
    if 'authorization' not in request.headers:
        abort(401, description="You are not authorized to view this content")

    bearer_token = request.headers['authorization']
    yt_music = YTMusic({'authorization': bearer_token})

    request_data = request.get_json()

    title = request_data['title']
    description = request_data.get('description', '')
    privacy_status = request_data.get('privacyStatus', 'PRIVATE')
    video_ids = request_data.get('videoIds', None)
    source_playlist = request_data.get('sourcePlaylist', None)

    return yt_music.create_playlist(title, description, privacy_status, video_ids, source_playlist)

@app.get("/v1/playlists/<playlist_id>")
def get_playlist(playlist_id):
    if 'authorization' not in request.headers:
        abort(401, description="You are not authorized to view this content")

    bearer_token = request.headers['authorization']
    yt_music = YTMusic({'authorization': bearer_token})
    return yt_music.get_playlist(playlist_id)

@app.delete("/v1/playlists/<playlist_id>")
def delete_playlist(playlist_id):
    if 'authorization' not in request.headers:
        abort(401, description="You are not authorized to view this content")

    bearer_token = request.headers['authorization']
    yt_music = YTMusic({'authorization': bearer_token})
    return yt_music.delete_playlist(playlist_id)

@app.post("/v1/playlists/<playlist_id>")
def add_to_playlist(playlist_id):
    if 'authorization' not in request.headers:
        abort(401, description="You are not authorized to view this content")

    bearer_token = request.headers['authorization']
    yt_music = YTMusic({'authorization': bearer_token})

    request_data = request.get_json()

    video_ids = request_data.get('videoIds', None)
    source_playlist = request_data.get('source_playlist', None)
    duplicates = request_data.get('duplicates', False)

    return yt_music.add_playlist_items(playlist_id, video_ids, source_playlist, duplicates)

@app.get("/v1/users/<channel_id>")
def get_user(channel_id):
    if 'authorization' not in request.headers:
        abort(401, description="You are not authorized to view this content")

    bearer_token = request.headers['authorization']
    yt_music = YTMusic({'authorization': bearer_token})
    return yt_music.get_user(channel_id)

@app.get("/v1/search")
def search():
    if 'authorization' not in request.headers:
        abort(401, description="You are not authorized to view this content")

    bearer_token = request.headers['authorization']
    yt_music = YTMusic({'authorization': bearer_token})

    query_params = request.args

    query = query_params.get('query')
    filter = query_params.get('filter', 'songs')
    scope = query_params.get('scope')
    limit = query_params.get('limit', 20)
    ignore_spelling = query_params.get('ignoreSpelling', False)

    return yt_music.search(query, filter, scope, limit, ignore_spelling)