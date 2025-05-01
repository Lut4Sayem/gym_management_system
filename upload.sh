#!/bin/bash

# Navigate to the current project folder
cd "$(dirname "$0")" || exit

# Pull latest changes from GitHub before pushing
git pull --rebase origin main

# Automatically add .gitkeep in empty folders (if any)
for folder in */; do
    if [ -d "$folder" ] && [ -z "$(ls -A "$folder")" ]; then
        echo "📂 Empty folder detected: $folder"
        touch "$folder/.gitkeep"
    fi
done

# Add all new and modified files to Git
git add .

# Commit and push if there are new changes
if git diff --cached --quiet; then
    echo "⚠️ No new files or changes detected. Skipping commit."
else
    git commit -m "Group project upload - Updated gym_management_system"
    git push origin main
    echo "✅ Project uploaded successfully to GitHub!"
fi
