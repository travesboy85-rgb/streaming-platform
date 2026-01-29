// --- Role enforcement ---
function requireRole(requiredRole) {
  const user = getUser();
  if (!user) {
    console.warn('No user found, redirecting to login.');
    window.location.href = '/auth/index.html';
    return;
  }

  const roles = normalizeRoles(user);
  if (!roles.includes(String(requiredRole).toLowerCase())) {
    redirectByRole(user);
  }
}

function redirectByRole(user) {
  const roles = normalizeRoles(user);
  if (roles.includes('admin')) {
    window.location.href = '/dashboards/admin.html';
  } else if (roles.includes('creator')) {
    window.location.href = '/dashboards/creator.html';
  } else {
    window.location.href = '/dashboards/user.html';
  }
}

function normalizeRoles(user) {
  if (!user) return [];
  if (Array.isArray(user.roles)) {
    return user.roles.map(r =>
      typeof r === 'string' ? r.toLowerCase() : (r?.name || '').toLowerCase()
    ).filter(Boolean);
  }
  if (user.role) return [String(user.role).toLowerCase()];
  return [];
}

// --- Video management ---
async function loadVideos(role = 'user') {
  const container = document.getElementById('videos');
  if (!container) return;

  try {
    const data = await apiRequest('/videos');   // ✅ calls /api/v1/videos
    const videos = Array.isArray(data?.data) ? data.data : (Array.isArray(data?.videos) ? data.videos : []);
    container.innerHTML = '';

    videos.forEach(v => {
      const div = document.createElement('div');
      div.className = 'video-card';

      const thumbnail = v.thumbnail || '/img/default-thumbnail.jpg';
      const title = v.title || 'Untitled';
      const views = Number(v.views || 0);
      const rating = v.rating != null ? v.rating : 'N/A';

      if (role === 'admin') {
        div.innerHTML = `
          <img src="${thumbnail}" alt="${title}">
          <h3>${title}</h3>
          <p>${views} views | ⭐ ${rating}</p>
          <button onclick="playVideo(${v.id})">Play</button>
          <button onclick="editVideo(${v.id})">Edit</button>
          <button onclick="deleteVideo(${v.id})">Delete</button>
        `;
      } else if (role === 'creator') {
        const user = getUser();
        const canDelete = v.user_id === (user?.id || -1);
        div.innerHTML = `
          <img src="${thumbnail}" alt="${title}">
          <h3>${title}</h3>
          <p>${views} views | ⭐ ${rating}</p>
          <button onclick="playVideo(${v.id})">Play</button>
          ${canDelete ? `<button onclick="deleteVideo(${v.id})">Delete</button>` : ''}
        `;
      } else {
        div.innerHTML = `
          <img src="${thumbnail}" alt="${title}">
          <h3>${title}</h3>
          <p>${views} views | ⭐ ${rating}</p>
          <button onclick="playVideo(${v.id})">Play</button>
        `;
        div.dataset.category = v?.category?.slug || '';
      }

      container.appendChild(div);
    });

    if (videos.length === 0) {
      container.innerHTML = '<p>No videos available yet.</p>';
    }
  } catch (err) {
    console.error('Error loading videos:', err);
    container.innerHTML = '<p style="color:red;">Error loading videos.</p>';
  }
}

function playVideo(id) {
  if (!id) return;
  const user = getUser();
  const roles = normalizeRoles(user);

  // ✅ If admin, add preview flag so views don't increment
  if (roles.includes('admin')) {
    window.location.href = `/streaming.html?id=${id}&preview=true`;
  } else {
    window.location.href = `/streaming.html?id=${id}`;
  }
}


async function deleteVideo(id) {
  if (!id) return;
  if (!confirm('Delete this video?')) return;

  try {
    await apiRequest(`/videos/${id}`, { method: 'DELETE' });   // ✅ /api/v1/videos/{id}
    alert('Video deleted successfully.');
    await loadVideos('admin');
    await loadAnalytics();
  } catch (err) {
    console.error(err);
    alert('Failed to delete video.');
  }
}

async function editVideo(id) {
  if (!id) return;
  const newTitle = prompt('Enter new video title:');
  if (!newTitle) return;

  try {
    await apiRequest(`/videos/${id}`, {   // ✅ /api/v1/videos/{id}
      method: 'PUT',
      body: JSON.stringify({ title: newTitle })
    });
    alert('Video updated successfully!');
    await loadVideos('admin');
    await loadAnalytics();
  } catch (err) {
    console.error(err);
    alert('Failed to update video.');
  }
}

// --- Creator upload + analytics ---
async function initCreator() {
  loadVideos('creator');
  loadCreatorAnalytics();

  const form = document.getElementById('uploadForm');
  if (!form) return;

  form.addEventListener('submit', async e => {
    e.preventDefault();
    try {
      const payload = {
        title: document.getElementById('title')?.value || '',
        url: document.getElementById('url')?.value || '',
        thumbnail: document.getElementById('thumbnail')?.value || '',
        duration: parseInt(document.getElementById('duration')?.value || '0', 10),
        description: document.getElementById('description')?.value || ''
      };

      await apiRequest('/videos', {   // ✅ /api/v1/videos
        method: 'POST',
        body: JSON.stringify(payload)
      });

      alert('Video uploaded successfully.');
      loadVideos('creator');
      loadCreatorAnalytics();
    } catch (err) {
      console.error(err);
      alert('Failed to upload video.');
    }
  });
}

async function loadCreatorAnalytics() {
  const statsContainer = document.getElementById('creator-analytics');
  if (!statsContainer) return;

  try {
    const data = await apiRequest('/videos');   // ✅ /api/v1/videos
    const videos = Array.isArray(data?.data) ? data.data : [];
    const creatorId = getUser()?.id;

    const myVideos = videos.filter(v => v.user_id === creatorId);

    const totalVideos = myVideos.length;
    const totalViews = myVideos.reduce((sum, v) => sum + Number(v.views || 0), 0);
    const avgRating = myVideos.length
      ? (myVideos.reduce((sum, v) => sum + Number(v.rating || 0), 0) / myVideos.length).toFixed(1)
      : 'N/A';

    statsContainer.innerHTML = `
      <p>Total Videos: ${totalVideos}</p>
      <p>Total Views: ${totalViews}</p>
      <p>Average Rating: ${avgRating}</p>
    `;
  } catch (err) {
    console.error(err);
    statsContainer.innerHTML = '<p style="color:red;">Error loading analytics.</p>';
  }
}

// --- Admin user management ---
async function loadUsers() {
  const tableBody = document.querySelector('#users-table tbody');
  if (!tableBody) return;

  try {
    const data = await apiRequest('/users');   // ✅ /api/v1/users
    tableBody.innerHTML = '';

    const users = Array.isArray(data?.users) ? data.users : [];
    users.forEach(u => {
      const rolesText = Array.isArray(u?.roles)
        ? u.roles.map(r => (typeof r === 'string' ? r : (r?.name || ''))).join(', ')
        : (u?.role || '');

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${u.id}</td>
        <td>${u.name}</td>
        <td>${u.email}</td>
        <td>${rolesText}</td>
        <td><button onclick="deleteUser(${u.id})">Delete</button></td>
      `;
      tableBody.appendChild(tr);
    });
  } catch (err) {
    console.error('Error loading users:', err);
    tableBody.innerHTML = '<tr><td colspan="5" style="color:red;">Error loading users.</td></tr>';
  }
}

async function deleteUser(id) {
  if (!id) return;
  if (!confirm('Delete this user?')) return;

  try {
    await apiRequest(`/users/${id}`, { method: 'DELETE' });   // ✅ /api/v1/users/{id}
    alert('User deleted successfully.');
    await loadUsers();
    await loadAnalytics();
  } catch (err) {
    console.error(err);
    alert('Failed to delete user.');
  }
}
function initHeader() {
  const titleEl = document.getElementById('dashboardTitle');
  const user = getUser();

  if (titleEl) {
    if (user) {
      const roles = normalizeRoles(user);
      if (roles.includes('admin')) {
        titleEl.textContent = 'Welcome Admin';
      } else {
        titleEl.textContent = `Welcome ${user.name || 'User'}`;
      }
    } else {
      titleEl.textContent = 'Welcome';
    }
  }

  const logoutBtn = document.getElementById('logoutBtn');
  if (logoutBtn) {
    logoutBtn.onclick = async () => {
      try {
        await apiRequest('/logout', { method: 'POST' });
      } catch (_) {}
      clearAuth();
      window.location.href = '/auth/index.html';
    };
  }
}

// --- Admin analytics ---
async function loadAnalytics() {
  try {
    const videos = await apiRequest('/videos');
    const users = await apiRequest('/users');


    const videoList = Array.isArray(videos?.data)
      ? videos.data
      : (Array.isArray(videos?.videos) ? videos.videos : []);

    const userList = Array.isArray(users?.data)
  ? users.data
  : (Array.isArray(users?.users) ? users.users : []);


    const totalViews = videoList.reduce((sum, v) => sum + Number(v.views || 0), 0);

    document.getElementById('total-videos').textContent = videoList.length;
    document.getElementById('total-users').textContent = userList.length;
    document.getElementById('total-views').textContent = totalViews;
  } catch (err) {
    console.error('Error loading analytics:', err);
  }
}   // ✅ <-- this closing brace was missing