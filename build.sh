#!/bin/bash
set -e

PROJECT_DIR="$(cd "$(dirname "$0")" && pwd)"
PHPDESKTOP_DIR="$PROJECT_DIR/phpdesktop"
DIST_DIR="$PROJECT_DIR/dist"
APP="LiasLaci"

echo ""
echo "  ╔══════════════════════════════════════╗"
echo "  ║  Build Lia's Laci → PHP Desktop      ║"
echo "  ╚══════════════════════════════════════╝"
echo ""

if [ ! -d "$PHPDESKTOP_DIR" ]; then
    echo "  [✗] Folder phpdesktop/ belum ada."
    echo "  Download: https://github.com/cztomczak/phpdesktop/releases"
    exit 1
fi

EXE=$(find "$PHPDESKTOP_DIR" -maxdepth 1 -name "phpdesktop-chrome*.exe" 2>/dev/null | head -1)
if [ -z "$EXE" ]; then echo "  [✗] phpdesktop-chrome.exe tidak ditemukan"; exit 1; fi
echo "  [✓] PHP Desktop ditemukan"

rm -rf "$DIST_DIR/$APP"
mkdir -p "$DIST_DIR/$APP"
cp -r "$PHPDESKTOP_DIR/"* "$DIST_DIR/$APP/"
cp "$PROJECT_DIR/phpdesktop-settings.json" "$DIST_DIR/$APP/settings.json"
echo "  [✓] PHP Desktop base + settings disalin"

rm -rf "$DIST_DIR/$APP/www"
mkdir -p "$DIST_DIR/$APP/www"
cp -r "$PROJECT_DIR/config"      "$DIST_DIR/$APP/www/config"
cp -r "$PROJECT_DIR/controllers" "$DIST_DIR/$APP/www/controllers"
cp -r "$PROJECT_DIR/models"      "$DIST_DIR/$APP/www/models"
cp -r "$PROJECT_DIR/views"       "$DIST_DIR/$APP/www/views"
cp -r "$PROJECT_DIR/public/"*    "$DIST_DIR/$APP/www/"
mkdir -p "$DIST_DIR/$APP/www/database" "$DIST_DIR/$APP/www/uploads"
echo "  [✓] Aplikasi disalin ke www/"

echo "  [~] Fix path..."
for f in "$DIST_DIR/$APP/www/index.php" "$DIST_DIR/$APP/www/api.php" "$DIST_DIR/$APP/www/struk.php"; do
    [ -f "$f" ] && sed -i "s|__DIR__ \. '/\.\./|__DIR__ . '/|g;s|__DIR__ \. \"/\.\./|__DIR__ . \"/|g" "$f"
done
echo "  [✓] Path disesuaikan"

mv "$DIST_DIR/$APP/phpdesktop-chrome.exe" "$DIST_DIR/$APP/$APP.exe"
echo "  [✓] Exe di-rename → $APP.exe"

echo ""
echo "  ══════════════════════════════════════"
echo "  BUILD SELESAI!"
echo "  ══════════════════════════════════════"
echo "  Output: $DIST_DIR/$APP/"
echo "  Distribusi: cd dist && zip -r $APP.zip $APP/"
echo ""
