<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My-Task Management Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #009688;
            --primary-dark: #00796b;
            --bg-dark: #0f172a;
            --card-bg: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
            
            --get-color: #3b82f6;
            --post-color: #10b981;
            --put-color: #f59e0b;
            --delete-color: #ef4444;
            
            --glass-bg: rgba(30, 41, 59, 0.7);
            --glass-border: rgba(255, 255, 255, 0.08);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            line-height: 1.6;
            padding: 24px;
            overflow-x: hidden;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Glassmorphism Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            margin-bottom: 28px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
        }
        
        .logo-section h1 {
            font-size: 26px;
            font-weight: 800;
            background: linear-gradient(135deg, #009688, #00f2fe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }
        
        .logo-section p {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
        }
        
        .server-status {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10b981;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .status-dot {
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 8px #10b981;
        }
        
        /* Premium Tab Switcher */
        .tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 28px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            padding: 6px;
            border-radius: 14px;
            width: fit-content;
        }
        
        .tab-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .tab-btn.active {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 14px rgba(0, 150, 136, 0.3);
        }
        
        .tab-content {
            display: none;
            animation: fadeIn 0.4s ease;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Stats Dashboard Grid */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }
        
        .metric-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
        }
        
        .metric-card:hover {
            transform: translateY(-4px);
        }
        
        .metric-card .icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 16px;
        }
        
        .metric-card.total .icon-wrapper { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
        .metric-card.trial .icon-wrapper { background: rgba(148, 163, 184, 0.15); color: #94a3b8; }
        .metric-card.premium .icon-wrapper { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
        .metric-card.employees .icon-wrapper { background: rgba(16, 185, 129, 0.15); color: #10b981; }
        
        .metric-card h3 {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .metric-card .value {
            font-size: 32px;
            font-weight: 800;
            color: #fff;
        }
        
        /* Table Glass Styling */
        .table-container {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
            margin-bottom: 40px;
        }
        
        .table-header {
            padding: 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-header h2 {
            font-size: 18px;
            font-weight: 700;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        
        th {
            padding: 16px 24px;
            font-weight: 600;
            color: var(--text-muted);
            font-size: 13px;
            border-bottom: 1px solid var(--border);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
            color: var(--text-main);
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }
        
        .user-id-badge {
            font-family: monospace;
            background: rgba(255, 255, 255, 0.05);
            padding: 4px 8px;
            border-radius: 6px;
            color: var(--text-muted);
            font-size: 12px;
        }
        
        .plan-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .plan-badge.trial {
            background: rgba(148, 163, 184, 0.15);
            border: 1px solid rgba(148, 163, 184, 0.3);
            color: var(--text-muted);
        }
        
        .plan-badge.premium {
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: #f59e0b;
        }
        
        .btn {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s ease;
        }
        
        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }
        
        .btn.outline {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-main);
        }
        
        .btn.outline:hover {
            border-color: var(--text-muted);
            background: rgba(255, 255, 255, 0.05);
        }
        
        /* Modal Window glass styling */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        
        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        
        .modal-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 28px;
            width: 90%;
            max-width: 700px;
            max-height: 80vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            transform: scale(0.95);
            transition: transform 0.3s ease;
        }
        
        .modal-overlay.active .modal-card {
            transform: scale(1);
        }
        
        .modal-header {
            padding: 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h3 {
            font-size: 18px;
            font-weight: 700;
        }
        
        .modal-body {
            padding: 24px;
            overflow-y: auto;
        }
        
        .modal-close {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 24px;
            cursor: pointer;
        }
        
        .detail-item-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            padding: 14px 18px;
            border-radius: 14px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Login Overlay */
        .login-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: var(--bg-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }
        
        .login-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 28px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            text-align: center;
            box-shadow: 0 20px 48px rgba(0, 0, 0, 0.4);
        }
        
        .login-card h2 {
            font-size: 24px;
            margin-bottom: 8px;
            font-weight: 700;
        }
        
        .login-card p {
            color: var(--text-muted);
            font-size: 13px;
            margin-bottom: 28px;
        }
        
        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        .input-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        .input-group input {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            color: #fff;
            padding: 12px 16px;
            border-radius: 10px;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
        }
        
        .input-group input:focus {
            border-color: var(--primary);
            outline: none;
        }

        /* Dev Docs Specific Styling */
        .api-docs-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        .base-url-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .endpoint-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            margin-bottom: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .endpoint-header {
            padding: 16px 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
            user-select: none;
        }
        .endpoint-method {
            font-weight: 800;
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 6px;
            margin-right: 16px;
            color: #fff;
            min-width: 70px;
            text-align: center;
        }
        .endpoint-method.get { background-color: var(--get-color); }
        .endpoint-method.post { background-color: var(--post-color); }
        .endpoint-method.put { background-color: var(--put-color); }
        .endpoint-method.delete { background-color: var(--delete-color); }
        .endpoint-path { font-family: monospace; font-weight: 600; font-size: 14px; color: var(--text-main); flex-grow: 1; }
        .endpoint-desc { font-size: 13px; color: var(--text-muted); }
        .endpoint-details { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; background-color: rgba(255,255,255,0.01); }
        .endpoint-details.active { max-height: 1000px; }
        .testing-section { padding: 20px; border-top: 1px solid var(--border); }
        .detail-title { font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .testing-form { background: rgba(0,0,0,0.2); padding: 16px; border-radius: 12px; border: 1px solid var(--border); }
        .response-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; font-size: 13px; color: var(--text-muted); }
        .response-box { background: #000; padding: 16px; border-radius: 12px; overflow-x: auto; max-height: 400px; border: 1px solid var(--border); }
        .response-box code { font-family: monospace; font-size: 13px; color: #10B981; }
    </style>
</head>
<body>

    <!-- Token Authentication Overlay -->
    <div id="loginOverlay" class="login-overlay">
        <div class="login-card">
            <h2>Admin Security Gate</h2>
            <p>Please enter the administrative credentials to view user rosters, registries, and system metrics.</p>
            <div class="input-group">
                <label for="adminToken">Admin Token</label>
                <input type="password" id="adminToken" placeholder="Enter Token Key" onkeydown="if(event.key === 'Enter') attemptLogin()">
            </div>
            <button class="btn" style="width: 100%; padding: 12px;" onclick="attemptLogin()">Enter Management Hub</button>
            <div id="loginError" style="color:var(--delete-color); font-size:12px; margin-top:12px; font-weight:600; display:none;">Invalid Token. Please try again.</div>
        </div>
    </div>

    <div class="container">
        <!-- Header -->
        <header>
            <div class="logo-section">
                <h1>My-Task Management Hub</h1>
                <p>Cloud Server Administration Console</p>
            </div>
            <div class="server-status">
                <span class="status-dot"></span>
                <span>Active Server Online</span>
            </div>
        </header>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('admin-panel')">Roster & Metrics</button>
            <button class="tab-btn" onclick="switchTab('api-docs')">API Console</button>
        </div>

        <!-- Tab Content 1: Admin Panel -->
        <div id="admin-panel" class="tab-content active">
            <!-- Metrics Summary Grid -->
            <div class="metrics-grid">
                <div class="metric-card total">
                    <div class="icon-wrapper">👤</div>
                    <h3>Total Users</h3>
                    <div class="value" id="metric-total-users">0</div>
                </div>
                <div class="metric-card trial">
                    <div class="icon-wrapper">🕒</div>
                    <h3>Free Trials</h3>
                    <div class="value" id="metric-trial-users">0</div>
                </div>
                <div class="metric-card premium">
                    <div class="icon-wrapper">⭐️</div>
                    <h3>Premium Lifetime</h3>
                    <div class="value" id="metric-premium-users">0</div>
                </div>
                <div class="metric-card employees">
                    <div class="icon-wrapper">💼</div>
                    <h3>Registered Helpers</h3>
                    <div class="value" id="metric-total-employees">0</div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="table-container">
                <div class="table-header">
                    <h2>Roster Directory</h2>
                    <button class="btn outline" onclick="loadAdminData()">Refresh Directory</button>
                </div>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>User Profile</th>
                                <th>Account ID</th>
                                <th>Plan Class</th>
                                <th>Validity / Expiry</th>
                                <th>Helpers</th>
                                <th>Workers</th>
                                <th>Trackers</th>
                                <th>Management</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            <!-- Dynamic user rows will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab Content 2: API Documentation -->
        <div id="api-docs" class="tab-content">
            <div class="api-docs-container">
                <div class="base-url-card">
                    <div>
                        <div style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight:600;">Root Endpoint DSN</div>
                        <div style="font-size: 18px; font-weight: 700; font-family: monospace; margin-top: 4px;" id="baseUrlCode">/api</div>
                    </div>
                </div>

                <!-- Endpoint Group: Authentication -->
                <h3 style="font-size:16px; margin: 24px 0 12px 0; color:var(--primary); font-weight:700;">Account Authentication</h3>
                
                <!-- Register Guest -->
                <div class="endpoint-card">
                    <div class="endpoint-header" onclick="toggleDetails(this)">
                        <span class="endpoint-method post">POST</span>
                        <span class="endpoint-path">/api/register-guest</span>
                        <span class="endpoint-desc">Initiates 30-day Guest Account</span>
                    </div>
                    <div class="endpoint-details">
                        <div class="testing-section">
                            <div class="detail-title">Try Sandbox Sandbox</div>
                            <div class="testing-form">
                                <button class="btn" onclick="testRequest('POST', '/api/register-guest')">Test Endpoint</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Google Auth -->
                <div class="endpoint-card">
                    <div class="endpoint-header" onclick="toggleDetails(this)">
                        <span class="endpoint-method post">POST</span>
                        <span class="endpoint-path">/api/login-google</span>
                        <span class="endpoint-desc">Google OAuth validation & sync</span>
                    </div>
                    <div class="endpoint-details">
                        <div class="testing-section">
                            <div class="detail-title">Test Endpoint</div>
                            <div class="testing-form">
                                <div style="font-size:12px; color:var(--text-muted); margin-bottom:8px;">Request Payload:</div>
                                <textarea id="body-login-google" style="width:100%; height:80px; background:var(--bg-dark); border:1px solid var(--border); color:#fff; padding:10px; border-radius:10px; font-family:monospace; margin-bottom:10px;">{
  "googleId": "123456789",
  "email": "user@example.com"
}</textarea>
                                <button class="btn" onclick="testRequest('POST', '/api/login-google', 'body-login-google')">Send Request</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Endpoint Group: Core Scoped APIs -->
                <h3 style="font-size:16px; margin: 24px 0 12px 0; color:var(--primary); font-weight:700;">Scoped Resource Registers</h3>

                <!-- Employees List -->
                <div class="endpoint-card">
                    <div class="endpoint-header" onclick="toggleDetails(this)">
                        <span class="endpoint-method get">GET</span>
                        <span class="endpoint-path">/api/employees</span>
                        <span class="endpoint-desc">List all house helpers / employees</span>
                    </div>
                    <div class="endpoint-details">
                        <div class="testing-section">
                            <div class="detail-title">Test Scoped Endpoint</div>
                            <div class="testing-form">
                                <div style="font-size:12px; color:var(--text-muted); margin-bottom:8px;">Provide active X-User-Id:</div>
                                <input type="text" id="header-employees-get" placeholder="X-User-Id Header Value" style="background:var(--bg-dark); border:1px solid var(--border); color:#fff; padding:10px; border-radius:10px; margin-bottom:10px; width: 100%;">
                                <button class="btn" onclick="testRequestWithHeader('GET', '/api/employees', null, 'header-employees-get')">Send Request</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Response Overlay Dialog/Panel -->
                <div style="margin-top: 48px; border-top: 1px solid var(--border); padding-top: 24px; display: none;" id="liveResponsePanel">
                    <h3 style="font-size: 16px; margin-bottom: 8px; color: var(--primary);">Live Response Sandbox Console</h3>
                    <div class="response-header">
                        <span>Request Endpoint: <code id="consoleRequestUrl">/api</code></span>
                        <span>HTTP Code: <span id="consoleResponseStatus" class="response-status">200 OK</span></span>
                    </div>
                    <pre class="response-box"><code id="consoleResponseCode">{}</code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- User Details Modal overlay -->
    <div id="detailsModal" class="modal-overlay" onclick="if(event.target === this) closeDetailsModal()">
        <div class="modal-card">
            <div class="modal-header">
                <h3 id="modalUserTitle">User Workspace Details</h3>
                <button class="modal-close" onclick="closeDetailsModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalUserContent">
                <!-- User resource logs will load here -->
            </div>
        </div>
    </div>

    <script>
        const origin = window.location.origin;
        document.getElementById('baseUrlCode').innerText = origin + '/api';

        let adminToken = localStorage.getItem('todo_admin_token') || '';
        if (adminToken) {
            document.getElementById('loginOverlay').style.display = 'none';
            loadAdminData();
        }

        function attemptLogin() {
            const tokenInput = document.getElementById('adminToken').value.trim();
            if (!tokenInput) return;
            
            adminToken = tokenInput;
            loadAdminData(true);
        }

        async function loadAdminData(isFirstAttempt = false) {
            try {
                const response = await fetch(`${origin}/api/admin-overview?token=${adminToken}`);
                if (response.status === 401) {
                    localStorage.removeItem('todo_admin_token');
                    document.getElementById('loginOverlay').style.display = 'flex';
                    if (!isFirstAttempt) {
                        document.getElementById('loginError').style.display = 'block';
                    }
                    return;
                }
                
                const data = await response.json();
                if (data.success) {
                    localStorage.setItem('todo_admin_token', adminToken);
                    document.getElementById('loginOverlay').style.display = 'none';
                    document.getElementById('loginError').style.display = 'none';
                    
                    // Render Metrics
                    document.getElementById('metric-total-users').innerText = data.metrics.total_users;
                    document.getElementById('metric-trial-users').innerText = data.metrics.trial_users;
                    document.getElementById('metric-premium-users').innerText = data.metrics.premium_users;
                    document.getElementById('metric-total-employees').innerText = data.metrics.total_employees;
                    
                    // Render Table rows
                    const tableBody = document.getElementById('userTableBody');
                    tableBody.innerHTML = '';
                    
                    data.users.forEach(user => {
                        const tr = document.createElement('tr');
                        const createdDate = new Date(user.createdAt).toLocaleDateString(undefined, {month: 'short', day: 'numeric', year: 'numeric'});
                        
                        let expiryText = 'Lifetime access';
                        if (user.userType === 'guest' && user.expiresAt) {
                            const expires = new Date(user.expiresAt);
                            const diffDays = Math.ceil((expires - new Date()) / (1000 * 60 * 60 * 24));
                            expiryText = diffDays > 0 ? `${diffDays} Days Left` : 'Expired';
                        }
                        
                        const profilePicSrc = user.profilePic 
                            ? (user.profilePic.startsWith('http') ? user.profilePic : `${origin}/${user.profilePic}`)
                            : null;
                            
                        const profileAvatar = profilePicSrc
                            ? `<img src="${profilePicSrc}" style="width:36px; height:36px; border-radius:50%; object-fit:cover; margin-right:12px; border: 1.5px solid var(--primary);">`
                            : `<div style="width:36px; height:36px; border-radius:50%; background:var(--primary); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:bold; margin-right:12px; font-size: 14px;">${user.username[0].toUpperCase()}</div>`;
                        
                        tr.innerHTML = `
                            <td>
                                <div style="display:flex; align-items:center;">
                                    ${profileAvatar}
                                    <div style="font-weight:600;">${user.username}</div>
                                </div>
                            </td>
                            <td><span class="user-id-badge">${user.id}</span></td>
                            <td>
                                <span class="plan-badge ${user.userType === 'guest' ? 'trial' : 'premium'}">
                                    ${user.userType === 'guest' ? 'Free Trial' : 'Premium'}
                                </span>
                            </td>
                            <td style="font-size:13px; color:var(--text-muted);">${expiryText}</td>
                            <td style="font-weight:600; text-align:center;">${user.employee_count}</td>
                            <td style="font-weight:600; text-align:center;">${user.ironing_worker_count}</td>
                            <td style="font-weight:600; text-align:center;">${user.appliance_count}</td>
                            <td>
                                <button class="btn" style="padding: 6px 12px; font-size:12px;" onclick="viewUserWorkspace('${user.id}', '${user.username}')">Inspect Logs</button>
                            </td>
                        `;
                        tableBody.appendChild(tr);
                    });
                }
            } catch (err) {
                console.error(err);
            }
        }

        async function viewUserWorkspace(userId, username) {
            const modal = document.getElementById('detailsModal');
            const modalTitle = document.getElementById('modalUserTitle');
            const modalContent = document.getElementById('modalUserContent');
            
            modalTitle.innerText = `Workspace Log: ${username}`;
            modalContent.innerHTML = '<div style="text-align:center; padding: 40px; color:var(--text-muted);">Fetching workspace records...</div>';
            modal.classList.add('active');
            
            try {
                const response = await fetch(`${origin}/api/admin-user-details?token=${adminToken}&userId=${userId}`);
                const data = await response.json();
                
                if (data.success) {
                    let html = '';
                    
                    // Helpers section
                    html += `<h4 style="font-size:14px; margin-bottom:12px; color:var(--primary); font-weight:700;">Scoped Helper Registry (${data.employees.length})</h4>`;
                    if (data.employees.length === 0) {
                        html += '<div style="color:var(--text-muted); font-size:13px; margin-bottom:24px; padding-left:12px;">No helpers registered</div>';
                    } else {
                        html += '<div style="margin-bottom:24px;">';
                        data.employees.forEach(emp => {
                            html += `
                                <div class="detail-item-card">
                                    <div>
                                        <div style="font-weight:600; font-size:14px;">${emp.name}</div>
                                        <div style="font-size:12px; color:var(--text-muted);">Contact: ${emp.contact || 'N/A'}</div>
                                    </div>
                                    <div style="font-weight:700; color:var(--primary); font-size:13px;">₹${emp.baseSalary} / ${emp.salaryBasis}</div>
                                </div>
                            `;
                        });
                        html += '</div>';
                    }
                    
                    // Ironing Workers section
                    html += `<h4 style="font-size:14px; margin-bottom:12px; color:var(--primary); font-weight:700;">Ironing Registers (${data.ironingWorkers.length})</h4>`;
                    if (data.ironingWorkers.length === 0) {
                        html += '<div style="color:var(--text-muted); font-size:13px; margin-bottom:24px; padding-left:12px;">No ironing workers registered</div>';
                    } else {
                        html += '<div style="margin-bottom:24px;">';
                        data.ironingWorkers.forEach(worker => {
                            html += `
                                <div class="detail-item-card">
                                    <div>
                                        <div style="font-weight:600; font-size:14px;">${worker.name}</div>
                                        <div style="font-size:12px; color:var(--text-muted);">Contact: ${worker.contact || 'N/A'}</div>
                                    </div>
                                    <div style="font-size:12px; color:var(--text-muted);">Joined: ${new Date(worker.joiningDate).toLocaleDateString()}</div>
                                </div>
                            `;
                        });
                        html += '</div>';
                    }
                    
                    // Appliances section
                    html += `<h4 style="font-size:14px; margin-bottom:12px; color:var(--primary); font-weight:700;">Appliance Trackers (${data.appliances.length})</h4>`;
                    if (data.appliances.length === 0) {
                        html += '<div style="color:var(--text-muted); font-size:13px; padding-left:12px;">No appliance trackers registered</div>';
                    } else {
                        html += '<div>';
                        data.appliances.forEach(app => {
                            html += `
                                <div class="detail-item-card">
                                    <div>
                                        <div style="font-weight:600; font-size:14px;">${app.name} (${app.brand})</div>
                                        <div style="font-size:12px; color:var(--text-muted);">Serial Number: ${app.serialNumber || 'N/A'}</div>
                                    </div>
                                    <div style="font-size:12px; color:var(--text-muted);">Type: ${app.type}</div>
                                </div>
                            `;
                        });
                        html += '</div>';
                    }
                    
                    modalContent.innerHTML = html;
                }
            } catch (err) {
                modalContent.innerHTML = `<div style="text-align:center; padding: 40px; color:var(--delete-color);">Failed to fetch details: ${err.message}</div>`;
            }
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').classList.remove('active');
        }

        function switchTab(tabId) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            event.target.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        }

        function toggleDetails(header) {
            const card = header.parentElement;
            const details = card.querySelector('.endpoint-details');
            details.classList.toggle('active');
        }

        function showConsole(url, status, statusText, data) {
            document.getElementById('liveResponsePanel').style.display = 'block';
            document.getElementById('consoleRequestUrl').innerText = url;
            document.getElementById('consoleResponseStatus').innerText = status + ' ' + statusText;
            
            const codeEl = document.getElementById('consoleResponseCode');
            codeEl.innerText = JSON.stringify(data, null, 2);
            document.getElementById('liveResponsePanel').scrollIntoView({ behavior: 'smooth' });
        }

        async function makeRequest(method, path, body = null, userIdHeader = null) {
            const url = origin + path;
            const headers = { 'Content-Type': 'application/json' };
            if (userIdHeader) {
                headers['X-User-Id'] = userIdHeader;
            }
            const options = { method, headers };
            if (body) {
                options.body = typeof body === 'string' ? body : JSON.stringify(body);
            }

            try {
                const response = await fetch(url, options);
                const data = await response.json();
                showConsole(path, response.status, response.statusText, data);
            } catch (err) {
                showConsole(path, 0, 'Connection Refused', { error: err.message });
            }
        }

        function testRequest(method, path, bodyTextareaId = null) {
            let body = null;
            if (bodyTextareaId) {
                const rawText = document.getElementById(bodyTextareaId).value;
                try {
                    body = eval('(' + rawText + ')');
                } catch(e) {
                    body = JSON.parse(rawText);
                }
            }
            makeRequest(method, path, body);
        }

        function testRequestWithHeader(method, path, bodyTextareaId, headerInputId) {
            const headerVal = document.getElementById(headerInputId).value.trim();
            if (!headerVal) {
                alert('Please provide an X-User-Id for scoped endpoints');
                return;
            }
            makeRequest(method, path, null, headerVal);
        }
    </script>
</body>
</html>
