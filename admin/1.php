<?php
include 'main_db.php';
$today = date('Y-m-d');

// Modified query to only show pending applications
$baseQuery = "SELECT a.*, u.email as user_email 
             FROM alumni_id_cards a 
             LEFT JOIN users u ON a.user_id = u.id
             WHERE a.status = 'pending'
             ORDER BY a.created_at DESC LIMIT 20";
$applicationsResult = $mysqli->query($baseQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni ID Card Pending Applications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    .alm-bookings-container {
            padding: 1.5rem;
            background: var(--white);
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin: 1rem auto;
            max-width: 1400px;
        }

        .alm-header-content {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .alm-header-content h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alm-header-content h2 i {
            color: var(--primary-color);
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }

        .loading-content {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .loading-text {
            color: white;
            font-size: 14px;
            font-weight: 500;
            animation: pulse 1.5s ease-in-out infinite;
            margin: 0;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }

        .loading-overlay-show {
            animation: fadeIn 0.3s ease-in-out forwards;
        }

        .loading-overlay-hide {
            animation: fadeOut 0.3s ease-in-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        :root {
            --primary-color: #10b981;
            --primary-dark: #059669;
            --secondary-color: #64748b;
            --border-color: #e2e8f0;
            --danger-color: #ef4444;
            --success-color: #059669;
            --warning-color: #f59e0b;
            --text-dark: #1e293b;
            --text-light: #64748b;
            --bg-light: #f8fafc;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.5;
            color: var(--text-dark);
            background-color: #f1f5f9;
        }

        .alm-bookings-container {
            padding: 1.5rem;
            background: var(--white);
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin: 1rem auto;
            max-width: 1400px;
        }

        .alm-header-content {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .alm-header-content h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alm-header-content h2 i {
            color: var(--primary-color);
        }

        .alm-booking-tabs {
            display: flex;
            overflow-x: auto;
            gap: 0.5rem;
            padding: 0.5rem 0;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid var(--border-color);
            scrollbar-width: thin;
            scrollbar-color: var(--primary-color) var(--border-color);
        }

        .alm-booking-tabs::-webkit-scrollbar {
            height: 6px;
        }

        .alm-booking-tabs::-webkit-scrollbar-track {
            background: var(--border-color);
            border-radius: 3px;
        }

        .alm-booking-tabs::-webkit-scrollbar-thumb {
            background-color: var(--primary-color);
            border-radius: 3px;
        }

        .alm-booking-tab {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            color: var(--secondary-color);
            text-decoration: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            white-space: nowrap;
            transition: all 0.2s ease;
            cursor: pointer;
            user-select: none;
        }

        .alm-booking-tab i {
            font-size: 0.875rem;
        }

        .alm-booking-tab:hover {
            color: var(--primary-color);
            background-color: rgba(16, 185, 129, 0.05);
            border-radius: 0.375rem 0.375rem 0 0;
        }

        .alm-booking-tab.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
            font-weight: 500;
        }

        .alm-booking-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.5rem;
            height: 1.5rem;
            padding: 0 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
            background-color: var(--border-color);
            color: var(--secondary-color);
            border-radius: 9999px;
            transition: all 0.2s ease;
        }

        .alm-booking-tab.active .alm-booking-count {
            background-color: var(--primary-color);
            color: var(--white);
        }

        .alm-table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .alm-bookings-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.875rem;
        }

        .alm-bookings-table th {
            background: var(--bg-light);
            color: var(--text-light);
            font-weight: 500;
            text-align: left;
            padding: 1rem;
            white-space: nowrap;
            border-bottom: 1px solid var(--border-color);
        }

        .alm-bookings-table th:first-child {
            border-top-left-radius: 0.5rem;
        }

        .alm-bookings-table th:last-child {
            border-top-right-radius: 0.5rem;
        }

        .alm-bookings-table th i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }

        .alm-bookings-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-dark);
            vertical-align: top;
            background-color: var(--white);
            transition: background-color 0.2s ease;
        }

        .alm-bookings-table tr:last-child td:first-child {
            border-bottom-left-radius: 0.5rem;
        }

        .alm-bookings-table tr:last-child td:last-child {
            border-bottom-right-radius: 0.5rem;
        }

        .alm-bookings-table tbody tr:hover td {
            background-color: var(--bg-light);
        }

        .alm-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .alm-status-pending {
            background: #fef3c7;
            color: #d97706;
        }

        .alm-status-approved, 
        .alm-status-confirmed {
            background: #dcfce7;
            color: #15803d;
        }

        .alm-status-completed {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .alm-status-cancelled {
            background: #fee2e2;
            color: #dc2626;
        }

        .alm-status-select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            background: var(--white);
            color: var(--text-dark);
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.5rem center;
            background-size: 1.25rem;
            padding-right: 2rem;
        }

        .alm-status-select:hover {
            border-color: var(--primary-color);
        }

        .alm-status-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .alm-booking-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1000;
            padding: 1rem;
            overflow-y: auto;
        }

        .alm-modal-content {
            background: var(--white);
            border-radius: 0.75rem;
            max-width: 800px;
            margin: 2rem auto;
            max-height: calc(100vh - 4rem);
            overflow-y: auto;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alm-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .alm-modal-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alm-modal-close {
            font-size: 1.5rem;
            color: var(--text-light);
            cursor: pointer;
            transition: color 0.2s ease;
            background: none;
            border: none;
            padding: 0.25rem;
            border-radius: 0.375rem;
            line-height: 1;
        }

        .alm-modal-close:hover {
            color: var(--text-dark);
            background-color: var(--bg-light);
        }

        .alm-modal-body {
            padding: 1.5rem;
        }

        .alm-detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .alm-detail-section {
            margin-bottom: 1.5rem;
        }

        .alm-detail-section h3 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alm-detail-section h3 i {
            color: var(--primary-color);
        }

        .alm-detail-item {
            margin-bottom: 1rem;
        }

        .alm-detail-item label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-light);
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .alm-detail-item span {
            color: var(--text-dark);
            font-weight: 500;
            display: block;
            padding-left: 1.5rem;
        }

        .alm-button-group {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }

        .alm-btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            transition: all 0.2s ease;
        }

        .alm-btn i {
            font-size: 0.875rem;
        }

        .alm-btn-primary {
            background-color: var(--primary-color);
            color: var(--white);
        }

        .alm-btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .alm-btn-secondary {
            background-color: var(--border-color);
            color: var(--text-dark);
        }

        .alm-btn-secondary:hover {
            background-color: #d1d5db;
        }

        .alm-error-message {
            background-color: #fee2e2;
            border: 1px solid var(--danger-color);
            color: #dc2626;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alm-error-message::before {
            content: '⚠️';
        }

        .alm-text-center {
            text-align: center !important;
        }

        .mb-4 {
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .alm-hide-mobile {
                display: none;
            }
            
            .alm-booking-tabs {
                padding: 0.5rem;
                margin: -0.5rem -0.5rem 1rem -0.5rem;
            }
            
            .alm-booking-tab {
                padding: 0.5rem 0.75rem;
            }
            
            .alm-modal-content {
                margin: 1rem;
                max-height: calc(100vh - 2rem);
            }
            
            .alm-bookings-container {
                padding: 1rem;
                margin: 0.5rem;
            }
            
            .alm-bookings-table td,
            .alm-bookings-table th {
                padding: 0.75rem;
            }

            .alm-detail-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .alm-button-group {
                flex-direction: column;
            }

            .alm-btn {
                width: 100%;
                justify-content: center;
            }

            .alm-status-select {
                font-size: 0.813rem;
                padding: 0.375rem 1.5rem 0.375rem 0.5rem;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(10px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .alm-loading {
            position: relative;
            pointer-events: none;
        }

        .alm-loading::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: var(--primary-color);
        }

        .alm-loading-spinner {
            display: inline-block;
            width: 2rem;
            height: 2rem;
            border: 3px solid var(--border-color);
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .alm-bookings-table tbody tr {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .alm-bookings-table tbody tr:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            position: relative;
            z-index: 1;
        }

        .alm-modal-content::-webkit-scrollbar {
            width: 8px;
        }

        .alm-modal-content::-webkit-scrollbar-track {
            background: var(--border-color);
            border-radius: 4px;
        }

        .alm-modal-content::-webkit-scrollbar-thumb {
            background-color: var(--primary-color);
            border-radius: 4px;
        }

        .alm-form-group {
            margin-bottom: 1rem;
        }

        .alm-form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
            font-weight: 500;
        }

        .alm-form-input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .alm-form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .alm-status-badge {
            position: relative;
            overflow: hidden;
        }

        .alm-status-badge::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                45deg,
                transparent 0%,
                rgba(255, 255, 255, 0.2) 50%,
                transparent 100%
            );
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            from {
                transform: translateX(-100%);
            }
            to {
                transform: translateX(100%);
            }
        }

        @media print {
            .alm-bookings-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
            }

            .alm-booking-tabs,
            .alm-status-select,
            .alm-btn {
                display: none;
            }

            .alm-hide-mobile {
                display: table-cell !important;
            }

            .alm-bookings-table {
                border: 1px solid #ddd;
            }

            .alm-bookings-table th,
            .alm-bookings-table td {
                border: 1px solid #ddd;
            }
        }

        .alm-toast {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            padding: 1rem;
            border-radius: 0.5rem;
            background: var(--white);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            z-index: 1100;
            animation: slideUp 0.3s ease-out;
            max-width: 24rem;
        }

        .alm-toast-success {
            border-left: 4px solid var(--success-color);
        }

        .alm-toast-error {
            border-left: 4px solid var(--danger-color);
        }

        .alm-toast-message {
            flex-grow: 1;
            font-size: 0.875rem;
            color: var(--text-dark);
        }

        .alm-toast-close {
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            padding: 0.25rem;
            font-size: 1.25rem;
            line-height: 1;
            transition: color 0.2s ease;
        }

        .alm-toast-close:hover {
            color: var(--text-dark);
        }

        .alm-tooltip {
            position: relative;
        }

        .alm-tooltip:hover::before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 0.5rem;
            background: var(--text-dark);
            color: var(--white);
            font-size: 0.75rem;
            border-radius: 0.25rem;
            white-space: nowrap;
            z-index: 1000;
            animation: fadeIn 0.2s ease-out;
        }

        .alm-tooltip:hover::after {
            content: '';
            position: absolute;
            bottom: calc(100% - 5px);
            left: 50%;
            transform: translateX(-50%);
            border: 5px solid transparent;
            border-top-color: var(--text-dark);
            animation: fadeIn 0.2s ease-out;
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        .alm-sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .alm-booking-tab:focus,
        .alm-btn:focus,
        .alm-status-select:focus,
        .alm-modal-close:focus {
            outline: 2px solid var(--primary-color);
            outline-offset: 2px;
        }
        .alm-delete-btn {
            background-color: #ef4444;
            color: white;
            border: none;
            border-radius: 0.375rem;
            padding: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .alm-delete-btn:hover {
            background-color: #dc2626;
            transform: translateY(-1px);
        }
        [data-theme="dark"] {
            --primary-color: #10b981;
            --primary-hover: #059669;
            --primary-light: rgba(16, 185, 129, 0.2);
            --text-primary: #ffffff;
            --text-secondary: #ffffff;
            --bg-primary: #000000;
            --bg-secondary: #000000;
            --bg-light: #000000;
            --border-color: #333333;
            --container-border: #333333;
            --hover-bg: #1a1a1a;
        }

        [data-theme="dark"] * {
            color: #ffffff !important;
        }

        [data-theme="dark"] body {
            background-color: #000000;
        }

        [data-theme="dark"] .alm-bookings-container {
            background: #000000;
            border: 1px solid #333333;
        }

        [data-theme="dark"] .alm-header-content {
            border-bottom: 1px solid #333333;
        }

        [data-theme="dark"] .alm-header-content h2 i {
            color: #ffffff !important;
        }

        [data-theme="dark"] .alm-booking-tabs {
            border-bottom: 1px solid #333333;
        }

        [data-theme="dark"] .alm-booking-tab {
            color: #ffffff !important;
        }

        [data-theme="dark"] .alm-booking-tab i {
            color: #ffffff !important;
        }

        [data-theme="dark"] .alm-booking-tab:hover {
            background-color: #1a1a1a;
            border-color: #333333;
        }

        [data-theme="dark"] .alm-booking-tab.active {
            border-bottom-color: var(--primary-color);
            color: var(--primary-color) !important;
        }

        [data-theme="dark"] .alm-booking-count {
            background-color: #333333;
            color: #ffffff !important;
        }

        [data-theme="dark"] .alm-table-responsive {
            background: #000000;
            border: 1px solid #333333;
        }

        [data-theme="dark"] .alm-bookings-table th {
            background: #000000;
            border: 1px solid #333333;
            color: #ffffff !important;
        }

        [data-theme="dark"] .alm-bookings-table th i {
            color: #ffffff !important;
        }

        [data-theme="dark"] .alm-bookings-table td {
            background: #000000;
            border: 1px solid #333333;
            color: #ffffff !important;
        }

        [data-theme="dark"] .alm-bookings-table tr:hover td {
            background: #1a1a1a;
        }

        [data-theme="dark"] .alm-status-pending {
            background: #332200;
            color: #fbbf24 !important;
            border: 1px solid #854d0e;
        }

        [data-theme="dark"] .alm-status-approved,
        [data-theme="dark"] .alm-status-confirmed {
            background: #132517;
            color: #4ade80 !important;
            border: 1px solid #15803d;
        }

        [data-theme="dark"] .alm-status-completed {
            background: #172554;
            color: #60a5fa !important;
            border: 1px solid #1d4ed8;
        }

        [data-theme="dark"] .alm-status-cancelled {
            background: #2a1215;
            color: #f87171 !important;
            border: 1px solid #dc2626;
        }

        [data-theme="dark"] .alm-status-select {
            background-color: #000000;
            border: 1px solid #333333;
            color: #ffffff !important;
        }

        [data-theme="dark"] .alm-status-select:hover {
            border-color: var(--primary-color);
        }

        [data-theme="dark"] .alm-status-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        }

        [data-theme="dark"] .alm-status-select option {
            background-color: #000000;
            color: #ffffff !important;
        }

        [data-theme="dark"] .alm-booking-modal {
            background: rgba(0, 0, 0, 0.8);
        }

        [data-theme="dark"] .alm-modal-content {
            background: #000000;
            border: 1px solid #333333;
        }

        [data-theme="dark"] .alm-modal-header {
            border-bottom: 1px solid #333333;
        }

        [data-theme="dark"] .alm-modal-header h2 {
            color: #ffffff !important;
        }

        [data-theme="dark"] .alm-modal-header h2 i {
            color: #ffffff !important;
        }

        [data-theme="dark"] .alm-modal-close {
            color: #ffffff !important;
        }

        [data-theme="dark"] .alm-detail-section {
            border: 1px solid #333333;
            background: #000000;
        }

        [data-theme="dark"] .alm-detail-section h3 {
            color: #ffffff !important;
        }

        [data-theme="dark"] .alm-detail-section h3 i {
            color: #ffffff !important;
        }

        [data-theme="dark"] .alm-detail-item label {
            color: #ffffff !important;
        }

        [data-theme="dark"] .alm-detail-item label i {
            color: #ffffff !important;
        }

        [data-theme="dark"] .alm-detail-item span {
            color: #ffffff !important;
        }

        [data-theme="dark"] p,
        [data-theme="dark"] span:not(.alm-status-badge),
        [data-theme="dark"] small,
        [data-theme="dark"] i:not(.alm-status-badge i) {
            color: #ffffff !important;
        }

        [data-theme="dark"] .alm-btn {
            border: 1px solid #333333;
        }

        [data-theme="dark"] .alm-btn-primary {
            background-color: var(--primary-color);
            color: #000000 !important;
            border-color: var(--primary-color);
        }

        [data-theme="dark"] .alm-btn-secondary {
            background-color: #1a1a1a;
            color: #ffffff !important;
            border-color: #333333;
        }

        [data-theme="dark"] ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        [data-theme="dark"] ::-webkit-scrollbar-track {
            background: #000000;
        }

        [data-theme="dark"] ::-webkit-scrollbar-thumb {
            background-color: #333333;
            border-radius: 4px;
        }

        [data-theme="dark"] ::-webkit-scrollbar-thumb:hover {
            background-color: #444444;
        }

        [data-theme="dark"] .alm-error-message {
            background-color: #2c1215;
            border-color: var(--danger-color);
            color: #fca5a5 !important;
        }

        [data-theme="dark"] .alm-loading::after {
            background: rgba(0, 0, 0, 0.8);
        }

        [data-theme="dark"] .alm-text-center {
            color: #ffffff !important;
        }

        [data-theme="dark"] *:focus {
            outline-color: var(--primary-color);
        }

        .view-booking-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 9999;
            min-width: 300px;
            max-width: 400px;
            animation: viewBookingSlideDown 0.3s ease-out;
        }

        .view-booking-toast-success {
            border-left: 4px solid var(--success-color);
        }

        .view-booking-toast-success .view-booking-toast-icon i {
            color: var(--success-color);
        }

        .view-booking-toast-message {
            flex-grow: 1;
            font-size: 14px;
            color: var(--text-dark);
        }

        .view-booking-toast-close {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text-light);
            cursor: pointer;
            padding: 4px;
        }

        .view-booking-toast-close:hover {
            color: var(--text-dark);
        }

        @keyframes viewBookingSlideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
</style>
    
</head>
<body>
<div id="loadingOverlay" class="loading-overlay">
    <div class="loading-content">
        <div class="loading-spinner"></div>
        <p class="loading-text">Processing your request...</p>
    </div>
</div>

<div class="alm-bookings-container">
    <div class="alm-header-content">
        <h2><i class="fas fa-id-card"></i> Alumni ID Card Pending Applications</h2>
    </div>

    <div class="alm-table-responsive">
        <table class="alm-bookings-table">
            <thead>
                <tr>
                    <th><i class="fas fa-user"></i> Applicant</th>
                    <th><i class="fas fa-graduation-cap"></i> Course</th>
                    <th class="alm-hide-mobile"><i class="fas fa-calendar"></i> Year Graduated</th>
                    <th class="alm-hide-mobile"><i class="fas fa-school"></i> High School</th>
                    <th class="alm-hide-mobile"><i class="fas fa-tag"></i> Type</th>
                    <th><i class="fas fa-info-circle"></i> Status</th>
                    <th class="alm-hide-mobile"><i class="fas fa-cog"></i> Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($applicationsResult && $applicationsResult->num_rows > 0): ?>
                    <?php while($application = $applicationsResult->fetch_assoc()): ?>
                        <tr data-user-id="<?php echo htmlspecialchars($application['user_id']); ?>">
                            <td>
                                <?php 
                                $fullName = implode(' ', array_filter([
                                    $application['first_name'],
                                    $application['middle_name'],
                                    $application['last_name']
                                ]));
                                echo htmlspecialchars($fullName); 
                                ?>
                                <br>
                                <small><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($application['email']); ?></small>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($application['course']); ?>
                            </td>
                            <td class="alm-hide-mobile">
                                <?php echo htmlspecialchars($application['year_graduated']); ?>
                            </td>
                            <td class="alm-hide-mobile">
                                <?php echo htmlspecialchars($application['highschool_graduated']); ?>
                            </td>
                            <td class="alm-hide-mobile">
                                <?php echo htmlspecialchars($application['membership_type']); ?>
                            </td>
                            <td>
                                <span class="alm-status-badge alm-status-pending">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            </td>
                            <td class="alm-hide-mobile">
                                <select class="alm-status-select" data-application-id="<?php echo $application['id']; ?>">
                                    <option value="pending" selected>Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="declined">Declined</option>
                                </select>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="alm-text-center">
                            <i class="fas fa-inbox fa-2x"></i><br>
                            No pending ID card applications found
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

    <!-- Status Change Confirmation Modal -->
    <div id="almStatusConfirmModal" class="alm-booking-modal">
        <div class="alm-modal-content" style="max-width: 400px;">
            <div class="alm-modal-header">
                <h2><i class="fas fa-question-circle"></i> Confirm Status Change</h2>
                <span class="alm-modal-close">&times;</span>
            </div>
            <div class="alm-modal-body">
                <p id="almStatusConfirmMessage" class="text-center mb-4">
                    Are you sure you want to change the status of this application?
                </p>
                <div class="alm-button-group">
                    <button id="almStatusConfirmBtn" class="alm-btn alm-btn-primary">
                        <i class="fas fa-check"></i> Confirm
                    </button>
                    <button id="almStatusCancelBtn" class="alm-btn alm-btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="almDeleteConfirmModal" class="alm-booking-modal">
        <div class="alm-modal-content" style="max-width: 400px;">
            <div class="alm-modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h2>
                <span class="alm-modal-close">&times;</span>
            </div>
            <div class="alm-modal-body">
                <p id="almDeleteConfirmMessage" class="text-center mb-4">
                    Are you sure you want to delete this application? This action cannot be undone.
                </p>
                <div class="alm-button-group">
                    <button id="almDeleteConfirmBtn" class="alm-btn alm-btn-primary" style="background-color: #ef4444;">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                    <button id="almDeleteCancelBtn" class="alm-btn alm-btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusConfirmModal = document.getElementById('almStatusConfirmModal');
    const deleteModal = document.getElementById('almDeleteConfirmModal');
    let applicationId, newStatus, originalValue, select;

    function showLoading(message = 'Processing your request...') {
        const overlay = document.getElementById('loadingOverlay');
        const loadingText = overlay.querySelector('.loading-text');
        if (loadingText) {
            loadingText.textContent = message;
        }
        overlay.style.display = 'flex';
        overlay.classList.add('loading-overlay-show');
        overlay.classList.remove('loading-overlay-hide');
        document.body.style.overflow = 'hidden';
    }

    function hideLoading() {
        const overlay = document.getElementById('loadingOverlay');
        overlay.classList.add('loading-overlay-hide');
        overlay.classList.remove('loading-overlay-show');
        setTimeout(() => {
            overlay.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }

    const getStatusIcon = (status) => {
        switch(status) {
            case 'pending':
                return '<i class="fas fa-clock"></i> ';
            case 'confirmed':
                return '<i class="fas fa-check"></i> ';
            case 'paid':
                return '<i class="fas fa-dollar-sign"></i> ';
            case 'declined':
                return '<i class="fas fa-times-circle"></i> ';
            default:
                return '';
        }
    };

    window.deleteApplication = function(appId, event) {
        event.stopPropagation();
        
        const confirmBtn = document.getElementById('almDeleteConfirmBtn');
        const cancelBtn = document.getElementById('almDeleteCancelBtn');
        const closeBtn = deleteModal.querySelector('.alm-modal-close');
        
        deleteModal.style.display = "block";

        const handleDelete = async () => {
            try {
                deleteModal.style.display = "none";
                showLoading('Deleting application...');

                const formData = new FormData();
                formData.append('application_id', appId);

                const response = await fetch('/Alumni-CvSU/admin/delete_id_application.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                
                if (data.success) {
                    const row = event.target.closest('tr');
                    if (row) {
                        row.remove();
                    }
                    hideLoading();
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    throw new Error(data.message || 'Failed to delete application');
                }
            } catch (error) {
                console.error('Delete error:', error);
                hideLoading();
                alert('Failed to delete application: ' + error.message);
            }
        };

        confirmBtn.onclick = handleDelete;
        cancelBtn.onclick = () => deleteModal.style.display = "none";
        closeBtn.onclick = () => deleteModal.style.display = "none";
    };

    document.querySelectorAll('.alm-status-select').forEach(selectElement => {
        selectElement.addEventListener('change', function(e) {
            e.stopPropagation();
            applicationId = this.getAttribute('data-application-id');
            newStatus = this.value;
            originalValue = this.value;
            select = this;
            statusConfirmModal.style.display = "block";
        });
    });

    const statusConfirmBtn = document.getElementById('almStatusConfirmBtn');
    const statusCancelBtn = document.getElementById('almStatusCancelBtn');
    const statusCloseBtn = statusConfirmModal.querySelector('.alm-modal-close');

    if (statusConfirmBtn) {
        statusConfirmBtn.onclick = async function() {
            try {
                const formData = new FormData();
                formData.append('application_id', applicationId);
                formData.append('status', newStatus);

                statusConfirmModal.style.display = "none";
                showLoading('Updating application status...');

                const response = await fetch('/Alumni-CvSU/admin/update_id_status.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();if (data.success) {
                    // Update the status badge in the UI
                    const row = select.closest('tr');
                    const statusCell = row.querySelector('.alm-status-badge');
                    const statusIcon = getStatusIcon(newStatus);
                    statusCell.className = `alm-status-badge alm-status-${newStatus}`;
                    statusCell.innerHTML = statusIcon + newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                    
                    hideLoading();
                    
                    // Optional: Reload the page to update counts
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    throw new Error(data.message || 'Failed to update status');
                }
            } catch (error) {
                console.error('Update error:', error);
                // Revert the select value to its original state
                if (select) {
                    select.value = originalValue;
                }
                hideLoading();
                alert('Failed to update status: ' + error.message);
            }
        };
    }

    if (statusCancelBtn) {
        statusCancelBtn.onclick = function() {
            statusConfirmModal.style.display = "none";
            // Revert the select value to its original state
            if (select) {
                select.value = originalValue;
            }
        };
    }

    if (statusCloseBtn) {
        statusCloseBtn.onclick = function() {
            statusConfirmModal.style.display = "none";
            // Revert the select value to its original state
            if (select) {
                select.value = originalValue;
            }
        };
    }

    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target === statusConfirmModal) {
            statusConfirmModal.style.display = "none";
            if (select) {
                select.value = originalValue;
            }
        }
        if (event.target === deleteModal) {
            deleteModal.style.display = "none";
        }
    };

    // Handle escape key press
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            if (statusConfirmModal.style.display === "block") {
                statusConfirmModal.style.display = "none";
                if (select) {
                    select.value = originalValue;
                }
            }
            if (deleteModal.style.display === "block") {
                deleteModal.style.display = "none";
            }
        }
    });
});
</script>

</body>
</html>