#!/bin/bash
set -e

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
DIST_DIR="$PROJECT_DIR/dist"
STAGE_DIR="$PROJECT_DIR/stage"

VERSION="1.2.20"
ZIP_NAME="dharma_starter-$VERSION.zip"

rm -rf "$DIST_DIR" "$STAGE_DIR"
mkdir -p "$DIST_DIR" "$STAGE_DIR"

echo "Building dharma_starter v$VERSION..."

# Копируем файлы шаблона
cp "$PROJECT_DIR/templateDetails.xml" "$STAGE_DIR/"
cp "$PROJECT_DIR/index.php" "$STAGE_DIR/"
cp "$PROJECT_DIR/error.php" "$STAGE_DIR/"
cp "$PROJECT_DIR/script.php" "$STAGE_DIR/"
cp "$PROJECT_DIR/joomla.asset.json" "$STAGE_DIR/"
cp "$PROJECT_DIR/README.md" "$STAGE_DIR/"
cp "$PROJECT_DIR/LICENSE.txt" "$STAGE_DIR/"

cp -R "$PROJECT_DIR/header" "$STAGE_DIR/"
cp -R "$PROJECT_DIR/html" "$STAGE_DIR/"
cp -R "$PROJECT_DIR/images" "$STAGE_DIR/"
cp -R "$PROJECT_DIR/language" "$STAGE_DIR/"
cp -R "$PROJECT_DIR/media" "$STAGE_DIR/"

# Удаляем системные файлы macOS
find "$STAGE_DIR" -name ".DS_Store" -delete

cd "$STAGE_DIR"
zip -r "$DIST_DIR/$ZIP_NAME" . -x "*.git*"

cd "$PROJECT_DIR"
rm -rf "$STAGE_DIR"

echo "Build complete: $DIST_DIR/$ZIP_NAME"
