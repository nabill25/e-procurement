@echo off
:: ============================================================
:: DPBJ UI — Setup PostgreSQL Database di Laragon
:: Jalankan script ini sebagai ADMINISTRATOR
:: ============================================================

echo.
echo  ================================================
echo   DPBJ UI E-Procurement — Database Setup
echo   Universitas Indonesia
echo  ================================================
echo.

:: ── Cari psql di lokasi umum Laragon / PostgreSQL ──
set PSQL=
if exist "C:\laragon\bin\pgsql\pgsql-16\bin\psql.exe"   set PSQL=C:\laragon\bin\pgsql\pgsql-16\bin\psql.exe
if exist "C:\laragon\bin\pgsql\pgsql-15\bin\psql.exe"   set PSQL=C:\laragon\bin\pgsql\pgsql-15\bin\psql.exe
if exist "C:\laragon\bin\pgsql\pgsql-14\bin\psql.exe"   set PSQL=C:\laragon\bin\pgsql\pgsql-14\bin\psql.exe
if exist "C:\laragon\bin\pgsql\bin\psql.exe"            set PSQL=C:\laragon\bin\pgsql\bin\psql.exe
if exist "C:\Program Files\PostgreSQL\16\bin\psql.exe"  set PSQL=C:\Program Files\PostgreSQL\16\bin\psql.exe
if exist "C:\Program Files\PostgreSQL\15\bin\psql.exe"  set PSQL=C:\Program Files\PostgreSQL\15\bin\psql.exe

if "%PSQL%"=="" (
  echo [ERROR] psql.exe tidak ditemukan!
  echo.
  echo Pastikan PostgreSQL sudah diinstall via:
  echo   Laragon tray → Tools → Quick Add → postgresql-16
  echo   ATAU download dari: https://www.postgresql.org/download/windows/
  echo.
  pause
  exit /b 1
)

echo [OK] Ditemukan psql: %PSQL%
echo.

:: ── Konfigurasi koneksi ──
set PGHOST=localhost
set PGPORT=5432
set PGUSER=postgres

echo  Masukkan password PostgreSQL (default: kosong, tekan Enter):
set /p PGPASSWORD=  Password: 
echo.

:: ── Buat database ──
echo [1/3] Membuat database dpbj_ui...
"%PSQL%" -h %PGHOST% -p %PGPORT% -U %PGUSER% -c "CREATE DATABASE dpbj_ui ENCODING 'UTF8' LC_COLLATE 'en-US' LC_CTYPE 'en-US' TEMPLATE template0;" 2>nul
if %errorlevel%==0 (
  echo [OK] Database dpbj_ui berhasil dibuat.
) else (
  echo [INFO] Database mungkin sudah ada, melanjutkan...
)

:: ── Install extensions ──
echo.
echo [2/3] Mengaktifkan ekstensi uuid-ossp dan pgcrypto...
"%PSQL%" -h %PGHOST% -p %PGPORT% -U %PGUSER% -d dpbj_ui -c "CREATE EXTENSION IF NOT EXISTS \"uuid-ossp\"; CREATE EXTENSION IF NOT EXISTS \"pgcrypto\";"
if %errorlevel%==0 (
  echo [OK] Ekstensi berhasil diaktifkan.
) else (
  echo [WARN] Gagal mengaktifkan ekstensi. Pastikan PostgreSQL versi 12+.
)

:: ── Jalankan schema.sql ──
echo.
echo [3/3] Menerapkan schema database...
"%PSQL%" -h %PGHOST% -p %PGPORT% -U %PGUSER% -d dpbj_ui -f "%~dp0schema.sql"
if %errorlevel%==0 (
  echo.
  echo  ================================================
  echo   [SUKSES] Database dpbj_ui siap digunakan!
  echo  ================================================
  echo.
  echo  Koneksi database:
  echo    Host     : localhost
  echo    Port     : 5432
  echo    Database : dpbj_ui
  echo    User     : postgres
  echo.
  echo  Login admin sistem:
  echo    Email    : admin@dpbj.ui.ac.id
  echo    Password : Admin@DPBJ2025
  echo.
) else (
  echo.
  echo [ERROR] Gagal menerapkan schema. Cek error di atas.
)

pause
