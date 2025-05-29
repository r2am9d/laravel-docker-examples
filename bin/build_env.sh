#!/usr/bin/env bash

uid="$(id -u)"
gid="$(id -g)"

# Replace UID
sed "s/UID=1000/UID=$uid/" .env.example > .env

# Replace GID, use platform check for macOS/Linux
if [[ "$OSTYPE" == "darwin"* ]]; then
  sed -i "" "s/GID=1000/GID=$gid/" .env
else
  sed -i "s/GID=1000/GID=$gid/" .env
fi
