<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Dashboard - St. Paul University Philippines</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e5631;
            --primary-dark: #154a1c;
            --primary-light: rgba(30, 86, 49, 0.1);
            --accent-color: #ffc107;
            --text-color: #333;
            --light-gray: #f5f5f5;
            --border-color: #ddd;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: var(--text-color);
            margin: 0;
            padding: 0;
        }

        /* Header Styles */
        .header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }

        .university-logo {
            display: flex;
            align-items: center;
        }

        .logo-img {
            width: 50px;
            height: 50px;
            margin-right: 15px;
        }

        .university-name {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.2;
        }

        .office-name {
            font-size: 14px;
            margin: 0;
            opacity: 0.9;
        }

        .logout-btn {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .logout-btn:hover {
            background-color: rgba(255, 255, 255, 0.25);
            color: white;
        }

        .logout-btn i {
            margin-right: 5px;
        }

        /* Dashboard Header */
        .dashboard-header {
            background-color: #ffffff;
            padding: 15px 0;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 25px;
        }

        .dashboard-title {
            font-size: 22px;
            font-weight: 600;
            margin: 0;
            color: var(--primary-color);
            text-align: center;
        }

        /* Welcome Section */
        .welcome-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding: 0 15px;
        }

        .welcome-text {
            font-size: 20px;
            font-weight: 500;
        }

        .user-actions {
            display: flex;
            gap: 15px;
        }

        .action-link {
            display: inline-flex;
            align-items: center;
            color: var(--primary-color);
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .action-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .action-link i {
            margin-right: 5px;
        }

        /* Card Styles */
        .dashboard-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            margin-bottom: 25px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px 20px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .card-header i {
            margin-right: 10px;
            font-size: 20px;
        }

        .card-body {
            padding: 20px;
        }

        /* Scholarship Cards */
        .scholarship-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .scholarship-card {
            background-color: white;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            padding: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .scholarship-card:hover {
            border-color: var(--primary-color);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .scholarship-card.active {
            border-color: var(--primary-color);
            background-color: var(--primary-light);
        }

        .scholarship-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .scholarship-description {
            font-size: 14px;
            color: #555;
            margin-bottom: 15px;
        }

        .apply-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .apply-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        .apply-btn i {
            margin-right: 5px;
        }

        /* Application Form Styles */
        .application-form-container {
            display: none;
            margin-top: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .application-form-container.active {
            display: block;
        }

        .form-header {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            font-size: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .form-header i {
            margin-right: 10px;
        }

        .close-form-btn {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            padding: 5px;
            border-radius: 3px;
            transition: background-color 0.3s ease;
        }

        .close-form-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .form-body {
            padding: 30px;
        }

        .form-section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-light);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(30, 86, 49, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .submit-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .submit-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* Alert Styles */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 5px;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .submit-btn i {
            margin-right: 8px;
        }

        /* Application Forms Header */
        .application-forms-header {
            flex: 1;
        }

        .application-forms-header h3 {
            margin: 0 0 15px 0;
            font-size: 24px;
            font-weight: 700;
            color: #2c5530;
        }



        /* Inline Radio Groups */
        .radio-group-inline {
            display: flex;
            gap: 20px;
            margin-top: 5px;
        }

        .radio-option-inline {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .radio-option-inline input[type="radio"] {
            margin: 0;
            width: 18px;
            height: 18px;
        }

        .radio-option-inline .radio-label {
            font-size: 14px;
            color: #333;
        }

        /* Full Width Form Group */
        .form-group.full-width {
            flex: 1 1 100%;
        }

        /* Form Title */
        .form-title {
            margin-bottom: 15px;
        }

        .form-title h4 {
            color: #2c5530;
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #2c5530;
            display: inline-block;
        }

        /* Form Description */
        .form-description {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #2c5530;
        }

        .form-description p {
            margin: 0;
            color: #495057;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Student ID Group */
        .form-group.student-id-group {
            flex: 0 0 300px;
            max-width: 300px;
        }

        /* File Upload Styles */
        .file-upload-container {
            position: relative;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .file-upload-container:hover {
            border-color: #2c5530;
            background: #f0f8f0;
        }

        .file-upload-container input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .file-upload-info {
            pointer-events: none;
        }

        .file-upload-info i {
            font-size: 24px;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .file-upload-info p {
            margin: 0 0 5px 0;
            color: #495057;
            font-weight: 500;
        }

        .file-upload-info small {
            color: #6c757d;
            font-size: 12px;
        }

        .file-upload-container.dragover {
            border-color: #2c5530;
            background: #e8f5e8;
        }

        /* Uploaded Files List */
        .uploaded-files-list {
            margin-top: 15px;
        }

        .uploaded-file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            margin-bottom: 8px;
        }

        .uploaded-file-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .uploaded-file-info i {
            color: #2c5530;
        }

        .uploaded-file-name {
            font-size: 14px;
            color: #495057;
        }

        .uploaded-file-size {
            font-size: 12px;
            color: #6c757d;
        }

        .remove-file-btn {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 12px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .remove-file-btn:hover {
            background: #c82333;
        }

        /* Student ID Validation Styles */
        .form-group input.error {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .student-id-error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            padding: 5px 10px;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            display: none;
        }

        .student-id-error::before {
            content: "⚠️ ";
            margin-right: 5px;
        }

        /* Success state for valid IDs */
        .form-group input.valid {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .student-id-success {
            color: #155724;
            font-size: 12px;
            margin-top: 5px;
            padding: 5px 10px;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            display: none;
        }

        .student-id-success::before {
            content: "✅ ";
            margin-right: 5px;
        }

        /* Disability Field Container */
        .disability-field-container {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .disability-select {
            flex: 0 0 200px;
            max-width: 200px;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }

        .disability-info {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            flex: 1;
            margin-top: 2px;
        }

        .disability-info i {
            color: #17a2b8;
            margin-top: 2px;
            flex-shrink: 0;
            font-size: 16px;
        }

        .disability-info span {
            font-size: 13px;
            color: #495057;
            line-height: 1.4;
        }

        .disability-info strong {
            font-weight: 600;
            color: #333;
        }

        /* FAQ Styles */
        .faq-container {
            margin-top: 10px;
        }

        .faq-item {
            border-bottom: 1px solid #eee;
            margin-bottom: 10px;
        }

        .faq-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .faq-question {
            display: flex;
            align-items: center;
            padding: 15px 0;
            cursor: pointer;
            transition: all 0.3s ease;
            user-select: none;
        }

        .faq-question:hover {
            color: var(--primary-color);
        }

        .faq-icon {
            margin-right: 10px;
            transition: transform 0.3s ease;
            color: var(--primary-color);
            font-size: 12px;
        }

        .faq-question.active .faq-icon {
            transform: rotate(90deg);
        }

        .faq-question span {
            font-weight: 500;
            font-size: 14px;
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding: 0 25px;
        }

        .faq-answer.active {
            max-height: 500px;
            padding: 0 25px 15px 25px;
        }

        /* Student ID Validation Styles */
        .student-id-error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            padding: 8px 12px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .student-id-error:before {
            content: "⚠️";
            font-size: 14px;
        }

        .student-id-success {
            color: #155724;
            font-size: 12px;
            margin-top: 5px;
            padding: 8px 12px;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .student-id-success:before {
            content: "✅";
            font-size: 14px;
        }

        input[name="student_id"].error {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
            background-color: #fff5f5;
        }

        input[name="student_id"].valid {
            border-color: #28a745 !important;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
            background-color: #f8fff8;
        }



        .faq-answer p {
            margin: 0 0 10px 0;
            color: #666;
            font-size: 13px;
            line-height: 1.5;
        }

        .faq-answer ul {
            margin: 10px 0;
            padding-left: 20px;
        }

        .faq-answer li {
            margin-bottom: 5px;
            color: #666;
            font-size: 13px;
            line-height: 1.4;
        }

        .faq-answer strong {
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Radio Button Styles */
        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 8px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 12px 16px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            transition: all 0.3s ease;
            background-color: white;
        }

        .radio-option:hover {
            border-color: var(--primary-color);
            background-color: var(--primary-light);
        }

        .radio-option input[type="radio"] {
            margin-right: 10px;
            width: 18px;
            height: 18px;
            accent-color: var(--primary-color);
        }

        .radio-option.selected {
            border-color: var(--primary-color);
            background-color: var(--primary-light);
        }

        .radio-label {
            font-weight: 500;
            color: var(--text-color);
        }

        /* Conditional Fields */
        .bsu-fields,
        .college-fields {
            transition: all 0.3s ease;
        }

        /* Information Lists */
        .info-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .info-list li {
            padding: 10px 0;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
        }

        .info-list li:last-child {
            border-bottom: none;
        }

        .info-list li i {
            margin-right: 10px;
            color: var(--primary-color);
            width: 20px;
            text-align: center;
        }

        /* Contact Information */
        .contact-info {
            margin-top: 10px;
        }

        .contact-info p {
            margin: 5px 0;
            font-size: 14px;
        }

        .contact-label {
            font-weight: 600;
            color: var(--primary-color);
        }

        /* Document Checklist Styles */
        .document-checklist {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-top: 10px;
        }

        .checklist-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            padding: 8px 0;
        }

        .checklist-item:last-child {
            margin-bottom: 0;
        }

        .checklist-item input[type="checkbox"] {
            margin-right: 12px;
            width: 18px;
            height: 18px;
            accent-color: var(--primary-color);
        }

        .checklist-item label {
            font-size: 14px;
            color: #495057;
            cursor: pointer;
            margin-bottom: 0;
        }

        .checklist-item input[type="checkbox"]:checked + label {
            color: var(--primary-color);
            font-weight: 500;
        }

        /* File Upload Styles */
        .file-upload-container {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .file-upload-container:hover {
            border-color: var(--primary-color);
            background-color: #f0f8f0;
        }

        .file-upload-container.dragover {
            border-color: var(--primary-color);
            background-color: #e8f5e8;
        }

        .file-upload-area {
            cursor: pointer;
        }

        .file-upload-area i {
            font-size: 48px;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .file-upload-area p {
            font-size: 16px;
            color: #495057;
            margin-bottom: 8px;
        }

        .file-upload-area small {
            color: #6c757d;
            font-size: 12px;
        }

        .uploaded-files-list {
            margin-top: 20px;
            text-align: left;
        }

        .uploaded-file-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: white;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 8px;
        }

        .uploaded-file-info {
            display: flex;
            align-items: center;
        }

        .uploaded-file-info i {
            color: var(--primary-color);
            margin-right: 12px;
            font-size: 18px;
        }

        .uploaded-file-name {
            font-weight: 500;
            color: #495057;
            font-size: 14px;
        }

        .uploaded-file-size {
            color: #6c757d;
            font-size: 12px;
        }

        .remove-file-btn {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        .remove-file-btn:hover {
            background-color: #f8d7da;
        }

        /* Conditional Fields Animation */
        .ched-bsu-fields, .ched-college-fields,
        .ched-strand-field, .subjects-section {
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .ched-bsu-fields.show, .ched-college-fields.show,
        .subjects-section.show {
            display: block !important;
            animation: slideDown 0.3s ease;
        }

        /* Subjects and Grades Styles */
        .subjects-section {
            margin: 20px 0;
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            background-color: #f8f9ff;
        }

        .subjects-container {
            width: 100%;
        }

        .subjects-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 15px;
            padding: 15px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 8px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .subject-code-header,
        .grades-header,
        .units-header {
            text-align: center;
        }

        .subjects-list {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: white;
        }

        .subject-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 15px;
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            align-items: center;
        }

        .subject-row:last-child {
            border-bottom: none;
        }

        .subject-row:nth-child(even) {
            background-color: #f9f9f9;
        }

        .subject-info {
            text-align: left;
        }

        .subject-code {
            font-weight: bold;
            color: var(--primary-color);
            font-size: 14px;
        }

        .subject-title {
            color: #666;
            font-size: 13px;
            margin-top: 2px;
        }

        .subject-grade {
            text-align: center;
        }

        .subject-grade input {
            width: 80px;
            padding: 8px;
            border: 2px solid #ddd;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        .subject-grade input:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .subject-units {
            text-align: center;
            font-weight: bold;
            color: #333;
        }

        .gwa-calculation {
            margin-top: 20px;
            padding: 20px;
            background-color: white;
            border: 2px solid var(--primary-color);
            border-radius: 10px;
        }

        .gwa-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .gwa-row:last-child {
            border-bottom: none;
        }

        .gwa-final {
            background-color: var(--primary-light);
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 16px;
        }

        .gwa-label {
            color: #333;
        }

        .gwa-value {
            color: var(--primary-color);
            font-weight: bold;
        }

        /* No Subjects Message Styles */
        .no-subjects-message {
            padding: 40px 20px;
            text-align: center;
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            margin: 20px 0;
        }

        .no-subjects-content {
            max-width: 500px;
            margin: 0 auto;
        }

        .no-subjects-content i {
            font-size: 48px;
            color: #6c757d;
            margin-bottom: 20px;
        }

        .no-subjects-content h4 {
            color: #495057;
            margin-bottom: 15px;
            font-size: 24px;
        }

        .no-subjects-content p {
            color: #6c757d;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .no-subjects-content ul {
            text-align: left;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            margin: 20px 0;
            list-style: none;
        }

        .no-subjects-content ul li {
            padding: 8px 0;
            border-bottom: 1px solid #f1f3f4;
            color: #495057;
        }

        .no-subjects-content ul li:last-child {
            border-bottom: none;
        }

        .no-subjects-content ul li strong {
            color: var(--primary-color);
            display: inline-block;
            width: 100px;
        }

        .no-subjects-content .note {
            font-style: italic;
            color: #868e96;
            font-size: 14px;
            margin-top: 20px;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                max-height: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                max-height: 500px;
                transform: translateY(0);
            }
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            .scholarship-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
        }

        @media (max-width: 768px) {
            .welcome-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .user-actions {
                width: 100%;
                justify-content: space-between;
            }

            .scholarship-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .header-container {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            .university-logo {
                justify-content: center;
            }

            .logo-img {
                margin-right: 10px;
            }

            .radio-group {
                flex-direction: column;
                gap: 10px;
            }

            .radio-option {
                padding: 10px 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="university-logo">
                <img src="{{ asset('images/5x5 ft_LOGO.png') }}" alt="St. Paul University Philippines Logo" class="logo-img">
                <div>
                    <h1 class="university-name">St. Paul University Philippines</h1>
                    <p class="office-name">OFFICE OF THE REGISTRAR</p>
                </div>
            </div>
            <a href="{{ route('logout') }}" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Log Out
            </a>
        </div>
    </header>

    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h2 class="dashboard-title">STUDENT DASHBOARD</h2>
    </div>

    <div class="container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-text">
                Welcome, Student User!
            </div>
            <div class="user-actions">
                <a href="/student/applications" class="action-link">
                    <i class="fas fa-clipboard-list"></i> My Applications
                </a>
                <a href="{{ route('scholarship.tracker') }}" class="action-link">
                    <i class="fas fa-search"></i> Track Application
                </a>
                <a href="/student/profile" class="action-link">
                    <i class="fas fa-user-circle"></i> My Profile
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Scholarship Opportunities -->
            <div class="col-lg-8">
                <div class="dashboard-card">
                    <div class="card-header">
                        <i class="fas fa-graduation-cap"></i> Scholarship Opportunities
                    </div>
                    <div class="card-body">
                        <div class="scholarship-grid">
                            <!-- CHED Scholarship -->
                            <div class="scholarship-card" data-scholarship="ched">
                                <h3 class="scholarship-title">CHED Scholarship</h3>
                                <p class="scholarship-description">Government scholarship for qualified students.</p>
                                <button class="apply-btn" data-form="ched-form">
                                    <i class="fas fa-paper-plane"></i> Apply Now
                                </button>
                            </div>

                            <!-- Institutional Scholarship -->
                            <div class="scholarship-card" data-scholarship="presidents">
                                <h3 class="scholarship-title">Institutional Scholarship</h3>
                                <p class="scholarship-description">For students with exceptional academic performance.</p>
                                <button class="apply-btn" data-form="presidents-form">
                                    <i class="fas fa-paper-plane"></i> Apply Now
                                </button>
                            </div>

                            <!-- Employee's Scholarship -->
                            <div class="scholarship-card" data-scholarship="employees">
                                <h3 class="scholarship-title">Employee's Scholarship</h3>
                                <p class="scholarship-description">For children of university employees.</p>
                                <button class="apply-btn" data-form="employees-form">
                                    <i class="fas fa-paper-plane"></i> Apply Now
                                </button>
                            </div>

                            <!-- Private Scholarships -->
                            <div class="scholarship-card" data-scholarship="private">
                                <h3 class="scholarship-title">Private Scholarships</h3>
                                <p class="scholarship-description">Funded by private organizations and donors.</p>
                                <button class="apply-btn" data-form="private-form">
                                    <i class="fas fa-paper-plane"></i> Apply Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Application Forms -->
                <!-- CHED Application Form -->
                <div class="application-form-container" id="ched-form">
                    <div class="form-header">
                        <div class="application-forms-header">
                            <h3>CHED Scholarship Application</h3>
                        </div>
                        <button class="close-form-btn" onclick="closeForm()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="form-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul style="margin: 0; padding-left: 20px;">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('scholarship.submit') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="scholarship_type" value="ched">

                            <!-- Form Title -->
                            <div class="form-title">
                                <h4>Application Instructions</h4>
                            </div>

                            <!-- Form Description -->
                            <div class="form-description">
                                <p>Please fill out all required fields marked with an asterisk (*). Ensure all information is accurate and complete before submitting your application.</p>
                            </div>

                            <div class="form-section-title">Personal Information</div>
                            <div class="form-row">
                                <div class="form-group student-id-group">
                                    <label for="student_id">Student ID *</label>
                                    <input type="text" id="student_id" name="student_id" required>
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Last Name *</label>
                                    <input type="text" id="last_name" name="last_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="first_name">First Name *</label>
                                    <input type="text" id="first_name" name="first_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="middle_name">Middle Name</label>
                                    <input type="text" id="middle_name" name="middle_name">
                                </div>
                            </div>

                            <!-- Education Stage, Sex, Birthdate -->
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Sex</label>
                                    <div class="radio-group-inline">
                                        <label class="radio-option-inline">
                                            <input type="radio" name="sex" value="Male" id="sex_male" required>
                                            <span class="radio-label">Male</span>
                                        </label>
                                        <label class="radio-option-inline">
                                            <input type="radio" name="sex" value="Female" id="sex_female" required>
                                            <span class="radio-label">Female</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="birthdate">Birthdate *</label>
                                    <input type="date" id="birthdate" name="birthdate" required>
                                </div>
                            </div>

                            <!-- Academic Information Section -->
                            <div class="form-section-title">Academic Information</div>
                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label>Education Stage *</label>
                                    <div class="radio-group">
                                        <div class="radio-option">
                                            <input type="radio" id="ched_bsu" name="education_stage" value="BSU" required>
                                            <label for="ched_bsu">BEU</label>
                                        </div>
                                        <div class="radio-option">
                                            <input type="radio" id="ched_college" name="education_stage" value="College" required>
                                            <label for="ched_college">College</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BEU Fields (Hidden by default) -->
                            <div class="ched-bsu-fields" style="display: none;">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="ched_grade_level">Grade Level *</label>
                                        <select id="ched_grade_level" name="grade_level">
                                            <option value="">Select Grade Level</option>
                                            <option value="Grade 7">Grade 7</option>
                                            <option value="Grade 8">Grade 8</option>
                                            <option value="Grade 9">Grade 9</option>
                                            <option value="Grade 10">Grade 10</option>
                                            <option value="Grade 11">Grade 11</option>
                                            <option value="Grade 12">Grade 12</option>
                                        </select>
                                    </div>
                                    <div class="form-group ched-strand-field" style="display: none;">
                                        <label for="ched_strand">Strand *</label>
                                        <select id="ched_strand" name="strand">
                                            <option value="">Select Strand</option>
                                            <option value="STEM">STEM (Science, Technology, Engineering, Mathematics)</option>
                                            <option value="ABM">ABM (Accountancy, Business, Management)</option>
                                            <option value="HUMSS">HUMSS (Humanities and Social Sciences)</option>
                                            <option value="GAS">GAS (General Academic Strand)</option>
                                            <option value="TVL">TVL (Technical-Vocational-Livelihood)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- College Fields (Hidden by default) -->
                            <div class="ched-college-fields" style="display: none;">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="ched_department">Department *</label>
                                        <select id="ched_department" name="department">
                                            <option value="">Select Department</option>
                                            <option value="SITE">SITE</option>
                                            <option value="SASTE">SASTE</option>
                                            <option value="SBAHM">SBAHM</option>
                                            <option value="SNAHS">SNAHS</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="ched_course">Course *</label>
                                        <select id="ched_course" name="course">
                                            <option value="">Select Course</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="ched_year_level">Year Level *</label>
                                        <select id="ched_year_level" name="year_level">
                                            <option value="">Select Year Level</option>
                                            <option value="1st Year">1st Year</option>
                                            <option value="2nd Year">2nd Year</option>
                                            <option value="3rd Year">3rd Year</option>
                                            <option value="4th Year">4th Year</option>
                                            <option value="5th Year">5th Year</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Father's Name -->
                            <div class="form-section-title">Father's Name</div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="father_last_name">Last name</label>
                                    <input type="text" id="father_last_name" name="father_last_name">
                                </div>
                                <div class="form-group">
                                    <label for="father_first_name">First name</label>
                                    <input type="text" id="father_first_name" name="father_first_name">
                                </div>
                                <div class="form-group">
                                    <label for="father_middle_name">Middle name</label>
                                    <input type="text" id="father_middle_name" name="father_middle_name">
                                </div>
                            </div>

                            <!-- Mother's Maiden Name -->
                            <div class="form-section-title">Mother's Maiden Name</div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="mother_last_name">Last name</label>
                                    <input type="text" id="mother_last_name" name="mother_last_name">
                                </div>
                                <div class="form-group">
                                    <label for="mother_first_name">First name</label>
                                    <input type="text" id="mother_first_name" name="mother_first_name">
                                </div>
                                <div class="form-group">
                                    <label for="mother_middle_name">Middle name</label>
                                    <input type="text" id="mother_middle_name" name="mother_middle_name">
                                </div>
                            </div>

                            <!-- Permanent Address -->
                            <div class="form-section-title">Permanent Address</div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="street">Street</label>
                                    <input type="text" id="street" name="street">
                                </div>
                                <div class="form-group">
                                    <label for="barangay">Barangay</label>
                                    <input type="text" id="barangay" name="barangay">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" id="city" name="city">
                                </div>
                                <div class="form-group">
                                    <label for="province">Province</label>
                                    <input type="text" id="province" name="province">
                                </div>
                                <div class="form-group">
                                    <label for="zipcode">Zipcode</label>
                                    <input type="text" id="zipcode" name="zipcode">
                                </div>
                            </div>

                            <!-- Disability -->
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="disability">Disability</label>
                                    <div class="disability-field-container">
                                        <select id="disability" name="disability" class="disability-select">
                                            <option value="">Disability</option>
                                            <option value="None">None</option>
                                            <option value="Communication Disability">Communication Disability</option>
                                            <option value="Disability due to Chronic Illness">Disability due to Chronic Illness</option>
                                            <option value="Learning Disability">Learning Disability</option>
                                            <option value="Intellectual Disability">Intellectual Disability</option>
                                            <option value="Orthopedic Disability">Orthopedic Disability</option>
                                            <option value="Mental/Psychological Disability">Mental/Psychological Disability</option>
                                            <option value="Visual Disability">Visual Disability</option>
                                        </select>
                                        <div class="disability-info">
                                            <i class="fas fa-info-circle"></i>
                                            <span>Spell out. Possible values <strong>(Communication Disability, Disability due to Chronic Illness, Learning Disability, Intellectual Disability, Orthopedic Disability, Mental/Psychological Disability, Visual Disability)</strong> [Leave blank if not applicable]</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="contact_number">Contact Number</label>
                                    <input type="tel" id="contact_number" name="contact_number">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email Address *</label>
                                    <input type="email" id="email" name="email" required>
                                </div>
                                <div class="form-group">
                                    <label for="indigenous_people">Indigenous People</label>
                                    <input type="text" id="indigenous_people" name="indigenous" placeholder="Indigenous People">
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-row">
                                <div class="form-group">
                                    <button type="submit" class="submit-btn">
                                        <i class="fas fa-paper-plane"></i> Submit Application
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Institutional Scholarship Application Form -->
                <div class="application-form-container" id="presidents-form">
                    <div class="form-header">
                        <div class="application-forms-header">
                            <h3>Institutional Scholarship Application</h3>
                        </div>
                        <button class="close-form-btn" onclick="closeForm()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="form-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul style="margin: 0; padding-left: 20px;">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <form action="{{ route('scholarship.submit') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="scholarship_type" value="institutional">

                            <!-- Form Title -->
                            <div class="form-title">
                                <h4>Application Instructions</h4>
                            </div>

                            <!-- Form Description -->
                            <div class="form-description">
                                <p>Please fill out all required fields marked with an asterisk (*). Ensure all information is accurate and complete before submitting your application.</p>
                            </div>

                            <div class="form-section-title">Personal Information</div>
                            <div class="form-row">
                                <div class="form-group student-id-group">
                                    <label for="presidents_student_id">Student ID *</label>
                                    <input type="text" id="presidents_student_id" name="student_id" required>
                                </div>
                                <div class="form-group">
                                    <label for="presidents_last_name">Last Name *</label>
                                    <input type="text" id="presidents_last_name" name="last_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="presidents_first_name">First Name *</label>
                                    <input type="text" id="presidents_first_name" name="first_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="presidents_middle_name">Middle Name</label>
                                    <input type="text" id="presidents_middle_name" name="middle_name">
                                </div>
                            </div>

                            <!-- Academic Information Section -->
                            <div class="form-section-title">Academic Information</div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="presidents_department">College/Department *</label>
                                    <select id="presidents_department" name="department" required>
                                        <option value="">Select College/Department</option>
                                        <option value="SITE">School of Information Technology and Engineering (SITE)</option>
                                        <option value="SASTE">School of Arts, Sciences and Teacher Education (SASTE)</option>
                                        <option value="SBAHM">School of Business Administration and Hospitality Management (SBAHM)</option>
                                        <option value="SNAHS">School of Nursing and Allied Health Sciences (SNAHS)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="presidents_course">Course *</label>
                                    <select id="presidents_course" name="course" required>
                                        <option value="">Select Course</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="presidents_year_level">Year Level *</label>
                                    <select id="presidents_year_level" name="year_level" required>
                                        <option value="">Select Year Level</option>
                                        <option value="1st Year">1st Year</option>
                                        <option value="2nd Year">2nd Year</option>
                                        <option value="3rd Year">3rd Year</option>
                                        <option value="4th Year">4th Year</option>
                                        <option value="5th Year">5th Year</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="presidents_semester">Semester *</label>
                                    <select id="presidents_semester" name="semester" required>
                                        <option value="">Select Semester</option>
                                        <option value="1st Semester">1st Semester</option>
                                        <option value="2nd Semester">2nd Semester</option>
                                        <option value="Summer">Summer</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Subjects and Grades Section -->
                            <div class="subjects-section" id="presidents-subjects-section" style="display: none;">
                                <div class="form-section-title">Academic Performance - Subjects and Grades</div>
                                <div class="subjects-container">
                                    <div class="subjects-header">
                                        <div class="subject-code-header">Subject Code & Course Title</div>
                                        <div class="grades-header">Grades</div>
                                        <div class="units-header">Units</div>
                                    </div>
                                    <div class="subjects-list" id="presidents-subjects-list">
                                        <!-- Subjects will be dynamically populated here -->
                                    </div>
                                    <div class="gwa-calculation">
                                        <div class="gwa-row">
                                            <div class="gwa-label">Total Units:</div>
                                            <div class="gwa-value" id="presidents-total-units">0</div>
                                        </div>
                                        <div class="gwa-row">
                                            <div class="gwa-label">Total Grade Points:</div>
                                            <div class="gwa-value" id="presidents-total-grade-points">0.00</div>
                                        </div>
                                        <div class="gwa-row gwa-final">
                                            <div class="gwa-label"><strong>GWA (General Weighted Average):</strong></div>
                                            <div class="gwa-value" id="presidents-calculated-gwa"><strong>0.00</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Year -->
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="inst_academic_year">Academic Year *</label>
                                    <input type="text" id="inst_academic_year" name="academic_year" required placeholder="e.g., 2023-2024">
                                </div>
                            </div>

                            <!-- Hidden GWA field for form submission -->
                            <input type="hidden" id="inst_calculated_gwa" name="gwa" value="0.00">

                            <!-- Contact Information -->
                            <div class="form-section-title">Contact Information</div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="inst_contact_number">Contact Number *</label>
                                    <input type="tel" id="inst_contact_number" name="contact_number" required>
                                </div>
                                <div class="form-group">
                                    <label for="inst_email">Email Address *</label>
                                    <input type="email" id="inst_email" name="email" required>
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div class="form-section-title">Address Information</div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="inst_address">Complete Address *</label>
                                    <textarea id="inst_address" name="address" rows="3" required placeholder="House No., Street, Barangay, City/Municipality, Province"></textarea>
                                </div>
                            </div>

                            <!-- Document Publication -->
                            <div class="form-section-title">Document Publication</div>
                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="inst_document_upload">Upload Required Documents *</label>
                                    <div class="file-upload-container">
                                        <input type="file" id="inst_document_upload" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                                        <div class="file-upload-area">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p>Click to upload or drag and drop files here</p>
                                            <small>Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max 5MB per file)</small>
                                        </div>
                                        <div class="uploaded-files-list"></div>
                                    </div>
                                </div>
                            </div>



                            <!-- Submit Button -->
                            <div class="form-row">
                                <div class="form-group">
                                    <button type="submit" class="submit-btn">
                                        <i class="fas fa-paper-plane"></i> Submit Application
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Employee's Scholarship Application Form -->
                <div class="application-form-container" id="employees-form">
                    <div class="form-header">
                        <div class="application-forms-header">
                            <h3>Employee's Scholar Application</h3>
                        </div>
                        <button class="close-form-btn" onclick="closeForm()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="form-body">
                        <form action="{{ route('scholarship.submit') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="scholarship_type" value="employees">

                            <!-- Form Title -->
                            <div class="form-title">
                                <h4>Application Instructions</h4>
                            </div>

                            <!-- Form Description -->
                            <div class="form-description">
                                <p>Please fill out all required fields marked with an asterisk (*). Ensure all information is accurate and complete before submitting your application.</p>
                            </div>

                            <div class="form-section-title">Personal Information</div>
                            <div class="form-row">
                                <div class="form-group student-id-group">
                                    <label for="employees_student_id">Student ID *</label>
                                    <input type="text" id="employees_student_id" name="student_id" required>
                                </div>
                                <div class="form-group">
                                    <label for="employees_last_name">Last Name *</label>
                                    <input type="text" id="employees_last_name" name="last_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="employees_first_name">First Name *</label>
                                    <input type="text" id="employees_first_name" name="first_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="employees_middle_name">Middle Name</label>
                                    <input type="text" id="employees_middle_name" name="middle_name">
                                </div>
                            </div>

                            <div class="form-section-title">Employee Information</div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="employee_name">Employee Name *</label>
                                    <input type="text" id="employee_name" name="employee_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="employee_relationship">Relationship to Employee *</label>
                                    <select id="employee_relationship" name="employee_relationship" required>
                                        <option value="">Select Relationship</option>
                                        <option value="Son">Son</option>
                                        <option value="Daughter">Daughter</option>
                                        <option value="Spouse">Spouse</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="employee_department">Employee Department *</label>
                                    <input type="text" id="employee_department" name="employee_department" required>
                                </div>
                                <div class="form-group">
                                    <label for="employee_position">Employee Position *</label>
                                    <input type="text" id="employee_position" name="employee_position" required>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="form-section-title">Contact Information</div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="employees_contact_number">Contact Number</label>
                                    <input type="tel" id="employees_contact_number" name="contact_number">
                                </div>
                                <div class="form-group">
                                    <label for="employees_email">Email Address *</label>
                                    <input type="email" id="employees_email" name="email" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <button type="submit" class="submit-btn">
                                        <i class="fas fa-paper-plane"></i> Submit Application
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Private Scholarship Application Form -->
                <div class="application-form-container" id="private-form">
                    <div class="form-header">
                        <div class="application-forms-header">
                            <h3>Private Scholarship Application</h3>
                        </div>
                        <button class="close-form-btn" onclick="closeForm()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="form-body">
                        <form action="{{ route('scholarship.submit') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="scholarship_type" value="private">

                            <!-- Form Title -->
                            <div class="form-title">
                                <h4>Application Instructions</h4>
                            </div>

                            <!-- Form Description -->
                            <div class="form-description">
                                <p>Please fill out all required fields marked with an asterisk (*). Ensure all information is accurate and complete before submitting your application.</p>
                            </div>

                            <div class="form-section-title">Personal Information</div>
                            <div class="form-row">
                                <div class="form-group student-id-group">
                                    <label for="private_student_id">Student ID *</label>
                                    <input type="text" id="private_student_id" name="student_id" required>
                                </div>
                                <div class="form-group">
                                    <label for="private_last_name">Last Name *</label>
                                    <input type="text" id="private_last_name" name="last_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="private_first_name">First Name *</label>
                                    <input type="text" id="private_first_name" name="first_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="private_middle_name">Middle Name</label>
                                    <input type="text" id="private_middle_name" name="middle_name">
                                </div>
                            </div>

                            <div class="form-section-title">Scholarship Information</div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="scholarship_name">Scholarship Name *</label>
                                    <input type="text" id="scholarship_name" name="scholarship_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="other_scholarship">Other Scholarship Details</label>
                                    <textarea id="other_scholarship" name="other_scholarship" rows="3" placeholder="Please provide additional details about the scholarship"></textarea>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="form-section-title">Contact Information</div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="private_contact_number">Contact Number</label>
                                    <input type="tel" id="private_contact_number" name="contact_number">
                                </div>
                                <div class="form-group">
                                    <label for="private_email">Email Address *</label>
                                    <input type="email" id="private_email" name="email" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <button type="submit" class="submit-btn">
                                        <i class="fas fa-paper-plane"></i> Submit Application
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Important Information -->
            <div class="col-lg-4">
                <!-- Upcoming Deadlines -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <i class="fas fa-calendar-alt"></i> Upcoming Deadlines
                    </div>
                    <div class="card-body">
                        <ul class="info-list">
                            <li>
                                <i class="fas fa-file-alt"></i> CHED Scholarship: July 30, 2023
                            </li>
                            <li>
                                <i class="fas fa-file-alt"></i> Institutional Scholarship: August 15, 2023
                            </li>
                            <li>
                                <i class="fas fa-file-alt"></i> Private Scholarships: Varies by program
                            </li>
                        </ul>
                    </div>
                </div>


                <!-- Contact Information -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <i class="fas fa-address-card"></i> Contact Information
                    </div>
                    <div class="card-body">
                        <div class="contact-info">
                            <p><span class="contact-label">Office of the Registrar or Admissions Office</span></p>
                            <p>St. Paul University Philippines</p>
                            <p>Mabini Street, Tuguegarao City, 3500</p>
                            <p>Cagayan, Philippines</p>
                            <p><i class="fas fa-phone"></i> Telephone Number: (078) 396-1987 to 1997 loc. 502 or 513</p>
                            <p><i class="fas fa-envelope"></i> E-mail: spupregistrar@spup.edu.ph/admissions@spup.edu.ph</p>
                        </div>
                    </div>
                </div>


                <!-- Frequently Asked Questions -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <i class="fas fa-question-circle"></i> Frequently Asked Questions
                    </div>
                    <div class="card-body">
                        <div class="faq-container">
                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-chevron-right faq-icon"></i>
                                    <span>What documents do I need to submit for my scholarship application?</span>
                                </div>
                                <div class="faq-answer">
                                    <p>Required documents typically include:</p>
                                    <ul>
                                        <li>Official transcript of records</li>
                                        <li>Certificate of enrollment</li>
                                        <li>Income tax return or certificate of indigency</li>
                                        <li>Birth certificate</li>
                                        <li>Recent passport-sized photos</li>
                                        <li>Letter of recommendation (if applicable)</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-chevron-right faq-icon"></i>
                                    <span>When is the deadline for scholarship applications?</span>
                                </div>
                                <div class="faq-answer">
                                    <p>Deadlines vary by scholarship type:</p>
                                    <ul>
                                        <li><strong>CHED Scholarship:</strong> July 30, 2024</li>
                                        <li><strong>Institutional Scholarship:</strong> August 15, 2024</li>
                                        <li><strong>Private Scholarships:</strong> Varies by program</li>
                                    </ul>
                                    <p>Please check the specific scholarship requirements for exact deadlines.</p>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-chevron-right faq-icon"></i>
                                    <span>Can I apply for multiple scholarships?</span>
                                </div>
                                <div class="faq-answer">
                                    <p>Students can apply for multiple scholarship programs, but please note:</p>
                                    <ul>
                                        <li>Each student ID can only be used once per scholarship type</li>
                                        <li>Some scholarships may have exclusivity clauses</li>
                                        <li>Priority will be given based on eligibility and need</li>
                                        <li>You must inform the office if you receive multiple awards</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-chevron-right faq-icon"></i>
                                    <span>How will I know if my application was approved?</span>
                                </div>
                                <div class="faq-answer">
                                    <p>You will be notified through:</p>
                                    <ul>
                                        <li>Email notification to your registered email address</li>
                                        <li>SMS notification (if phone number provided)</li>
                                        <li>Posted announcements on the university bulletin board</li>
                                        <li>Your student portal dashboard</li>
                                    </ul>
                                    <p>Processing typically takes 2-4 weeks after the deadline.</p>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">
                                    <i class="fas fa-chevron-right faq-icon"></i>
                                    <span>What are the GPA requirements for scholarships?</span>
                                </div>
                                <div class="faq-answer">
                                    <p>GPA requirements vary by scholarship:</p>
                                    <ul>
                                        <li><strong>CHED Scholarship:</strong> Minimum 2.5 GPA</li>
                                        <li><strong>Institutional Scholarship:</strong> Minimum 3.5 GPA</li>
                                        <li><strong>Dean's Lister:</strong> Minimum 3.0 GPA</li>
                                        <li><strong>Employee's Scholar:</strong> Minimum 2.75 GPA</li>
                                    </ul>
                                    <p>Maintain the required GPA throughout the scholarship period.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation to cards
            const cards = document.querySelectorAll('.dashboard-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 + (index * 100));
            });

            // Add click event to apply buttons
            const applyButtons = document.querySelectorAll('.apply-btn');
            applyButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const formId = this.getAttribute('data-form');
                    const scholarshipCard = this.closest('.scholarship-card');

                    // Remove active class from all cards
                    document.querySelectorAll('.scholarship-card').forEach(card => {
                        card.classList.remove('active');
                    });

                    // Hide all forms
                    document.querySelectorAll('.application-form-container').forEach(form => {
                        form.classList.remove('active');
                    });

                    // Show selected form
                    const targetForm = document.getElementById(formId);
                    if (targetForm) {
                        scholarshipCard.classList.add('active');
                        targetForm.classList.add('active');

                        // Smooth scroll to form
                        setTimeout(() => {
                            targetForm.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }, 100);
                    }
                });
            });

            // Initialize CHED form functionality
            initializeCHEDForm();

            // Initialize President's form functionality
            initializePresidentsForm();

            // Initialize Institutional form functionality
            initializeInstitutionalForm();

            // Initialize file upload functionality
            initializeFileUploads();

            // Initialize duplicate ID prevention
            initializeDuplicateIDPrevention();

            // Initialize tab functionality
            initializeTabFunctionality();

            // Initialize FAQ functionality
            initializeFAQ();
        });

        // Tab Functionality
        function initializeTabFunctionality() {
            const tabButtons = document.querySelectorAll('.tab-btn:not(.add-more)');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');

                    // Remove active class from all tabs
                    tabButtons.forEach(btn => btn.classList.remove('active'));

                    // Add active class to clicked tab
                    this.classList.add('active');

                    // Hide all forms
                    document.querySelectorAll('.application-form-container').forEach(form => {
                        form.classList.remove('active');
                    });

                    // Show target form
                    const targetForm = document.getElementById(targetTab + '-form');
                    if (targetForm) {
                        targetForm.classList.add('active');
                    }
                });
            });
        }

        // CHED Form Dynamic Functionality
        function initializeCHEDForm() {
            // Academic Information card-style radio button handling
            const academicEducationRadios = document.querySelectorAll('#ched-form input[name="education_stage"]');
            const chedBsuFields = document.querySelectorAll('.ched-bsu-fields');
            const chedCollegeFields = document.querySelectorAll('.ched-college-fields');

            academicEducationRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Show/hide appropriate fields based on selection
                    if (this.value === 'BSU') {
                        // Show BEU fields, hide college fields
                        chedBsuFields.forEach(field => {
                            field.style.display = 'block';
                            // Make BEU fields required
                            const selects = field.querySelectorAll('select');
                            selects.forEach(select => select.required = true);
                        });
                        chedCollegeFields.forEach(field => {
                            field.style.display = 'none';
                            // Remove required from college fields
                            const selects = field.querySelectorAll('select');
                            selects.forEach(select => {
                                select.required = false;
                                select.value = '';
                            });
                        });
                    } else if (this.value === 'College') {
                        // Show college fields, hide BEU fields
                        chedCollegeFields.forEach(field => {
                            field.style.display = 'block';
                            // Make college fields required
                            const selects = field.querySelectorAll('select');
                            selects.forEach(select => select.required = true);
                        });
                        chedBsuFields.forEach(field => {
                            field.style.display = 'none';
                            // Remove required from BEU fields
                            const selects = field.querySelectorAll('select');
                            selects.forEach(select => {
                                select.required = false;
                                select.value = '';
                            });
                        });
                    }
                });
            });

            // Department to Course mapping
            const departmentCourses = {
                'SITE': [
                    'Bachelor of Science in Information Technology',
                    'Bachelor of Library and Information Science',
                    'Bachelor of Science in Civil Engineering',
                    'Bachelor of Science in Environmental and Sanitary Engineering',
                    'Bachelor of Science in Computer Engineering'
                ],
                'SASTE': [
                    'Bachelor of Arts in English Language Studies',
                    'Bachelor of Science in Psychology',
                    'Bachelor of Science in Biology',
                    'Bachelor of Science in Social Work',
                    'Bachelor of Science in Public Administration',
                    'Bachelor of Science in Biology Major in Microbiology',
                    'Bachelor of Secondary Education',
                    'Bachelor of Elementary Education',
                    'Bachelor of Physical Education'
                ],
                'SBAHM': [
                    'Bachelor of Science in Accountancy',
                    'Bachelor of Science in Entrepreneurship',
                    'Bachelor of Science in Business Administration major in: Marketing Management, Financial Management and Operations Management',
                    'Bachelor of Science in Management Accounting',
                    'Bachelor of Science in Hospitality Management',
                    'Bachelor of Science in Tourism Management',
                    'Bachelor of Science in Product Design and Marketing Innovation'
                ],
                'SNAHS': [
                    'Bachelor of Science in Nursing',
                    'Bachelor of Science in Pharmacy',
                    'Bachelor of Science in Medical Technology',
                    'Bachelor of Science in Physical Therapy',
                    'Bachelor of Science in Radiologic Technology',
                    'Bachelor of Science in Midwifery'
                ]
            };

            // Grade level change handler for CHED form
            const chedGradeLevelSelect = document.getElementById('ched_grade_level');
            const chedStrandField = document.querySelector('.ched-strand-field');
            const chedStrandSelect = document.getElementById('ched_strand');

            if (chedGradeLevelSelect && chedStrandField && chedStrandSelect) {
                chedGradeLevelSelect.addEventListener('change', function() {
                    const selectedGrade = this.value;

                    if (selectedGrade === 'Grade 11' || selectedGrade === 'Grade 12') {
                        // Show strand field for Grade 11 and 12
                        chedStrandField.style.display = 'block';
                        chedStrandSelect.required = true;
                    } else {
                        // Hide strand field for other grades
                        chedStrandField.style.display = 'none';
                        chedStrandSelect.required = false;
                        chedStrandSelect.value = ''; // Clear selection
                    }
                });
            }

            // Department change handler for CHED form
            const chedDepartmentSelect = document.getElementById('ched_department');
            const chedCourseSelect = document.getElementById('ched_course');

            if (chedDepartmentSelect && chedCourseSelect) {
                chedDepartmentSelect.addEventListener('change', function() {
                    const selectedDepartment = this.value;

                    // Clear existing options
                    chedCourseSelect.innerHTML = '<option value="">Select Course</option>';

                    if (selectedDepartment && departmentCourses[selectedDepartment]) {
                        // Add courses for selected department
                        departmentCourses[selectedDepartment].forEach(course => {
                            const option = document.createElement('option');
                            option.value = course;
                            option.textContent = course;
                            chedCourseSelect.appendChild(option);
                        });

                        // Enable course select
                        chedCourseSelect.disabled = false;
                    } else {
                        // Disable course select if no department selected
                        chedCourseSelect.disabled = true;
                    }
                });
            }

            // Department change handler for President's scholarship form
            const presidentsDepartmentSelect = document.getElementById('presidents_department');
            const presidentsCourseSelect = document.getElementById('presidents_course');

            if (presidentsDepartmentSelect && presidentsCourseSelect) {
                presidentsDepartmentSelect.addEventListener('change', function() {
                    const selectedDepartment = this.value;

                    // Clear existing options
                    presidentsCourseSelect.innerHTML = '<option value="">Select Course</option>';

                    if (selectedDepartment && departmentCourses[selectedDepartment]) {
                        // Add courses for selected department
                        departmentCourses[selectedDepartment].forEach(course => {
                            const option = document.createElement('option');
                            option.value = course;
                            option.textContent = course;
                            presidentsCourseSelect.appendChild(option);
                        });

                        // Enable course select
                        presidentsCourseSelect.disabled = false;
                    } else {
                        // Disable course select if no department selected
                        presidentsCourseSelect.disabled = true;
                    }
                });
            }
        }

        // President's Form Dynamic Functionality
        async function initializePresidentsForm() {
            // Global variables to store data from API
            let courseDuration = {};
            let departmentCourses = {};

            // Load data from API first
            await loadScholarshipData();

            // Then set up event handlers
            setupEventHandlers();

            // Function to load scholarship data from API
            async function loadScholarshipData() {
                try {
                    console.log('Loading scholarship data from API...');

                    // Load course durations
                    const courseDurationResponse = await fetch('/api/scholarship/course-durations');
                    if (!courseDurationResponse.ok) {
                        throw new Error(`Course durations API failed: ${courseDurationResponse.status}`);
                    }
                    courseDuration = await courseDurationResponse.json();
                    console.log('Course durations loaded:', Object.keys(courseDuration).length, 'courses');

                    // Load department-course mapping
                    const departmentMappingResponse = await fetch('/api/scholarship/department-course-mapping');
                    if (!departmentMappingResponse.ok) {
                        throw new Error(`Department mapping API failed: ${departmentMappingResponse.status}`);
                    }
                    departmentCourses = await departmentMappingResponse.json();
                    console.log('Department courses loaded:', Object.keys(departmentCourses).length, 'departments');

                    console.log('✅ All scholarship data loaded successfully');
                } catch (error) {
                    console.error('❌ Error loading scholarship data:', error);
                    // Fallback to empty objects if API fails
                    courseDuration = {};
                    departmentCourses = {};
                }
            }

            function setupEventHandlers() {
                // Department change handler for Presidents form (College only)
                const presidentsDepartmentSelect = document.getElementById('presidents_department');
                const presidentsCourseSelect = document.getElementById('presidents_course');
                const presidentsSemesterSelect = document.getElementById('presidents_semester');
                const presidentsYearLevelSelect = document.getElementById('presidents_year_level');
                const presidentsSubjectsSection = document.getElementById('presidents-subjects-section');

            if (presidentsDepartmentSelect && presidentsCourseSelect) {
                presidentsDepartmentSelect.addEventListener('change', function() {
                    const selectedDepartment = this.value;
                    console.log('Department selected:', selectedDepartment);
                    console.log('Available departments:', Object.keys(departmentCourses));

                    // Clear existing options
                    presidentsCourseSelect.innerHTML = '<option value="">Select Course</option>';
                    hideSubjectsSection();

                    if (selectedDepartment && departmentCourses[selectedDepartment]) {
                        console.log('Courses for', selectedDepartment, ':', departmentCourses[selectedDepartment]);

                        // Add courses for selected department
                        departmentCourses[selectedDepartment].forEach(course => {
                            const option = document.createElement('option');
                            option.value = course;
                            option.textContent = course;
                            presidentsCourseSelect.appendChild(option);
                        });

                        // Enable course select
                        presidentsCourseSelect.disabled = false;
                        console.log('✅ Courses populated successfully');
                    } else {
                        console.log('❌ No courses found for department:', selectedDepartment);
                        // Disable course select if no department selected
                        presidentsCourseSelect.disabled = true;
                    }
                });
            }

            // Course change handler
            if (presidentsCourseSelect) {
                presidentsCourseSelect.addEventListener('change', function() {
                    const selectedCourse = this.value;
                    hideSubjectsSection();
                    updateYearLevelOptions(selectedCourse);
                    checkAndShowSubjects();
                });
            }

            // Function to update year level options based on course duration
            function updateYearLevelOptions(selectedCourse) {
                if (!selectedCourse || !courseDuration[selectedCourse]) {
                    // Reset to default if no course selected
                    presidentsYearLevelSelect.innerHTML = `
                        <option value="">Select Year Level</option>
                        <option value="1st Year">1st Year</option>
                        <option value="2nd Year">2nd Year</option>
                        <option value="3rd Year">3rd Year</option>
                        <option value="4th Year">4th Year</option>
                        <option value="5th Year">5th Year</option>
                    `;
                    return;
                }

                const duration = courseDuration[selectedCourse];
                let yearOptions = '<option value="">Select Year Level</option>';

                for (let i = 1; i <= duration; i++) {
                    const yearText = i === 1 ? '1st Year' :
                                   i === 2 ? '2nd Year' :
                                   i === 3 ? '3rd Year' :
                                   i === 4 ? '4th Year' :
                                   '5th Year';
                    yearOptions += `<option value="${yearText}">${yearText}</option>`;
                }

                presidentsYearLevelSelect.innerHTML = yearOptions;

                // Clear current selection
                presidentsYearLevelSelect.value = '';
            }

            // Semester change handler
            if (presidentsSemesterSelect) {
                presidentsSemesterSelect.addEventListener('change', function() {
                    checkAndShowSubjects();
                });
            }

            // Year level change handler
            if (presidentsYearLevelSelect) {
                presidentsYearLevelSelect.addEventListener('change', function() {
                    checkAndShowSubjects();
                });
            }

            function checkAndShowSubjects() {
                const selectedCourse = presidentsCourseSelect.value;
                const selectedSemester = presidentsSemesterSelect.value;
                const selectedYearLevel = presidentsYearLevelSelect.value;

                if (selectedCourse && selectedSemester && selectedYearLevel) {
                    // Create semester key based on year level and semester
                    let semesterKey = selectedSemester;

                    if (selectedYearLevel === '2nd Year') {
                        semesterKey = selectedSemester + ' (2nd Year)';
                    } else if (selectedYearLevel === '3rd Year') {
                        semesterKey = selectedSemester + ' (3rd Year)';
                    } else if (selectedYearLevel === '4th Year') {
                        semesterKey = selectedSemester + ' (4th Year)';
                    } else if (selectedYearLevel === '5th Year') {
                        semesterKey = selectedSemester + ' (5th Year)';
                    }
                    // For 1st Year, keep the original semester name

                    // Load subjects from API
                    loadSubjectsFromAPI(selectedCourse, selectedYearLevel, selectedSemester);
                } else {
                    hideSubjectsSection();
                }
            }



            function showNoSubjectsMessage(course, yearLevel, semester) {
                const subjectsList = document.getElementById('presidents-subjects-list');

                // Clear existing content
                subjectsList.innerHTML = '';

                // Create no subjects message
                const noSubjectsMessage = document.createElement('div');
                noSubjectsMessage.className = 'no-subjects-message';
                noSubjectsMessage.innerHTML = `
                    <div class="no-subjects-content">
                        <i class="fas fa-info-circle"></i>
                        <h4>No Subjects Available</h4>
                        <p>No subjects have been configured for:</p>
                        <ul>
                            <li><strong>Course:</strong> ${course}</li>
                            <li><strong>Year Level:</strong> ${yearLevel}</li>
                            <li><strong>Semester:</strong> ${semester}</li>
                        </ul>
                        <p class="note">Please contact the academic office or try a different semester selection.</p>
                    </div>
                `;
                subjectsList.appendChild(noSubjectsMessage);

                // Show subjects section with message
                presidentsSubjectsSection.style.display = 'block';
                presidentsSubjectsSection.classList.add('show');

                // Reset GWA calculation
                document.getElementById('presidents-total-units').textContent = '0';
                document.getElementById('presidents-total-grade-points').textContent = '0.00';
                document.getElementById('presidents-calculated-gwa').innerHTML = '<strong>0.00</strong>';

                // Clear the form input
                const gwaInput = document.getElementById('inst_calculated_gwa');
                if (gwaInput) {
                    gwaInput.value = '0.00';
                }
            }

            // Function to load subjects from API
            async function loadSubjectsFromAPI(courseName, yearLevel, semester) {
                try {
                    // Convert year level to number for API
                    const yearLevelNumber = parseInt(yearLevel.replace(/\D/g, ''));

                    const response = await fetch(`/api/scholarship/subjects/${encodeURIComponent(courseName)}/${yearLevelNumber}/${encodeURIComponent(semester)}`);
                    const data = await response.json();

                    if (response.ok && data.subjects && data.subjects.length > 0) {
                        showSubjectsFromAPI(data.subjects);
                    } else {
                        showNoSubjectsMessage(courseName, yearLevel, semester);
                    }
                } catch (error) {
                    console.error('Error loading subjects:', error);
                    showNoSubjectsMessage(courseName, yearLevel, semester);
                }
            }

            // Function to display subjects from API response
            function showSubjectsFromAPI(subjects) {
                const subjectsList = document.getElementById('presidents-subjects-list');

                // Clear existing subjects
                subjectsList.innerHTML = '';

                // Add subjects
                subjects.forEach((subject, index) => {
                    const subjectRow = document.createElement('div');
                    subjectRow.className = 'subject-row';
                    subjectRow.innerHTML = `
                        <div class="subject-info">
                            <div class="subject-code">${subject.code}</div>
                            <div class="subject-title">${subject.title}</div>
                        </div>
                        <div class="subject-grade">
                            <input type="number"
                                   id="grade_${index}"
                                   name="grades[${subject.code}]"
                                   min="1.00"
                                   max="5.00"
                                   step="0.01"
                                   placeholder="0.00"
                                   data-units="${subject.units}"
                                   onchange="calculateGWA()">
                        </div>
                        <div class="subject-units">${subject.units}</div>
                    `;
                    subjectsList.appendChild(subjectRow);
                });

                // Show subjects section
                presidentsSubjectsSection.style.display = 'block';
                presidentsSubjectsSection.classList.add('show');

                // Reset GWA calculation
                calculateGWA();
            }

            function hideSubjectsSection() {
                if (presidentsSubjectsSection) {
                    presidentsSubjectsSection.style.display = 'none';
                    presidentsSubjectsSection.classList.remove('show');
                }
            }
            } // End of setupEventHandlers function
        }

        // GWA Calculation Function
        function calculateGWA() {
            const gradeInputs = document.querySelectorAll('#presidents-subjects-list input[type="number"]');
            let totalUnits = 0;
            let totalGradePoints = 0;
            let validGrades = 0;

            gradeInputs.forEach(input => {
                const grade = parseFloat(input.value);
                const units = parseInt(input.dataset.units);

                if (!isNaN(grade) && grade >= 1.00 && grade <= 5.00) {
                    totalUnits += units;
                    totalGradePoints += (grade * units);
                    validGrades++;
                }
            });

            // Update display
            document.getElementById('presidents-total-units').textContent = totalUnits;
            document.getElementById('presidents-total-grade-points').textContent = totalGradePoints.toFixed(2);

            // Calculate GWA
            let gwa = 0;
            if (totalUnits > 0) {
                gwa = totalGradePoints / totalUnits;
            }

            document.getElementById('presidents-calculated-gwa').innerHTML = `<strong>${gwa.toFixed(2)}</strong>`;

            // Update the form input
            const gwaInput = document.getElementById('inst_calculated_gwa');
            if (gwaInput) {
                gwaInput.value = gwa.toFixed(2);
            }
        }

        // Institutional Form Dynamic Functionality
        async function initializeInstitutionalForm() {
            // Load department-course mapping from API
            let departmentCourses = {};

            try {
                const response = await fetch('/api/scholarship/department-course-mapping');
                departmentCourses = await response.json();
                console.log('Institutional form data loaded:', departmentCourses);
            } catch (error) {
                console.error('Error loading institutional form data:', error);
                departmentCourses = {};
            }

            // Department change handler for Institutional form
            const instDepartmentSelect = document.getElementById('inst_department');
            const instCourseSelect = document.getElementById('inst_course');

            if (instDepartmentSelect && instCourseSelect) {
                instDepartmentSelect.addEventListener('change', function() {
                    const selectedDepartment = this.value;

                    // Clear course options
                    instCourseSelect.innerHTML = '<option value="">Select Course</option>';

                    if (selectedDepartment && departmentCourses[selectedDepartment]) {
                        // Populate courses for selected department
                        departmentCourses[selectedDepartment].forEach(course => {
                            const option = document.createElement('option');
                            option.value = course;
                            option.textContent = course;
                            instCourseSelect.appendChild(option);
                        });

                        // Enable course select
                        instCourseSelect.disabled = false;
                    } else {
                        // Disable course select if no department selected
                        instCourseSelect.disabled = true;
                    }
                });
            }
        }

        // File Upload Functionality
        function initializeFileUploads() {
            const fileInputs = document.querySelectorAll('input[type="file"]');

            fileInputs.forEach(input => {
                const container = input.closest('.file-upload-container');
                const listContainer = input.parentElement.querySelector('.uploaded-files-list');

                // Handle file selection
                input.addEventListener('change', function(e) {
                    handleFileSelection(e.target.files, listContainer, input);
                });

                // Handle drag and drop
                container.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    container.classList.add('dragover');
                });

                container.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    container.classList.remove('dragover');
                });

                container.addEventListener('drop', function(e) {
                    e.preventDefault();
                    container.classList.remove('dragover');
                    handleFileSelection(e.dataTransfer.files, listContainer, input);
                });
            });
        }

        function handleFileSelection(files, listContainer, input) {
            Array.from(files).forEach(file => {
                // Validate file size (5MB limit)
                if (file.size > 5 * 1024 * 1024) {
                    alert(`File "${file.name}" is too large. Maximum size is 5MB.`);
                    return;
                }

                // Create file item
                const fileItem = document.createElement('div');
                fileItem.className = 'uploaded-file-item';
                fileItem.innerHTML = `
                    <div class="uploaded-file-info">
                        <i class="fas fa-file-alt"></i>
                        <div>
                            <div class="uploaded-file-name">${file.name}</div>
                            <div class="uploaded-file-size">${formatFileSize(file.size)}</div>
                        </div>
                    </div>
                    <button type="button" class="remove-file-btn" onclick="removeFile(this)">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                listContainer.appendChild(fileItem);
            });
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function removeFile(button) {
            button.closest('.uploaded-file-item').remove();
        }

        // Duplicate Student ID Prevention
        function initializeDuplicateIDPrevention() {
            // Store submitted student IDs (in a real application, this would come from the server)
            let submittedStudentIDs = new Set();

            // Get all student ID input fields
            const studentIdInputs = document.querySelectorAll('input[name="student_id"]');

            studentIdInputs.forEach(input => {
                // Add real-time validation
                input.addEventListener('input', function() {
                    validateStudentID(this);
                });

                // Add blur validation (when user leaves the field)
                input.addEventListener('blur', function() {
                    validateStudentID(this);
                });
            });

            // Add form submission validation
            const forms = document.querySelectorAll('form[action*="scholarship.submit"]');
            forms.forEach(form => {
                form.addEventListener('submit', async function(e) {
                    const studentIdInput = this.querySelector('input[name="student_id"]');
                    if (studentIdInput) {
                        const isValid = await validateStudentID(studentIdInput);
                        if (!isValid) {
                            e.preventDefault();
                            return false;
                        }
                    }
                });
            });
        }

        async function validateStudentID(input) {
            const studentId = input.value.trim();
            const errorContainer = getOrCreateErrorContainer(input);
            const successContainer = getOrCreateSuccessContainer(input);

            // Clear previous styling
            input.classList.remove('error', 'valid');
            errorContainer.style.display = 'none';
            successContainer.style.display = 'none';

            if (!studentId) {
                return true; // Let required validation handle empty fields
            }

            // Check if ID format is valid (you can customize this pattern)
            const idPattern = /^[0-9]{4}-[0-9]{4,6}$/; // Format: YYYY-XXXXXX
            if (!idPattern.test(studentId)) {
                showIDError(input, errorContainer, 'Invalid Student ID format. Please use format: YYYY-XXXXXX (e.g., 2023-123456)');
                return false;
            }

            // Check for duplicate IDs
            const isDuplicateValid = await checkDuplicateID(studentId, input, errorContainer);
            if (!isDuplicateValid) {
                return false;
            }

            // Show success state if validation passes
            showIDSuccess(input, successContainer, 'Student ID is valid and available.');
            return true;
        }

        async function checkDuplicateID(studentId, input, errorContainer) {
            try {
                // Make AJAX call to check for duplicate IDs in the database
                const response = await fetch('/api/check-student-id', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ student_id: studentId })
                });

                const data = await response.json();

                if (data.exists) {
                    const scholarshipType = data.scholarship_type ? ` (${data.scholarship_type})` : '';
                    const applicationDate = data.application_date ? ` on ${data.application_date}` : '';
                    const applicationId = data.application_id ? ` (ID: ${data.application_id})` : '';
                    showIDError(input, errorContainer,
                        `This Student ID has already been used for a scholarship application${scholarshipType}${applicationDate}${applicationId}. Each student can only apply once per scholarship type.`);
                    return false;
                }

                return true;
            } catch (error) {
                console.error('Error checking student ID:', error);
                // On error, allow submission but log the issue
                return true;
            }
        }

        function getOrCreateErrorContainer(input) {
            let errorContainer = input.parentElement.querySelector('.student-id-error');

            if (!errorContainer) {
                errorContainer = document.createElement('div');
                errorContainer.className = 'student-id-error';
                errorContainer.style.display = 'none';
                input.parentElement.appendChild(errorContainer);
            }

            return errorContainer;
        }

        function getOrCreateSuccessContainer(input) {
            let successContainer = input.parentElement.querySelector('.student-id-success');

            if (!successContainer) {
                successContainer = document.createElement('div');
                successContainer.className = 'student-id-success';
                successContainer.style.display = 'none';
                input.parentElement.appendChild(successContainer);
            }

            return successContainer;
        }

        function showIDError(input, errorContainer, message) {
            input.classList.add('error');
            input.classList.remove('valid');
            errorContainer.textContent = message;
            errorContainer.style.display = 'block';
        }

        function showIDSuccess(input, successContainer, message) {
            input.classList.add('valid');
            input.classList.remove('error');
            successContainer.textContent = message;
            successContainer.style.display = 'block';
        }

        // FAQ Functionality
        function initializeFAQ() {
            // Add click event listeners to all FAQ questions
            const faqQuestions = document.querySelectorAll('.faq-question');
            faqQuestions.forEach(question => {
                question.addEventListener('click', function() {
                    toggleFAQ(this);
                });
            });
        }

        function toggleFAQ(questionElement) {
            const faqItem = questionElement.closest('.faq-item');
            const answer = faqItem.querySelector('.faq-answer');
            const isActive = questionElement.classList.contains('active');

            // Close all other FAQ items
            document.querySelectorAll('.faq-question').forEach(q => {
                q.classList.remove('active');
            });
            document.querySelectorAll('.faq-answer').forEach(a => {
                a.classList.remove('active');
            });

            // Toggle current FAQ item
            if (!isActive) {
                questionElement.classList.add('active');
                answer.classList.add('active');
            }
        }

        // Function to close forms
        function closeForm() {
            // Remove active class from all cards
            document.querySelectorAll('.scholarship-card').forEach(card => {
                card.classList.remove('active');
            });

            // Hide all forms
            document.querySelectorAll('.application-form-container').forEach(form => {
                form.classList.remove('active');
            });
        }


    </script>
</body>
</html>




