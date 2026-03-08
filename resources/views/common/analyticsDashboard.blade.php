@extends('layout.blankpage')

@section('content')
@include('layout.partials.message')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #1dd3b0 0%, #17a98b 100%);
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --card-hover-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    body {
        background: #f8f9fa;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .page-header {
        background: #1f2937;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--card-shadow);
    }

    .page-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin: 0;
    }

    .breadcrumb {
        margin: 0.5rem 0 0 0;
        background: transparent;
        padding: 0;
    }

    .breadcrumb-item a {
        color: #667eea;
        text-decoration: none;
    }

    .modern-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        height: 100%;
    }

    .modern-card:hover {
        box-shadow: var(--card-hover-shadow);
        transform: translateY(-2px);
    }

    .card-header-modern {
        background: transparent;
        border: none;
        padding: 1.5rem 1.5rem 1rem 1.5rem;
    }

    .card-header-modern h5 {
        font-size: 1rem;
        font-weight: 600;
        color: #1a202c;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-header-modern .icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--primary-gradient);
        color: white;
        font-size: 0.875rem;
    }

    .card-body-modern {
        padding: 1.5rem;
        padding-top: 0.5rem;
    }

    .filter-section {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .filter-section input[type="date"] {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    .filter-section input[type="date"]:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .filter-date-input,
    .filter-year-input {
        width: 140px;
        min-width: 120px;
    }

    @media (min-width: 769px) {
        .filter-date-input,
        .filter-year-input {
            flex: 0 0 auto;
        }
    }

    .filter-select {
        width: auto;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .btn-filter {
        background: var(--primary-gradient);
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: white;
        transition: all 0.2s;
    }

    .btn-filter:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .toggle-switch {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #f7fafc;
        padding: 0.5rem 1rem;
        border-radius: 8px;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e0;
        transition: 0.3s;
        border-radius: 24px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }

    input:checked+.slider {
        background: var(--primary-gradient);
    }

    input:checked+.slider:before {
        transform: translateX(20px);
    }

    .stats-text {
        color: #4a5568;
        font-size: 0.875rem;
    }

    .stats-text strong {
        color: #1a202c;
    }

    .chart-container {
        min-height: 300px;
        position: relative;
    }

    .ai-sections-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .ai-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .ai-section:hover {
        border-color: #1dd3b0;
        background: #f0fdf9;
    }

    .ai-section:last-child {
        margin-bottom: 0;
    }

    .ai-section h6 {
        font-size: 0.875rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #1dd3b0;
    }

    .ai-section h6 .ai-section-icon {
        width: 26px;
        height: 26px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--primary-gradient);
        color: white;
        font-size: 0.75rem;
        flex-shrink: 0;
    }

    .ai-section p {
        font-size: 0.85rem;
        color: #4a5568;
        line-height: 1.8;
        margin: 0;
        white-space: pre-line;
    }

    .ai-loading {
        text-align: center;
        padding: 3rem 1rem;
        color: #6b7280;
        min-height: 300px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .ai-loading .spinner {
        display: inline-block;
        width: 36px;
        height: 36px;
        border: 3px solid rgba(29, 211, 176, 0.25);
        border-top-color: #1dd3b0;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-bottom: 1rem;
    }

    .ai-loading p {
        font-size: 0.875rem;
        margin: 0;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .ai-error {
        text-align: center;
        padding: 2rem 1rem;
        color: #ef4444;
        background: #fef2f2;
        border-radius: 12px;
        border: 1px solid #fecaca;
    }

    .ai-error p {
        margin: 0;
        font-size: 0.875rem;
    }

    /* Floating AI Button */
    .ai-floating-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--primary-gradient);
        border: none;
        box-shadow: 0 4px 20px rgba(29, 211, 176, 0.4);
        cursor: pointer;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        animation: pulse-glow 2s infinite;
    }

    .ai-floating-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 30px rgba(29, 211, 176, 0.6);
    }

    .ai-floating-btn .btn-icon {
        font-size: 1.5rem;
    }

    .ai-floating-btn .btn-tooltip {
        position: absolute;
        right: 70px;
        background: #1f2937;
        color: white;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 500;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .ai-floating-btn:hover .btn-tooltip {
        opacity: 1;
        visibility: visible;
    }

    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 4px 20px rgba(29, 211, 176, 0.4); }
        50% { box-shadow: 0 4px 30px rgba(29, 211, 176, 0.7); }
    }

    /* AI Modal */
    .ai-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        z-index: 1001;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .ai-modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .ai-modal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) scale(0.9);
        width: 95%;
        max-width: 950px;
        height: 80vh;
        max-height: 650px;
        background: #f8fafc;
        border-radius: 24px;
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.35);
        z-index: 1002;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .ai-modal.active {
        opacity: 1;
        visibility: visible;
        transform: translate(-50%, -50%) scale(1);
    }

    .ai-modal-header {
        background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        flex-shrink: 0;
    }

    .ai-modal-header h5 {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0;
        color: white;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .ai-modal-header .ai-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--primary-gradient);
        color: white;
        font-size: 1rem;
    }

    .ai-modal-close {
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: white;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        transition: all 0.2s ease;
    }

    .ai-modal-close:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: scale(1.05);
    }

    .ai-modal-content {
        display: flex;
        flex: 1;
        overflow: hidden;
    }

    /* Sidebar Tabs */
    .ai-modal-sidebar {
        width: 200px;
        background: white;
        border-right: 1px solid #e2e8f0;
        padding: 1rem 0;
        flex-shrink: 0;
        overflow-y: auto;
    }

    .ai-tab-btn {
        width: 100%;
        padding: 0.875rem 1.25rem;
        border: none;
        background: transparent;
        text-align: left;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #64748b;
        transition: all 0.2s ease;
        position: relative;
    }

    .ai-tab-btn:hover {
        background: #f1f5f9;
        color: #1f2937;
    }

    .ai-tab-btn.active {
        background: linear-gradient(90deg, #f0fdf9 0%, transparent 100%);
        color: #1dd3b0;
        font-weight: 600;
    }

    .ai-tab-btn.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 60%;
        background: var(--primary-gradient);
        border-radius: 0 4px 4px 0;
    }

    .ai-tab-btn .tab-icon {
        font-size: 1.1rem;
        width: 24px;
        text-align: center;
    }

    /* Tab Content */
    .ai-modal-body {
        flex: 1;
        padding: 0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .ai-tab-content {
        display: none;
        padding: 1.5rem;
        height: 100%;
        overflow-y: auto;
        animation: fadeIn 0.3s ease;
    }

    .ai-tab-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .ai-content-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        height: 100%;
        overflow-y: auto;
    }

    .ai-content-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .ai-content-header .content-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--primary-gradient);
        font-size: 1.25rem;
    }

    .ai-content-header h4 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
    }

    .ai-content-text {
        font-size: 0.925rem;
        color: #475569;
        line-height: 1.9;
        white-space: pre-line;
    }

    .ai-modal-date {
        font-size: 0.75rem;
        color: #9ca3af;
        background: rgba(255, 255, 255, 0.15);
        padding: 0.35rem 0.85rem;
        border-radius: 20px;
        white-space: nowrap;
    }

    .ai-modal-header-right {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-shrink: 0;
    }

    .ai-modal-title-text {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Mobile responsive */
    @media (max-width: 768px) {
        .ai-modal {
            width: 100%;
            height: 100%;
            max-height: 100%;
            border-radius: 0;
        }

        .ai-modal-content {
            flex-direction: column;
        }

        .ai-modal-sidebar {
            width: 100%;
            border-right: none;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.5rem;
            display: flex;
            overflow-x: auto;
            overflow-y: hidden;
            flex-shrink: 0;
        }

        .ai-tab-btn {
            width: auto;
            padding: 0.75rem 1rem;
            flex-direction: column;
            gap: 0.25rem;
            min-width: max-content;
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        .ai-tab-btn.active::before {
            display: none;
        }

        .ai-tab-btn.active {
            background: var(--primary-gradient);
            color: white;
            border-radius: 8px;
        }

        .ai-tab-content {
            padding: 1rem;
        }

        .ai-content-card {
            padding: 1rem;
        }
    }

    /* Top row: overview full width. Next 2x2 grid for the rest */
    @media (min-width: 768px) {
        .ai-sections-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .ai-section-overview {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 767px) {
        .ai-modal-header {
            padding: 1rem;
            flex-direction: row;
        }

        .ai-modal-body {
            padding: 1rem;
        }

        .ai-section {
            padding: 1rem;
        }

        .ai-section p {
            font-size: 0.8rem;
            line-height: 1.7;
        }

        .ai-floating-btn {
            bottom: 20px;
            right: 20px;
            width: 55px;
            height: 55px;
        }

        .ai-floating-btn .btn-tooltip {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .filter-section {
            flex-direction: column;
            align-items: stretch;
        }

        .toggle-switch {
            justify-content: center;
        }
    }

    /* ========== MOBILE RESPONSIVE STYLES ========== */
    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
        }

        .page-header {
            padding: 1.25rem;
            border-radius: 12px;
            margin-bottom: 1rem;
        }

        .page-header h1 {
            font-size: 1.25rem;
        }

        .breadcrumb {
            font-size: 0.8rem;
        }

        .modern-card {
            border-radius: 12px;
        }

        .card-header-modern {
            padding: 1rem;
        }

        .card-header-modern h5 {
            font-size: 0.875rem;
            flex-wrap: wrap;
        }

        .card-header-modern .icon {
            width: 28px;
            height: 28px;
            font-size: 0.75rem;
        }

        .card-body-modern {
            padding: 1rem;
            padding-top: 0.5rem;
        }

        /* Filter section mobile fixes */
        .filter-section {
            width: 100%;
        }

        .filter-section .d-flex {
            flex-direction: column !important;
            width: 100%;
            gap: 0.5rem !important;
        }

        .filter-section input[type="date"],
        .filter-section input[type="number"],
        .filter-date-input,
        .filter-year-input {
            width: 100% !important;
            min-width: unset !important;
        }

        .filter-section .btn-filter,
        .filter-section .btn-outline-secondary {
            width: 100%;
        }

        .filter-select {
            width: 100% !important;
            margin-top: 0.5rem;
        }

        .toggle-switch {
            width: 100%;
            justify-content: center;
            margin-top: 0.5rem;
        }

        /* Chart containers */
        .chart-container {
            min-height: 250px;
            margin: 0 -0.5rem;
        }

        #monthlyRequestsChart,
        #unclaimedChart,
        #docTypeChart,
        #modeChart,
        #gradeLevelChart,
        #revenueChart {
            width: 100% !important;
        }

        .stats-text {
            font-size: 0.8rem;
        }

        /* Card header with dropdown flex */
        .card-header-modern .d-flex.justify-content-between {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 0.75rem;
        }

        /* Grid row gaps */
        .row.g-4 {
            --bs-gutter-y: 1rem;
        }

        .row.mb-4 {
            margin-bottom: 1rem !important;
        }

        /* AI Floating Button */
        .ai-floating-btn {
            bottom: 15px;
            right: 15px;
            width: 50px;
            height: 50px;
        }

        .ai-floating-btn .btn-icon {
            font-size: 1.25rem;
        }
    }

    /* AI Modal Mobile Styles */
    @media (max-width: 768px) {
        .ai-modal {
            width: 100%;
            height: calc(100% - 60px);
            max-height: calc(100% - 60px);
            border-radius: 16px 16px 0 0;
            max-width: 100%;
            top: auto;
            bottom: 0;
            left: 0;
            transform: translateY(100%);
        }

        .ai-modal.active {
            transform: translateY(0);
        }

        .ai-modal-header {
            padding: 0.75rem 1rem;
            flex-wrap: nowrap;
            gap: 0.5rem;
        }

        .ai-modal-header h5 {
            font-size: 0.85rem;
            gap: 0.5rem;
            flex: 1;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .ai-modal-header-right {
            flex-shrink: 0;
            gap: 0.5rem;
        }

        .ai-modal-header .ai-icon {
            width: 28px;
            height: 28px;
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        .ai-modal-date {
            font-size: 0.6rem;
            padding: 0.2rem 0.4rem;
            display: none;
        }

        .ai-modal-close {
            width: 36px;
            height: 36px;
            font-size: 1.5rem;
            flex-shrink: 0;
            min-width: 36px;
        }

        .ai-modal-content {
            flex-direction: column;
            flex: 1;
            overflow: hidden;
        }

        .ai-modal-sidebar {
            width: 100%;
            border-right: none;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.5rem;
            display: flex;
            overflow-x: auto;
            overflow-y: hidden;
            flex-shrink: 0;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .ai-modal-sidebar::-webkit-scrollbar {
            display: none;
        }

        .ai-tab-btn {
            width: auto;
            padding: 0.6rem 0.75rem;
            flex-direction: column;
            gap: 0.2rem;
            min-width: max-content;
            font-size: 0.7rem;
            flex-shrink: 0;
        }

        .ai-tab-btn .tab-icon {
            font-size: 1rem;
        }

        .ai-tab-btn.active::before {
            display: none;
        }

        .ai-tab-btn.active {
            background: var(--primary-gradient);
            color: white;
            border-radius: 8px;
        }

        .ai-modal-body {
            flex: 1;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .ai-tab-content {
            padding: 1rem;
            height: auto;
            min-height: 100%;
        }

        .ai-content-card {
            padding: 1rem;
            border-radius: 12px;
            height: auto;
            overflow: visible;
        }

        .ai-content-header {
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
        }

        .ai-content-header .content-icon {
            width: 34px;
            height: 34px;
            font-size: 1rem;
        }

        .ai-content-header h4 {
            font-size: 1rem;
        }

        .ai-content-text {
            font-size: 0.85rem;
            line-height: 1.75;
        }

        .ai-loading {
            min-height: 200px;
            padding: 2rem 1rem;
        }

        .ai-loading .spinner {
            width: 30px;
            height: 30px;
        }
    }

    /* Extra small mobile screens */
    @media (max-width: 400px) {
        .ai-modal-header h5 {
            font-size: 0.75rem;
        }

        .ai-modal-header .ai-icon {
            width: 24px;
            height: 24px;
            font-size: 0.7rem;
        }

        .ai-modal-close {
            width: 32px;
            height: 32px;
            min-width: 32px;
            font-size: 1.25rem;
        }

        .ai-tab-btn {
            padding: 0.5rem 0.6rem;
            font-size: 0.65rem;
        }

        .ai-tab-btn .tab-icon {
            font-size: 0.9rem;
        }

        .ai-content-header h4 {
            font-size: 0.9rem;
        }

        .ai-content-text {
            font-size: 0.8rem;
            line-height: 1.7;
        }
    }

    /* Extra small devices - general */
    @media (max-width: 400px) {
        .page-header h1 {
            font-size: 1.1rem;
        }

        .card-header-modern h5 {
            font-size: 0.8rem;
        }
    }

    /* Prevent horizontal overflow globally */
    html, body {
        overflow-x: hidden;
        max-width: 100vw;
    }

    .container-fluid {
        overflow-x: hidden;
    }

    /* ApexCharts responsive fix */
    .apexcharts-canvas {
        max-width: 100% !important;
    }

    .apexcharts-svg {
        max-width: 100% !important;
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="page-header">
        <h1>📊 Analytics Dashboard</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard')}}">Dashboard</a></li>
            <li class="breadcrumb-item active text-white">Analytics</li>
        </ol>
    </div>

    <!-- Main Chart - Full Width -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card">
                <div class="card-header-modern">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <h5 class="mb-0">
                            <span class="icon">📈</span>
                            <span id="mainChartTitle">Monthly Document Requests</span>
                        </h5>
                        <div class="filter-section">
                            <div class="d-flex align-items-center gap-2 flex-wrap w-100">
                                <input type="date" id="mainStartDate" name="start_date" value="{{ $startDate }}" class="form-control form-control-sm filter-date-input">
                                <input type="date" id="mainEndDate" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm filter-date-input">
                                <input type="number" id="mainStartYear" placeholder="Start Year" class="form-control form-control-sm filter-year-input" style="display: none;" min="2000" max="2100">
                                <input type="number" id="mainEndYear" placeholder="End Year" class="form-control form-control-sm filter-year-input" style="display: none;" min="2000" max="2100">
                                <button type="button" id="mainFilterBtn" class="btn btn-filter btn-sm">Filter</button>
                                <button type="button" id="mainResetBtn" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">Reset</button>
                                <div class="toggle-switch mt-2 mt-md-0">
                                    <span class="small text-muted">Monthly</span>
                                    <label class="switch mb-0">
                                        <input type="checkbox" id="toggleYearly">
                                        <span class="slider"></span>
                                    </label>
                                    <span class="small text-muted">Yearly</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body-modern">
                    <div class="chart-container" id="monthlyRequestsChart"></div>
                    <div class="text-center mt-3 stats-text">
                        <strong>Total Requests:</strong> <span id="totalRequests">{{ $totalRequestsInInterval }}</span><br>
                        <small id="dateRangeText">
                            From <strong>{{ \Carbon\Carbon::parse($startDate)->format('F j, Y') }}</strong>
                            to <strong>{{ \Carbon\Carbon::parse($endDate)->format('F j, Y') }}</strong>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-12 mb-4">
        <div class="modern-card">
            <div class="card-header-modern">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h5>
                        <span class="icon">📦</span>
                        Unclaimed Documents
                    </h5>
                    <div class="filter-section">
                        <div class="d-flex align-items-center gap-2 flex-wrap w-100">
                            <input type="date" id="unclaimedStartDate" class="form-control form-control-sm filter-date-input">
                            <input type="date" id="unclaimedEndDate" class="form-control form-control-sm filter-date-input">
                            <button type="button" id="unclaimedFilterBtn" class="btn btn-filter btn-sm">Filter</button>
                            <button type="button" id="unclaimedResetBtn" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">Reset</button>
                            <div class="toggle-switch mt-2 mt-md-0">
                                <span class="small text-muted">Monthly</span>
                                <label class="switch mb-0">
                                    <input type="checkbox" id="toggleUnclaimedYearly">
                                    <span class="slider"></span>
                                </label>
                                <span class="small text-muted">Yearly</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body-modern">
                <div class="chart-container" id="unclaimedChart" style="min-height: 250px;"></div>
            </div>
        </div>
    </div>

    <!-- Two Column Grid -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-6">
            <div class="modern-card">
                <div class="card-header-modern">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5>
                            <span class="icon">📄</span>
                            Request by Document Type
                        </h5>
                        <select id="docTypeFilter" class="form-select form-select-sm filter-select">
                            <option value="all">All Types</option>
                        </select>
                    </div>
                </div>
                <div class="card-body-modern">
                    <div class="chart-container" id="docTypeChart"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="modern-card">
                <div class="card-header-modern">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5>
                            <span class="icon">🌐</span>
                            Request Mode
                        </h5>
                        <select id="requestModeFilter" class="form-select form-select-sm filter-select">
                            <option value="all">All Types</option>
                        </select>
                    </div>
                </div>
                <div class="card-body-modern">
                    <div class="chart-container" id="modeChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Three Column Grid -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-6">
            <div class="modern-card">
                <div class="card-header-modern">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5>
                            <span class="icon">🎓</span>
                            Grade Level Distribution
                        </h5>
                        <select id="gradeLevelFilter" class="form-select form-select-sm filter-select">
                            <option value="all">All Types</option>
                        </select>
                    </div>
                </div>
                <div class="card-body-modern">
                    <div class="chart-container" id="gradeLevelChart" style="min-height: 250px;"></div>
                    <div class="text-center mt-3 stats-text">
                        <strong>Total:</strong> <span id="gradeLevelTotal">856</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="modern-card">
                <div class="card-header-modern">
                    <h5>
                        <span class="icon">💰</span>
                        Monthly Revenue
                    </h5>
                </div>
                <div class="card-body-modern">
                    <div class="chart-container" id="revenueChart" style="min-height: 250px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating AI Button -->
<button class="ai-floating-btn" id="aiFloatingBtn" title="AI Insights">
    <span class="btn-icon">🤖</span>
    <span class="btn-tooltip">AI Analytics Insights</span>
</button>

<!-- AI Modal Overlay -->
<div class="ai-modal-overlay" id="aiModalOverlay"></div>

<!-- AI Modal -->
<div class="ai-modal" id="aiModal">
    <div class="ai-modal-header">
        <h5>
            <span class="ai-icon">🤖</span>
            <span class="ai-modal-title-text">AI Analytics Insights</span>
        </h5>
        <div class="ai-modal-header-right">
            <span class="ai-modal-date" id="aiGeneratedDate">Click to generate</span>
            <button class="ai-modal-close" id="aiModalClose">&times;</button>
        </div>
    </div>
    <div class="ai-modal-content">
        <!-- Sidebar Tabs -->
        <div class="ai-modal-sidebar" id="aiSidebar">
            <button class="ai-tab-btn active" data-tab="overview">
                <span class="tab-icon">📋</span>
                <span>Overview</span>
            </button>
            <button class="ai-tab-btn" data-tab="busiestMonths">
                <span class="tab-icon">📅</span>
                <span>Busiest Months</span>
            </button>
            <button class="ai-tab-btn" data-tab="trends">
                <span class="tab-icon">📈</span>
                <span>Trends</span>
            </button>
            <button class="ai-tab-btn" data-tab="forecast">
                <span class="tab-icon">🔮</span>
                <span>Forecast</span>
            </button>
            <button class="ai-tab-btn" data-tab="processAndOperations">
                <span class="tab-icon">⚙️</span>
                <span>Operations</span>
            </button>
        </div>
        <!-- Tab Content -->
        <div class="ai-modal-body" id="aiContent">
            <div class="ai-loading">
                <div class="spinner"></div>
                <p>Click to generate AI insights...</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const monthlyRequests = @json($monthlyRequestsData);
    const yearlyRequests = @json($yearlyRequestsData);

    const allMonths = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    const mapDataToAllMonths = data => allMonths.map(month => data[month] ?? 0);

    let monthlyRequestsChart;
    let currentViewMode = 'monthly';

    // Function to render/update main chart
    function renderMainChart(startDate = null, endDate = null, viewMode = 'monthly') {
        let categories, chartData, chartTitle, chartColor;

        if (viewMode === 'yearly') {
            // Yearly view
            let yearlyData = yearlyRequests;

            // Filter by year range if provided
            if (startDate && endDate) {
                const startYear = parseInt(startDate);
                const endYear = parseInt(endDate);

                yearlyData = Object.keys(yearlyRequests)
                    .filter(year => Number(year) >= startYear && Number(year) <= endYear)
                    .reduce((obj, year) => {
                        obj[year] = yearlyRequests[year];
                        return obj;
                    }, {});
            }

            categories = Object.keys(yearlyData);
            chartData = Object.values(yearlyData);
            chartTitle = 'Requests Per Year';
            chartColor = '#1dd3b0';
        } else {
            // Monthly view - data is already filtered by the backend
            // Keys are month names (e.g. 'January', 'February', ...) or 'Jan 2025' for multi-year
            const keys = Object.keys(monthlyRequests);
            const isMultiYear = keys.length > 0 && keys[0].split(' ').length > 1;

            if (isMultiYear) {
                // Multi-year range: use actual keys as categories
                categories = keys;
                chartData = Object.values(monthlyRequests);
            } else {
                // Single year: map to all 12 months
                categories = allMonths;
                chartData = mapDataToAllMonths(monthlyRequests);
            }
            chartTitle = 'Requests Per Month';
            chartColor = '#36A2EB';
        }

        // Calculate total
        const total = chartData.reduce((a, b) => a + b, 0);
        document.getElementById('totalRequests').textContent = total;

        const chartOptions = {
            chart: {
                type: 'bar',
                height: 300,
                toolbar: {
                    show: true
                }
            },
            series: [{
                name: chartTitle,
                data: chartData
            }],
            xaxis: {
                categories: categories,
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            colors: [chartColor],
            plotOptions: {
                bar: {
                    borderRadius: 4
                }
            },
            title: {
                text: chartTitle,
                align: 'center'
            },
            tooltip: {
                y: {
                    formatter: val => val + ' requests'
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return Math.round(val);
                }
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return Math.round(val);
                    }
                },
                tickAmount: undefined,
                forceNiceScale: true,
                decimalsInFloat: 0
            },
            responsive: [{
                breakpoint: 576,
                options: {
                    chart: {
                        height: 250,
                        toolbar: { show: false }
                    },
                    xaxis: {
                        labels: {
                            rotate: -45,
                            style: { fontSize: '10px' }
                        }
                    },
                    dataLabels: { enabled: false }
                }
            }]
        };

        if (monthlyRequestsChart) {
            monthlyRequestsChart.destroy();
        }
        monthlyRequestsChart = new ApexCharts(document.querySelector("#monthlyRequestsChart"), chartOptions);
        monthlyRequestsChart.render();
    }

    // Notification function
    function showNotification(message, type = 'info') {
        const existingNotif = document.querySelector('.custom-notification');
        if (existingNotif) {
            existingNotif.remove();
        }

        const notification = document.createElement('div');
        notification.className = 'custom-notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: ${type === 'warning' ? '#ffc107' : type === 'success' ? '#28a745' : '#17a2b8'};
            color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            font-size: 14px;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        `;
        notification.textContent = message;

        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(400px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(400px); opacity: 0; }
            }
        `;
        if (!document.querySelector('style[data-notification-style]')) {
            style.setAttribute('data-notification-style', 'true');
            document.head.appendChild(style);
        }

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Initial render
    renderMainChart();

    // Toggle between monthly/yearly
    document.getElementById('toggleYearly').addEventListener('change', function() {
        currentViewMode = this.checked ? 'yearly' : 'monthly';

        // Toggle input visibility
        const dateInputs = document.querySelectorAll('#mainStartDate, #mainEndDate');
        const yearInputs = document.querySelectorAll('#mainStartYear, #mainEndYear');

        if (this.checked) {
            // Show year inputs, hide date inputs
            dateInputs.forEach(input => input.style.display = 'none');
            yearInputs.forEach(input => input.style.display = 'block');
            document.getElementById('mainChartTitle').textContent = 'Yearly Document Requests';
        } else {
            // Show date inputs, hide year inputs
            dateInputs.forEach(input => input.style.display = 'block');
            yearInputs.forEach(input => input.style.display = 'none');
            document.getElementById('mainChartTitle').textContent = 'Monthly Document Requests';
        }

        renderMainChart(null, null, currentViewMode);
    });

    // Filter button - reload page with query parameters so backend re-queries
    document.getElementById('mainFilterBtn').addEventListener('click', function() {
        let startValue, endValue;

        if (currentViewMode === 'yearly') {
            startValue = document.getElementById('mainStartYear').value;
            endValue = document.getElementById('mainEndYear').value;
            if (startValue && endValue) {
                startValue = startValue + '-01-01';
                endValue = endValue + '-12-31';
            }
        } else {
            startValue = document.getElementById('mainStartDate').value;
            endValue = document.getElementById('mainEndDate').value;
        }

        if (startValue && endValue) {
            const url = new URL(window.location.href);
            url.searchParams.set('start_date', startValue);
            url.searchParams.set('end_date', endValue);
            window.location.href = url.toString();
        } else {
            showNotification('Please select both start and end ' + (currentViewMode === 'yearly' ? 'years' : 'dates'), 'warning');
        }
    });

    // Reset button - reload page without filters (defaults to current year)
    document.getElementById('mainResetBtn').addEventListener('click', function() {
        const url = new URL(window.location.href);
        url.searchParams.delete('start_date');
        url.searchParams.delete('end_date');
        window.location.href = url.toString();
    });

    // Date/year input changes are handled by the filter button above
    // No auto-reload on change to avoid accidental reloads

    // ✅ Document Type (Donut)
    const docTypeData = @json($docTypeData);
    let docTypeChart; // Store chart instance

    // Define color palette
    const docTypeColorPalette = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];

    // Create dynamic color mapping based on actual data keys from database
    const docTypeColors = {};
    Object.keys(docTypeData).forEach((type, index) => {
        docTypeColors[type] = docTypeColorPalette[index % docTypeColorPalette.length];
    });

    // Populate filter dropdown
    const docTypeFilter = document.getElementById('docTypeFilter');
    Object.keys(docTypeData).forEach(type => {
        const option = document.createElement('option');
        option.value = type;
        option.textContent = type;
        docTypeFilter.appendChild(option);
    });

    // Function to render/update chart
    function renderDocTypeChart(filterValue = 'all') {
        let filteredData = {};
        let filteredColors = [];

        if (filterValue === 'all') {
            filteredData = docTypeData;
            filteredColors = Object.keys(docTypeData).map(type => docTypeColors[type]);
        } else {
            filteredData[filterValue] = docTypeData[filterValue];
            filteredColors = [docTypeColors[filterValue]];
        }

        const chartOptions = {
            chart: {
                type: 'donut',
                height: 300,
                toolbar: {
                    show: true
                }
            },
            series: Object.values(filteredData),
            labels: Object.keys(filteredData),
            colors: filteredColors,
            legend: {
                position: 'bottom'
            },
            title: {
                text: 'Document Types',
                align: 'center'
            },
            dataLabels: {
                formatter: function(val, opts) {
                    const actualValue = opts.w.config.series[opts.seriesIndex];
                    return actualValue;
                },
                style: {
                    fontSize: '14px',
                    colors: ['#fff']
                }
            },
            responsive: [{
                breakpoint: 576,
                options: {
                    chart: { height: 250 },
                    legend: { position: 'bottom', fontSize: '11px' }
                }
            }]
        };

        // Destroy existing chart and create new one
        if (docTypeChart) {
            docTypeChart.destroy();
        }
        docTypeChart = new ApexCharts(document.querySelector("#docTypeChart"), chartOptions);
        docTypeChart.render();
    }

    // Initial render
    renderDocTypeChart();

    // Add event listener for filter
    docTypeFilter.addEventListener('change', function() {
        renderDocTypeChart(this.value);
    });

    const modeData = @json($modeData);
    let modeChart; // Store chart instance

    // Define color palette
    const colorPalette = ['#1dd3b0', '#1f2937', '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'];

    // Create dynamic color mapping based on actual data keys from database
    const modeColors = {};
    Object.keys(modeData).forEach((mode, index) => {
        modeColors[mode] = colorPalette[index % colorPalette.length];
    });

    // Populate filter dropdown
    const requestModeFilter = document.getElementById('requestModeFilter');
    Object.keys(modeData).forEach(mode => {
        const option = document.createElement('option');
        option.value = mode;
        option.textContent = mode;
        requestModeFilter.appendChild(option);
    });

    // Function to render/update chart
    function renderModeChart(filterValue = 'all') {
        let filteredData = {};
        let filteredColors = [];

        if (filterValue === 'all') {
            filteredData = modeData;
            filteredColors = Object.keys(modeData).map(mode => modeColors[mode]);
        } else {
            filteredData[filterValue] = modeData[filterValue];
            filteredColors = [modeColors[filterValue]];
        }

        const chartOptions = {
            chart: {
                type: 'pie',
                height: 300,
                toolbar: {
                    show: true
                }
            },
            series: Object.values(filteredData),
            labels: Object.keys(filteredData),
            colors: filteredColors,
            legend: {
                position: 'bottom'
            },
            title: {
                text: 'Request Mode',
                align: 'center'
            },
            dataLabels: {
                formatter: function(val, opts) {
                    const actualValue = opts.w.config.series[opts.seriesIndex];
                    return actualValue;
                },
                style: {
                    fontSize: '14px',
                    colors: ['#fff']
                }
            },
            responsive: [{
                breakpoint: 576,
                options: {
                    chart: { height: 250 },
                    legend: { position: 'bottom', fontSize: '11px' }
                }
            }]
        };

        // Destroy existing chart and create new one
        if (modeChart) {
            modeChart.destroy();
        }
        modeChart = new ApexCharts(document.querySelector("#modeChart"), chartOptions);
        modeChart.render();
    }

    // Initial render
    renderModeChart();

    // Add event listener for filter
    requestModeFilter.addEventListener('change', function() {
        renderModeChart(this.value);
    });

    // ✅ Grade Level
    const gradeLevelData = @json($gradeLevelData);
    let gradeLevelChart; // Store chart instance

    // Populate filter dropdown
    const gradeLevelFilter = document.getElementById('gradeLevelFilter');
    Object.keys(gradeLevelData).forEach(grade => {
        const option = document.createElement('option');
        option.value = grade;
        option.textContent = `Grade ${grade}`;
        gradeLevelFilter.appendChild(option);
    });

    // Function to render/update chart
    function renderGradeLevelChart(filterValue = 'all') {
        let filteredData = {};

        if (filterValue === 'all') {
            filteredData = gradeLevelData;
        } else {
            filteredData[filterValue] = gradeLevelData[filterValue];
        }

        const gradeLabels = Object.keys(filteredData).map(l => `Grade ${l}`);
        const gradeValues = Object.values(filteredData);
        document.getElementById('gradeLevelTotal').textContent = gradeValues.reduce((a, b) => a + b, 0);

        const chartOptions = {
            chart: {
                type: 'bar',
                height: 300
            },
            series: [{
                name: 'Requests',
                data: gradeValues
            }],
            xaxis: {
                categories: gradeLabels
            },
            colors: ['#1dd3b0'],
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return Math.round(val);
                }
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return Math.round(val);
                    }
                }
            },
            title: {
                text: 'Requests by Grade Level',
                align: 'center'
            },
            responsive: [{
                breakpoint: 576,
                options: {
                    chart: { height: 250, toolbar: { show: false } },
                    xaxis: { labels: { rotate: -45, style: { fontSize: '10px' } } },
                    dataLabels: { enabled: false }
                }
            }]
        };

        // Destroy existing chart and create new one
        if (gradeLevelChart) {
            gradeLevelChart.destroy();
        }
        gradeLevelChart = new ApexCharts(document.querySelector("#gradeLevelChart"), chartOptions);
        gradeLevelChart.render();
    }

    // Initial render
    renderGradeLevelChart();

    // Add event listener for filter
    gradeLevelFilter.addEventListener('change', function() {
        renderGradeLevelChart(this.value);
    });

    // ✅ Revenue (Area)
    const revenueData = @json($revenueData);
    new ApexCharts(document.querySelector("#revenueChart"), {
        chart: {
            type: 'area',
            height: 300,
            toolbar: {
                show: true,
                offsetX: 0,
                offsetY: -10,
                tools: {
                    download: true,
                    selection: true,
                    zoom: true,
                    zoomin: true,
                    zoomout: true,
                    pan: true,
                    reset: true
                }
            }
        },
        series: [{
            name: 'Revenue (₱)',
            data: mapDataToAllMonths(revenueData)
        }],
        xaxis: {
            categories: allMonths
        },
        colors: ['#4BC0C0'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        yaxis: {
            labels: {
                formatter: val => '₱' + val.toLocaleString()
            }
        },
        title: {
            text: 'Monthly Revenue',
            align: 'center',
            margin: 10,
            offsetY: 0,
            style: {
                fontSize: '16px',
                fontWeight: 600
            }
        },
        responsive: [{
            breakpoint: 768,
            options: {
                chart: {
                    toolbar: {
                        show: false // Hide toolbar on mobile to prevent overlap
                    }
                },
                title: {
                    style: {
                        fontSize: '14px'
                    }
                }
            }
        }, {
            breakpoint: 576,
            options: {
                chart: {
                    height: 220
                },
                xaxis: {
                    labels: {
                        rotate: -45,
                        style: { fontSize: '10px' }
                    }
                }
            }
        }]
    }).render();

    // ✅ Unclaimed Documents (Bar)
    const unclaimedData = @json($unclaimedData);
    const unclaimedYearlyData = @json($unclaimedYearlyData);
    let unclaimedChart; // Store chart instance

    // Function to render/update chart
    function renderUnclaimedChart(startDate = null, endDate = null, viewMode = 'monthly') {
        let categories, chartData;

        if (viewMode === 'yearly') {
            // Use yearly data from backend
            let yearlyData = unclaimedYearlyData;

            // Filter by date range if provided
            if (startDate && endDate) {
                const startYear = new Date(startDate).getFullYear();
                const endYear = new Date(endDate).getFullYear();

                yearlyData = Object.keys(unclaimedYearlyData)
                    .filter(year => Number(year) >= startYear && Number(year) <= endYear)
                    .reduce((obj, year) => {
                        obj[year] = unclaimedYearlyData[year];
                        return obj;
                    }, {});
            }

            categories = Object.keys(yearlyData);
            chartData = Object.values(yearlyData);
        } else {
            // Monthly view
            let filteredData = unclaimedData;

            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const startMonth = start.getMonth(); // 0-11
                const endMonth = end.getMonth(); // 0-11

                // Filter months based on the date range
                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ];

                filteredData = {};
                monthNames.forEach((month, index) => {
                    if (index >= startMonth && index <= endMonth && unclaimedData[month]) {
                        filteredData[month] = unclaimedData[month];
                    }
                });
            }

            categories = allMonths;
            chartData = mapDataToAllMonths(filteredData);
        }

        const chartOptions = {
            chart: {
                type: 'bar',
                height: 300
            },
            series: [{
                name: 'Unclaimed Documents',
                data: chartData
            }],
            xaxis: {
                categories: categories,
                labels: {
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            colors: ['#FF6384'],
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return Math.round(val);
                }
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return Math.round(val);
                    }
                },
                tickAmount: undefined,
                forceNiceScale: true,
                decimalsInFloat: 0
            },
            title: {
                text: 'Unclaimed Documents',
                align: 'center'
            },
            responsive: [{
                breakpoint: 576,
                options: {
                    chart: { height: 220, toolbar: { show: false } },
                    xaxis: { labels: { rotate: -45, style: { fontSize: '10px' } } },
                    dataLabels: { enabled: false }
                }
            }]
        };

        // Destroy existing chart and create new one
        if (unclaimedChart) {
            unclaimedChart.destroy();
        }
        unclaimedChart = new ApexCharts(document.querySelector("#unclaimedChart"), chartOptions);
        unclaimedChart.render();
    }
    // Initial render
    renderUnclaimedChart();

    // Add event listeners
    document.getElementById('unclaimedStartDate').addEventListener('change', function() {
        const startDate = this.value;
        const endDate = document.getElementById('unclaimedEndDate').value;
        const viewMode = document.getElementById('toggleUnclaimedYearly').checked ? 'yearly' : 'monthly';
        if (startDate && endDate) {
            renderUnclaimedChart(startDate, endDate, viewMode);
        }
    });

    document.getElementById('unclaimedEndDate').addEventListener('change', function() {
        const startDate = document.getElementById('unclaimedStartDate').value;
        const endDate = this.value;
        const viewMode = document.getElementById('toggleUnclaimedYearly').checked ? 'yearly' : 'monthly';
        if (startDate && endDate) {
            renderUnclaimedChart(startDate, endDate, viewMode);
        }
    });

    document.getElementById('toggleUnclaimedYearly').addEventListener('change', function() {
        const startDate = document.getElementById('unclaimedStartDate').value;
        const endDate = document.getElementById('unclaimedEndDate').value;
        const viewMode = this.checked ? 'yearly' : 'monthly';
        renderUnclaimedChart(startDate, endDate, viewMode);
    });

    document.getElementById('unclaimedFilterBtn').addEventListener('click', function() {
        const startDate = document.getElementById('unclaimedStartDate').value;
        const endDate = document.getElementById('unclaimedEndDate').value;

        if (startDate && endDate) {
            const url = new URL(window.location.href);
            url.searchParams.set('start_date', startDate);
            url.searchParams.set('end_date', endDate);
            window.location.href = url.toString();
        } else {
            showNotification('Please select both start and end dates', 'warning');
        }
    });

    document.getElementById('unclaimedResetBtn').addEventListener('click', function() {
        const url = new URL(window.location.href);
        url.searchParams.delete('start_date');
        url.searchParams.delete('end_date');
        window.location.href = url.toString();
    });

    // ✅ AI Analytics - auto-load on page open
    let aiData = null; // Store AI data globally for tab switching
    
    const aiSections = [
        { key: 'overview', icon: '📋', title: 'Overview' },
        { key: 'busiestMonths', icon: '📅', title: 'Busiest Months' },
        { key: 'trends', icon: '📈', title: 'Trends' },
        { key: 'forecast', icon: '🔮', title: 'Forecast' },
        { key: 'processAndOperations', icon: '⚙️', title: 'Process & Operations' },
    ];

    function renderTabContent(tabKey) {
        if (!aiData || !aiData[tabKey]) {
            return `<div class="ai-content-card">
                <div class="ai-loading">
                    <p>No data available for this section.</p>
                </div>
            </div>`;
        }

        const section = aiSections.find(s => s.key === tabKey);
        return `
            <div class="ai-tab-content active" data-content="${tabKey}">
                <div class="ai-content-card">
                    <div class="ai-content-header">
                        <span class="content-icon">${section.icon}</span>
                        <h4>${section.title}</h4>
                    </div>
                    <div class="ai-content-text">${aiData[tabKey]}</div>
                </div>
            </div>
        `;
    }

    function renderAIContent(data) {
        aiData = data;
        
        // Render the first tab (overview) by default
        document.getElementById('aiContent').innerHTML = renderTabContent('overview');

        // Update date
        const generatedAt = data.generated_at || data.data_period?.start || '';
        if (generatedAt) {
            const date = new Date(generatedAt);
            document.getElementById('aiGeneratedDate').textContent = date.toLocaleDateString('en-US', {
                month: 'short', day: 'numeric', year: 'numeric'
            });
        }
    }

    // Tab switching
    document.getElementById('aiSidebar').addEventListener('click', function(e) {
        const tabBtn = e.target.closest('.ai-tab-btn');
        if (!tabBtn || !aiData) return;

        // Update active tab button
        document.querySelectorAll('.ai-tab-btn').forEach(btn => btn.classList.remove('active'));
        tabBtn.classList.add('active');

        // Render the selected tab content
        const tabKey = tabBtn.dataset.tab;
        document.getElementById('aiContent').innerHTML = renderTabContent(tabKey);
    });

    function loadAIAnalytics() {
        fetch('{{ route("analytics.generateAI") }}')
            .then(response => response.json())
            .then(result => {
                if (result.error) {
                    document.getElementById('aiContent').innerHTML = `
                        <div class="ai-tab-content active">
                            <div class="ai-content-card">
                                <div class="ai-error">
                                    <p>⚠️ ${result.error}</p>
                                </div>
                            </div>
                        </div>
                    `;
                    return;
                }

                if (result.data) {
                    // Already generated today — display cached data
                    renderAIContent(result.data);
                } else if (result.success === 'success') {
                    // Freshly generated — fetch the latest
                    fetch('{{ route("analytics.latestAI") }}')
                        .then(r => r.json())
                        .then(latest => {
                            if (latest.data) {
                                renderAIContent(latest.data);
                                const genDate = latest.generated_at;
                                if (genDate) {
                                    const date = new Date(genDate);
                                    document.getElementById('aiGeneratedDate').textContent = date.toLocaleDateString('en-US', {
                                        month: 'short', day: 'numeric', year: 'numeric'
                                    });
                                }
                            }
                        });
                }
            })
            .catch(err => {
                document.getElementById('aiContent').innerHTML = `
                    <div class="ai-tab-content active">
                        <div class="ai-content-card">
                            <div class="ai-error">
                                <p>⚠️ Failed to load AI insights. Please try again later.</p>
                            </div>
                        </div>
                    </div>
                `;
            });
    }

    // Modal functionality
    const aiFloatingBtn = document.getElementById('aiFloatingBtn');
    const aiModalOverlay = document.getElementById('aiModalOverlay');
    const aiModal = document.getElementById('aiModal');
    const aiModalClose = document.getElementById('aiModalClose');
    let aiLoaded = false; // Track if AI has been loaded

    function openAIModal() {
        aiModalOverlay.classList.add('active');
        aiModal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Reset to first tab
        document.querySelectorAll('.ai-tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelector('.ai-tab-btn[data-tab="overview"]').classList.add('active');
        
        // Only load AI analytics on first open
        if (!aiLoaded) {
            aiLoaded = true;
            document.getElementById('aiContent').innerHTML = `
                <div class="ai-tab-content active">
                    <div class="ai-content-card">
                        <div class="ai-loading">
                            <div class="spinner"></div>
                            <p>Generating AI insights...</p>
                        </div>
                    </div>
                </div>
            `;
            loadAIAnalytics();
        } else if (aiData) {
            // Re-render the overview tab if data exists
            document.getElementById('aiContent').innerHTML = renderTabContent('overview');
        }
    }

    function closeAIModal() {
        aiModalOverlay.classList.remove('active');
        aiModal.classList.remove('active');
        document.body.style.overflow = '';
    }

    aiFloatingBtn.addEventListener('click', openAIModal);
    aiModalClose.addEventListener('click', closeAIModal);
    aiModalOverlay.addEventListener('click', closeAIModal);

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && aiModal.classList.contains('active')) {
            closeAIModal();
        }
    });
</script>
@endsection
