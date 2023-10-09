const clientId = process.env.SPOTIFY_CLIENT_ID;
const redirectUri = "http://localhost:8080";

const urlParams = new URLSearchParams(window.location.search);
let code = urlParams.get("code");

/**
 * Generate code verifier
 */
function generateRandomString(length) {
    let text = "";
    let possible =
        "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (let i = 0; i < length; i++) {
        text += possible.charAt(Math.floor(Math.random() * possible.length));
    }
    return text;
}

/**
 * Generate code challenge needed to request access token (PKCE grant)
 * @param codeVerifier
 * @returns
 */
async function generateCodeChallenge(codeVerifier) {
    function base64encode(string) {
        return btoa(String.fromCharCode.apply(null, new Uint8Array(string)))
            .replace(/\+/g, "-")
            .replace(/\//g, "_")
            .replace(/=+$/, "");
    }

    const data = new TextEncoder().encode(codeVerifier);
    const digest = await window.crypto.subtle.digest("SHA-256", data);

    return base64encode(digest);
}

function requestAccessToken(): void {
    let codeVerifier = localStorage.getItem("code_verifier");
    let body = new URLSearchParams({
        grant_type: "authorization_code",
        code: code,
        redirect_uri: redirectUri,
        client_id: clientId,
        code_verifier: codeVerifier,
    });
    const response = fetch("https://accounts.spotify.com/api/token", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: body,
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error("HTTP status " + response.status);
            }
            return response.json();
        })
        .then((data) => {
            localStorage.setItem("access_token", data.access_token);
        })
        .catch((error) => {
            console.error("Error:", error);
        });
}

async function getProfile() {
    let accessToken = localStorage.getItem("access_token");

    const response = await fetch("https://api.spotify.com/v1/me", {
        headers: {
            Authorization: "Bearer " + accessToken,
        },
    });

    return await response.json();
}
