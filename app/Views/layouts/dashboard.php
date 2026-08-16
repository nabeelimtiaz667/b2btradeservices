<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="<?= base_url('assets/images/b2b-icon.svg') ?>" rel="icon">
    <link href="<?= base_url('assets/images/b2b-icon.svg') ?>" rel="apple-touch-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Zalando+Sans:ital,wdth,wght@0,75..125,200..900;1,75..125,200..900&display=swap"
        rel="stylesheet">

    <link rel="apple-touch-icon" sizes="57x57" href="<?= base_url('assets/site-identity/apple-icon-57x57.png') ?>">
    <link rel="apple-touch-icon" sizes="60x60" href="<?= base_url('assets/site-identity/apple-icon-60x60.png') ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?= base_url('assets/site-identity/apple-icon-72x72.png') ?>">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= base_url('assets/site-identity/apple-icon-76x76.png') ?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?= base_url('assets/site-identity/apple-icon-114x114.png') ?>">
    <link rel="apple-touch-icon" sizes="120x120" href="<?= base_url('assets/site-identity/apple-icon-120x120.png') ?>">
    <link rel="apple-touch-icon" sizes="144x144" href="<?= base_url('assets/site-identity/apple-icon-144x144.png') ?>">
    <link rel="apple-touch-icon" sizes="152x152" href="<?= base_url('assets/site-identity/apple-icon-152x152.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/site-identity/apple-icon-180x180.png') ?>">
    <link rel="icon" type="image/png" sizes="192x192"
        href="<?= base_url('assets/site-identity/android-icon-192x192.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/site-identity/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="96x96" href="<?= base_url('assets/site-identity/favicon-96x96.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/site-identity/favicon-16x16.png') ?>">
    <link rel="manifest" href="<?= base_url('assets/site-identity/manifest.json') ?>">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?= base_url('assets/site-identity/ms-icon-144x144.png') ?>">
    <meta name="theme-color" content="#ffffff">

    <title><?= esc($title ?? (($siteSettings['site_name'] ?? 'B2B Trade Services') . ' - Dashboard')) ?></title>
    <style>
    :root {
        --primary-dark: #0A504F;
        --primary-teal: #0F9EA5;
        --primary-gradient: linear-gradient(45deg, #15A2A0, #5FC86B);
    }

    body {
        font-family: "Zalando Sans", sans-serif;
        background-color: #f8f9fa;
    }

    .sidebar {
        height: 100vh;
        background: var(--primary-dark);
        position: fixed;
        left: 0;
        top: 0;
        width: 60px;
        padding: 0;
        z-index: 100;
        transition: width 0.3s ease;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .sidebar:hover {
        width: 260px;
    }

    .sidebar-user {
        padding: 10px;
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        flex-shrink: 0;
        white-space: nowrap;
        overflow: hidden;
    }

    .sidebar:hover .sidebar-user {
        padding: 20px;
    }

    .sidebar-user .user-avatar {
        width: 40px;
        height: 40px;
        background: var(--primary-gradient);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 5px;
        font-size: 18px;
        color: #fff;
        font-weight: 600;
        transition: all 0.3s;
    }

    .sidebar:hover .sidebar-user .user-avatar {
        width: 80px;
        height: 80px;
        font-size: 32px;
        margin-bottom: 10px;
    }

    .sidebar-user .user-name,
    .sidebar-user .user-role {
        opacity: 0;
        height: 0;
        overflow: hidden;
        transition: opacity 0.3s;
    }

    .sidebar:hover .sidebar-user .user-name {
        opacity: 1;
        height: auto;
        color: #fff;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .sidebar:hover .sidebar-user .user-role {
        opacity: 1;
        height: auto;
        color: rgba(255, 255, 255, 0.7);
        font-size: 12px;
        text-transform: uppercase;
    }

    .sidebar-nav {
        padding: 20px 0;
        flex: 1;
        overflow-y: auto;
        min-height: 0;
    }

    .sidebar-nav::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-nav::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-nav::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 2px;
    }

    .sidebar-nav::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.4);
    }

    .sidebar-nav .nav-link {
        color: rgba(255, 255, 255, 0.8);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.3s;
        border-left: 3px solid transparent;
        white-space: nowrap;
        overflow: hidden;
    }

    .sidebar-nav .nav-link:hover,
    .sidebar-nav .nav-link.active {
        color: #fff;
        background: rgba(255, 255, 255, 0.1);
        border-left-color: #5FC86B;
    }

    .sidebar-nav .nav-link svg {
        width: 20px;
        height: 20px;
        min-width: 20px;
    }

    .sidebar-nav .nav-section-label {
        padding: 8px 20px 4px;
        color: rgba(255, 255, 255, 0.4);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        white-space: nowrap;
        overflow: hidden;
        opacity: 0;
        height: 0;
        transition: opacity 0.3s;
    }

    .sidebar:hover .nav-section-label {
        opacity: 1;
        height: auto;
        padding: 8px 25px 4px;
    }

    .sidebar-footer {
        flex-shrink: 0;
        padding: 10px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        overflow: hidden;
    }

    .sidebar:hover .sidebar-footer {
        padding: 15px 20px;
    }

    .logout-btn span {
        display: none;
    }

    .sidebar:hover .logout-btn span {
        display: inline;
    }

    .logout-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 12px;
        background: transparent;
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: #fff;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s;
    }

    .logout-btn:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: #5FC86B;
        color: #5FC86B;
    }

    .main-content {
        margin-left: 60px;
        padding: 30px;
        padding-top: 30px;
        min-height: 100vh;
        transition: margin-left 0.3s ease;
    }

    .page-header {
        margin-bottom: 30px;
    }

    .page-title {
        color: var(--primary-dark);
        font-size: 28px;
        font-weight: 600;
        margin: 0;
    }

    .stat-card {
        background: #fff;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .stat-card .stat-icon {
        width: 60px;
        height: 60px;
        background: var(--primary-gradient);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
    }

    .stat-card .stat-icon svg {
        width: 30px;
        height: 30px;
        color: #fff;
    }

    .stat-card .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--primary-dark);
    }

    .stat-card .stat-label {
        color: #6c757d;
        font-size: 14px;
    }

    .card-custom {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border: none;
    }

    .card-custom .card-header {
        background: transparent;
        border-bottom: 1px solid #eee;
        padding: 20px 25px;
    }

    .card-custom .card-header h5 {
        color: var(--primary-dark);
        font-weight: 600;
        margin: 0;
    }

    .card-custom .card-body {
        padding: 25px;
    }

    .table-custom {
        margin-bottom: 0;
    }

    .table-custom thead th {
        background: #f8f9fa;
        color: var(--primary-dark);
        font-weight: 600;
        border: none;
        padding: 15px;
    }

    .table-custom tbody td {
        padding: 15px;
        vertical-align: middle;
        border-color: #eee;
    }

    .badge-pending {
        background: #ffc107;
        color: #000;
    }

    .badge-approved {
        background: #28a745;
        color: #fff;
    }

    .badge-rejected {
        background: #dc3545;
        color: #fff;
    }

    .btn-approve {
        background: #28a745;
        color: #fff;
        border: none;
        padding: 6px 15px;
        border-radius: 5px;
        font-size: 13px;
    }

    .btn-approve:hover {
        background: #218838;
        color: #fff;
    }

    .btn-reject {
        background: #dc3545;
        color: #fff;
        border: none;
        padding: 6px 15px;
        border-radius: 5px;
        font-size: 13px;
    }

    .btn-reject:hover {
        background: #c82333;
        color: #fff;
    }

    .btn-delete {
        background: #6c757d;
        color: #fff;
        border: none;
        padding: 6px 15px;
        border-radius: 5px;
        font-size: 13px;
    }

    .btn-delete:hover {
        background: #5a6268;
        color: #fff;
    }

    .profile-card {
        background: #fff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .profile-card .profile-avatar {
        width: 120px;
        height: 120px;
        background: var(--primary-gradient);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: #fff;
        font-weight: 600;
        margin: 0 auto 20px;
    }

    .profile-card .profile-name {
        font-size: 24px;
        font-weight: 600;
        color: var(--primary-dark);
        text-align: center;
    }

    .profile-card .profile-email {
        color: #6c757d;
        text-align: center;
        margin-bottom: 20px;
    }

    .profile-info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .profile-info-list li {
        padding: 12px 0;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
    }

    .profile-info-list li:last-child {
        border-bottom: none;
    }

    .profile-info-list .label {
        color: #6c757d;
    }

    .profile-info-list .value {
        color: var(--primary-dark);
        font-weight: 500;
    }

    .pending-notice {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);
        border: 1px solid #ffc107;
        border-radius: 15px;
        padding: 30px;
        text-align: center;
    }

    .pending-notice svg {
        width: 60px;
        height: 60px;
        color: #856404;
        margin-bottom: 15px;
    }

    .pending-notice h4 {
        color: #856404;
        margin-bottom: 10px;
    }

    .pending-notice p {
        color: #856404;
        margin: 0;
    }

    @media (max-width: 991px) {
        .sidebar {
            display: none !important;
        }

        .main-content {
            margin-left: 0 !important;
            padding-top: 60px;
        }

        .mobile-topbar {
            display: flex !important;
        }
    }

    /* ── Mobile drawer (completely separate from desktop sidebar) ── */
    #mobile-drawer {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 82vw;
        max-width: 300px;
        background: #fff;
        z-index: 1060;
        flex-direction: column;
        transform: translateX(-100%);
        transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    #mobile-drawer.md-open {
        transform: translateX(0);
        box-shadow: 6px 0 32px rgba(0, 0, 0, 0.22);
    }

    .md-header {
        background: linear-gradient(135deg, #0A504F, #15A2A0);
        padding: 18px 16px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }

    .md-user-info {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .md-avatar {
        width: 44px;
        height: 44px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #fff;
        font-weight: 700;
        flex-shrink: 0;
    }

    .md-user-text {
        min-width: 0;
    }

    .md-name {
        color: #fff;
        font-weight: 600;
        font-size: 14px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .md-role {
        color: rgba(255, 255, 255, 0.75);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .md-close-btn {
        background: rgba(255, 255, 255, 0.15);
        border: none;
        color: #fff;
        font-size: 20px;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        line-height: 1;
    }

    .md-nav {
        flex: 1;
        overflow-y: auto;
        padding: 6px 0;
    }

    .md-nav::-webkit-scrollbar {
        width: 3px;
    }

    .md-nav::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 2px;
    }

    .md-nav a {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 0 20px;
        min-height: 52px;
        color: #333;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        border-left: 3px solid transparent;
        transition: background 0.15s, color 0.15s;
        white-space: nowrap;
    }

    .md-nav a:active,
    .md-nav a.active {
        background: #edf7ed;
        color: #0A504F;
        border-left-color: #5FC86B;
    }

    .md-nav a svg {
        width: 19px;
        height: 19px;
        min-width: 19px;
        color: #0A504F;
        flex-shrink: 0;
    }

    .md-section {
        padding: 14px 20px 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #aaa;
    }

    .md-divider {
        height: 1px;
        background: #f0f0f0;
        margin: 6px 0;
    }

    .md-footer {
        flex-shrink: 0;
        padding: 14px 20px;
        border-top: 1px solid #f0f0f0;
    }

    .md-logout {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #dc3545;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        padding: 10px 0;
    }

    .md-logout svg {
        width: 19px;
        height: 19px;
        flex-shrink: 0;
    }

    @media (max-width: 991px) {
        #mobile-drawer {
            display: flex;
        }
    }

    .mobile-topbar {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 56px;
        background: var(--primary-dark);
        z-index: 1040;
        align-items: center;
        padding: 0 16px;
        gap: 12px;
    }

    .mobile-topbar .hamburger-btn {
        background: none;
        border: none;
        color: #fff;
        font-size: 22px;
        line-height: 1;
        padding: 4px 8px;
        cursor: pointer;
        border-radius: 6px;
        transition: background 0.2s;
    }

    .mobile-topbar .hamburger-btn:hover {
        background: rgba(255, 255, 255, 0.15);
    }

    .mobile-topbar .topbar-title {
        color: #fff;
        font-size: 17px;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    #sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        z-index: 1045;
    }

    #sidebar-overlay.active {
        display: block;
    }
    </style>
</head>

<body>
    <div class="mobile-topbar">
        <button class="hamburger-btn" id="hamburger-btn" aria-label="Open menu">&#9776;</button>
        <span class="topbar-title"><?= esc($siteSettings['site_name'] ?? 'B2B Trade Services') ?></span>
    </div>
    <div id="sidebar-overlay"></div>

    <!-- Mobile drawer — completely separate from desktop sidebar -->
    <div id="mobile-drawer">
        <div class="md-header">
            <div class="md-user-info">
                <div class="md-avatar"><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></div>
                <div class="md-user-text">
                    <div class="md-name"><?= esc($user['name'] ?? 'User') ?></div>
                    <div class="md-role"><?= esc(ucfirst($user['user_type'] ?? 'User')) ?></div>
                </div>
            </div>
            <button class="md-close-btn" id="md-close-btn" aria-label="Close menu">&times;</button>
        </div>
        <nav class="md-nav">
            <?php if (in_array(($user['user_type'] ?? ''), ['admin', 'agent'])): ?>
            <a href="<?= base_url('dashboard') ?>"
                class="<?= (current_url() == base_url('dashboard') || current_url() == base_url('dashboard/admin')) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146z" />
                </svg>
                Dashboard
            </a>
            <div class="md-section">Lead Management</div>
            <a href="<?= base_url('leads/all') ?>"
                class="<?= preg_match('#/leads/all/?(\?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 0-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z" />
                </svg>
                All Leads
            </a>
            <a href="<?= base_url('leads/buyer') ?>"
                class="<?= preg_match('#/leads/buyer/?(\?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5z" />
                </svg>
                Buyer Leads
            </a>
            <a href="<?= base_url('leads/supplier') ?>"
                class="<?= preg_match('#/leads/supplier/?(\?|$)#', current_url()) && !preg_match('#/leads/my-supplier#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M3 2.5a2.5 2.5 0 0 1 5 0 2.5 2.5 0 0 1 5 0v.006c0 .07 0 .27-.038.494H15a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 14.5V7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h2.038A2.968 2.968 0 0 1 3 2.506V2.5z" />
                </svg>
                Supplier Leads
            </a>
            <a href="<?= base_url('leads/popup') ?>"
                class="<?= preg_match('#/leads/popup/?(\?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M2.5 4a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zm2 0a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zM16 3a1 1 0 0 0-1-1H1a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V3zM1 3h14v2H1V3zm0 3h14v7H1V6z" />
                </svg>
                Popup Leads
            </a>
            <div class="md-section">My Leads</div>
            <a href="<?= base_url('leads/my-supplier') ?>"
                class="<?= preg_match('#/leads/my-supplier/?(\?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                    <path fill-rule="evenodd"
                        d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z" />
                </svg>
                My Supplier Leads
            </a>
            <a href="<?= base_url('leads/my-buyer') ?>"
                class="<?= preg_match('#/leads/my-buyer/?(\?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                    <path fill-rule="evenodd"
                        d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z" />
                </svg>
                My Buyer Leads
            </a>
            <?php if (($user['user_type'] ?? '') === 'admin'): ?>
            <div class="md-section">Administration</div>
            <a href="<?= base_url('dashboard/users') ?>"
                class="<?= preg_match('#/dashboard/users/?(\?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0Z" />
                    <path
                        d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6.5a.5.5 0 0 1-1 0V1H3v14h3a.5.5 0 0 1 0 1H3a1 1 0 0 1-1-1V1Z" />
                </svg>
                Manage Users
            </a>
            <a href="<?= base_url('dashboard/agents') ?>"
                class="<?= preg_match('#/dashboard/agents/?(\?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                    <path fill-rule="evenodd"
                        d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z" />
                    <path d="M4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z" />
                </svg>
                Manage Agents
            </a>
            <a href="<?= base_url('dashboard/suppliers') ?>"
                class="<?= preg_match('#/dashboard/suppliers/?(\?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M3 2.5a2.5 2.5 0 0 1 5 0 2.5 2.5 0 0 1 5 0v.006c0 .07 0 .27-.038.494H15a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 14.5V7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h2.038A2.968 2.968 0 0 1 3 2.506V2.5z" />
                </svg>
                Manage Suppliers
            </a>
            <a href="<?= base_url('dashboard/inquiries') ?>"
                class="<?= preg_match('#/dashboard/inquiries/?(?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z" />
                </svg>
                Buyer Inquiries
            </a>
            <a href="<?= base_url('dashboard/submissions') ?>"
                class="<?= preg_match('#/dashboard/submissions/?(?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z" />
                    <path
                        d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 8zm0 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z" />
                </svg>
                Form Submissions
            </a>
            <a href="<?= base_url('admin/import/suppliers') ?>"
                class="<?= preg_match('#/admin/import/#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
                    <path
                        d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z" />
                </svg>
                Import Data
            </a>
            <a href="<?= base_url('admin/settings') ?>"
                class="<?= preg_match('#/admin/settings/?(?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z" />
                    <path
                        d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z" />
                </svg>
                Site Settings
            </a>
            <?php endif; ?>
            <?php else: ?>
            <a href="<?= base_url('dashboard') ?>"
                class="<?= (current_url() == base_url('dashboard') || current_url() == base_url('dashboard/supplier') || current_url() == base_url('dashboard/buyer')) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146z" />
                </svg>
                Dashboard
            </a>
            <?php if (($user['user_type'] ?? '') === 'supplier'): ?>
            <a href="<?= base_url('dashboard/supplier/products') ?>"
                class="<?= strpos(current_url(), 'dashboard/supplier/products') !== false ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2l-2.218-.887zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z" />
                </svg>
                My Products
            </a>
            <a href="<?= base_url('dashboard/supplier/profile/edit') ?>"
                class="<?= strpos(current_url(), 'dashboard/supplier/profile/edit') !== false ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z" />
                </svg>
                Edit Company / Products
            </a>
            <?php elseif (($user['user_type'] ?? '') === 'buyer'): ?>
            <a href="<?= base_url('dashboard/buyer/inquiries') ?>"
                class="<?= strpos(current_url(), 'dashboard/buyer/inquiries') !== false ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z" />
                </svg>
                My Inquiries
            </a>
            <?php endif; ?>
            <?php endif; ?>
            <div class="md-divider"></div>
            <a href="<?= base_url('dashboard/profile') ?>"
                class="<?= preg_match('#/dashboard/profile/?(?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                    <path fill-rule="evenodd"
                        d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
                </svg>
                Update Profile
            </a>
            <?php if (($user['user_type'] ?? '') === 'supplier' && !empty($user['slug'])): ?>
            <a href="<?= base_url('supplier/profile/' . esc($user['slug'])) ?>" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                    <path fill-rule="evenodd"
                        d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
                </svg>
                View My Profile
            </a>
            <?php else: ?>
            <a href="<?= base_url() ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z" />
                </svg>
                Back to Website
            </a>
            <?php endif; ?>
        </nav>
        <div class="md-footer">
            <a href="<?= base_url('logout') ?>" class="md-logout">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z" />
                    <path fill-rule="evenodd"
                        d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z" />
                </svg>
                Logout
            </a>
        </div>
    </div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-user">
            <div class="user-avatar">
                <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="user-name"><?= esc($user['name'] ?? 'User') ?></div>
            <div class="user-role"><?= esc(ucfirst($user['user_type'] ?? 'User')) ?></div>
        </div>

        <nav class="sidebar-nav">
            <?php if (in_array(($user['user_type'] ?? ''), ['admin', 'agent'])): ?>
            <a href="<?= base_url('dashboard') ?>"
                class="nav-link <?= (current_url() == base_url('dashboard') || current_url() == base_url('dashboard/admin')) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146z" />
                </svg>
                Dashboard
            </a>

            <div class="nav-section-label">Lead Management</div>

            <a href="<?= base_url('leads/all') ?>"
                class="nav-link <?= preg_match('#/leads/all/?(\?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z" />
                </svg>
                All Leads
            </a>
            <a href="<?= base_url('leads/buyer') ?>"
                class="nav-link <?= preg_match('#/leads/buyer/?(\?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5z" />
                </svg>
                Buyer Leads
            </a>
            <a href="<?= base_url('leads/supplier') ?>"
                class="nav-link <?= preg_match('#/leads/supplier/?(\?|$)#', current_url()) && !preg_match('#/leads/my-supplier#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M3 2.5a2.5 2.5 0 0 1 5 0 2.5 2.5 0 0 1 5 0v.006c0 .07 0 .27-.038.494H15a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 14.5V7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h2.038A2.968 2.968 0 0 1 3 2.506V2.5z" />
                </svg>
                Supplier Leads
            </a>
            <a href="<?= base_url('leads/popup') ?>"
                class="nav-link <?= preg_match('#/leads/popup/?(\?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M2.5 4a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zm2 0a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1zM16 3a1 1 0 0 0-1-1H1a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V3zM1 3h14v2H1V3zm0 3h14v7H1V6z" />
                </svg>
                Popup Leads
            </a>

            <div class="nav-section-label">My Leads</div>

            <a href="<?= base_url('leads/my-supplier') ?>"
                class="nav-link <?= preg_match('#/leads/my-supplier/?(\?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                    <path fill-rule="evenodd"
                        d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z" />
                </svg>
                My Supplier Leads
            </a>
            <a href="<?= base_url('leads/my-buyer') ?>"
                class="nav-link <?= preg_match('#/leads/my-buyer/?(\?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                    <path fill-rule="evenodd"
                        d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z" />
                </svg>
                My Buyer Leads
            </a>

            <?php if (($user['user_type'] ?? '') === 'admin'): ?>
            <div class="nav-section-label">Administration</div>

            <a href="<?= base_url('dashboard/users') ?>"
                class="nav-link <?= preg_match('#/dashboard/users/?(\?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0Z" />
                    <path
                        d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6.5a.5.5 0 0 1-1 0V1H3v14h3a.5.5 0 0 1 0 1H3a1 1 0 0 1-1-1V1Z" />
                </svg>
                Manage Users
            </a>
            <a href="<?= base_url('dashboard/agents') ?>"
                class="nav-link <?= preg_match('#/dashboard/agents/?(\?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                    <path fill-rule="evenodd"
                        d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z" />
                    <path d="M4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z" />
                </svg>
                Manage Agents
            </a>
            <a href="<?= base_url('dashboard/suppliers') ?>"
                class="nav-link <?= preg_match('#/dashboard/suppliers/?(\?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M3 2.5a2.5 2.5 0 0 1 5 0 2.5 2.5 0 0 1 5 0v.006c0 .07 0 .27-.038.494H15a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 14.5V7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h2.038A2.968 2.968 0 0 1 3 2.506V2.5zm1.068.5H7v-.5a1.5 1.5 0 1 0-3 0c0 .085.002.274.045.43a.522.522 0 0 0 .023.07zM9 3h2.932a.56.56 0 0 0 .023-.07c.043-.156.045-.345.045-.43a1.5 1.5 0 0 0-3 0V3zM1 4v2h6V4H1zm8 0v2h6V4H9zm5 3H9v8h4.5a.5.5 0 0 0 .5-.5V7zm-7 8V7H2v7.5a.5.5 0 0 0 .5.5H7z" />
                </svg>
                Manage Suppliers
            </a>
            <a href="<?= base_url('dashboard/inquiries') ?>"
                class="nav-link <?= preg_match('#/dashboard/inquiries/?(?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z" />
                </svg>
                Buyer Inquiries
            </a>
            <a href="<?= base_url('dashboard/submissions') ?>"
                class="nav-link <?= preg_match('#/dashboard/submissions/?(?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z" />
                    <path
                        d="M3 5.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3 8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 8zm0 2.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z" />
                </svg>
                Form Submissions
            </a>
            <a href="<?= base_url('admin/import/suppliers') ?>"
                class="nav-link <?= preg_match('#/admin/import/#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
                    <path
                        d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z" />
                </svg>
                Import Data
            </a>
            <a href="<?= base_url('admin/settings') ?>"
                class="nav-link <?= preg_match('#/admin/settings/?(?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z" />
                    <path
                        d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z" />
                </svg>
                Site Settings
            </a>
            <?php endif; ?>
            <?php else: ?>
            <a href="<?= base_url('dashboard') ?>"
                class="nav-link <?= (current_url() == base_url('dashboard') || current_url() == base_url('dashboard/supplier') || current_url() == base_url('dashboard/buyer')) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146z" />
                </svg>
                Dashboard
            </a>
            <?php if (($user['user_type'] ?? '') === 'supplier'): ?>
            <a href="<?= base_url('dashboard/supplier/products') ?>"
                class="nav-link <?= strpos(current_url(), 'dashboard/supplier/products') !== false ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2l-2.218-.887zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z" />
                </svg>
                My Products
            </a>
            <a href="<?= base_url('dashboard/supplier/profile/edit') ?>"
                class="nav-link <?= strpos(current_url(), 'dashboard/supplier/profile/edit') !== false ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z" />
                </svg>
                Edit Company/Products
            </a>
            <?php elseif (($user['user_type'] ?? '') === 'buyer'): ?>
            <a href="<?= base_url('dashboard/buyer/inquiries') ?>"
                class="nav-link <?= strpos(current_url(), 'dashboard/buyer/inquiries') !== false ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z" />
                </svg>
                My Inquiries
            </a>
            <?php endif; ?>
            <?php endif; ?>

            <a href="<?= base_url('dashboard/profile') ?>"
                class="nav-link <?= preg_match('#/dashboard/profile/?(?|$)#', current_url()) ? 'active' : '' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                    <path fill-rule="evenodd"
                        d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
                </svg>
                Update Profile
            </a>
            <?php if (($user['user_type'] ?? '') === 'supplier' && !empty($user['slug'])): ?>
            <a href="<?= base_url('supplier/profile/' . esc($user['slug'])) ?>" class="nav-link" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                    <path fill-rule="evenodd"
                        d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
                </svg>
                View My Profile
            </a>
            <?php else: ?>
            <a href="<?= base_url() ?>" class="nav-link">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path
                        d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4a9.267 9.267 0 0 1 .64-1.539 6.7 6.7 0 0 1 .597-.933A7.025 7.025 0 0 0 2.255 4H4.09zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a6.958 6.958 0 0 0-.656 2.5h2.49zM4.847 5a12.5 12.5 0 0 0-.338 2.5H7.5V5H4.847zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5a12.5 12.5 0 0 0 .337 2.5H7.5V8.5H4.51zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12c.138.386.295.744.468 1.068.552 1.035 1.218 1.65 1.887 1.855V12H5.145zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11a13.652 13.652 0 0 1-.312-2.5h-2.49c.062.89.291 1.733.656 2.5H3.82zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12v2.923c.67-.204 1.335-.82 1.887-1.855.173-.324.33-.682.468-1.068H8.5zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5a6.959 6.959 0 0 0-.656-2.5H12.18c.174.782.282 1.623.312 2.5h2.49zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4a7.966 7.966 0 0 0-.468-1.068C9.835 1.897 9.17 1.282 8.5 1.077V4h2.355z" />
                </svg>
                Back to Website
            </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <a href="<?= base_url('logout') ?>" class="logout-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z" />
                    <path fill-rule="evenodd"
                        d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z" />
                </svg>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="main-content">
        <?= $this->renderSection('content') ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    const overlay = document.getElementById('sidebar-overlay');
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const mobileDrawer = document.getElementById('mobile-drawer');
    const mdCloseBtn = document.getElementById('md-close-btn');

    function isMobile() {
        return window.innerWidth <= 991;
    }

    /* ── Mobile drawer controls ── */
    function openMobileDrawer() {
        if (mobileDrawer) mobileDrawer.classList.add('md-open');
        if (overlay) overlay.classList.add('active');
    }

    function closeMobileDrawer() {
        if (mobileDrawer) mobileDrawer.classList.remove('md-open');
        if (overlay) overlay.classList.remove('active');
    }

    /* ── Desktop sidebar controls (unchanged) ── */
    function openSidebar() {
        sidebar.classList.add('sidebar-open');
        if (overlay) overlay.classList.add('active');
    }

    function closeSidebar() {
        sidebar.classList.remove('sidebar-open');
        if (overlay) overlay.classList.remove('active');
    }

    // DESKTOP: Hover expansion — checked dynamically so resize works correctly
    sidebar.addEventListener('mouseenter', function() {
        if (!isMobile()) mainContent.style.marginLeft = '260px';
    });
    sidebar.addEventListener('mouseleave', function() {
        if (!isMobile()) mainContent.style.marginLeft = '60px';
    });

    // MOBILE: Hamburger opens mobile drawer; desktop: no action needed (hover handles it)
    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', function(event) {
            event.stopPropagation();
            if (mobileDrawer && mobileDrawer.classList.contains('md-open')) {
                closeMobileDrawer();
            } else {
                openMobileDrawer();
            }
        });
    }

    // Close mobile drawer when X button is tapped
    if (mdCloseBtn) {
        mdCloseBtn.addEventListener('click', closeMobileDrawer);
    }

    // Close mobile drawer when overlay is tapped
    if (overlay) {
        overlay.addEventListener('click', closeMobileDrawer);
    }

    // Close mobile drawer when any nav link inside it is tapped
    if (mobileDrawer) {
        mobileDrawer.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', closeMobileDrawer);
        });
    }

    // On resize to desktop: close mobile drawer and restore margin
    window.addEventListener('resize', function() {
        if (!isMobile()) {
            closeMobileDrawer();
            mainContent.style.marginLeft = '60px';
        } else {
            mainContent.style.marginLeft = '';
        }
    });

    function togglePwd(el) {
        var input = el.closest('.position-relative').querySelector('.pwd-toggle');
        var icon = el.querySelector('.pwd-eye-icon');
        if (!input || !icon) return;
        if (input.type === 'password') {
            input.type = 'text';
            icon.style.fill = '#0F9EA5';
        } else {
            input.type = 'password';
            icon.style.fill = '#DBDBDB';
        }
    }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/intlTelInput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="<?= base_url('assets/js/script.js') ?>"></script>
    <script>
    document.addEventListener('error', function(e) {
        var img = e.target;
        if (img.tagName !== 'IMG') return;
        if (img.dataset.fallbackApplied) return;
        var src = img.getAttribute('src') || '';
        if (src.indexOf('/flags/') !== -1) return;
        if (img.closest('.supplier-profile-banner, .supplier-profile-slider')) return;
        img.dataset.fallbackApplied = '1';
        img.src = 'https://img.freepik.com/free-vector/illustration-gallery-icon_53876-27002.jpg';
    }, true);
    </script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>