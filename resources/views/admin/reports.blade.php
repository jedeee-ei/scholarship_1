@extends('layouts.admin')

@section('title', 'Reports & Archive')

@section('content')
<div class="container-fluid">
    <div class="dashboard-header">
        <h1>Reports & Archive</h1>
    </div>

    <!-- Quick Advanced Reports -->
    <div class="quick-reports-section" style="margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
        <h3 style="margin-bottom: 15px; color: #052f11;">📊 Executive Reports</h3>
        <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 15px;">
            <a href="{{ route('admin.reports.executive-summary') }}" class="quick-report-btn" style="background: #052f11; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-chart-line"></i>
                Executive Summary (PDF)
            </a>
            <a href="{{ route('admin.reports.department-performance') }}" class="quick-report-btn" style="background: #2ecc71; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-building"></i>
                Department Performance (PDF)
            </a>
            <a href="{{ route('admin.reports.enhanced-excel') }}" class="quick-report-btn" style="background: #3498db; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-file-excel"></i>
                Comprehensive Excel Report
            </a>
        </div>
        <p style="margin: 0; color: #666; font-size: 14px;">
            <strong>✨ Professional reports</strong> ready for presentations, board meetings, and executive decision-making.
        </p>
    </div>
@endsection

@push('scripts')
    <script>
        // Clean reports page - Advanced reporting system only
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Advanced Reports page loaded successfully');
        });
    </script>
@endpush
