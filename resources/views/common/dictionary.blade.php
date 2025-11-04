<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Dictionary - Document Request System</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .header-icon {
            width: 40px;
            height: 40px;
            color: #667eea;
        }

        h1 {
            font-size: 2em;
            color: #1a202c;
        }

        .subtitle {
            color: #718096;
            margin-bottom: 20px;
        }

        .search-container {
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            width: 20px;
            height: 20px;
        }

        #searchInput {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

        #searchInput:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .table-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
            transition: transform 0.2s;
        }

        .table-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .table-header {
            padding: 25px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            transition: background 0.2s;
        }

        .table-header:hover {
            background: #f7fafc;
        }

        .table-title-section {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 1;
        }

        .table-icon {
            width: 30px;
            height: 30px;
            color: #667eea;
            flex-shrink: 0;
        }

        .table-info h2 {
            font-size: 1.3em;
            color: #1a202c;
            margin-bottom: 5px;
        }

        .table-description {
            color: #718096;
            font-size: 0.9em;
        }

        .table-meta {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .column-count {
            background: #edf2f7;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85em;
            color: #4a5568;
        }

        .chevron {
            width: 20px;
            height: 20px;
            color: #a0aec0;
            transition: transform 0.3s;
        }

        .chevron.rotated {
            transform: rotate(90deg);
        }

        .export-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.85em;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            margin-right: 10px;
        }

        .export-btn:hover {
            background: #5568d3;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .export-btn:active {
            transform: translateY(0);
        }

        .export-icon {
            width: 16px;
            height: 16px;
        }

        .table-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            border-top: 1px solid #e2e8f0;
        }

        .table-content.expanded {
            max-height: 2000px;
        }

        .columns-table {
            width: 100%;
            border-collapse: collapse;
        }

        .columns-table thead {
            background: #f7fafc;
        }

        .columns-table th {
            padding: 15px 20px;
            text-align: left;
            font-size: 0.75em;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #4a5568;
            font-weight: 600;
        }

        .columns-table td {
            padding: 15px 20px;
            border-top: 1px solid #e2e8f0;
        }

        .columns-table tbody tr:hover {
            background: #f7fafc;
        }

        .column-name {
            font-family: 'Courier New', monospace;
            color: #667eea;
            font-weight: 600;
            font-size: 0.9em;
        }

        .column-type {
            background: #edf2f7;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.85em;
            color: #4a5568;
            display: inline-block;
        }

        .column-constraint {
            background: #e2e8f0;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.85em;
            color: #2d3748;
            display: inline-block;
            font-weight: 500;
        }

        .column-description {
            color: #4a5568;
            font-size: 0.9em;
        }

        .no-results {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 50px;
            text-align: center;
            color: #718096;
            font-size: 1.1em;
        }

        .overview {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-top: 30px;
        }

        .overview h3 {
            color: #1a202c;
            margin-bottom: 20px;
            font-size: 1.2em;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .stat-card {
            padding: 20px;
            border-radius: 8px;
        }

        .stat-card.indigo {
            background: #eef2ff;
        }

        .stat-card.green {
            background: #f0fdf4;
        }

        .stat-card.purple {
            background: #faf5ff;
        }

        .stat-label {
            font-size: 0.85em;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .stat-card.indigo .stat-label {
            color: #4c51bf;
        }

        .stat-card.green .stat-label {
            color: #047857;
        }

        .stat-card.purple .stat-label {
            color: #7c3aed;
        }

        .stat-value {
            font-size: 2em;
            font-weight: bold;
        }

        .stat-card.indigo .stat-value {
            color: #5a67d8;
        }

        .stat-card.green .stat-value {
            color: #059669;
        }

        .stat-card.purple .stat-value {
            color: #8b5cf6;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            h1 {
                font-size: 1.5em;
            }

            .table-header {
                padding: 15px;
            }

            .columns-table th,
            .columns-table td {
                padding: 10px;
                font-size: 0.85em;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <svg class="header-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                </svg>
                <h1>Database Dictionary</h1>
            </div>
            <p class="subtitle">Document Request Management System - Complete database schema documentation</p>

            <div class="search-container">
                <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" id="searchInput" placeholder="Search tables, columns, or descriptions...">
            </div>
        </div>

        <div id="tablesContainer"></div>

        <div class="overview">
            <h3>Database Overview</h3>
            <div class="stats-grid">
                <div class="stat-card indigo">
                    <p class="stat-label">Total Tables</p>
                    <p class="stat-value" id="totalTables">24</p>
                </div>
                <div class="stat-card green">
                    <p class="stat-label">Core Business Tables</p>
                    <p class="stat-value">13</p>
                </div>
                <div class="stat-card purple">
                    <p class="stat-label">System Tables</p>
                    <p class="stat-value">11</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const tables = [{
                name: 'acc_users',
                description: 'Stores user account information for all system users (students, admin, registrar)',
                columns: [
                    { name: 'user_account_id', type: 'bigint', constraint: 'PRIMARY KEY', description: 'Unique user account identifier' },
                    { name: 'std_students_id', type: 'bigint unsigned', constraint: 'FOREIGN KEY', description: 'Foreign key linking to std_students table' },
                    { name: 'role_id', type: 'int', constraint: 'FOREIGN KEY', description: 'Foreign key to role table defining user permissions' },
                    { name: 'email_address', type: 'varchar(255)', constraint: 'NOT NULL', description: 'User email address for login and notifications' },
                    { name: 'username', type: 'varchar(255)', constraint: 'NOT NULL', description: 'Display name/username for the user' },
                    { name: 'password', type: 'varchar(255)', constraint: 'NOT NULL', description: 'Encrypted password hash' },
                    { name: 'email_verified_at', type: 'timestamp', constraint: 'NULL', description: 'Email verification timestamp' },
                    { name: 'remember_token', type: 'varchar(100)', constraint: 'NULL', description: 'Token for "remember me" functionality' },
                    { name: 'timestamps', type: 'timestamp', constraint: 'NULL', description: 'General timestamp field' },
                    { name: 'account_created', type: 'timestamp', constraint: 'NULL', description: 'Account creation date and time' },
                    { name: 'account_edited', type: 'timestamp', constraint: 'NULL', description: 'Last account modification timestamp' },
                    { name: 'deleted_at', type: 'timestamp', constraint: 'NULL', description: 'Soft delete timestamp (null if active)' },
                    { name: 'fcm_token', type: 'varchar(45)', constraint: 'NULL', description: 'Firebase Cloud Messaging token for push notifications' }
                ]
            },
            {
                name: 'std_students',
                description: 'Contains student demographic and academic information',
                columns: [
                    { name: 'id', type: 'bigint unsigned', constraint: 'PRIMARY KEY', description: 'Unique student identifier' },
                    { name: 'LastName', type: 'varchar(255)', constraint: 'NOT NULL', description: 'Student last name/surname' },
                    { name: 'FirstName', type: 'varchar(255)', constraint: 'NOT NULL', description: 'Student first name' },
                    { name: 'MiddleName', type: 'varchar(255)', constraint: 'NULL', description: 'Student middle name (optional)' },
                    { name: 'Suffix', type: 'varchar(255)', constraint: 'NULL', description: 'Name suffix (Jr., Sr., III, etc.)' },
                    { name: 'LRN', type: 'varchar(255)', constraint: 'UNIQUE', description: 'Learner Reference Number - unique education ID' },
                    { name: 'Grade_level', type: 'int', constraint: 'NULL', description: 'Current or last grade level attended' },
                    { name: 'Std_status', type: 'varchar(255)', constraint: 'NULL', description: 'Student status (Regular, Alumni, etc.)' },
                    { name: 'Last_sy_attended', type: 'varchar(25)', constraint: 'NULL', description: 'Last school year attended' },
                    { name: 'id_image', type: 'varchar(500)', constraint: 'NULL', description: 'Path to student ID image file' }
                ]
            },
            {
                name: 'doc_requests',
                description: 'Tracks all document requests from students including status and processing information',
                columns: [
                    { name: 'id', type: 'bigint', constraint: 'PRIMARY KEY', description: 'Unique request identifier' },
                    { name: 'clm_claimers_id', type: 'bigint unsigned', constraint: 'FOREIGN KEY', description: 'Foreign key to claimer who will pick up document' },
                    { name: 'std_students_id', type: 'bigint unsigned', constraint: 'FOREIGN KEY', description: 'Foreign key to requesting student' },
                    { name: 'doc_categories_id', type: 'bigint unsigned', constraint: 'FOREIGN KEY', description: 'Foreign key to document type' },
                    { name: 'request_time', type: 'time', constraint: 'NULL', description: 'Time when request was submitted' },
                    { name: 'request_date', type: 'date', constraint: 'NULL', description: 'Date when request was submitted' },
                    { name: 'request_schl_entity', type: 'varchar(255)', constraint: 'NULL', description: 'School/institution requesting the document' },
                    { name: 'request_mode', type: 'varchar(255)', constraint: 'NULL', description: 'How request was made (Online, Walk-in)' },
                    { name: 'release_mode', type: 'varchar(255)', constraint: 'NULL', description: 'How document will be released (Pick Up, Delivery)' },
                    { name: 'remarks', type: 'text', constraint: 'NULL', description: 'Additional notes or comments about the request' },
                    { name: 'status', type: 'varchar(45)', constraint: 'NULL', description: 'Current status (Pending, Processing, For Release, Claimed, Declined)' },
                    { name: 'receipt_no', type: 'int', constraint: 'NULL', description: 'Payment receipt number' },
                    { name: 'approve_date', type: 'date', constraint: 'NULL', description: 'Date request was approved' },
                    { name: 'forRelease_date', type: 'date', constraint: 'NULL', description: 'Date document was prepared for release' },
                    { name: 'claimed_date', type: 'date', constraint: 'NULL', description: 'Date document was claimed/picked up' },
                    { name: 'req_no', type: 'int', constraint: 'AUTO_INCREMENT', description: 'Sequential request number (auto-increment)' },
                    { name: 'image', type: 'varchar(500)', constraint: 'NULL', description: 'Path to related image file' },
                    { name: 'supporting_document', type: 'varchar(500)', constraint: 'NULL', description: 'Path to supporting documents uploaded' },
                    { name: 'claimed_time', type: 'time', constraint: 'NULL', description: 'Time when document was claimed' }
                ]
            },
            {
                name: 'doc_categories',
                description: 'Defines types of documents that can be requested',
                columns: [
                    { name: 'id', type: 'bigint unsigned', constraint: 'PRIMARY KEY', description: 'Unique document type identifier' },
                    { name: 'DocType', type: 'varchar(255)', constraint: 'NOT NULL', description: 'Name of document type (Good Moral, Form 137, etc.)' },
                    { name: 'DocPrice', type: 'varchar(45)', constraint: 'NULL', description: 'Price/fee for the document' }
                ]
            },
            {
                name: 'clm_claimers',
                description: 'Information about people authorized to claim documents',
                columns: [
                    { name: 'id', type: 'bigint unsigned', constraint: 'PRIMARY KEY', description: 'Unique claimer identifier' },
                    { name: 'Fname', type: 'varchar(255)', constraint: 'NOT NULL', description: 'Claimer first name' },
                    { name: 'Lname', type: 'varchar(255)', constraint: 'NOT NULL', description: 'Claimer last name' },
                    { name: 'contact_no', type: 'varchar(255)', constraint: 'NULL', description: 'Contact number of claimer' },
                    { name: 'claimed_date', type: 'date', constraint: 'NULL', description: 'Date when documents were claimed' }
                ]
            },
            {
                name: 'audit_table',
                description: 'Tracks all system changes and modifications for audit trail purposes',
                columns: [
                    { name: 'id', type: 'int', constraint: 'PRIMARY KEY', description: 'Unique audit entry identifier' },
                    { name: 'type', type: 'varchar(45)', constraint: 'NULL', description: 'Type of action (INSERT, UPDATE, DELETE, Back Up, Restore, User Logged In)' },
                    { name: 'old_data', type: 'text', constraint: 'NULL', description: 'Previous data values before change' },
                    { name: 'new_data', type: 'text', constraint: 'NULL', description: 'New data values after change' },
                    { name: 'time', type: 'datetime', constraint: 'NULL', description: 'Timestamp of the action' },
                    { name: 'changedBy', type: 'varchar(45)', constraint: 'NULL', description: 'Username who performed the action' },
                    { name: 'fromTableName', type: 'varchar(45)', constraint: 'NULL', description: 'Name of table that was modified' },
                    { name: 'description', type: 'varchar(500)', constraint: 'NULL', description: 'Additional description of the action' }
                ]
            },
            {
                name: 'role',
                description: 'Defines user roles in the system',
                columns: [
                    { name: 'id', type: 'int', constraint: 'PRIMARY KEY', description: 'Unique role identifier' },
                    { name: 'name', type: 'varchar(45)', constraint: 'NOT NULL', description: 'Role name (student, Admin, Super admin, Registrar Window)' }
                ]
            },
            {
                name: 'permission',
                description: 'Defines specific permissions that can be granted to roles',
                columns: [
                    { name: 'id', type: 'int', constraint: 'PRIMARY KEY', description: 'Unique permission identifier' },
                    { name: 'name', type: 'varchar(45)', constraint: 'NOT NULL', description: 'Permission name (dashboard, pending, editPending, etc.)' },
                    { name: 'slug', type: 'varchar(45)', constraint: 'UNIQUE', description: 'URL-friendly permission identifier' },
                    { name: 'groupBy', type: 'int', constraint: 'NULL', description: 'Group number for organizing related permissions' },
                    { name: 'created_at', type: 'datetime', constraint: 'NULL', description: 'Permission creation timestamp' },
                    { name: 'updated_at', type: 'datetime', constraint: 'NULL', description: 'Last update timestamp' }
                ]
            },
            {
                name: 'permission_role',
                description: 'Links permissions to roles (many-to-many relationship)',
                columns: [
                    { name: 'id', type: 'int', constraint: 'PRIMARY KEY', description: 'Unique link identifier' },
                    { name: 'role_id', type: 'int', constraint: 'FOREIGN KEY', description: 'Foreign key to role table' },
                    { name: 'permission_id', type: 'int', constraint: 'FOREIGN KEY', description: 'Foreign key to permission table' },
                    { name: 'created_at', type: 'datetime', constraint: 'NULL', description: 'Assignment creation timestamp' },
                    { name: 'updated_at', type: 'datetime', constraint: 'NULL', description: 'Last update timestamp' }
                ]
            },
            {
                name: 'docu_payment_fees',
                description: 'Records payment information for document requests',
                columns: [
                    { name: 'receipt_no', type: 'bigint', constraint: 'PRIMARY KEY', description: 'Unique receipt number' },
                    { name: 'docu_categories_id', type: 'bigint unsigned', constraint: 'FOREIGN KEY', description: 'Foreign key to document type' },
                    { name: 'doc_amount', type: 'int', constraint: 'NULL', description: 'Payment amount for the document' },
                    { name: 'name_request', type: 'varchar(100)', constraint: 'NULL', description: 'Name/ID of person making payment' },
                    { name: 'time_request', type: 'datetime', constraint: 'NULL', description: 'Payment timestamp' }
                ]
            },
            {
                name: 'notifications',
                description: 'Stores system notifications sent to users',
                columns: [
                    { name: 'id', type: 'bigint', constraint: 'PRIMARY KEY', description: 'Unique notification identifier' },
                    { name: 'account_id', type: 'bigint', constraint: 'FOREIGN KEY', description: 'User account receiving notification' },
                    { name: 'doc_request_id', type: 'bigint', constraint: 'FOREIGN KEY', description: 'Related document request (if applicable)' },
                    { name: 'type', type: 'text', constraint: 'NULL', description: 'Type/category of notification' },
                    { name: 'title', type: 'varchar(255)', constraint: 'NULL', description: 'Notification title/subject' },
                    { name: 'content', type: 'text', constraint: 'NULL', description: 'Notification message content' },
                    { name: 'status', type: 'enum', constraint: 'NULL', description: 'Delivery status (Pending, Sent, Failed, Completed, Processing, Released)' },
                    { name: 'created_at', type: 'datetime', constraint: 'NULL', description: 'Notification creation timestamp' },
                    { name: 'sent_at', type: 'datetime', constraint: 'NULL', description: 'When notification was sent' }
                ]
            },
            {
                name: 'report_doc_requests',
                description: 'Archived/reporting data for completed document requests',
                columns: [
                    { name: 'report_id', type: 'bigint', constraint: 'PRIMARY KEY', description: 'Unique report entry identifier' },
                    { name: 'request_id', type: 'bigint', constraint: 'NULL', description: 'Original request ID' },
                    { name: 'student_name', type: 'varchar(50)', constraint: 'NULL', description: 'Name of requesting student' },
                    { name: 'grade_level', type: 'varchar(45)', constraint: 'NULL', description: 'Student grade level' },
                    { name: 'document_type', type: 'varchar(50)', constraint: 'NULL', description: 'Type of document requested' },
                    { name: 'request_schl_entity', type: 'varchar(50)', constraint: 'NULL', description: 'Requesting school/institution' },
                    { name: 'request_date', type: 'date', constraint: 'NULL', description: 'Request date' },
                    { name: 'release_mode', type: 'varchar(50)', constraint: 'NULL', description: 'Release method' },
                    { name: 'status', type: 'varchar(45)', constraint: 'NULL', description: 'Final status' },
                    { name: 'processed_by', type: 'varchar(50)', constraint: 'NULL', description: 'Staff who processed request' },
                    { name: 'remarks', type: 'text', constraint: 'NULL', description: 'Additional notes' },
                    { name: 'claimers_name', type: 'varchar(50)', constraint: 'NULL', description: 'Person who claimed document' },
                    { name: 'created_at', type: 'datetime', constraint: 'NULL', description: 'Report entry creation timestamp' },
                    { name: 'updated_at', type: 'datetime', constraint: 'NULL', description: 'Last update timestamp' }
                ]
            },
            {
                name: 'std_addresses',
                description: 'Stores student residential address information',
                columns: [
                    { name: 'std_students_id', type: 'bigint unsigned', constraint: 'PRIMARY KEY, FOREIGN KEY', description: 'Primary/Foreign key to student record' },
                    { name: 'HouseNumber_Street', type: 'varchar(255)', constraint: 'NULL', description: 'House number and street name' },
                    { name: 'subdivision_village', type: 'varchar(255)', constraint: 'NULL', description: 'Subdivision or village name' },
                    { name: 'Barangay', type: 'varchar(255)', constraint: 'NULL', description: 'Barangay (district) name' },
                    { name: 'City_municipality', type: 'varchar(255)', constraint: 'NULL', description: 'City or municipality' },
                    { name: 'Province', type: 'varchar(255)', constraint: 'NULL', description: 'Province name' },
                    { name: 'PostalCode', type: 'varchar(45)', constraint: 'NULL', description: 'Postal/ZIP code' }
                ]
            },
            {
                name: 'sessions',
                description: 'Laravel session management table for tracking user sessions',
                columns: [
                    { name: 'id', type: 'varchar(255)', constraint: 'PRIMARY KEY', description: 'Unique session identifier' },
                    { name: 'user_id', type: 'bigint unsigned', constraint: 'NULL', description: 'Associated user account ID' },
                    { name: 'ip_address', type: 'varchar(45)', constraint: 'NULL', description: 'IP address of the session' },
                    { name: 'user_agent', type: 'text', constraint: 'NULL', description: 'Browser/device information' },
                    { name: 'payload', type: 'longtext', constraint: 'NOT NULL', description: 'Serialized session data' },
                    { name: 'last_activity', type: 'int', constraint: 'NOT NULL', description: 'Unix timestamp of last activity' }
                ]
            },
            {
                name: 'cache',
                description: 'Laravel cache storage table for application performance optimization',
                columns: [
                    { name: 'key', type: 'varchar(255)', constraint: 'PRIMARY KEY', description: 'Cache key identifier' },
                    { name: 'value', type: 'mediumtext', constraint: 'NOT NULL', description: 'Cached data value' },
                    { name: 'expiration', type: 'int', constraint: 'NOT NULL', description: 'Unix timestamp when cache expires' }
                ]
            },
            {
                name: 'cache_locks',
                description: 'Laravel cache locking mechanism to prevent race conditions',
                columns: [
                    { name: 'key', type: 'varchar(255)', constraint: 'PRIMARY KEY', description: 'Lock identifier' },
                    { name: 'owner', type: 'varchar(255)', constraint: 'NOT NULL', description: 'Process/thread owning the lock' },
                    { name: 'expiration', type: 'int', constraint: 'NOT NULL', description: 'Unix timestamp when lock expires' }
                ]
            },
            {
                name: 'fdbk_feedback',
                description: 'Stores user feedback and suggestions',
                columns: [
                    { name: 'id', type: 'bigint', constraint: 'PRIMARY KEY', description: 'Unique feedback identifier' },
                    { name: 'account_id', type: 'bigint', constraint: 'FOREIGN KEY', description: 'User account who submitted feedback' },
                    { name: 'feedback_text', type: 'text', constraint: 'NULL', description: 'Feedback message content' },
                    { name: 'feedback_date', type: 'datetime', constraint: 'NULL', description: 'Submission timestamp' }
                ]
            },
            {
                name: 'log_access',
                description: 'Logs user account creation and access events',
                columns: [
                    { name: 'id', type: 'int', constraint: 'PRIMARY KEY', description: 'Unique log entry identifier' },
                    { name: 'user_account_id', type: 'bigint', constraint: 'NULL', description: 'User account ID' },
                    { name: 'username', type: 'varchar(45)', constraint: 'NULL', description: 'Username involved' },
                    { name: 'email_address', type: 'varchar(45)', constraint: 'NULL', description: 'Email address involved' },
                    { name: 'action_type', type: 'varchar(50)', constraint: 'NULL', description: 'Type of action (Account Created, etc.)' },
                    { name: 'remarks', type: 'text', constraint: 'NULL', description: 'Additional details about the action' },
                    { name: 'timestamp', type: 'datetime', constraint: 'NULL', description: 'When action occurred' }
                ]
            },
            {
                name: 'log_requests',
                description: 'Logs document request lifecycle events',
                columns: [
                    { name: 'id', type: 'bigint', constraint: 'PRIMARY KEY', description: 'Unique log entry identifier' },
                    { name: 'account_id', type: 'bigint', constraint: 'NULL', description: 'User account performing action' },
                    { name: 'doc_request_id', type: 'bigint', constraint: 'NULL', description: 'Document request being modified' },
                    { name: 'action_type', type: 'enum', constraint: 'NULL', description: 'Type of action (created, updated, approved, rejected, claimed)' },
                    { name: 'action_timestamp', type: 'datetime', constraint: 'NULL', description: 'When action occurred' },
                    { name: 'remarks', type: 'text', constraint: 'NULL', description: 'Additional notes about the action' }
                ]
            },
            {
                name: 'log_transactions',
                description: 'Transaction log for document request operations',
                columns: [
                    { name: 'id', type: 'int', constraint: 'PRIMARY KEY', description: 'Unique log entry identifier' },
                    { name: 'doc_request_id', type: 'bigint', constraint: 'NULL', description: 'Document request ID' },
                    { name: 'std_student_id', type: 'bigint', constraint: 'NULL', description: 'Student ID' },
                    { name: 'name', type: 'varchar(45)', constraint: 'NULL', description: 'Name of person/action' },
                    { name: 'action', type: 'text', constraint: 'NULL', description: 'Description of action taken' },
                    { name: 'date', type: 'date', constraint: 'NULL', description: 'Date of transaction' },
                    { name: 'time', type: 'time', constraint: 'NULL', description: 'Time of transaction' }
                ]
            },
            {
                name: 'migrations',
                description: 'Laravel migration tracking table',
                columns: [
                    { name: 'id', type: 'int unsigned', constraint: 'PRIMARY KEY', description: 'Migration entry identifier' },
                    { name: 'migration', type: 'varchar(255)', constraint: 'NOT NULL', description: 'Migration file name' },
                    { name: 'batch', type: 'int', constraint: 'NOT NULL', description: 'Batch number for grouping migrations' }
                ]
            },
            {
                name: 'password_reset_tokens',
                description: 'Stores tokens for password reset functionality',
                columns: [
                    { name: 'email', type: 'varchar(255)', constraint: 'PRIMARY KEY', description: 'Email address for reset' },
                    { name: 'token', type: 'varchar(255)', constraint: 'NOT NULL', description: 'Reset token generated' },
                    { name: 'created_at', type: 'timestamp', constraint: 'NULL', description: 'Token creation timestamp' }
                ]
            },
            {
                name: 'temp_passwords',
                description: 'Temporary passwords for new user account setup',
                columns: [
                    { name: 'id', type: 'bigint unsigned', constraint: 'PRIMARY KEY', description: 'Entry identifier' },
                    { name: 'email_address', type: 'varchar(255)', constraint: 'NOT NULL', description: 'User email address' },
                    { name: 'temp_password', type: 'varchar(255)', constraint: 'NOT NULL', description: 'Temporary password (encrypted)' },
                    { name: 'email_sent', type: 'tinyint(1)', constraint: 'NOT NULL', description: 'Whether email was sent (0=no, 1=yes)' },
                    { name: 'created_at', type: 'timestamp', constraint: 'NULL', description: 'Creation timestamp' },
                    { name: 'updated_at', type: 'timestamp', constraint: 'NULL', description: 'Last update timestamp' }
                ]
            },
            {
                name: 'users',
                description: 'Laravel default users table (appears unused in favor of acc_users)',
                columns: [
                    { name: 'id', type: 'bigint unsigned', constraint: 'PRIMARY KEY', description: 'User identifier' },
                    { name: 'name', type: 'varchar(255)', constraint: 'NOT NULL', description: 'User name' },
                    { name: 'email', type: 'varchar(255)', constraint: 'UNIQUE', description: 'User email (unique)' },
                    { name: 'email_verified_at', type: 'timestamp', constraint: 'NULL', description: 'Email verification timestamp' },
                    { name: 'password', type: 'varchar(255)', constraint: 'NOT NULL', description: 'Encrypted password' },
                    { name: 'remember_token', type: 'varchar(100)', constraint: 'NULL', description: 'Remember me token' },
                    { name: 'created_at', type: 'timestamp', constraint: 'NULL', description: 'Creation timestamp' },
                    { name: 'updated_at', type: 'timestamp', constraint: 'NULL', description: 'Last update timestamp' }
                ]
            }
        ];

        let expandedTables = {};

        function renderTables(tablesToRender) {
            const container = document.getElementById('tablesContainer');

            if (tablesToRender.length === 0) {
                container.innerHTML = '<div class="no-results">No tables found matching your search.</div>';
                return;
            }

            container.innerHTML = tablesToRender.map((table, index) => `
                <div class="table-card" id="table-card-${table.name}">
                    <div class="table-header" onclick="toggleTable('${table.name}')">
                        <div class="table-title-section">
                            <svg class="table-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <div class="table-info">
                                <h2>${table.name}</h2>
                                <p class="table-description">${table.description}</p>
                            </div>
                        </div>
                        <div class="table-meta">
                            <button class="export-btn" onclick="event.stopPropagation(); exportTableAsImage('${table.name}')" title="Export table as image">
                                <svg class="export-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                Export
                            </button>
                            <span class="column-count">${table.columns.length} columns</span>
                            <svg class="chevron" id="chevron-${table.name}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                    <div class="table-content" id="content-${table.name}">
                        <table class="columns-table">
                            <thead>
                                <tr>
                                    <th>Field Name</th>
                                    <th>Data Type</th>
                                    <th>Constraint</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${table.columns.map(col => `
                                    <tr>
                                        <td><span class="column-name">${col.name}</span></td>
                                        <td><span class="column-type">${col.type}</span></td>
                                        <td><span class="column-constraint">${col.constraint || 'NULL'}</span></td>
                                        <td><span class="column-description">${col.description}</span></td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            `).join('');
        }

        function toggleTable(tableName) {
            expandedTables[tableName] = !expandedTables[tableName];
            const content = document.getElementById(`content-${tableName}`);
            const chevron = document.getElementById(`chevron-${tableName}`);

            if (expandedTables[tableName]) {
                content.classList.add('expanded');
                chevron.classList.add('rotated');
            } else {
                content.classList.remove('expanded');
                chevron.classList.remove('rotated');
            }
        }

        function filterTables(searchTerm) {
            const term = searchTerm.toLowerCase();

            if (!term) {
                return tables;
            }

            return tables.filter(table => {
                const nameMatch = table.name.toLowerCase().includes(term);
                const descMatch = table.description.toLowerCase().includes(term);
                const columnMatch = table.columns.some(col =>
                    col.name.toLowerCase().includes(term) ||
                    col.description.toLowerCase().includes(term)
                );

                return nameMatch || descMatch || columnMatch;
            });
        }

        // Export table as image
        // Export table as image with document-style layout
        async function exportTableAsImage(tableName) {
            const content = document.getElementById(`content-${tableName}`);
            const chevron = document.getElementById(`chevron-${tableName}`);

            // Temporarily expand the table if it's collapsed
            const wasExpanded = expandedTables[tableName];
            if (!wasExpanded) {
                content.classList.add('expanded');
                chevron.classList.add('rotated');
                // Wait for expansion animation
                await new Promise(resolve => setTimeout(resolve, 300));
            }

            try {
                // Get the table info
                const table = tables.find(t => t.name === tableName);

                // Create a temporary container for export
                const exportContainer = document.createElement('div');
                exportContainer.style.position = 'absolute';
                exportContainer.style.left = '-9999px';
                exportContainer.style.background = 'white';
                exportContainer.style.padding = '30px';
                exportContainer.style.fontFamily = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif';

                exportContainer.innerHTML = `
                    <div style="margin-bottom: 15px; text-align: center;">
                        <h2 style="font-size: 14pt; color: #000000; margin-bottom: 5px; font-weight: bold;">Table: ${table.name}</h2>
                        <p style="color: #000000; font-size: 10pt; font-style: italic;">${table.description}</p>
                    </div>
                    <table style="width: 100%; border-collapse: collapse; border: 2px solid #000000; background: transparent;">
                        <thead>
                            <tr>
                                <th style="padding: 8px 10px; text-align: left; font-size: 10pt; color: #000000; font-weight: bold; border: 1px solid #000000; background: #ffffff;">Field Name</th>
                                <th style="padding: 8px 10px; text-align: left; font-size: 10pt; color: #000000; font-weight: bold; border: 1px solid #000000; background: #ffffff;">Data Type</th>
                                <th style="padding: 8px 10px; text-align: left; font-size: 10pt; color: #000000; font-weight: bold; border: 1px solid #000000; background: #ffffff;">Constraint</th>
                                <th style="padding: 8px 10px; text-align: left; font-size: 10pt; color: #000000; font-weight: bold; border: 1px solid #000000; background: #ffffff;">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${table.columns.map((col, idx) => `
                                <tr>
                                    <td style="padding: 8px 10px; border: 1px solid #000000; background: #ffffff;">
                                        <span style="font-family: 'Courier New', monospace; color: #000000; font-size: 9pt;">${col.name}</span>
                                    </td>
                                    <td style="padding: 8px 10px; border: 1px solid #000000; background: #ffffff;">
                                        <span style="color: #000000; font-size: 9pt;">${col.type}</span>
                                    </td>
                                    <td style="padding: 8px 10px; border: 1px solid #000000; background: #ffffff;">
                                        <span style="color: #000000; font-size: 9pt;">${col.constraint || 'NULL'}</span>
                                    </td>
                                    <td style="padding: 8px 10px; border: 1px solid #000000; background: #ffffff;">
                                        <span style="color: #000000; font-size: 9pt;">${col.description}</span>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;

                document.body.appendChild(exportContainer);

                // Capture the export container
                const canvas = await html2canvas(exportContainer, {
                    backgroundColor: '#ffffff',
                    scale: 2,
                    logging: false,
                    useCORS: true,
                    width: 1000,
                    windowWidth: 1000
                });

                // Remove temporary container
                document.body.removeChild(exportContainer);

                canvas.toBlob((blob) => {
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.download = `${tableName}_data_dictionary.png`;
                    link.href = url;
                    link.click();
                    URL.revokeObjectURL(url);
                });
            } catch (error) {
                console.error('Error exporting table:', error);
                alert('Failed to export table. Please try again.');
                if (document.body.contains(exportContainer)) {
                    document.body.removeChild(exportContainer);
                }
            }
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', (e) => {
            const filteredTables = filterTables(e.target.value);
            renderTables(filteredTables);
        });

        // Initial render
        renderTables(tables);
        document.getElementById('totalTables').textContent = tables.length;
    </script>
</body>

</html>