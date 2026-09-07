<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My-Task Enterprise Admin Portal</title>
    <!-- Roboto Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500;600&family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary: #DC2626; /* Crimson Red */
            --primary-hover: #B91C1C;
            --primary-light: #FEE2E2;
            --primary-subtle: #FEF2F2;
            
            --bg-base: #F8FAFC;
            --bg-surface: #FFFFFF;
            --bg-input: #FFFFFF;
            --bg-hover: #F1F5F9;
            
            --border: #E2E8F0;
            --border-focus: #DC2626;
            
            --text-main: #0F172A;
            --text-secondary: #475569;
            --text-muted: #94A3B8;
            
            --success: #16A34A;
            --success-light: #DCFCE7;
            --warning: #EA580C;
            --warning-light: #FFEDD5;
            --danger: #DC2626;
            --danger-light: #FEE2E2;
            --info: #2563EB;
            --info-light: #DBEAFE;
            
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 16px;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--bg-base);
            color: var(--text-main);
            line-height: 1.45;
            min-height: 100vh;
            padding: 20px;
            font-size: 13.5px;
        }

        .container {
            max-width: 1360px;
            margin: 0 auto;
        }

        /* Top Brand App Bar */
        header {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-top: 3.5px solid var(--primary);
            border-radius: var(--radius-md);
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-sm);
            margin-bottom: 20px;
        }

        .brand-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-logo {
            width: 38px;
            height: 38px;
            background: var(--primary);
            color: #FFFFFF;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 900;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
        }

        .brand-text h1 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.3px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .role-tag {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            background: var(--primary-light);
            color: var(--primary);
            padding: 2px 6px;
            border-radius: 4px;
        }

        .brand-text p {
            font-size: 11.5px;
            color: var(--text-muted);
            font-weight: 400;
        }

        .header-meta {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .server-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--success-light);
            color: var(--success);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
        }

        .pulse {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 6px var(--success);
        }

        .admin-profile-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--bg-hover);
            padding: 4px 10px 4px 6px;
            border-radius: 20px;
            border: 1px solid var(--border);
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .admin-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--primary);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
        }

        .btn-logout {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-secondary);
            padding: 6px 12px;
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
        }

        .btn-logout:hover {
            background: var(--primary-light);
            color: var(--primary);
            border-color: var(--primary-light);
        }

        /* Navigation Bar */
        .nav-tabs {
            display: flex;
            gap: 6px;
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 5px;
            margin-bottom: 20px;
            width: fit-content;
            box-shadow: var(--shadow-sm);
        }

        .nav-tab-btn {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-family: inherit;
            font-size: 12.5px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .nav-tab-btn:hover {
            background: var(--bg-hover);
            color: var(--text-main);
        }

        .nav-tab-btn.active {
            background: var(--primary);
            color: #FFFFFF;
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
        }

        /* Tab Views */
        .view-pane {
            display: none;
            animation: fadeIn 0.25s ease;
        }

        .view-pane.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Compact KPI Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            margin-bottom: 20px;
        }

        .kpi-card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 14px 16px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .kpi-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .kpi-title {
            font-size: 11.5px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .kpi-icon {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        .kpi-card.red .kpi-icon { background: var(--primary-light); color: var(--primary); }
        .kpi-card.orange .kpi-icon { background: var(--warning-light); color: var(--warning); }
        .kpi-card.green .kpi-icon { background: var(--success-light); color: var(--success); }
        .kpi-card.blue .kpi-icon { background: var(--info-light); color: var(--info); }

        .kpi-value {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.1;
            margin-bottom: 4px;
        }

        .kpi-sub {
            font-size: 11px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Action Toolbar */
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 14px;
        }

        .search-wrap {
            position: relative;
            flex: 1;
            max-width: 360px;
        }

        .search-wrap i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 13px;
        }

        .search-field {
            width: 100%;
            background: var(--bg-surface);
            border: 1px solid var(--border);
            color: var(--text-main);
            padding: 8px 12px 8px 34px;
            border-radius: var(--radius-sm);
            font-size: 12.5px;
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .search-field:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        .filter-row {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-pill {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            color: var(--text-secondary);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .filter-pill:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .filter-pill.active {
            background: var(--primary-light);
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            color: var(--text-main);
            padding: 7px 14px;
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
        }

        .btn:hover {
            background: var(--bg-hover);
        }

        .btn-primary {
            background: var(--primary);
            color: #FFFFFF;
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            border-color: var(--primary-hover);
            color: #FFFFFF;
        }

        /* Clean White Table */
        .table-card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        thead {
            background: #F1F5F9;
            border-bottom: 1px solid var(--border);
        }

        th {
            padding: 10px 16px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            font-size: 12.5px;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: var(--bg-subtle, #FAFAFA);
        }

        .user-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
            border: 1px solid var(--primary);
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-name {
            font-weight: 600;
            color: var(--text-main);
            font-size: 13px;
        }

        .user-sub {
            font-size: 11px;
            color: var(--text-muted);
        }

        .id-badge {
            font-family: 'Roboto Mono', monospace;
            font-size: 11px;
            background: var(--bg-hover);
            border: 1px solid var(--border);
            padding: 3px 6px;
            border-radius: 4px;
            color: var(--text-secondary);
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
        }

        .id-badge:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .plan-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .plan-tag.trial {
            background: var(--warning-light);
            color: var(--warning);
        }

        .plan-tag.premium {
            background: var(--primary-light);
            color: var(--primary);
        }

        .count-badge {
            background: var(--bg-hover);
            border: 1px solid var(--border);
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 11.5px;
            color: var(--text-secondary);
        }

        .action-btns {
            display: flex;
            align-items: center;
            gap: 6px;
            justify-content: flex-end;
        }

        .icon-btn {
            width: 28px;
            height: 28px;
            border-radius: 4px;
            border: 1px solid var(--border);
            background: var(--bg-surface);
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11.5px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .icon-btn:hover {
            background: var(--bg-hover);
            color: var(--text-main);
        }

        .icon-btn.inspect:hover {
            background: var(--primary-light);
            color: var(--primary);
            border-color: var(--primary);
        }

        .icon-btn.delete:hover {
            background: #FEE2E2;
            color: #DC2626;
            border-color: #DC2626;
        }

        /* Modal Overlays */
        .modal-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
            padding: 16px;
        }

        .modal-bg.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-box {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-top: 4px solid var(--primary);
            border-radius: var(--radius-md);
            width: 100%;
            max-width: 820px;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-lg);
            transform: scale(0.96);
            transition: transform 0.25s ease;
            overflow: hidden;
        }

        .modal-bg.active .modal-box {
            transform: scale(1);
        }

        .modal-head {
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #FAFAFA;
        }

        .modal-head h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-main);
        }

        .close-btn {
            background: transparent;
            border: none;
            font-size: 18px;
            color: var(--text-muted);
            cursor: pointer;
        }

        .close-btn:hover {
            color: var(--text-main);
        }

        .modal-actions-bar {
            padding: 10px 20px;
            background: #F8FAFC;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .modal-content {
            padding: 18px 20px;
            overflow-y: auto;
        }

        .subtabs-bar {
            display: flex;
            gap: 4px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 16px;
            padding-bottom: 8px;
        }

        .subtab-btn {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            padding: 6px 12px;
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .subtab-btn.active {
            background: var(--primary-light);
            color: var(--primary);
        }

        .item-card {
            background: #FAFAFA;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }



        /* Proper Login Screen (Red & White Theme) */
        .login-gate {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: #F8FAFC;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 20px;
        }

        .login-panel {
            background: #FFFFFF;
            border: 1px solid var(--border);
            border-top: 4.5px solid var(--primary);
            border-radius: var(--radius-md);
            padding: 36px 32px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            text-align: left;
        }

        .login-head {
            text-align: center;
            margin-bottom: 24px;
        }

        .login-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin: 0 auto 12px;
        }

        .login-head h2 {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .login-head p {
            font-size: 12px;
            color: var(--text-muted);
        }

        .input-group {
            margin-bottom: 16px;
        }

        .input-label {
            display: block;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .input-field-wrap {
            position: relative;
        }

        .input-field-wrap i.prefix-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 13px;
        }

        .input-control {
            width: 100%;
            background: #FFFFFF;
            border: 1px solid var(--border);
            color: var(--text-main);
            padding: 10px 12px 10px 36px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-family: inherit;
            outline: none;
            transition: all 0.2s ease;
        }

        .input-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }



        .error-alert {
            background: #FEE2E2;
            border: 1px solid #FCA5A5;
            color: #B91C1C;
            padding: 8px 12px;
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-weight: 600;
            margin-top: 14px;
            display: none;
        }

        /* Toast Component */
        #toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--text-main);
            color: #FFFFFF;
            padding: 10px 18px;
            border-radius: var(--radius-sm);
            font-size: 12.5px;
            font-weight: 600;
            box-shadow: var(--shadow-lg);
            transform: translateY(80px);
            opacity: 0;
            transition: all 0.25s ease;
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        #toast.show {
            transform: translateY(0);
            opacity: 1;
        }
    </style>
</head>
<body>

    <!-- Proper Administrator Login Gate (Red & White Theme) -->
    <div id="loginGate" class="login-gate">
        <div class="login-panel">
            <div class="login-head">
                <div class="login-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h2>Admin Management Portal</h2>
                <p>Enter your credentials to access system control</p>
            </div>



            <div class="input-group">
                <label class="input-label" for="loginEmail">Email Address</label>
                <div class="input-field-wrap">
                    <i class="fa-regular fa-envelope prefix-icon"></i>
                    <input type="email" id="loginEmail" class="input-control" placeholder="admin@mytask.com" onkeydown="if(event.key==='Enter') attemptLogin()">
                </div>
            </div>

            <div class="input-group">
                <label class="input-label" for="loginPass">Password</label>
                <div class="input-field-wrap">
                    <i class="fa-solid fa-lock prefix-icon"></i>
                    <input type="password" id="loginPass" class="input-control" placeholder="••••••••••••" onkeydown="if(event.key==='Enter') attemptLogin()">
                </div>
            </div>

            <button type="button" class="btn btn-primary" style="width:100%; justify-content:center; padding:10px; font-size:13px;" onclick="attemptLogin()">
                <i class="fa-solid fa-right-to-bracket"></i> Sign In to Portal
            </button>

            <div id="loginError" class="error-alert">
                <i class="fa-solid fa-circle-exclamation"></i> Invalid administrator email or password.
            </div>
        </div>
    </div>

    <!-- Main Administrative Container -->
    <div class="container">
        <!-- Top App Bar -->
        <header>
            <div class="brand-wrap">
                <div class="brand-logo">M</div>
                <div class="brand-text">
                    <h1>My-Task Console <span class="role-tag">Super Admin</span></h1>
                    <p>Production Cloud Registry & Operations Hub</p>
                </div>
            </div>
            <div class="header-meta">
                <div class="server-badge">
                    <span class="pulse"></span>
                    <span id="headerLatency">Online • 18ms</span>
                </div>
                <div class="admin-profile-chip">
                    <div class="admin-avatar">A</div>
                    <span id="adminNameDisplay">admin@mytask.com</span>
                </div>
                <button class="btn-logout" onclick="logoutAdmin()">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                </button>
            </div>
        </header>

        <!-- Navigation Tabs -->
        <div class="nav-tabs">
            <button class="nav-tab-btn active" onclick="switchTab('tab-roster')">
                <i class="fa-solid fa-users"></i> Users & Roster
            </button>
            <button class="nav-tab-btn" onclick="switchTab('tab-health')">
                <i class="fa-solid fa-heart-pulse"></i> System Diagnostics
            </button>
        </div>

        <!-- Tab 1: Users & Roster -->
        <div id="tab-roster" class="view-pane active">
            <!-- KPI Metrics -->
            <div class="kpi-grid">
                <div class="kpi-card red">
                    <div class="kpi-top">
                        <span class="kpi-title">Total Users</span>
                        <div class="kpi-icon"><i class="fa-solid fa-user-group"></i></div>
                    </div>
                    <div class="kpi-value" id="metric-total-users">0</div>
                    <div class="kpi-sub"><i class="fa-solid fa-arrow-trend-up"></i> Registered accounts</div>
                </div>

                <div class="kpi-card orange">
                    <div class="kpi-top">
                        <span class="kpi-title">Active Trials</span>
                        <div class="kpi-icon"><i class="fa-regular fa-clock"></i></div>
                    </div>
                    <div class="kpi-value" id="metric-active-trials">0</div>
                    <div class="kpi-sub"><span id="metric-expired-trials">0</span> Expired</div>
                </div>

                <div class="kpi-card red">
                    <div class="kpi-top">
                        <span class="kpi-title">Lifetime Premium</span>
                        <div class="kpi-icon"><i class="fa-solid fa-crown"></i></div>
                    </div>
                    <div class="kpi-value" id="metric-premium-users">0</div>
                    <div class="kpi-sub">Uncapped memberships</div>
                </div>

                <div class="kpi-card green">
                    <div class="kpi-top">
                        <span class="kpi-title">Helpers & Workers</span>
                        <div class="kpi-icon"><i class="fa-solid fa-user-nurse"></i></div>
                    </div>
                    <div class="kpi-value" id="metric-total-helpers">0</div>
                    <div class="kpi-sub"><span id="metric-total-attendance">0</span> attendance logs</div>
                </div>

                <div class="kpi-card blue">
                    <div class="kpi-top">
                        <span class="kpi-title">Appliances Tracked</span>
                        <div class="kpi-icon"><i class="fa-solid fa-plug"></i></div>
                    </div>
                    <div class="kpi-value" id="metric-total-appliances">0</div>
                    <div class="kpi-sub">₹<span id="metric-total-spend">0</span> maintenance</div>
                </div>
            </div>

            <!-- Roster Search & Filter Toolbar -->
            <div class="toolbar">
                <div class="search-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="rosterSearch" class="search-field" placeholder="Search by username or user ID..." oninput="filterRoster()">
                </div>
                <div class="filter-row">
                    <button class="filter-pill active" onclick="setFilter('all', this)">All</button>
                    <button class="filter-pill" onclick="setFilter('trial', this)">Active Trial</button>
                    <button class="filter-pill" onclick="setFilter('expired', this)">Expired</button>
                    <button class="filter-pill" onclick="setFilter('premium', this)">Premium</button>
                    <button class="btn btn-primary" onclick="loadAdminData()">
                        <i class="fa-solid fa-rotate"></i> Refresh
                    </button>
                </div>
            </div>

            <!-- Clean Table Card -->
            <div class="table-card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>User Account</th>
                                <th>User ID</th>
                                <th>Membership Plan</th>
                                <th>Validity / Remaining</th>
                                <th style="text-align:center;">Helpers</th>
                                <th style="text-align:center;">Ironing</th>
                                <th style="text-align:center;">Appliances</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="rosterTableBody">
                            <!-- Dynamic User Rows -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        <!-- Tab 3: System Diagnostics -->
        <div id="tab-health" class="view-pane">
            <div class="kpi-grid">
                <div class="kpi-card green">
                    <div class="kpi-top">
                        <span class="kpi-title">Database Latency</span>
                        <div class="kpi-icon"><i class="fa-solid fa-bolt"></i></div>
                    </div>
                    <div class="kpi-value"><span id="diag-latency">0</span> <span style="font-size:14px;">ms</span></div>
                    <div class="kpi-sub">Hostinger Remote MySQL</div>
                </div>

                <div class="kpi-card red">
                    <div class="kpi-top">
                        <span class="kpi-title">Upload Storage</span>
                        <div class="kpi-icon"><i class="fa-solid fa-hard-drive"></i></div>
                    </div>
                    <div class="kpi-value"><span id="diag-storage">0</span> <span style="font-size:14px;">MB</span></div>
                    <div class="kpi-sub"><span id="diag-files">0</span> Invoices & photos</div>
                </div>

                <div class="kpi-card blue">
                    <div class="kpi-top">
                        <span class="kpi-title">PHP Runtime</span>
                        <div class="kpi-icon"><i class="fa-brands fa-php"></i></div>
                    </div>
                    <div class="kpi-value" id="diag-php" style="font-size:20px;">PHP 8.2</div>
                    <div class="kpi-sub">PDO MySQL Driver Active</div>
                </div>
            </div>

            <div class="table-card" style="padding:16px 20px;">
                <h4 style="font-size:14px; font-weight:700; color:var(--text-main); margin-bottom:12px;">
                    <i class="fa-solid fa-database" style="color:var(--primary); margin-right:6px;"></i> Indexed Database Tables
                </h4>
                <div id="diagTableGrid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(180px, 1fr)); gap:10px;">
                    <!-- Dynamic Table Stats -->
                </div>
            </div>
        </div>
    </div>

    <!-- Workspace Deep Inspector Modal -->
    <div id="inspectorModal" class="modal-bg" onclick="if(event.target===this) closeInspector()">
        <div class="modal-box">
            <div class="modal-head">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div class="user-avatar" id="modalAvatar">U</div>
                    <div>
                        <h3 id="modalUserName">User Workspace</h3>
                        <div style="font-family:'Roboto Mono', monospace; font-size:11px; color:var(--text-muted);" id="modalUserId">user_id</div>
                    </div>
                </div>
                <button class="close-btn" onclick="closeInspector()">&times;</button>
            </div>

            <div class="modal-actions-bar">
                <div style="font-size:12px; color:var(--text-secondary);">
                    Plan: <span id="modalPlanTag" class="plan-tag">Trial</span> <span id="modalExpiryLabel" style="color:var(--text-muted); margin-left:4px;"></span>
                </div>
                <div style="display:flex; gap:6px;">
                    <button class="btn" style="padding:4px 10px; font-size:11.5px;" onclick="extendTrialModal()">
                        <i class="fa-solid fa-plus"></i> +30 Days Trial
                    </button>
                    <button class="btn btn-primary" style="padding:4px 10px; font-size:11.5px;" onclick="upgradePremiumModal()">
                        <i class="fa-solid fa-crown"></i> Make Lifetime
                    </button>
                    <button class="btn" style="padding:4px 10px; font-size:11.5px; color:var(--danger); border-color:var(--danger-light);" onclick="deleteUserModal()">
                        <i class="fa-solid fa-trash"></i> Purge
                    </button>
                </div>
            </div>

            <div class="modal-content">
                <div class="subtabs-bar">
                    <button class="subtab-btn active" onclick="switchSubtab('sub-helpers', this)">Helpers & Wages</button>
                    <button class="subtab-btn" onclick="switchSubtab('sub-ironing', this)">Ironing Registry</button>
                    <button class="subtab-btn" onclick="switchSubtab('sub-appliances', this)">Appliances & Bills</button>
                    <button class="subtab-btn" onclick="switchSubtab('sub-raw', this)">Raw Schema JSON</button>
                </div>

                <div id="sub-helpers" class="subtab-pane">
                    <div id="modalHelperList">Loading helpers...</div>
                </div>

                <div id="sub-ironing" class="subtab-pane" style="display:none;">
                    <div id="modalIroningList">Loading ironing workers...</div>
                </div>

                <div id="sub-appliances" class="subtab-pane" style="display:none;">
                    <div id="modalApplianceList">Loading appliances...</div>
                </div>

                <div id="sub-raw" class="subtab-pane" style="display:none;">
                    <pre style="background:#0F172A; padding:14px; border-radius:6px; max-height:350px; overflow:auto;"><code id="modalRawJson">{}</code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast"><i class="fa-solid fa-circle-check" style="color:#22C55E;"></i> <span id="toastText">Action complete</span></div>

    <script>
        const API_BASE = window.location.origin + '/api';
        let adminToken = localStorage.getItem('todo_admin_token') || '';
        let adminEmail = localStorage.getItem('todo_admin_email') || 'admin@mytask.com';
        let fullRoster = [];
        let currentFilter = 'all';
        let activeInspectedUser = null;

        if (adminToken) {
            document.getElementById('loginGate').style.display = 'none';
            document.getElementById('adminNameDisplay').innerText = adminEmail;
            loadAdminData();
            loadDiagnostics();
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastText').innerText = msg;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }



        async function attemptLogin() {
            const email = document.getElementById('loginEmail').value.trim();
            const pass = document.getElementById('loginPass').value.trim();
            const errBox = document.getElementById('loginError');

            if (!email || !pass) {
                errBox.innerText = 'Please enter both email and password';
                errBox.style.display = 'block';
                return;
            }

            try {
                const res = await fetch(`${API_BASE}/admin-login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password: pass })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    adminToken = data.token;
                    adminEmail = data.admin.email;
                    localStorage.setItem('todo_admin_token', adminToken);
                    localStorage.setItem('todo_admin_email', adminEmail);
                    document.getElementById('adminNameDisplay').innerText = adminEmail;
                    document.getElementById('loginGate').style.display = 'none';
                    errBox.style.display = 'none';
                    loadAdminData();
                    loadDiagnostics();
                } else {
                    errBox.innerText = data.error || 'Invalid administrator email or password';
                    errBox.style.display = 'block';
                }
            } catch (err) {
                errBox.innerText = 'Server connection failed: ' + err.message;
                errBox.style.display = 'block';
            }
        }

        function logoutAdmin() {
            localStorage.removeItem('todo_admin_token');
            localStorage.removeItem('todo_admin_email');
            window.location.reload();
        }

        function switchTab(tabId) {
            document.querySelectorAll('.nav-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.view-pane').forEach(p => p.classList.remove('active'));
            
            event.currentTarget.classList.add('active');
            document.getElementById(tabId).classList.add('active');

            if (tabId === 'tab-health') {
                loadDiagnostics();
            }
        }

        async function loadAdminData() {
            const t0 = performance.now();
            try {
                const res = await fetch(`${API_BASE}/admin-overview?token=${adminToken}`);
                if (res.status === 401) {
                    logoutAdmin();
                    return;
                }

                const data = await res.json();
                if (data.success) {
                    const elapsed = Math.round(performance.now() - t0);
                    document.getElementById('headerLatency').innerText = `Online • ${elapsed}ms`;

                    // Update KPIs
                    document.getElementById('metric-total-users').innerText = data.metrics.total_users;
                    document.getElementById('metric-active-trials').innerText = data.metrics.active_trials;
                    document.getElementById('metric-expired-trials').innerText = data.metrics.expired_trials;
                    document.getElementById('metric-premium-users').innerText = data.metrics.premium_users;
                    document.getElementById('metric-total-helpers').innerText = data.metrics.total_employees;
                    document.getElementById('metric-total-attendance').innerText = data.metrics.total_attendance;
                    document.getElementById('metric-total-appliances').innerText = data.metrics.total_appliances;
                    document.getElementById('metric-total-spend').innerText = Number(data.metrics.total_service_spend).toLocaleString();

                    fullRoster = data.users;
                    renderRoster(fullRoster);

                    if (fullRoster.length > 0 && !document.getElementById('activeTestUserId').value) {
                        document.getElementById('activeTestUserId').value = fullRoster[0].id;
                    }
                }
            } catch (err) {
                console.error(err);
            }
        }

        function setFilter(filter, el) {
            currentFilter = filter;
            document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
            el.classList.add('active');
            filterRoster();
        }

        function filterRoster() {
            const q = document.getElementById('rosterSearch').value.toLowerCase().trim();
            const now = new Date();

            const filtered = fullRoster.filter(u => {
                const matches = u.username.toLowerCase().includes(q) || u.id.toLowerCase().includes(q);
                if (!matches) return false;

                if (currentFilter === 'trial') {
                    return u.userType === 'guest' && (!u.expiresAt || new Date(u.expiresAt) > now);
                } else if (currentFilter === 'expired') {
                    return u.userType === 'guest' && u.expiresAt && new Date(u.expiresAt) <= now;
                } else if (currentFilter === 'premium') {
                    return u.userType === 'registered';
                }
                return true;
            });

            renderRoster(filtered);
        }

        function renderRoster(users) {
            const tbody = document.getElementById('rosterTableBody');
            tbody.innerHTML = '';

            if (users.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:28px; color:var(--text-muted);">No user accounts found</td></tr>`;
                return;
            }

            const now = new Date();

            users.forEach(u => {
                const tr = document.createElement('tr');
                const createdDate = new Date(u.createdAt).toLocaleDateString();

                let validityText = '<span style="color:var(--primary); font-weight:700;"><i class="fa-solid fa-infinity"></i> Lifetime</span>';
                if (u.userType === 'guest') {
                    if (u.expiresAt) {
                        const days = Math.ceil((new Date(u.expiresAt) - now) / (1000 * 60 * 60 * 24));
                        if (days > 0) {
                            validityText = `<span style="color:var(--warning); font-weight:600;"><i class="fa-regular fa-clock"></i> ${days} days left</span>`;
                        } else {
                            validityText = `<span style="color:var(--danger); font-weight:700;"><i class="fa-solid fa-circle-exclamation"></i> Expired</span>`;
                        }
                    } else {
                        validityText = '<span style="color:var(--text-muted);">No expiry set</span>';
                    }
                }

                const initial = u.username ? u.username[0].toUpperCase() : 'U';
                const avatar = u.profilePic 
                    ? `<img src="${u.profilePic.startsWith('http') ? u.profilePic : window.location.origin + '/' + u.profilePic}">`
                    : initial;

                tr.innerHTML = `
                    <td>
                        <div class="user-item">
                            <div class="user-avatar">${avatar}</div>
                            <div>
                                <div class="user-name">${u.username}</div>
                                <div class="user-sub">Joined ${createdDate}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="id-badge" onclick="copyId('${u.id}')" title="Click to copy ID">
                            ${u.id} <i class="fa-regular fa-copy"></i>
                        </span>
                    </td>
                    <td>
                        <span class="plan-tag ${u.userType === 'guest' ? 'trial' : 'premium'}">
                            ${u.userType === 'guest' ? 'Free Trial' : 'Premium'}
                        </span>
                    </td>
                    <td>${validityText}</td>
                    <td style="text-align:center;"><span class="count-badge">${u.employee_count}</span></td>
                    <td style="text-align:center;"><span class="count-badge">${u.ironing_worker_count}</span></td>
                    <td style="text-align:center;"><span class="count-badge">${u.appliance_count}</span></td>
                    <td style="text-align:right;">
                        <div class="action-btns">
                            <button class="icon-btn inspect" title="Inspect Workspace" onclick="inspectUser('${u.id}')">
                                <i class="fa-solid fa-folder-open"></i>
                            </button>
                            <button class="icon-btn delete" title="Purge User" onclick="deleteUser('${u.id}', '${u.username}')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function copyId(id) {
            navigator.clipboard.writeText(id);
            showToast(`Copied ${id}`);
        }

        function setContextUser(userId) {
            document.getElementById('activeTestUserId').value = userId;
            switchTab('tab-api');
            showToast(`Context set to: ${userId}`);
        }

        // Inspector Modal Logic
        async function inspectUser(userId) {
            const modal = document.getElementById('inspectorModal');
            modal.classList.add('active');

            document.getElementById('modalUserName').innerText = 'Loading...';
            document.getElementById('modalUserId').innerText = userId;
            document.getElementById('modalHelperList').innerText = 'Loading helper records...';

            try {
                const res = await fetch(`${API_BASE}/admin-user-details?token=${adminToken}&userId=${userId}`);
                const data = await res.json();
                if (data.success) {
                    activeInspectedUser = data.user;
                    document.getElementById('modalUserName').innerText = data.user.username;
                    document.getElementById('modalAvatar').innerText = data.user.username[0].toUpperCase();

                    const isPrem = data.user.userType === 'registered';
                    document.getElementById('modalPlanTag').className = `plan-tag ${isPrem ? 'premium' : 'trial'}`;
                    document.getElementById('modalPlanTag').innerText = isPrem ? 'Premium Lifetime' : 'Free Trial';
                    document.getElementById('modalExpiryLabel').innerText = data.user.expiresAt ? `(Expires: ${new Date(data.user.expiresAt).toLocaleDateString()})` : '';

                    // Helpers
                    let helperHtml = '';
                    if (data.employees.length === 0) {
                        helperHtml = '<div style="color:var(--text-muted); padding:10px 0;">No house helpers recorded.</div>';
                    } else {
                        data.employees.forEach(emp => {
                            helperHtml += `
                                <div class="item-card">
                                    <div>
                                        <div style="font-weight:700; color:var(--text-main);">${emp.name}</div>
                                        <div style="font-size:11.5px; color:var(--text-muted);">Contact: ${emp.contact || 'N/A'} • Joined: ${emp.joiningDate || 'N/A'}</div>
                                    </div>
                                    <div style="font-weight:700; color:var(--primary);">₹${emp.baseSalary} / ${emp.salaryBasis}</div>
                                </div>
                            `;
                        });
                    }
                    document.getElementById('modalHelperList').innerHTML = helperHtml;

                    // Ironing
                    let ironHtml = '';
                    if (data.ironingWorkers.length === 0) {
                        ironHtml = '<div style="color:var(--text-muted); padding:10px 0;">No ironing workers registered.</div>';
                    } else {
                        data.ironingWorkers.forEach(w => {
                            ironHtml += `
                                <div class="item-card">
                                    <div>
                                        <div style="font-weight:700;">${w.name}</div>
                                        <div style="font-size:11.5px; color:var(--text-muted);">Contact: ${w.contact || 'N/A'}</div>
                                    </div>
                                    <div style="font-family:'Roboto Mono', monospace; font-size:11.5px; color:var(--text-secondary);">${w.id}</div>
                                </div>
                            `;
                        });
                    }
                    document.getElementById('modalIroningList').innerHTML = ironHtml;

                    // Appliances
                    let appHtml = '';
                    if (data.appliances.length === 0) {
                        appHtml = '<div style="color:var(--text-muted); padding:10px 0;">No appliances recorded.</div>';
                    } else {
                        data.appliances.forEach(a => {
                            appHtml += `
                                <div class="item-card">
                                    <div>
                                        <div style="font-weight:700;">${a.name} <span style="font-size:11px; color:var(--text-muted);">(${a.brand || 'Generic'})</span></div>
                                        <div style="font-size:11.5px; color:var(--text-muted);">S/N: ${a.serialNumber || 'N/A'} • Warranty: ${a.warrantyEnd || 'N/A'}</div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                    document.getElementById('modalApplianceList').innerHTML = appHtml;

                    // Raw JSON
                    document.getElementById('modalRawJson').innerText = JSON.stringify(data, null, 2);
                }
            } catch (err) {
                console.error(err);
            }
        }

        function closeInspector() {
            document.getElementById('inspectorModal').classList.remove('active');
        }

        function switchSubtab(paneId, el) {
            document.querySelectorAll('.subtab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.subtab-pane').forEach(p => p.style.display = 'none');
            el.classList.add('active');
            document.getElementById(paneId).style.display = 'block';
        }

        async function extendTrialModal() {
            if (!activeInspectedUser) return;
            const res = await fetch(`${API_BASE}/admin-update-user?token=${adminToken}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ userId: activeInspectedUser.id, extendDays: 30 })
            });
            const data = await res.json();
            if (data.success) {
                showToast('Trial extended +30 days');
                inspectUser(activeInspectedUser.id);
                loadAdminData();
            }
        }

        async function upgradePremiumModal() {
            if (!activeInspectedUser) return;
            const res = await fetch(`${API_BASE}/admin-update-user?token=${adminToken}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ userId: activeInspectedUser.id, userType: 'registered' })
            });
            const data = await res.json();
            if (data.success) {
                showToast('Upgraded to Lifetime Premium');
                inspectUser(activeInspectedUser.id);
                loadAdminData();
            }
        }

        async function deleteUserModal() {
            if (!activeInspectedUser) return;
            if (confirm(`Purge user account ${activeInspectedUser.username} and all records?`)) {
                const res = await fetch(`${API_BASE}/admin-delete-user?token=${adminToken}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ userId: activeInspectedUser.id })
                });
                const data = await res.json();
                if (data.success) {
                    showToast('User purged');
                    closeInspector();
                    loadAdminData();
                }
            }
        }

        async function deleteUser(userId, username) {
            if (confirm(`Purge user account ${username} (${userId})?`)) {
                const res = await fetch(`${API_BASE}/admin-delete-user?token=${adminToken}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ userId })
                });
                const data = await res.json();
                if (data.success) {
                    showToast('User purged');
                    loadAdminData();
                }
            }
        }

        // Diagnostics
        async function loadDiagnostics() {
            try {
                const res = await fetch(`${API_BASE}/admin-system-health?token=${adminToken}`);
                const data = await res.json();
                if (data.success) {
                    document.getElementById('diag-latency').innerText = data.system.db_latency_ms;
                    document.getElementById('diag-storage').innerText = data.system.uploads.total_size_mb;
                    document.getElementById('diag-files').innerText = data.system.uploads.file_count;
                    document.getElementById('diag-php').innerText = `PHP ${data.system.php_version}`;

                    const grid = document.getElementById('diagTableGrid');
                    grid.innerHTML = '';
                    for (const [tbl, count] of Object.entries(data.system.tables)) {
                        const div = document.createElement('div');
                        div.className = 'item-card';
                        div.innerHTML = `
                            <div>
                                <div style="font-weight:700;">${tbl}</div>
                                <div style="font-size:11px; color:var(--text-muted);">Table records</div>
                            </div>
                            <div class="count-badge" style="color:var(--primary); font-size:12px;">${count}</div>
                        `;
                        grid.appendChild(div);
                    }
                }
            } catch (err) {
                console.error(err);
            }
        }


    </script>
</body>
</html>
