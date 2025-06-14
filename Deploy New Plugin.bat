@echo off
setlocal EnableDelayedExpansion

:: ======================================
::      Plugin Setup Wizard
:: ======================================

:: Get Plugin Slug (used for filenames & paths)
set /p PLUGIN_SLUG="Enter the plugin slug (e.g., my-plugin): "

:: Convert slug to function format (replace hyphens with underscores)
set "PLUGIN_SLUG_UNDERSCORES=!PLUGIN_SLUG:-=_!"

:: Get Plugin Name
set /p PLUGIN_NAME="Enter the plugin display name (e.g., My Plugin): "

:: Get Plugin Description
set /p DESCRIPTION="Enter the plugin description: "

:: Get Plugin Function Prefix
set /p FUNCTION_PREFIX="Enter a unique function prefix (e.g., rup_): "

:: Convert prefix and slug to lowercase for function names
for /f "delims=" %%A in ('powershell -Command "[System.Globalization.CultureInfo]::InvariantCulture.TextInfo.ToLower('!FUNCTION_PREFIX!!PLUGIN_SLUG_UNDERSCORES!')"') do set "LOWERCASE_PREFIX=%%A"

:: Get Plugin Version
set /p PLUGIN_VERSION="Enter the plugin version (default: 1.0.0): "
if "!PLUGIN_VERSION!"=="" set PLUGIN_VERSION=1.0.0

:: ======================================
:: Generate .env File
:: ======================================
echo # Environment Variables for Plugin Processing > .env
echo PLUGIN_SLUG=!PLUGIN_SLUG! >> .env
echo PLUGIN_NAME=!PLUGIN_NAME! >> .env
echo PLUGIN_VERSION=!PLUGIN_VERSION! >> .env
echo FUNCTION_PREFIX=!FUNCTION_PREFIX! >> .env
echo FUNCTION_PREFIX_LOWER=!LOWERCASE_PREFIX! >> .env
echo DESCRIPTION=!DESCRIPTION! >> .env
echo JSON_FILE=release.json >> .env
echo BACKUP_FILE=previous.json >> .env
echo CHANGELOG_FILE=C:\Users\Nathan\Git\rup-changelogs\!PLUGIN_SLUG!.txt >> .env
echo ZIP_FILE=!PLUGIN_SLUG!.zip >> .env
echo CHANGELOG_PATH=C:\Users\Nathan\Git\rup-changelogs\!PLUGIN_SLUG!.txt >> .env
echo RELEASE_URL=https://reallyusefulplugins.com/releases/!PLUGIN_SLUG!/release.html >> .env
echo BASE_URL=https://reallyusefulplugins.com/webhook/capture/X4T9eo87xX >> .env
echo POST_META=641 >> .env
echo REQUIRES_WP=5.0 >> .env
echo TESTED_WP=6.4 >> .env
echo REQUIRES_PHP=7.4 >> .env
echo AUTHOR=Your Name >> .env
echo AUTHOR_URI=https://yourwebsite.com >> .env
echo USER_AGENT=Mozilla/5.0 (Windows NT 10.0; Win64; x64) >> .env
echo CONTENT_TYPE=application/x-www-form-urlencoded >> .env

echo ✅ .env file has been created successfully!

:: ======================================
:: Extract plugin files
:: ======================================
set "BASE_ZIP=my-plugin.zip"
set "TEMP_DIR=tmp-plugin"
set "PLUGIN_DIR=!PLUGIN_SLUG!"

:: Clean up old extraction if exists
if exist "!TEMP_DIR!" rmdir /s /q "!TEMP_DIR!"

if not exist "!BASE_ZIP!" (
    echo ❌ Error: !BASE_ZIP! not found!
    pause
    exit /b
)

echo 📦 Extracting plugin files...
powershell -Command "Expand-Archive -Path '!BASE_ZIP!' -DestinationPath '!TEMP_DIR!' -Force"

:: Rename extracted folder if needed
if exist "!TEMP_DIR!\my-plugin" (
    ren "!TEMP_DIR!\my-plugin" "!PLUGIN_DIR!"
    move "!TEMP_DIR!\!PLUGIN_DIR!" . >nul
)
rmdir /s /q "!TEMP_DIR!"

:: Rename main plugin file
if exist "!PLUGIN_DIR!\my-plugin.php" ren "!PLUGIN_DIR!\my-plugin.php" "!PLUGIN_SLUG!.php"

:: ======================================
:: Run PHP script to modify plugin files
:: ======================================
echo 🛠️ Running PHP setup script...
php working-php\setup-plugin.php "!PLUGIN_DIR!" "!PLUGIN_NAME!" "!DESCRIPTION!" "!FUNCTION_PREFIX!" "!PLUGIN_SLUG_UNDERSCORES!" "!LOWERCASE_PREFIX!" "!PLUGIN_VERSION!"

IF %ERRORLEVEL% NEQ 0 (
    echo ❌ PHP script failed. Aborting.
    pause
    exit /b
)

:: ======================================
:: Log activity
:: ======================================
echo [%date% %time%] Setup completed for plugin: !PLUGIN_SLUG! >> setup-log.txt

echo 🎉 Plugin setup is complete! The updated plugin is located in !PLUGIN_DIR!.
pause
