@echo off
echo ========================================
echo Populating Audit Table with Historical Data
echo ========================================
echo.
echo This will add all document requests and student account creations to the audit table.
echo Changed By will be set to: Registrar Window
echo.
pause

php artisan db:seed --class=PopulateAuditTableSeeder

echo.
echo ========================================
echo Process completed!
echo ========================================
pause
