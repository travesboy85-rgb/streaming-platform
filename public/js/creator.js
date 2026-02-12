document.addEventListener('DOMContentLoaded', () => {
  const API_BASE = 'http://127.0.0.1:8000/api/v1';   // ✅ Absolute backend URL

  const token = localStorage.getItem('token');
  const currentUser = JSON.parse(localStorage.getItem('user'));
  const videoGrid = document.getElementById('videos');
  const uploadForm = document.getElementById('uploadForm');

  const totalVideosEl = document.getElementById('totalVideos');
  const totalViewsEl = document.getElementById('totalViews');
  const avgRatingEl = document.getElementById('avgRating');

  if (!token || !currentUser) {
    alert('No token found. Please log in.');
    window.location.href = '/auth/index.html';
    return;
  }

  async function loadVideos() {
    try {
      const res = await fetch(`${API_BASE}/videos/mine`, {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      });
      const data = await res.json();
      console.log('API response:', data);

      videoGrid.innerHTML = '';
      const myVideos = Array.isArray(data) ? data : [];

      if (totalVideosEl) totalVideosEl.textContent = myVideos.length;
      if (totalViewsEl) totalViewsEl.textContent = myVideos.reduce((sum, v) => sum + (v.views || 0), 0);
      if (avgRatingEl) {
        avgRatingEl.textContent = myVideos.length
          ? (myVideos.reduce((sum, v) => sum + (v.rating || 0), 0) / myVideos.length).toFixed(1)
          : '0.0';
      }

      myVideos.forEach(video => {
        const card = document.createElement('div');
        card.className = 'video-card';

        const playbackUrl = video.playback_url || '';
        const thumbnailUrl = video.thumbnail_url || '/img/default-thumbnail.jpg';

        card.innerHTML = `
          <h3>${video.title}</h3>
          <img src="${thumbnailUrl}" alt="Thumbnail" width="160" height="90"
               onerror="this.src='/img/default-thumbnail.jpg'">
          <p>${video.description || ''}</p>
          <video width="320" height="180" controls>
            <source src="${playbackUrl}" type="video/mp4">
            Your browser does not support the video tag.
          </video>
          <p><strong>Views:</strong> ${video.views || 0}</p>
          <button class="btn delete-btn" data-id="${video.id}">Delete</button>
        `;

        videoGrid.appendChild(card);
      });

      if (myVideos.length === 0) {
        videoGrid.innerHTML = '<p>No videos found for this user.</p>';
      }

      document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
          const videoId = e.target.getAttribute('data-id');
          if (confirm('Delete this video?')) {
            try {
              const res = await fetch(`${API_BASE}/videos/${videoId}`, {
                method: 'DELETE',
                headers: {
                  'Authorization': `Bearer ${token}`,
                  'Accept': 'application/json'
                }
              });
              const result = await res.json();
              console.log('Delete response:', result);
              if (res.ok) {
                alert('Video deleted.');
                loadVideos();
              } else {
                alert('Delete failed: ' + (result.message || 'Unknown error'));
              }
            } catch (err) {
              console.error('Delete error:', err);
              alert('An error occurred while deleting.');
            }
          }
        });
      });
    } catch (err) {
      console.error('Error fetching videos:', err);
      videoGrid.innerHTML += '<p>Failed to load videos.</p>';
    }
  }

  loadVideos();

  uploadForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData();
    formData.append('title', document.getElementById('title').value);
    formData.append('description', document.getElementById('description').value);
    formData.append('rating', document.getElementById('rating').value);
    formData.append('thumbnail', document.getElementById('thumbnail').value);
    formData.append('file', document.getElementById('file').files[0]);   // ✅ must be "file"

    try {
      const res = await fetch(`${API_BASE}/videos`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        },
        body: formData
      });

      const data = await res.json();
      console.log('Upload response:', data);

      if (res.ok) {
        alert('Video uploaded!');
        loadVideos();
        uploadForm.reset();
      } else {
        alert('Upload failed: ' + (data.message || 'Unknown error'));
      }
    } catch (err) {
      console.error('Upload error:', err);
      alert('An error occurred while uploading.');
    }
  });
});




