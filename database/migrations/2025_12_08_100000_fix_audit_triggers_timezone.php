<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Updates audit triggers to use Asia/Manila timezone
     */
    public function up(): void
    {
        // Set the database timezone to Asia/Manila
        DB::statement("SET time_zone = '+08:00'");

        // Drop and recreate all triggers with timezone fix
        $this->dropAllTriggers();
        $this->createDocRequestsTriggers();
        $this->createStdStudentsTriggers();
        $this->createAccUsersTriggers();
        $this->createDocCategoriesTriggers();
        $this->createClmClaimersTriggers();
        $this->createBulkRequestsTriggers();
        $this->createBulkStudentsTriggers();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No action needed - triggers remain
    }

    private function dropAllTriggers(): void
    {
        $triggers = [
            'audit_doc_requests_insert', 'audit_doc_requests_update', 'audit_doc_requests_delete',
            'audit_std_students_insert', 'audit_std_students_update', 'audit_std_students_delete',
            'audit_acc_users_insert', 'audit_acc_users_update', 'audit_acc_users_delete',
            'audit_doc_categories_insert', 'audit_doc_categories_update', 'audit_doc_categories_delete',
            'audit_clm_claimers_insert', 'audit_clm_claimers_update', 'audit_clm_claimers_delete',
            'audit_bulk_requests_insert', 'audit_bulk_requests_update', 'audit_bulk_requests_delete',
            'audit_bulk_students_insert', 'audit_bulk_students_update', 'audit_bulk_students_delete',
        ];

        foreach ($triggers as $trigger) {
            DB::unprepared("DROP TRIGGER IF EXISTS `{$trigger}`");
        }
    }

    private function createDocRequestsTriggers(): void
    {
        DB::unprepared("
            CREATE TRIGGER `audit_doc_requests_insert` AFTER INSERT ON `doc_requests`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                DECLARE v_student_name VARCHAR(255);
                DECLARE v_doc_type VARCHAR(255);

                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    SELECT CONCAT(FirstName, ' ', LastName) INTO v_student_name
                    FROM std_students WHERE id = NEW.std_students_id;

                    SELECT DocType INTO v_doc_type
                    FROM doc_categories WHERE id = NEW.doc_categories_id;

                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'INSERT',
                        NULL,
                        JSON_OBJECT(
                            'Request No', IFNULL(NEW.req_no, 'N/A'),
                            'Student Name', IFNULL(v_student_name, 'Unknown'),
                            'Document Type', IFNULL(v_doc_type, 'Unknown'),
                            'Request Mode', IFNULL(NEW.request_mode, 'N/A'),
                            'Release Mode', IFNULL(NEW.release_mode, 'N/A'),
                            'Status', IFNULL(NEW.status, 'Pending'),
                            'Remarks', IFNULL(NEW.remarks, 'N/A')
                        ),
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'doc_requests',
                        CONCAT('Document request created: ', IFNULL(v_doc_type, 'Unknown'), ' for ', IFNULL(v_student_name, 'Unknown'))
                    );
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER `audit_doc_requests_update` AFTER UPDATE ON `doc_requests`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                DECLARE v_student_name VARCHAR(255);
                DECLARE v_doc_type VARCHAR(255);

                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    SELECT CONCAT(FirstName, ' ', LastName) INTO v_student_name
                    FROM std_students WHERE id = NEW.std_students_id;

                    SELECT DocType INTO v_doc_type
                    FROM doc_categories WHERE id = NEW.doc_categories_id;

                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'UPDATE',
                        JSON_OBJECT(
                            'Request No', IFNULL(OLD.req_no, 'N/A'),
                            'Status', IFNULL(OLD.status, 'N/A'),
                            'Remarks', IFNULL(OLD.remarks, 'N/A'),
                            'Release Mode', IFNULL(OLD.release_mode, 'N/A')
                        ),
                        JSON_OBJECT(
                            'Request No', IFNULL(NEW.req_no, 'N/A'),
                            'Status', IFNULL(NEW.status, 'N/A'),
                            'Remarks', IFNULL(NEW.remarks, 'N/A'),
                            'Release Mode', IFNULL(NEW.release_mode, 'N/A')
                        ),
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'doc_requests',
                        CONCAT('Document request updated: ', IFNULL(NEW.req_no, 'N/A'))
                    );
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER `audit_doc_requests_delete` AFTER DELETE ON `doc_requests`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                DECLARE v_student_name VARCHAR(255);
                DECLARE v_doc_type VARCHAR(255);

                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    SELECT CONCAT(FirstName, ' ', LastName) INTO v_student_name
                    FROM std_students WHERE id = OLD.std_students_id;

                    SELECT DocType INTO v_doc_type
                    FROM doc_categories WHERE id = OLD.doc_categories_id;

                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'DELETE',
                        JSON_OBJECT(
                            'Request No', IFNULL(OLD.req_no, 'N/A'),
                            'Student Name', IFNULL(v_student_name, 'Unknown'),
                            'Document Type', IFNULL(v_doc_type, 'Unknown'),
                            'Status', IFNULL(OLD.status, 'N/A')
                        ),
                        NULL,
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'doc_requests',
                        CONCAT('Document request deleted: ', IFNULL(OLD.req_no, 'N/A'))
                    );
                END IF;
            END
        ");
    }

    private function createStdStudentsTriggers(): void
    {
        DB::unprepared("
            CREATE TRIGGER `audit_std_students_insert` AFTER INSERT ON `std_students`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'INSERT',
                        NULL,
                        JSON_OBJECT(
                            'Student ID', NEW.id,
                            'Full Name', CONCAT(IFNULL(NEW.FirstName, ''), ' ', IFNULL(NEW.LastName, '')),
                            'LRN', IFNULL(NEW.LRN, 'N/A'),
                            'Grade Level', IFNULL(NEW.Grade_level, 'N/A'),
                            'Status', IFNULL(NEW.Std_status, 'N/A')
                        ),
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'std_students',
                        CONCAT('Student account created: ', IFNULL(NEW.FirstName, ''), ' ', IFNULL(NEW.LastName, ''))
                    );
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER `audit_std_students_update` AFTER UPDATE ON `std_students`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'UPDATE',
                        JSON_OBJECT(
                            'Full Name', CONCAT(IFNULL(OLD.FirstName, ''), ' ', IFNULL(OLD.LastName, '')),
                            'LRN', IFNULL(OLD.LRN, 'N/A'),
                            'Grade Level', IFNULL(OLD.Grade_level, 'N/A'),
                            'Status', IFNULL(OLD.Std_status, 'N/A')
                        ),
                        JSON_OBJECT(
                            'Full Name', CONCAT(IFNULL(NEW.FirstName, ''), ' ', IFNULL(NEW.LastName, '')),
                            'LRN', IFNULL(NEW.LRN, 'N/A'),
                            'Grade Level', IFNULL(NEW.Grade_level, 'N/A'),
                            'Status', IFNULL(NEW.Std_status, 'N/A')
                        ),
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'std_students',
                        CONCAT('Student account updated: ', IFNULL(NEW.FirstName, ''), ' ', IFNULL(NEW.LastName, ''))
                    );
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER `audit_std_students_delete` AFTER DELETE ON `std_students`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'DELETE',
                        JSON_OBJECT(
                            'Student ID', OLD.id,
                            'Full Name', CONCAT(IFNULL(OLD.FirstName, ''), ' ', IFNULL(OLD.LastName, '')),
                            'LRN', IFNULL(OLD.LRN, 'N/A')
                        ),
                        NULL,
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'std_students',
                        CONCAT('Student account deleted: ', IFNULL(OLD.FirstName, ''), ' ', IFNULL(OLD.LastName, ''))
                    );
                END IF;
            END
        ");
    }

    private function createAccUsersTriggers(): void
    {
        DB::unprepared("
            CREATE TRIGGER `audit_acc_users_insert` AFTER INSERT ON `acc_users`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'INSERT',
                        NULL,
                        JSON_OBJECT(
                            'Account ID', NEW.user_account_id,
                            'Username', IFNULL(NEW.username, 'N/A'),
                            'Email', IFNULL(NEW.email_address, 'N/A'),
                            'Role ID', NEW.role_id
                        ),
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'acc_users',
                        CONCAT('User account created: ', IFNULL(NEW.username, 'N/A'))
                    );
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER `audit_acc_users_update` AFTER UPDATE ON `acc_users`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'UPDATE',
                        JSON_OBJECT(
                            'Username', IFNULL(OLD.username, 'N/A'),
                            'Email', IFNULL(OLD.email_address, 'N/A'),
                            'Role ID', OLD.role_id
                        ),
                        JSON_OBJECT(
                            'Username', IFNULL(NEW.username, 'N/A'),
                            'Email', IFNULL(NEW.email_address, 'N/A'),
                            'Role ID', NEW.role_id
                        ),
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'acc_users',
                        CONCAT('User account updated: ', IFNULL(NEW.username, 'N/A'))
                    );
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER `audit_acc_users_delete` AFTER DELETE ON `acc_users`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'DELETE',
                        JSON_OBJECT(
                            'Account ID', OLD.user_account_id,
                            'Username', IFNULL(OLD.username, 'N/A'),
                            'Email', IFNULL(OLD.email_address, 'N/A')
                        ),
                        NULL,
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'acc_users',
                        CONCAT('User account deleted: ', IFNULL(OLD.username, 'N/A'))
                    );
                END IF;
            END
        ");
    }

    private function createDocCategoriesTriggers(): void
    {
        DB::unprepared("
            CREATE TRIGGER `audit_doc_categories_insert` AFTER INSERT ON `doc_categories`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'INSERT',
                        NULL,
                        JSON_OBJECT(
                            'Document Type', IFNULL(NEW.DocType, 'N/A'),
                            'Document Price', CONCAT('Php ', FORMAT(IFNULL(NEW.DocPrice, 0), 2))
                        ),
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'doc_categories',
                        CONCAT('Document type created: ', IFNULL(NEW.DocType, 'N/A'))
                    );
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER `audit_doc_categories_update` AFTER UPDATE ON `doc_categories`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'UPDATE',
                        JSON_OBJECT(
                            'Document Type', IFNULL(OLD.DocType, 'N/A'),
                            'Document Price', CONCAT('Php ', FORMAT(IFNULL(OLD.DocPrice, 0), 2))
                        ),
                        JSON_OBJECT(
                            'Document Type', IFNULL(NEW.DocType, 'N/A'),
                            'Document Price', CONCAT('Php ', FORMAT(IFNULL(NEW.DocPrice, 0), 2))
                        ),
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'doc_categories',
                        CONCAT('Document type updated: ', IFNULL(NEW.DocType, 'N/A'))
                    );
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER `audit_doc_categories_delete` AFTER DELETE ON `doc_categories`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'DELETE',
                        JSON_OBJECT(
                            'Document Type', IFNULL(OLD.DocType, 'N/A'),
                            'Document Price', CONCAT('Php ', FORMAT(IFNULL(OLD.DocPrice, 0), 2))
                        ),
                        NULL,
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'doc_categories',
                        CONCAT('Document type deleted: ', IFNULL(OLD.DocType, 'N/A'))
                    );
                END IF;
            END
        ");
    }

    private function createClmClaimersTriggers(): void
    {
        DB::unprepared("
            CREATE TRIGGER `audit_clm_claimers_insert` AFTER INSERT ON `clm_claimers`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'INSERT',
                        NULL,
                        JSON_OBJECT(
                            'Claimer ID', NEW.id,
                            'Full Name', CONCAT(IFNULL(NEW.Fname, ''), ' ', IFNULL(NEW.Lname, '')),
                            'Contact No', IFNULL(NEW.contact_no, 'N/A')
                        ),
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'clm_claimers',
                        CONCAT('Claimer created: ', IFNULL(NEW.Fname, ''), ' ', IFNULL(NEW.Lname, ''))
                    );
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER `audit_clm_claimers_update` AFTER UPDATE ON `clm_claimers`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'UPDATE',
                        JSON_OBJECT(
                            'Full Name', CONCAT(IFNULL(OLD.Fname, ''), ' ', IFNULL(OLD.Lname, '')),
                            'Contact No', IFNULL(OLD.contact_no, 'N/A')
                        ),
                        JSON_OBJECT(
                            'Full Name', CONCAT(IFNULL(NEW.Fname, ''), ' ', IFNULL(NEW.Lname, '')),
                            'Contact No', IFNULL(NEW.contact_no, 'N/A')
                        ),
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'clm_claimers',
                        CONCAT('Claimer updated: ', IFNULL(NEW.Fname, ''), ' ', IFNULL(NEW.Lname, ''))
                    );
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER `audit_clm_claimers_delete` AFTER DELETE ON `clm_claimers`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'DELETE',
                        JSON_OBJECT(
                            'Claimer ID', OLD.id,
                            'Full Name', CONCAT(IFNULL(OLD.Fname, ''), ' ', IFNULL(OLD.Lname, ''))
                        ),
                        NULL,
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'clm_claimers',
                        CONCAT('Claimer deleted: ', IFNULL(OLD.Fname, ''), ' ', IFNULL(OLD.Lname, ''))
                    );
                END IF;
            END
        ");
    }

    private function createBulkRequestsTriggers(): void
    {
        DB::unprepared("
            CREATE TRIGGER `audit_bulk_requests_insert` AFTER INSERT ON `bulk_requests`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'INSERT',
                        NULL,
                        JSON_OBJECT(
                            'Request No', IFNULL(NEW.req_no, 'N/A'),
                            'School Name', IFNULL(NEW.School_Name, 'N/A'),
                            'Document Type', IFNULL(NEW.Doc_Type, 'N/A'),
                            'Status', IFNULL(NEW.Status, 'Pending')
                        ),
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'bulk_requests',
                        CONCAT('Bulk request created: ', IFNULL(NEW.School_Name, 'N/A'))
                    );
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER `audit_bulk_requests_update` AFTER UPDATE ON `bulk_requests`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'UPDATE',
                        JSON_OBJECT(
                            'Request No', IFNULL(OLD.req_no, 'N/A'),
                            'Status', IFNULL(OLD.Status, 'N/A')
                        ),
                        JSON_OBJECT(
                            'Request No', IFNULL(NEW.req_no, 'N/A'),
                            'Status', IFNULL(NEW.Status, 'N/A')
                        ),
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'bulk_requests',
                        CONCAT('Bulk request updated: ', IFNULL(NEW.req_no, 'N/A'))
                    );
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER `audit_bulk_requests_delete` AFTER DELETE ON `bulk_requests`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'DELETE',
                        JSON_OBJECT(
                            'Request No', IFNULL(OLD.req_no, 'N/A'),
                            'School Name', IFNULL(OLD.School_Name, 'N/A')
                        ),
                        NULL,
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'bulk_requests',
                        CONCAT('Bulk request deleted: ', IFNULL(OLD.req_no, 'N/A'))
                    );
                END IF;
            END
        ");
    }

    private function createBulkStudentsTriggers(): void
    {
        DB::unprepared("
            CREATE TRIGGER `audit_bulk_students_insert` AFTER INSERT ON `bulk_students`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'INSERT',
                        NULL,
                        JSON_OBJECT(
                            'Student Name', IFNULL(NEW.Student_Name, 'N/A'),
                            'Request ID', NEW.Request_ID
                        ),
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'bulk_students',
                        CONCAT('Bulk student added: ', IFNULL(NEW.Student_Name, 'N/A'))
                    );
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER `audit_bulk_students_update` AFTER UPDATE ON `bulk_students`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'UPDATE',
                        JSON_OBJECT(
                            'Student Name', IFNULL(OLD.Student_Name, 'N/A')
                        ),
                        JSON_OBJECT(
                            'Student Name', IFNULL(NEW.Student_Name, 'N/A')
                        ),
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'bulk_students',
                        CONCAT('Bulk student updated: ', IFNULL(NEW.Student_Name, 'N/A'))
                    );
                END IF;
            END
        ");

        DB::unprepared("
            CREATE TRIGGER `audit_bulk_students_delete` AFTER DELETE ON `bulk_students`
            FOR EACH ROW
            BEGIN
                DECLARE v_user VARCHAR(45);
                SET v_user = IFNULL(@current_user, 'system');

                IF @DISABLE_AUDIT_TRIGGERS IS NULL OR @DISABLE_AUDIT_TRIGGERS = 0 THEN
                    INSERT INTO audit_table (type, old_data, new_data, time, changedBy, fromTableName, description)
                    VALUES (
                        'DELETE',
                        JSON_OBJECT(
                            'Student Name', IFNULL(OLD.Student_Name, 'N/A'),
                            'Request ID', OLD.Request_ID
                        ),
                        NULL,
                        CONVERT_TZ(NOW(), '+00:00', '+08:00'),
                        v_user,
                        'bulk_students',
                        CONCAT('Bulk student deleted: ', IFNULL(OLD.Student_Name, 'N/A'))
                    );
                END IF;
            END
        ");
    }
};
