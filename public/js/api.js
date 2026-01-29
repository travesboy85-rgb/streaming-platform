const API_BASE = 'http://127.0.0.1:8000/api/v1';



// --- Auth helpers ---
function getToken() {
  return localStorage.getItem('token');
}

function setAuth(token, user) {
  localStorage.setItem('token', token);
  localStorage.setItem('user', JSON.stringify(user));
}

function clearAuth() {
  localStorage.removeItem('token');
  localStorage.removeItem('user');
}

function getUser() {
  const raw = localStorage.getItem('user');
  return raw ? JSON.parse(raw) : null;
}

// --- Core request wrapper ---
async function apiRequest(path, options = {}) {
  if (!path.startsWith('/')) path = '/' + path;

  const headers = {
    Accept: 'application/json',
    ...(getToken() ? { Authorization: `Bearer ${getToken()}` } : {}),
    ...(options.body ? { 'Content-Type': 'application/json' } : {})
  };

  const res = await fetch(`${API_BASE}${path}`, { ...options, headers });
  let data;
  try {
    data = await res.json();
  } catch {
    throw new Error('Invalid JSON response from server');
  }

  if (!res.ok) {
    if (data.errors) {
      const firstError = Object.values(data.errors)[0][0];
      throw new Error(firstError);
    }
    throw new Error(data.message || 'Request failed');
  }
  return data;
}

// --- Auth API calls ---
async function loginUser(payload) {
  const data = await apiRequest('/login', {
    method: 'POST',
    body: JSON.stringify(payload)
  });
  // ✅ Persist token + user immediately
  setAuth(data.token, data.user);
  return data;
}

async function registerUser(payload) {
  const data = await apiRequest('/register', {
    method: 'POST',
    body: JSON.stringify(payload)
  });
  setAuth(data.token, data.user);
  return data;
}
