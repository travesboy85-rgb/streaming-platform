const API_BASE = 'http://localhost:8000/api/v1';
let authToken = localStorage.getItem('token') || '';
let currentUser = JSON.parse(localStorage.getItem('user')) || null;

// When streaming.html loads, fetch video by ID
window.onload = async function() {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');
    if (id) {
        await loadVideoById(id);
    } else {
        document.getElementById('player-title').textContent = 'No video selected';
    }
};

// Fetch single video by ID
async function loadVideoById(id) {
    try {
        const response = await fetch(`${API_BASE}/videos/${id}`, {
            headers: authToken ? { 'Authorization': `Bearer ${authToken}` } : {}
        });
        const video = await response.json();

        document.getElementById('player-title').textContent = video.title || 'Untitled';
        document.getElementById('player-description').textContent = video.description || '';
        const videoPlayer = document.getElementById('player-video');
        videoPlayer.src = video.file_path || video.url;
        videoPlayer.load();

        // Track view
        if (authToken) {
            fetch(`${API_BASE}/videos/${id}`, {
                headers: { 'Authorization': `Bearer ${authToken}` }
            });
        }
    } catch (error) {
        console.error('Error loading video:', error);
        document.getElementById('player-title').textContent = 'Error loading video';
    }
}

// Back button → redirect to correct dashboard
function goBack() {
    if (!currentUser) {
        window.location.href = '/auth/index.html';
        return;
    }
    const role = currentUser.role || (currentUser.roles?.[0]?.name ?? 'user');
    if (role.toLowerCase() === 'admin') window.location.href = '/dashboards/admin.html';
    else if (role.toLowerCase() === 'creator') window.location.href = '/dashboards/creator.html';
    else window.location.href = '/dashboards/user.html';
}
