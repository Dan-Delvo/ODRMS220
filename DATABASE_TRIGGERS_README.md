# Database Audit Triggers - Documentation

## Overview

This migration creates comprehensive MariaDB-compatible audit triggers for all major tables in the ODRMS system. These triggers automatically log INSERT, UPDATE, and DELETE operations to the `audit_table`.

## Tables Covered

1. ✅ **doc_requests** - Document requests
2. ✅ **std_students** - Student information
3. ✅ **acc_users** - User accounts
4. ✅ **doc_categories** - Document types
5. ✅ **clm_claimers** - Claimers information
6. ✅ **bulk_requests** - Bulk document requests
7. ✅ **bulk_students** - Bulk student entries

## How to Install

### Run the Migration

```bash
php artisan migrate
```

This will:

-   Drop any existing audit triggers (clean slate)
-   Create 21 new triggers (INSERT, UPDATE, DELETE for each of the 7 tables)

## How It Works

### Current User Tracking

The triggers use the `@current_user` session variable that you set in your controllers:

```php
$pdo = DB::connection()->getPdo();
$pdo->exec("SET @current_user = " . $pdo->quote(Auth::check() ? Auth::user()->username : 'guest'));
```

**Important**: This variable is already set in your controllers (DocumentRequestController, AccountController, etc.), so the triggers will automatically capture the correct user!

### Audit Bypass

The triggers respect the `@DISABLE_AUDIT_TRIGGERS` variable:

-   When set to `1`, triggers are skipped (used by the PopulateAuditTableSeeder)
-   When `NULL` or `0`, triggers execute normally

## Trigger Details

### doc_requests Triggers

**INSERT Trigger:**

-   Captures: Request No, Student Name, Document Type, Request Mode, Release Mode, Status, Remarks
-   Description: "Document request created: [DocType] for [Student Name]"

**UPDATE Trigger:**

-   Captures: Changes to Status, Remarks, Release Mode
-   Description: "Document request updated: [req_no]"

**DELETE Trigger:**

-   Captures: Request No, Student Name, Document Type, Status
-   Description: "Document request deleted: [req_no]"

### std_students Triggers

**INSERT Trigger:**

-   Captures: Student ID, Full Name, LRN, Grade Level, Status
-   Description: "Student account created: [Full Name]"

**UPDATE Trigger:**

-   Captures: Changes to Name, LRN, Grade Level, Status
-   Description: "Student account updated: [Full Name]"

**DELETE Trigger:**

-   Captures: Student ID, Full Name, LRN
-   Description: "Student account deleted: [Full Name]"

### acc_users Triggers

**INSERT Trigger:**

-   Captures: Account ID, Username, Email, Role ID
-   Description: "User account created: [Username]"

**UPDATE Trigger:**

-   Captures: Changes to Username, Email, Role ID
-   Description: "User account updated: [Username]"

**DELETE Trigger:**

-   Captures: Account ID, Username, Email
-   Description: "User account deleted: [Username]"

### doc_categories Triggers

**INSERT Trigger:**

-   Captures: Document Type, Document Price
-   Description: "Document type created: [DocType]"

**UPDATE Trigger:**

-   Captures: Changes to Document Type, Price
-   Description: "Document type updated: [DocType]"

**DELETE Trigger:**

-   Captures: Document Type, Price
-   Description: "Document type deleted: [DocType]"

### clm_claimers Triggers

**INSERT Trigger:**

-   Captures: Claimer ID, Full Name, Contact No
-   Description: "Claimer created: [Full Name]"

**UPDATE Trigger:**

-   Captures: Changes to Name, Contact No
-   Description: "Claimer updated: [Full Name]"

**DELETE Trigger:**

-   Captures: Claimer ID, Full Name
-   Description: "Claimer deleted: [Full Name]"

### bulk_requests Triggers

**INSERT Trigger:**

-   Captures: Request No, School Name, Document Type, Status
-   Description: "Bulk request created: [School Name]"

**UPDATE Trigger:**

-   Captures: Changes to Request No, Status
-   Description: "Bulk request updated: [req_no]"

**DELETE Trigger:**

-   Captures: Request No, School Name
-   Description: "Bulk request deleted: [req_no]"

### bulk_students Triggers

**INSERT Trigger:**

-   Captures: Student Name, Request ID
-   Description: "Bulk student added: [Student Name]"

**UPDATE Trigger:**

-   Captures: Changes to Student Name
-   Description: "Bulk student updated: [Student Name]"

**DELETE Trigger:**

-   Captures: Student Name, Request ID
-   Description: "Bulk student deleted: [Student Name]"

## Testing the Triggers

### 1. Test INSERT Trigger

```php
// In your controller, create a new document request
DocumentRequestModel::create([
    'clm_claimers_id' => 1,
    'std_students_id' => 123,
    'doc_categories_id' => 5,
    'request_date' => now(),
    'status' => 'Pending',
    // ... other fields
]);

// Check audit_table
SELECT * FROM audit_table WHERE fromTableName = 'doc_requests' ORDER BY id DESC LIMIT 1;
```

### 2. Test UPDATE Trigger

```php
// Update a document request
DocumentRequestModel::where('id', 12345)->update([
    'status' => 'Processing',
    'remarks' => 'Processing'
]);

// Check audit_table
SELECT * FROM audit_table WHERE type = 'UPDATE' AND fromTableName = 'doc_requests' ORDER BY id DESC LIMIT 1;
```

### 3. Test DELETE Trigger

```php
// Delete a record
DocumentRequestModel::find(12345)->delete();

// Check audit_table
SELECT * FROM audit_table WHERE type = 'DELETE' ORDER BY id DESC LIMIT 1;
```

## Verification Queries

### Check All Triggers

```sql
SHOW TRIGGERS;
```

### Check Recent Audit Entries

```sql
SELECT * FROM audit_table
ORDER BY time DESC
LIMIT 20;
```

### Check Specific Table Audits

```sql
SELECT * FROM audit_table
WHERE fromTableName = 'doc_requests'
ORDER BY time DESC
LIMIT 10;
```

### Check by User

```sql
SELECT * FROM audit_table
WHERE changedBy = 'your_username'
ORDER BY time DESC;
```

### Count Audits by Type

```sql
SELECT
    type,
    fromTableName,
    COUNT(*) as count
FROM audit_table
GROUP BY type, fromTableName
ORDER BY fromTableName, type;
```

## Important Notes

1. **MariaDB Compatible**: All triggers use MariaDB/MySQL syntax (JSON_OBJECT, IFNULL, etc.)

2. **Automatic Execution**: Triggers fire automatically - no code changes needed in your controllers

3. **Session Variables**: The `@current_user` variable must be set before database operations. It's already done in most controllers.

4. **Performance**: Triggers add minimal overhead. They use efficient JSON_OBJECT functions and only log essential data.

5. **Data Format**: All audit data is stored as JSON in the `new_data` and `old_data` columns for easy parsing.

6. **Bypass Mechanism**: Use `@DISABLE_AUDIT_TRIGGERS = 1` when you need to skip audit logging (like in seeders).

## Rollback

If you need to remove all triggers:

```bash
php artisan migrate:rollback --step=1
```

Or manually:

```sql
DROP TRIGGER IF EXISTS audit_doc_requests_insert;
DROP TRIGGER IF EXISTS audit_doc_requests_update;
DROP TRIGGER IF EXISTS audit_doc_requests_delete;
-- ... and so on for all triggers
```

## Troubleshooting

### Issue: "Trigger already exists"

**Solution**: Run the migration - it automatically drops existing triggers first.

### Issue: "changedBy is always 'system'"

**Solution**: Make sure you're setting `@current_user` in your controller:

```php
$pdo = DB::connection()->getPdo();
$pdo->exec("SET @current_user = " . $pdo->quote(Auth::user()->username));
```

### Issue: "No audit logs appearing"

**Solution**:

1. Check if triggers exist: `SHOW TRIGGERS;`
2. Check if `@DISABLE_AUDIT_TRIGGERS` is set to 1
3. Verify your database operations are actually executing

### Issue: "JSON format errors"

**Solution**: Make sure your MariaDB version supports JSON functions (MariaDB 10.2.7+)

## Adding More Triggers

To add triggers for additional tables, follow this pattern:

```php
private function createYourTableTriggers(): void
{
    // INSERT trigger
    DB::unprepared("
        CREATE TRIGGER `audit_your_table_insert` AFTER INSERT ON `your_table`
        FOR EACH ROW
        BEGIN
            DECLARE v_user VARCHAR(45);
            SET v_user = IFNULL(@current_user, 'system');

            IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                VALUES (
                    'INSERT',
                    NULL,
                    JSON_OBJECT('field1', NEW.field1, 'field2', NEW.field2),
                    NOW(),
                    v_user,
                    'your_table',
                    CONCAT('Record created: ', NEW.field1)
                );
            END IF;
        END
    ");

    // Add UPDATE and DELETE triggers similarly...
}
```

Then call it in the `up()` method:

```php
public function up(): void
{
    $this->dropAllTriggers();
    // ... existing triggers
    $this->createYourTableTriggers();
}
```
