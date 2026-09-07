<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My-Task Enterprise Management & API Console</title>
    <!-- Modern Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --bg-base: #090d16;
            --bg-surface: #0f172a;
            --bg-card: #141e33;
            --bg-card-hover: #1a2744;
            --bg-input: #0b1120;
            
            --border-subtle: rgba(255, 255, 255, 0.08);
            --border-focus: rgba(6, 182, 212, 0.5);
            
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            
            --primary: #06b6d4;
            --primary-glow: rgba(6, 182, 212, 0.25);
            --primary-dark: #0891b2;
            
            --accent-indigo: #6366f1;
            --accent-emerald: #10b981;
            --accent-amber: #f59e0b;
            --accent-rose: #f43f5e;
            
            --method-get: #38bdf8;
            --method-post: #10b981;
            --method-put: #f59e0b;
            --method-delete: #f43f5e;
            
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-xl: 26px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-base);
            color: var(--text-primary);
            line-height: 1.5;
            min-height: 100vh;
            padding: 24px;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(6, 182, 212, 0.07) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(99, 102, 241, 0.07) 0%, transparent 40%);
            background-attachment: fixed;
        }

        .container {
            max-width: 1320px;
            margin: 0 auto;
        }

        /* Glass App Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 26px;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            margin-bottom: 24px;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
        }

        .logo-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #06b6d4, #6366f1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #fff;
            box-shadow: 0 0 20px var(--primary-glow);
        }

        .logo-text h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #f8fafc, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .version-badge {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: rgba(6, 182, 212, 0.15);
            border: 1px solid rgba(6, 182, 212, 0.3);
            color: var(--primary);
            padding: 2px 8px;
            border-radius: 6px;
            -webkit-text-fill-color: var(--primary);
        }

        .logo-text p {
            font-size: 12px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .status-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 14px;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.25);
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            color: var(--accent-emerald);
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: var(--accent-emerald);
            box-shadow: 0 0 10px var(--accent-emerald);
            animation: pulseAnim 2s infinite;
        }

        @keyframes pulseAnim {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.3); opacity: 0.6; }
        }

        .btn-logout {
            background: rgba(244, 63, 94, 0.12);
            border: 1px solid rgba(244, 63, 94, 0.25);
            color: var(--accent-rose);
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .btn-logout:hover {
            background: rgba(244, 63, 94, 0.22);
            transform: translateY(-1px);
        }

        /* Top Navigation Tabs */
        .nav-tabs {
            display: flex;
            gap: 8px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border-subtle);
            padding: 6px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            width: fit-content;
        }

        .nav-tab-btn {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            padding: 9px 20px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13.5px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s ease;
        }

        .nav-tab-btn:hover {
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.04);
        }

        .nav-tab-btn.active {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            box-shadow: 0 4px 14px var(--primary-glow);
        }

        /* View Sections */
        .tab-view {
            display: none;
            animation: viewFadeIn 0.3s ease;
        }

        .tab-view.active {
            display: block;
        }

        @keyframes viewFadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Metrics KPI Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .kpi-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 20px;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .kpi-card:hover {
            transform: translateY(-3px);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .kpi-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--kpi-accent, var(--primary));
        }

        .kpi-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .kpi-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .kpi-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
            color: var(--kpi-accent, var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .kpi-value {
            font-family: 'Outfit', sans-serif;
            font-size: 30px;
            font-weight: 800;
            color: #fff;
            line-height: 1;
            margin-bottom: 6px;
        }

        .kpi-sub {
            font-size: 11.5px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Filter Toolbar */
        .table-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 16px;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 400px;
        }

        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
        }

        .search-input {
            width: 100%;
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            color: #fff;
            padding: 10px 14px 10px 38px;
            border-radius: var(--radius-sm);
            font-size: 13.5px;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 12px var(--primary-glow);
        }

        .filter-group {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .filter-chip {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            color: var(--text-secondary);
            padding: 7px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .filter-chip:hover {
            color: var(--text-primary);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .filter-chip.active {
            background: rgba(6, 182, 212, 0.15);
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-action {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            color: var(--text-primary);
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .btn-action:hover {
            background: var(--bg-card-hover);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            border: none;
            box-shadow: 0 4px 14px var(--primary-glow);
        }

        .btn-primary:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }

        /* Glass Table */
        .glass-panel {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            margin-bottom: 30px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        thead {
            background: rgba(255, 255, 255, 0.02);
            border-bottom: 1px solid var(--border-subtle);
        }

        th {
            padding: 14px 20px;
            font-size: 11.5px;
            font-weight: 700;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-subtle);
            font-size: 13.5px;
            color: var(--text-primary);
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--accent-indigo));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
            border: 1.5px solid rgba(255, 255, 255, 0.15);
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .user-meta-name {
            font-weight: 600;
            color: #fff;
            font-size: 14px;
        }

        .user-meta-created {
            font-size: 11.5px;
            color: var(--text-muted);
        }

        .id-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            background: rgba(255, 255, 255, 0.05);
            padding: 4px 8px;
            border-radius: 6px;
            color: var(--text-secondary);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .id-badge:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .plan-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .plan-tag.trial {
            background: rgba(148, 163, 184, 0.12);
            border: 1px solid rgba(148, 163, 184, 0.25);
            color: #cbd5e1;
        }

        .plan-tag.premium {
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: var(--accent-amber);
        }

        .count-pill {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-subtle);
            padding: 3px 10px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 12px;
            color: var(--text-secondary);
            display: inline-block;
        }

        .action-cell {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-icon-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border-subtle);
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s ease;
        }

        .btn-icon-action:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
        }

        .btn-icon-action.inspect:hover {
            background: rgba(6, 182, 212, 0.2);
            color: var(--primary);
            border-color: var(--primary);
        }

        .btn-icon-action.delete:hover {
            background: rgba(244, 63, 94, 0.2);
            color: var(--accent-rose);
            border-color: var(--accent-rose);
        }

        /* Modal Overlays */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(9, 13, 22, 0.85);
            backdrop-filter: blur(12px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            padding: 20px;
        }

        .modal-backdrop.active {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-window {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-xl);
            width: 100%;
            max-width: 860px;
            max-height: 88vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            transform: scale(0.95);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }

        .modal-backdrop.active .modal-window {
            transform: scale(1);
        }

        .modal-header {
            padding: 20px 26px;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.02);
        }

        .modal-header h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            font-weight: 700;
        }

        .modal-close-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 18px;
            cursor: pointer;
            padding: 4px;
            transition: color 0.2s ease;
        }

        .modal-close-btn:hover {
            color: var(--text-primary);
        }

        .modal-body {
            padding: 24px 26px;
            overflow-y: auto;
        }

        /* Modal Subtabs */
        .modal-subtabs {
            display: flex;
            gap: 6px;
            border-bottom: 1px solid var(--border-subtle);
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .modal-subtab-btn {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            padding: 6px 14px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .modal-subtab-btn.active {
            background: rgba(6, 182, 212, 0.15);
            color: var(--primary);
        }

        .detail-item {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 14px 18px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Interactive API Studio */
        .api-studio-wrap {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .context-selector-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }

        .endpoint-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            margin-bottom: 12px;
            overflow: hidden;
            transition: border-color 0.2s ease;
        }

        .endpoint-card:hover {
            border-color: rgba(255, 255, 255, 0.15);
        }

        .endpoint-header {
            padding: 14px 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
            user-select: none;
            gap: 14px;
        }

        .method-badge {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 800;
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 6px;
            min-width: 68px;
            text-align: center;
            color: #fff;
        }

        .method-badge.get { background-color: var(--method-get); }
        .method-badge.post { background-color: var(--method-post); }
        .method-badge.put { background-color: var(--method-put); }
        .method-badge.delete { background-color: var(--method-delete); }

        .endpoint-path {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13.5px;
            font-weight: 600;
            color: #fff;
            flex: 1;
        }

        .endpoint-summary {
            font-size: 12.5px;
            color: var(--text-secondary);
        }

        .endpoint-details {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: rgba(0, 0, 0, 0.2);
            border-top: 1px solid transparent;
        }

        .endpoint-details.active {
            max-height: 1200px;
            border-top-color: var(--border-subtle);
            padding: 20px;
        }

        .code-textarea {
            width: 100%;
            height: 100px;
            background: var(--bg-input);
            border: 1px solid var(--border-subtle);
            color: #38bdf8;
            padding: 12px;
            border-radius: var(--radius-sm);
            font-family: 'JetBrains Mono', monospace;
            font-size: 12.5px;
            margin-bottom: 12px;
            resize: vertical;
        }

        .code-textarea:focus {
            outline: none;
            border-color: var(--primary);
        }

        .response-console {
            background: #000;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 16px;
            margin-top: 24px;
            position: relative;
        }

        .console-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 12px;
            color: var(--text-secondary);
        }

        pre code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12.5px;
            color: #34d399;
            white-space: pre-wrap;
            word-break: break-all;
        }

        /* Login Overlay */
        .auth-gate-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: var(--bg-base);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 20px;
        }

        .auth-gate-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-xl);
            padding: 40px;
            width: 100%;
            max-width: 440px;
            text-align: center;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7);
            position: relative;
        }

        .auth-gate-card::before {
            content: '';
            position: absolute;
            top: -1px;
            left: 50%;
            transform: translateX(-50%);
            width: 140px;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
        }

        .auth-gate-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: rgba(6, 182, 212, 0.15);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin: 0 auto 20px;
            box-shadow: 0 0 24px var(--primary-glow);
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            background: var(--bg-input);
            border: 1px solid var(--border-subtle);
            color: #fff;
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 12px var(--primary-glow);
        }

        /* Toast notification */
        #toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            color: #fff;
            padding: 12px 20px;
            border-radius: var(--radius-md);
            font-size: 13.5px;
            font-weight: 600;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        @media (max-width: 768px) {
            body { padding: 12px; }
            header { flex-direction: column; gap: 16px; align-items: flex-start; }
            .header-actions { width: 100%; justify-content: space-between; }
            .table-toolbar { flex-direction: column; align-items: stretch; }
            .search-box { max-width: 100%; }
        }
    </style>
</head>
<body>

    <!-- Security Gate Login Overlay -->
    <div id="authGate" class="auth-gate-overlay">
        <div class="auth-gate-card">
            <div class="auth-gate-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h2 style="font-family:'Outfit', sans-serif; font-size:24px; font-weight:800; margin-bottom:8px;">Admin Security Gate</h2>
            <p style="color:var(--text-secondary); font-size:13px; margin-bottom:28px;">Authenticate with system credentials to access user registries, telemetry & API consoles.</p>
            
            <div class="form-group">
                <label for="adminTokenInput">Administrator Key</label>
                <input type="password" id="adminTokenInput" class="form-control" placeholder="Enter Token Key (e.g. TodoAdmin102030)" onkeydown="if(event.key === 'Enter') loginAdmin()">
            </div>

            <button class="btn-action btn-primary" style="width:100%; justify-content:center; padding:12px;" onclick="loginAdmin()">
                <i class="fa-solid fa-key"></i> Authenticate & Enter
            </button>

            <div id="authErrorMsg" style="color:var(--accent-rose); font-size:12.5px; font-weight:600; margin-top:14px; display:none;">
                <i class="fa-solid fa-circle-exclamation"></i> Invalid administrative token.
            </div>
        </div>
    </div>

    <!-- Main App Container -->
    <div class="container">
        <!-- Top App Header -->
        <header>
            <div class="logo-wrap">
                <div class="logo-icon">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div class="logo-text">
                    <h1>My-Task Hub <span class="version-badge">Enterprise Cloud</span></h1>
                    <p>Central Operations & Multi-Tenant Registry Suite</p>
                </div>
            </div>
            <div class="header-actions">
                <div class="status-pill">
                    <span class="pulse-dot"></span>
                    <span id="headerLatencyText">Cloud Online</span>
                </div>
                <button class="btn-logout" onclick="logoutAdmin()">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Exit
                </button>
            </div>
        </header>

        <!-- Top Navigation -->
        <div class="nav-tabs">
            <button class="nav-tab-btn active" onclick="switchNavTab('roster-view')">
                <i class="fa-solid fa-users"></i> Users & Roster
            </button>
            <button class="nav-tab-btn" onclick="switchNavTab('api-view')">
                <i class="fa-solid fa-terminal"></i> API Console Studio
            </button>
            <button class="nav-tab-btn" onclick="switchNavTab('diagnostics-view')">
                <i class="fa-solid fa-chart-line"></i> Server Diagnostics
            </button>
        </div>

        <!-- 1. Roster & Executive View -->
        <div id="roster-view" class="tab-view active">
            <!-- Metrics KPI Grid -->
            <div class="kpi-grid">
                <div class="kpi-card" style="--kpi-accent: #06b6d4;">
                    <div class="kpi-top">
                        <span class="kpi-title">Total Users</span>
                        <div class="kpi-icon"><i class="fa-solid fa-user-group"></i></div>
                    </div>
                    <div class="kpi-value" id="kpi-total-users">0</div>
                    <div class="kpi-sub"><i class="fa-solid fa-arrow-trend-up"></i> Registered Accounts</div>
                </div>

                <div class="kpi-card" style="--kpi-accent: #f59e0b;">
                    <div class="kpi-top">
                        <span class="kpi-title">Active Trials</span>
                        <div class="kpi-icon"><i class="fa-solid fa-hourglass-half"></i></div>
                    </div>
                    <div class="kpi-value" id="kpi-trial-users">0</div>
                    <div class="kpi-sub"><span id="kpi-expired-trials">0</span> Expired</div>
                </div>

                <div class="kpi-card" style="--kpi-accent: #10b981;">
                    <div class="kpi-top">
                        <span class="kpi-title">Premium Lifetime</span>
                        <div class="kpi-icon"><i class="fa-solid fa-crown"></i></div>
                    </div>
                    <div class="kpi-value" id="kpi-premium-users">0</div>
                    <div class="kpi-sub">Uncapped Members</div>
                </div>

                <div class="kpi-card" style="--kpi-accent: #6366f1;">
                    <div class="kpi-top">
                        <span class="kpi-title">Helpers & Attendance</span>
                        <div class="kpi-icon"><i class="fa-solid fa-user-nurse"></i></div>
                    </div>
                    <div class="kpi-value" id="kpi-total-helpers">0</div>
                    <div class="kpi-sub"><span id="kpi-total-attendance">0</span> Logs recorded</div>
                </div>

                <div class="kpi-card" style="--kpi-accent: #38bdf8;">
                    <div class="kpi-top">
                        <span class="kpi-title">Ironing Registry</span>
                        <div class="kpi-icon"><i class="fa-solid fa-shirt"></i></div>
                    </div>
                    <div class="kpi-value" id="kpi-ironing-workers">0</div>
                    <div class="kpi-sub"><span id="kpi-ironing-records">0</span> Work logs</div>
                </div>

                <div class="kpi-card" style="--kpi-accent: #ec4899;">
                    <div class="kpi-top">
                        <span class="kpi-title">Appliances & Repairs</span>
                        <div class="kpi-icon"><i class="fa-solid fa-plug"></i></div>
                    </div>
                    <div class="kpi-value" id="kpi-appliances">0</div>
                    <div class="kpi-sub">₹<span id="kpi-service-spend">0</span> Maintenance</div>
                </div>
            </div>

            <!-- Table & Filters Section -->
            <div class="table-toolbar">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="rosterSearchInput" class="search-input" placeholder="Search by username, user ID or date..." oninput="filterRoster()">
                </div>
                <div class="filter-group">
                    <button class="filter-chip active" onclick="setPlanFilter('all', this)">All</button>
                    <button class="filter-chip" onclick="setPlanFilter('active-trial', this)">Active Trial</button>
                    <button class="filter-chip" onclick="setPlanFilter('expired', this)">Expired</button>
                    <button class="filter-chip" onclick="setPlanFilter('premium', this)">Premium</button>
                    <button class="btn-action" onclick="fetchAdminData()">
                        <i class="fa-solid fa-rotate"></i> Refresh
                    </button>
                </div>
            </div>

            <!-- Roster Table Panel -->
            <div class="glass-panel">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>User Account</th>
                                <th>Account ID</th>
                                <th>Tier Class</th>
                                <th>Validity & Expiry</th>
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

        <!-- 2. Interactive API Studio Console -->
        <div id="api-view" class="tab-view">
            <div class="api-studio-wrap">
                <!-- Context Injector Banner -->
                <div class="context-selector-card">
                    <div>
                        <div style="font-size:12px; font-weight:700; text-transform:uppercase; color:var(--text-secondary); margin-bottom:4px;">
                            Active Testing Context (<code style="color:var(--primary);">X-User-Id</code>)
                        </div>
                        <div style="font-size:13px; color:var(--text-muted);">Select or enter a User ID from the roster to test scoped endpoints:</div>
                    </div>
                    <div style="display:flex; gap:8px;">
                        <input type="text" id="globalTestUserId" class="form-control" style="width:280px; padding:8px 12px; font-family:'JetBrains Mono', monospace;" placeholder="user_60a... / guest_...">
                    </div>
                </div>

                <!-- Endpoint Groups Accordion -->
                <div id="endpointsAccordion">
                    
                    <!-- Group: Authentication -->
                    <h3 style="font-size:14px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:0.6px; margin: 18px 0 10px;">
                        <i class="fa-solid fa-lock"></i> 1. Authentication & Accounts
                    </h3>

                    <!-- POST /api/register-guest -->
                    <div class="endpoint-card">
                        <div class="endpoint-header" onclick="toggleEndpoint(this)">
                            <span class="method-badge post">POST</span>
                            <span class="endpoint-path">/api/register-guest</span>
                            <span class="endpoint-summary">Initiates a 30-day Free Trial guest session</span>
                        </div>
                        <div class="endpoint-details">
                            <p style="font-size:13px; color:var(--text-secondary); margin-bottom:14px;">No payload required. Generates an instant guest ID with 30-day trial expiry timestamp.</p>
                            <button class="btn-action btn-primary" onclick="executeApiCall('POST', '/api/register-guest')">
                                <i class="fa-solid fa-play"></i> Run Sandbox Test
                            </button>
                        </div>
                    </div>

                    <!-- POST /api/login -->
                    <div class="endpoint-card">
                        <div class="endpoint-header" onclick="toggleEndpoint(this)">
                            <span class="method-badge post">POST</span>
                            <span class="endpoint-path">/api/login</span>
                            <span class="endpoint-summary">Standard user login with username & password</span>
                        </div>
                        <div class="endpoint-details">
                            <label style="font-size:12px; font-weight:700; color:var(--text-secondary);">Request Payload (JSON):</label>
                            <textarea id="payload-login" class="code-textarea">{
  "username": "arvind",
  "password": "password123"
}</textarea>
                            <button class="btn-action btn-primary" onclick="executeApiCall('POST', '/api/login', 'payload-login')">
                                <i class="fa-solid fa-play"></i> Run Sandbox Test
                            </button>
                        </div>
                    </div>

                    <!-- POST /api/login-google -->
                    <div class="endpoint-card">
                        <div class="endpoint-header" onclick="toggleEndpoint(this)">
                            <span class="method-badge post">POST</span>
                            <span class="endpoint-path">/api/login-google</span>
                            <span class="endpoint-summary">Google OAuth authentication & sync</span>
                        </div>
                        <div class="endpoint-details">
                            <label style="font-size:12px; font-weight:700; color:var(--text-secondary);">Request Payload (JSON):</label>
                            <textarea id="payload-google" class="code-textarea">{
  "googleId": "109823741982734",
  "email": "user@gmail.com"
}</textarea>
                            <button class="btn-action btn-primary" onclick="executeApiCall('POST', '/api/login-google', 'payload-google')">
                                <i class="fa-solid fa-play"></i> Run Sandbox Test
                            </button>
                        </div>
                    </div>

                    <!-- Group: Profile & Subscriptions -->
                    <h3 style="font-size:14px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:0.6px; margin: 24px 0 10px;">
                        <i class="fa-solid fa-id-card"></i> 2. Profile & Plan Management
                    </h3>

                    <!-- GET /api/get-profile -->
                    <div class="endpoint-card">
                        <div class="endpoint-header" onclick="toggleEndpoint(this)">
                            <span class="method-badge get">GET</span>
                            <span class="endpoint-path">/api/get-profile</span>
                            <span class="endpoint-summary">Fetch active profile info, validity, and plan</span>
                        </div>
                        <div class="endpoint-details">
                            <p style="font-size:13px; color:var(--text-secondary); margin-bottom:14px;">Requires <code>X-User-Id</code> header.</p>
                            <button class="btn-action btn-primary" onclick="executeApiCall('GET', '/api/get-profile', null, true)">
                                <i class="fa-solid fa-play"></i> Run Scoped Test
                            </button>
                        </div>
                    </div>

                    <!-- POST /api/update-subscription -->
                    <div class="endpoint-card">
                        <div class="endpoint-header" onclick="toggleEndpoint(this)">
                            <span class="method-badge post">POST</span>
                            <span class="endpoint-path">/api/update-subscription</span>
                            <span class="endpoint-summary">Upgrade active account to Lifetime Premium</span>
                        </div>
                        <div class="endpoint-details">
                            <p style="font-size:13px; color:var(--text-secondary); margin-bottom:14px;">Upgrades the scoped user context to <code>registered</code> tier with no expiration date.</p>
                            <button class="btn-action btn-primary" onclick="executeApiCall('POST', '/api/update-subscription', null, true)">
                                <i class="fa-solid fa-play"></i> Run Scoped Test
                            </button>
                        </div>
                    </div>

                    <!-- Group: House Helpers -->
                    <h3 style="font-size:14px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:0.6px; margin: 24px 0 10px;">
                        <i class="fa-solid fa-user-nurse"></i> 3. House Helpers & Attendance
                    </h3>

                    <!-- GET /api/employees -->
                    <div class="endpoint-card">
                        <div class="endpoint-header" onclick="toggleEndpoint(this)">
                            <span class="method-badge get">GET</span>
                            <span class="endpoint-path">/api/employees</span>
                            <span class="endpoint-summary">List all helpers registered in user workspace</span>
                        </div>
                        <div class="endpoint-details">
                            <p style="font-size:13px; color:var(--text-secondary); margin-bottom:14px;">Returns helper cards with wage details, joining dates, and photos.</p>
                            <button class="btn-action btn-primary" onclick="executeApiCall('GET', '/api/employees', null, true)">
                                <i class="fa-solid fa-play"></i> Run Scoped Test
                            </button>
                        </div>
                    </div>

                    <!-- GET /api/attendance -->
                    <div class="endpoint-card">
                        <div class="endpoint-header" onclick="toggleEndpoint(this)">
                            <span class="method-badge get">GET</span>
                            <span class="endpoint-path">/api/attendance</span>
                            <span class="endpoint-summary">Retrieve attendance history and advances</span>
                        </div>
                        <div class="endpoint-details">
                            <p style="font-size:13px; color:var(--text-secondary); margin-bottom:14px;">Query parameter <code>?employeeId=...</code> can be appended to filter by helper.</p>
                            <button class="btn-action btn-primary" onclick="executeApiCall('GET', '/api/attendance', null, true)">
                                <i class="fa-solid fa-play"></i> Run Scoped Test
                            </button>
                        </div>
                    </div>

                    <!-- Group: Ironing Hub -->
                    <h3 style="font-size:14px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:0.6px; margin: 24px 0 10px;">
                        <i class="fa-solid fa-shirt"></i> 4. Ironing Registry & Logs
                    </h3>

                    <!-- GET /api/ironing-workers -->
                    <div class="endpoint-card">
                        <div class="endpoint-header" onclick="toggleEndpoint(this)">
                            <span class="method-badge get">GET</span>
                            <span class="endpoint-path">/api/ironing-workers</span>
                            <span class="endpoint-summary">List all registered dhobi / ironing workers</span>
                        </div>
                        <div class="endpoint-details">
                            <button class="btn-action btn-primary" onclick="executeApiCall('GET', '/api/ironing-workers', null, true)">
                                <i class="fa-solid fa-play"></i> Run Scoped Test
                            </button>
                        </div>
                    </div>

                    <!-- Group: Appliances Hub -->
                    <h3 style="font-size:14px; font-weight:700; color:var(--primary); text-transform:uppercase; letter-spacing:0.6px; margin: 24px 0 10px;">
                        <i class="fa-solid fa-plug"></i> 5. Appliances & Service Records
                    </h3>

                    <!-- GET /api/appliances -->
                    <div class="endpoint-card">
                        <div class="endpoint-header" onclick="toggleEndpoint(this)">
                            <span class="method-badge get">GET</span>
                            <span class="endpoint-path">/api/appliances</span>
                            <span class="endpoint-summary">List appliances, warranty details, and bills</span>
                        </div>
                        <div class="endpoint-details">
                            <button class="btn-action btn-primary" onclick="executeApiCall('GET', '/api/appliances', null, true)">
                                <i class="fa-solid fa-play"></i> Run Scoped Test
                            </button>
                        </div>
                    </div>

                    <!-- GET /api/service-records -->
                    <div class="endpoint-card">
                        <div class="endpoint-header" onclick="toggleEndpoint(this)">
                            <span class="method-badge get">GET</span>
                            <span class="endpoint-path">/api/service-records</span>
                            <span class="endpoint-summary">List appliance service logs and repair prices</span>
                        </div>
                        <div class="endpoint-details">
                            <button class="btn-action btn-primary" onclick="executeApiCall('GET', '/api/service-records', null, true)">
                                <i class="fa-solid fa-play"></i> Run Scoped Test
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Live Response Inspector Console -->
                <div id="apiConsoleOutput" class="response-console" style="display:none;">
                    <div class="console-top">
                        <div>
                            <span style="font-weight:700; color:#fff;">Target:</span> <code id="consoleTargetUrl" style="color:var(--primary);">/api/...</code>
                        </div>
                        <div>
                            <span style="margin-right:12px;" id="consoleTiming">0 ms</span>
                            <span id="consoleStatusBadge" class="plan-tag" style="background:#10b98120; color:#10b981; border:1px solid #10b98140;">200 OK</span>
                        </div>
                    </div>
                    <div style="display:flex; justify-content:flex-end; margin-bottom:8px;">
                        <button class="btn-action" style="padding:4px 10px; font-size:11px;" onclick="copyConsoleJson()">
                            <i class="fa-solid fa-copy"></i> Copy JSON
                        </button>
                    </div>
                    <pre><code id="consoleJsonBody">{}</code></pre>
                </div>
            </div>
        </div>

        <!-- 3. Server & Database Diagnostics View -->
        <div id="diagnostics-view" class="tab-view">
            <div class="kpi-grid" style="margin-bottom:24px;">
                <div class="kpi-card" style="--kpi-accent: #10b981;">
                    <div class="kpi-top">
                        <span class="kpi-title">Database Latency</span>
                        <div class="kpi-icon"><i class="fa-solid fa-bolt"></i></div>
                    </div>
                    <div class="kpi-value"><span id="diag-db-latency">0</span> <span style="font-size:16px;">ms</span></div>
                    <div class="kpi-sub">Hostinger MySQL Direct Ping</div>
                </div>

                <div class="kpi-card" style="--kpi-accent: #6366f1;">
                    <div class="kpi-top">
                        <span class="kpi-title">Upload Storage</span>
                        <div class="kpi-icon"><i class="fa-solid fa-hard-drive"></i></div>
                    </div>
                    <div class="kpi-value"><span id="diag-upload-size">0</span> <span style="font-size:16px;">MB</span></div>
                    <div class="kpi-sub"><span id="diag-upload-files">0</span> Invoices & Photos</div>
                </div>

                <div class="kpi-card" style="--kpi-accent: #38bdf8;">
                    <div class="kpi-top">
                        <span class="kpi-title">PHP Runtime</span>
                        <div class="kpi-icon"><i class="fa-brands fa-php"></i></div>
                    </div>
                    <div class="kpi-value" id="diag-php-version" style="font-size:22px;">PHP 8.2</div>
                    <div class="kpi-sub">PDO MySQL Driver Active</div>
                </div>
            </div>

            <!-- Database Tables Matrix Panel -->
            <div class="glass-panel" style="padding:24px;">
                <h3 style="font-family:'Outfit', sans-serif; font-size:18px; font-weight:700; margin-bottom:16px;">
                    <i class="fa-solid fa-database" style="color:var(--primary); margin-right:8px;"></i>
                    Cloud Database Table Breakdown
                </h3>
                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); gap:14px;" id="diagTablesList">
                    <!-- Dynamic Table Stats -->
                </div>
            </div>
        </div>
    </div>

    <!-- Workspace Deep Inspector Modal -->
    <div id="inspectorModal" class="modal-backdrop" onclick="if(event.target === this) closeInspector()">
        <div class="modal-window">
            <div class="modal-header">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div class="avatar" id="modalUserAvatar">U</div>
                    <div>
                        <h3 id="modalUserName">User Workspace</h3>
                        <div style="font-size:12px; color:var(--text-muted); font-family:monospace;" id="modalUserId">user_id</div>
                    </div>
                </div>
                <button class="modal-close-btn" onclick="closeInspector()"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <!-- Modal Quick Action Ribbon -->
            <div style="padding:12px 26px; background:rgba(0,0,0,0.25); border-bottom:1px solid var(--border-subtle); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                <div style="font-size:13px; color:var(--text-secondary);">
                    Current Plan: <span id="modalPlanBadge" class="plan-tag">Trial</span> 
                    <span id="modalExpiryText" style="margin-left:8px; font-size:12px; color:var(--text-muted);"></span>
                </div>
                <div style="display:flex; gap:8px;">
                    <button class="btn-action" style="padding:5px 12px; font-size:12px;" onclick="extendModalUserTrial()">
                        <i class="fa-solid fa-calendar-plus"></i> +30 Days
                    </button>
                    <button class="btn-action btn-primary" style="padding:5px 12px; font-size:12px;" onclick="upgradeModalUserPremium()">
                        <i class="fa-solid fa-crown"></i> Make Lifetime
                    </button>
                    <button class="btn-action" style="padding:5px 12px; font-size:12px; color:var(--accent-rose);" onclick="deleteModalUser()">
                        <i class="fa-solid fa-trash"></i> Purge
                    </button>
                </div>
            </div>

            <div class="modal-body">
                <div class="modal-subtabs">
                    <button class="modal-subtab-btn active" onclick="switchModalSubtab('subtab-helpers', this)">Helpers & Wages</button>
                    <button class="modal-subtab-btn" onclick="switchModalSubtab('subtab-ironing', this)">Ironing Hub</button>
                    <button class="modal-subtab-btn" onclick="switchModalSubtab('subtab-appliances', this)">Appliances & Bills</button>
                    <button class="modal-subtab-btn" onclick="switchModalSubtab('subtab-json', this)">Raw Workspace JSON</button>
                </div>

                <div id="subtab-helpers" class="modal-subtab-content">
                    <div id="modalHelpersContent">Loading helper data...</div>
                </div>

                <div id="subtab-ironing" class="modal-subtab-content" style="display:none;">
                    <div id="modalIroningContent">Loading ironing records...</div>
                </div>

                <div id="subtab-appliances" class="modal-subtab-content" style="display:none;">
                    <div id="modalAppliancesContent">Loading appliance assets...</div>
                </div>

                <div id="subtab-json" class="modal-subtab-content" style="display:none;">
                    <pre style="background:#000; padding:16px; border-radius:12px; max-height:400px; overflow:auto;"><code id="modalRawJson" style="color:#38bdf8;">{}</code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Component -->
    <div id="toast"><i class="fa-solid fa-circle-check" style="color:var(--accent-emerald);"></i> <span id="toastMsg">Action complete</span></div>

    <!-- Application Script -->
    <script>
        const API_BASE = window.location.origin + '/api';
        let adminToken = localStorage.getItem('todo_admin_token') || '';
        let currentRoster = [];
        let currentFilter = 'all';
        let inspectedUserObj = null;

        // Initialize
        if (adminToken) {
            document.getElementById('authGate').style.display = 'none';
            fetchAdminData();
            fetchDiagnostics();
        }

        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMsg').innerText = msg;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        function loginAdmin() {
            const tokenInput = document.getElementById('adminTokenInput').value.trim();
            if (!tokenInput) return;
            adminToken = tokenInput;
            fetchAdminData(true);
        }

        function logoutAdmin() {
            localStorage.removeItem('todo_admin_token');
            window.location.reload();
        }

        function switchNavTab(viewId) {
            document.querySelectorAll('.nav-tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-view').forEach(view => view.classList.remove('active'));
            
            event.currentTarget.classList.add('active');
            document.getElementById(viewId).classList.add('active');

            if (viewId === 'diagnostics-view') {
                fetchDiagnostics();
            }
        }

        async function fetchAdminData(isLoginAttempt = false) {
            const startTime = performance.now();
            try {
                const res = await fetch(`${API_BASE}/admin-overview?token=${adminToken}`);
                if (res.status === 401) {
                    localStorage.removeItem('todo_admin_token');
                    document.getElementById('authGate').style.display = 'flex';
                    if (isLoginAttempt) {
                        document.getElementById('authErrorMsg').style.display = 'block';
                    }
                    return;
                }

                const data = await res.json();
                if (data.success) {
                    const elapsed = Math.round(performance.now() - startTime);
                    document.getElementById('headerLatencyText').innerText = `Cloud Online (${elapsed}ms)`;
                    localStorage.setItem('todo_admin_token', adminToken);
                    document.getElementById('authGate').style.display = 'none';

                    // Update KPIs
                    document.getElementById('kpi-total-users').innerText = data.metrics.total_users;
                    document.getElementById('kpi-trial-users').innerText = data.metrics.active_trials;
                    document.getElementById('kpi-expired-trials').innerText = data.metrics.expired_trials;
                    document.getElementById('kpi-premium-users').innerText = data.metrics.premium_users;
                    document.getElementById('kpi-total-helpers').innerText = data.metrics.total_employees;
                    document.getElementById('kpi-total-attendance').innerText = data.metrics.total_attendance;
                    document.getElementById('kpi-ironing-workers').innerText = data.metrics.total_ironing_workers;
                    document.getElementById('kpi-ironing-records').innerText = data.metrics.total_ironing_records;
                    document.getElementById('kpi-appliances').innerText = data.metrics.total_appliances;
                    document.getElementById('kpi-service-spend').innerText = Number(data.metrics.total_service_spend).toLocaleString();

                    currentRoster = data.users;
                    renderRosterTable(currentRoster);

                    // Pre-fill testing context user if empty
                    if (currentRoster.length > 0 && !document.getElementById('globalTestUserId').value) {
                        document.getElementById('globalTestUserId').value = currentRoster[0].id;
                    }
                }
            } catch (err) {
                console.error('Fetch error:', err);
            }
        }

        function setPlanFilter(filter, el) {
            currentFilter = filter;
            document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
            el.classList.add('active');
            filterRoster();
        }

        function filterRoster() {
            const query = document.getElementById('rosterSearchInput').value.toLowerCase().trim();
            const now = new Date();

            const filtered = currentRoster.filter(u => {
                const matchQuery = u.username.toLowerCase().includes(query) || u.id.toLowerCase().includes(query);
                if (!matchQuery) return false;

                if (currentFilter === 'active-trial') {
                    return u.userType === 'guest' && (!u.expiresAt || new Date(u.expiresAt) > now);
                } else if (currentFilter === 'expired') {
                    return u.userType === 'guest' && u.expiresAt && new Date(u.expiresAt) <= now;
                } else if (currentFilter === 'premium') {
                    return u.userType === 'registered';
                }
                return true;
            });

            renderRosterTable(filtered);
        }

        function renderRosterTable(users) {
            const tbody = document.getElementById('rosterTableBody');
            tbody.innerHTML = '';

            if (users.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:36px; color:var(--text-muted);">No users match criteria</td></tr>`;
                return;
            }

            const now = new Date();

            users.forEach(u => {
                const tr = document.createElement('tr');
                const createdDate = new Date(u.createdAt).toLocaleDateString(undefined, {month: 'short', day: 'numeric', year: 'numeric'});

                let expiryHtml = '<span style="color:var(--accent-emerald); font-weight:600;"><i class="fa-solid fa-infinity"></i> Lifetime</span>';
                if (u.userType === 'guest') {
                    if (u.expiresAt) {
                        const diffDays = Math.ceil((new Date(u.expiresAt) - now) / (1000 * 60 * 60 * 24));
                        if (diffDays > 0) {
                            expiryHtml = `<span style="color:var(--accent-amber); font-weight:600;"><i class="fa-regular fa-clock"></i> ${diffDays} days remaining</span>`;
                        } else {
                            expiryHtml = `<span style="color:var(--accent-rose); font-weight:700;"><i class="fa-solid fa-circle-exclamation"></i> Expired</span>`;
                        }
                    } else {
                        expiryHtml = `<span style="color:var(--text-muted);">No Expiry Set</span>`;
                    }
                }

                const avatarLetter = u.username ? u.username[0].toUpperCase() : 'U';
                const avatarContent = u.profilePic 
                    ? `<img src="${u.profilePic.startsWith('http') ? u.profilePic : window.location.origin + '/' + u.profilePic}">`
                    : avatarLetter;

                tr.innerHTML = `
                    <td>
                        <div class="user-cell">
                            <div class="avatar">${avatarContent}</div>
                            <div>
                                <div class="user-meta-name">${u.username}</div>
                                <div class="user-meta-created">Joined ${createdDate}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="id-badge" onclick="copyText('${u.id}')" title="Click to copy User ID">
                            ${u.id} <i class="fa-regular fa-copy"></i>
                        </span>
                    </td>
                    <td>
                        <span class="plan-tag ${u.userType === 'guest' ? 'trial' : 'premium'}">
                            ${u.userType === 'guest' ? '<i class="fa-regular fa-hourglass"></i> Free Trial' : '<i class="fa-solid fa-crown"></i> Premium'}
                        </span>
                    </td>
                    <td>${expiryHtml}</td>
                    <td style="text-align:center;"><span class="count-pill">${u.employee_count}</span></td>
                    <td style="text-align:center;"><span class="count-pill">${u.ironing_worker_count}</span></td>
                    <td style="text-align:center;"><span class="count-pill">${u.appliance_count}</span></td>
                    <td style="text-align:right;">
                        <div class="action-cell" style="justify-content:flex-end;">
                            <button class="btn-icon-action inspect" title="Inspect User Workspace" onclick="openInspector('${u.id}')">
                                <i class="fa-solid fa-folder-open"></i>
                            </button>
                            <button class="btn-icon-action" title="Use as Active Test Context" onclick="setTestContext('${u.id}')">
                                <i class="fa-solid fa-flask"></i>
                            </button>
                            <button class="btn-icon-action delete" title="Delete User" onclick="deleteUserPrompt('${u.id}', '${u.username}')">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function copyText(text) {
            navigator.clipboard.writeText(text);
            showToast(`Copied ${text} to clipboard`);
        }

        function setTestContext(userId) {
            document.getElementById('globalTestUserId').value = userId;
            switchNavTab('api-view');
            showToast(`Testing context set to: ${userId}`);
        }

        // Inspector Modal
        async function openInspector(userId) {
            const modal = document.getElementById('inspectorModal');
            modal.classList.add('active');

            document.getElementById('modalUserName').innerText = 'Loading...';
            document.getElementById('modalUserId').innerText = userId;
            document.getElementById('modalHelpersContent').innerHTML = '<div style="text-align:center; padding:30px; color:var(--text-muted);">Fetching workspace records...</div>';

            try {
                const res = await fetch(`${API_BASE}/admin-user-details?token=${adminToken}&userId=${userId}`);
                const data = await res.json();
                if (data.success) {
                    inspectedUserObj = data.user;
                    document.getElementById('modalUserName').innerText = data.user.username;
                    document.getElementById('modalUserAvatar').innerText = data.user.username[0].toUpperCase();
                    
                    const isPremium = data.user.userType === 'registered';
                    document.getElementById('modalPlanBadge').className = `plan-tag ${isPremium ? 'premium' : 'trial'}`;
                    document.getElementById('modalPlanBadge').innerText = isPremium ? 'Premium Lifetime' : 'Free Trial';
                    
                    if (data.user.expiresAt) {
                        document.getElementById('modalExpiryText').innerText = `(Expires: ${new Date(data.user.expiresAt).toLocaleDateString()})`;
                    } else {
                        document.getElementById('modalExpiryText').innerText = '(No Expiration)';
                    }

                    // Render Helpers
                    let helperHtml = '';
                    if (data.employees.length === 0) {
                        helperHtml = '<div style="color:var(--text-muted); padding:20px 0;">No house helpers registered in this workspace.</div>';
                    } else {
                        data.employees.forEach(emp => {
                            helperHtml += `
                                <div class="detail-item">
                                    <div>
                                        <div style="font-weight:700; color:#fff;">${emp.name}</div>
                                        <div style="font-size:12px; color:var(--text-muted);">Contact: ${emp.contact || 'N/A'} • Joined: ${emp.joiningDate || 'N/A'}</div>
                                    </div>
                                    <div style="font-weight:700; color:var(--primary); font-size:13.5px;">
                                        ₹${emp.baseSalary} / ${emp.salaryBasis}
                                    </div>
                                </div>
                            `;
                        });
                    }
                    document.getElementById('modalHelpersContent').innerHTML = helperHtml;

                    // Render Ironing
                    let ironHtml = '';
                    if (data.ironingWorkers.length === 0) {
                        ironHtml = '<div style="color:var(--text-muted); padding:20px 0;">No dhobi / ironing workers registered.</div>';
                    } else {
                        data.ironingWorkers.forEach(w => {
                            ironHtml += `
                                <div class="detail-item">
                                    <div>
                                        <div style="font-weight:700; color:#fff;">${w.name}</div>
                                        <div style="font-size:12px; color:var(--text-muted);">Contact: ${w.contact || 'N/A'}</div>
                                    </div>
                                    <div style="font-size:12px; color:var(--text-secondary);">Worker ID: <code style="font-family:monospace;">${w.id}</code></div>
                                </div>
                            `;
                        });
                    }
                    document.getElementById('modalIroningContent').innerHTML = ironHtml;

                    // Render Appliances
                    let appHtml = '';
                    if (data.appliances.length === 0) {
                        appHtml = '<div style="color:var(--text-muted); padding:20px 0;">No appliances or warranty items registered.</div>';
                    } else {
                        data.appliances.forEach(a => {
                            appHtml += `
                                <div class="detail-item">
                                    <div>
                                        <div style="font-weight:700; color:#fff;">${a.name} <span style="font-size:12px; color:var(--text-muted);">(${a.brand || 'Generic'})</span></div>
                                        <div style="font-size:12px; color:var(--text-muted);">Type: ${a.type || 'N/A'} • S/N: ${a.serialNumber || 'N/A'}</div>
                                    </div>
                                    <div style="font-size:12px; color:var(--accent-emerald);">Warranty: ${a.warrantyEnd || 'N/A'}</div>
                                </div>
                            `;
                        });
                    }
                    document.getElementById('modalAppliancesContent').innerHTML = appHtml;

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

        function switchModalSubtab(subtabId, el) {
            document.querySelectorAll('.modal-subtab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.modal-subtab-content').forEach(c => c.style.display = 'none');
            el.classList.add('active');
            document.getElementById(subtabId).style.display = 'block';
        }

        async function extendModalUserTrial() {
            if (!inspectedUserObj) return;
            const res = await fetch(`${API_BASE}/admin-update-user?token=${adminToken}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ userId: inspectedUserObj.id, extendDays: 30 })
            });
            const data = await res.json();
            if (data.success) {
                showToast('Trial extended by +30 days');
                openInspector(inspectedUserObj.id);
                fetchAdminData();
            }
        }

        async function upgradeModalUserPremium() {
            if (!inspectedUserObj) return;
            const res = await fetch(`${API_BASE}/admin-update-user?token=${adminToken}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ userId: inspectedUserObj.id, userType: 'registered' })
            });
            const data = await res.json();
            if (data.success) {
                showToast('User upgraded to Lifetime Premium');
                openInspector(inspectedUserObj.id);
                fetchAdminData();
            }
        }

        async function deleteModalUser() {
            if (!inspectedUserObj) return;
            if (confirm(`Are you sure you want to completely purge user ${inspectedUserObj.username} (${inspectedUserObj.id}) and all records?`)) {
                const res = await fetch(`${API_BASE}/admin-delete-user?token=${adminToken}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ userId: inspectedUserObj.id })
                });
                const data = await res.json();
                if (data.success) {
                    showToast('User purged successfully');
                    closeInspector();
                    fetchAdminData();
                }
            }
        }

        async function deleteUserPrompt(userId, username) {
            if (confirm(`Purge user account ${username} (${userId})? This will delete all scoped data.`)) {
                const res = await fetch(`${API_BASE}/admin-delete-user?token=${adminToken}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ userId })
                });
                const data = await res.json();
                if (data.success) {
                    showToast('User purged successfully');
                    fetchAdminData();
                }
            }
        }

        // Diagnostics
        async function fetchDiagnostics() {
            try {
                const res = await fetch(`${API_BASE}/admin-system-health?token=${adminToken}`);
                const data = await res.json();
                if (data.success) {
                    document.getElementById('diag-db-latency').innerText = data.system.db_latency_ms;
                    document.getElementById('diag-upload-size').innerText = data.system.uploads.total_size_mb;
                    document.getElementById('diag-upload-files').innerText = data.system.uploads.file_count;
                    document.getElementById('diag-php-version').innerText = `PHP ${data.system.php_version}`;

                    const tableContainer = document.getElementById('diagTablesList');
                    tableContainer.innerHTML = '';
                    for (const [tbl, count] of Object.entries(data.system.tables)) {
                        const item = document.createElement('div');
                        item.className = 'detail-item';
                        item.innerHTML = `
                            <div>
                                <div style="font-weight:700; color:#fff;">${tbl}</div>
                                <div style="font-size:11px; color:var(--text-muted);">Indexed MySQL Table</div>
                            </div>
                            <div class="count-pill" style="font-size:13px; color:var(--primary);">${count} rows</div>
                        `;
                        tableContainer.appendChild(item);
                    }
                }
            } catch (err) {
                console.error(err);
            }
        }

        // API Studio Console
        function toggleEndpoint(header) {
            const details = header.nextElementSibling;
            details.classList.toggle('active');
        }

        async function executeApiCall(method, path, payloadTextareaId = null, requireScoped = false) {
            const startTime = performance.now();
            const headers = { 'Content-Type': 'application/json' };
            
            if (requireScoped) {
                const userId = document.getElementById('globalTestUserId').value.trim();
                if (!userId) {
                    alert('Please select or provide a User ID context in the top banner');
                    return;
                }
                headers['X-User-Id'] = userId;
            }

            const options = { method, headers };
            if (payloadTextareaId) {
                try {
                    const raw = document.getElementById(payloadTextareaId).value;
                    options.body = JSON.stringify(JSON.parse(raw));
                } catch (e) {
                    alert('Invalid JSON in request payload');
                    return;
                }
            }

            const consolePanel = document.getElementById('apiConsoleOutput');
            consolePanel.style.display = 'block';
            document.getElementById('consoleTargetUrl').innerText = path;

            try {
                const res = await fetch(API_BASE + path, options);
                const elapsed = Math.round(performance.now() - startTime);
                const resData = await res.json();

                document.getElementById('consoleTiming').innerText = `${elapsed} ms`;
                const badge = document.getElementById('consoleStatusBadge');
                badge.innerText = `${res.status} ${res.statusText || ''}`;
                badge.style.color = res.ok ? '#10b981' : '#f43f5e';
                badge.style.borderColor = res.ok ? '#10b98140' : '#f43f5e40';

                document.getElementById('consoleJsonBody').innerText = JSON.stringify(resData, null, 2);
                consolePanel.scrollIntoView({ behavior: 'smooth' });
            } catch (err) {
                document.getElementById('consoleStatusBadge').innerText = 'Network Error';
                document.getElementById('consoleJsonBody').innerText = JSON.stringify({ error: err.message }, null, 2);
            }
        }

        function copyConsoleJson() {
            const text = document.getElementById('consoleJsonBody').innerText;
            navigator.clipboard.writeText(text);
            showToast('JSON response copied');
        }
    </script>
</body>
</html>
