@echo off
setlocal enabledelayedexpansion

:: ==========================================
:: Git and Database Backup Script
:: ==========================================

:: Set working directory to script location
cd /d "%~dp0"

:: 1. Get Inputs
echo ------------------------------------------
set /p GIT_PATH="1. Enter path for git add (e.g., . or specific file): "
set /p COMMIT_MESSAGE="2. Enter commit message: "
echo ------------------------------------------

:: 2. Execute Git Commands
echo.
echo [1/4] Adding files: git add "%GIT_PATH%"
git add "%GIT_PATH%"

echo.
echo [2/4] Committing: git commit -m "%COMMIT_MESSAGE%"
git commit -m "%COMMIT_MESSAGE%"

echo.
echo [3/4] Pushing: git push
git push

:: 3. Database Backup
echo.
echo [4/4] Creating Database Backup...

:: Get timestamp for filename (format: DDMMYY_HHMM)
for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /format:list') do set datetime=%%I
set TIMESTAMP=!datetime:~6,2!!datetime:~4,2!!datetime:~2,2!_!datetime:~8,2!!datetime:~10,2!

:: Ensure backup directory exists
if not exist "backups" mkdir "backups"

:: Database Configuration (from config/database.php)
set DB_HOST=127.0.0.1
set DB_PORT=5432
set DB_NAME=easycart
set DB_USER=postgres
set PGPASSWORD=root

:: PostgreSQL Bin Path (Auto-detected as version 18)
set PG_PATH=C:\Program Files\PostgreSQL\18\bin

set BACKUP_FILE=backups\%DB_NAME%_!TIMESTAMP!_backup.sql

:: Execute pg_dump
:: -F p is plain text (sql)
:: -v (verbose) for debugging
echo Executing: "%PG_PATH%\pg_dump.exe" -h %DB_HOST% -p %DB_PORT% -U %DB_USER% -d %DB_NAME% -f "!BACKUP_FILE!"
"%PG_PATH%\pg_dump.exe" -h %DB_HOST% -p %DB_PORT% -U %DB_USER% -d %DB_NAME% -v -f "!BACKUP_FILE!" 2> backup_error.txt

if !ERRORLEVEL! EQU 0 (
    echo.
    echo +------------------------------------------+
    echo ^| Backup created: !BACKUP_FILE! ^|
    echo +------------------------------------------+
    :: Check if file size is greater than 0
    for %%A in ("!BACKUP_FILE!") do if %%~zA==0 (
        echo [!] WARNING: Backup file is 0 bytes! Check backup_error.txt.
        type backup_error.txt
    ) else (
        del backup_error.txt
    )
) else (
    echo.
    echo [!] ERROR: Database backup failed. Check backup_error.txt for details.
    type backup_error.txt
)

echo.
echo Process Finished.
cmd /k
