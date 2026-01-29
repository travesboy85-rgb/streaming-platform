document.addEventListener('DOMContentLoaded', () => {
  const loginTab = document.getElementById('loginTab');
  const registerTab = document.getElementById('registerTab');
  const loginForm = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');

  // ---------------- TAB SWITCHING ----------------
  loginTab.addEventListener('click', () => {
    loginTab.classList.add('active');
    registerTab.classList.remove('active');
    loginForm.classList.add('active');
    registerForm.classList.remove('active');
  });

  registerTab.addEventListener('click', () => {
    registerTab.classList.add('active');
    loginTab.classList.remove('active');
    registerForm.classList.add('active');
    loginForm.classList.remove('active');
  });

  // ---------------- LOGIN ----------------
  loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
      const data = await loginUser({
        email: document.getElementById('loginEmail').value,
        password: document.getElementById('loginPassword').value
      });
      setAuth(data.token, data.user);   // ✅ matches api.js now
      redirectByRole(data.user);
    } catch (err) {
      document.getElementById('loginError').textContent = err.message;
    }
  });

  // ---------------- REGISTER ----------------
  registerForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    try {
      const data = await registerUser({
        name: document.getElementById('name').value,
        email: document.getElementById('registerEmail').value,
        password: document.getElementById('registerPassword').value,
        password_confirmation: document.getElementById('registerPasswordConfirm').value,
        role: document.getElementById('role').value
      });
      setAuth(data.token, data.user);   // ✅ matches api.js now
      redirectByRole(data.user);
    } catch (err) {
      document.getElementById('registerError').textContent = err.message;
    }
  });
});

// ---------------- HELPERS ----------------

// Call backend login
async function loginUser({ email, password }) {
  const res = await fetch('/api/v1/login', {
    method: 'POST',
    headers: { 
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ email, password })   // ✅ send JSON
  });
  const data = await res.json();
  if (!res.ok) throw new Error(data.message || 'Login failed');
  return data;
}

// Call backend register
async function registerUser({ name, email, password, password_confirmation, role }) {
  const res = await fetch('/api/v1/register', {
    method: 'POST',
    headers: { 
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ name, email, password, password_confirmation, role })   // ✅ send JSON
  });
  const data = await res.json();
  if (!res.ok) throw new Error(data.message || 'Registration failed');
  return data;
}

// Save token + user info
function setAuth(token, user) {
  localStorage.setItem('token', token);   // ✅ use 'token' to match api.js
  localStorage.setItem('user', JSON.stringify(user));
}

// Role-based redirection
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

// Normalize roles from backend response
function normalizeRoles(user) {
  if (!user) return [];
  if (Array.isArray(user.roles)) {
    return user.roles.map(r =>
      typeof r === 'string' ? r.toLowerCase() : r.name?.toLowerCase()
    );
  }
  if (user.role) return [user.role.toLowerCase()];
  return [];
}

// Logout helper
function logout() {
  localStorage.removeItem('token');   // ✅ matches api.js
  localStorage.removeItem('user');
  window.location.href = '/auth/index.html';
}
