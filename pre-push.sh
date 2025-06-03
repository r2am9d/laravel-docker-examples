#!/bin/bash

# Exit on error
set -e

echo "🔧 Installing dev dependencies..."
npm install --save-dev commitizen @commitlint/cli @commitlint/config-conventional husky

echo "📦 Setting up commitizen in package.json..."
npx json -I -f package.json -e '
  this.config = this.config || {};
  this.config.commitizen = {
    "path": "cz-conventional-changelog"
  };
  this.scripts = this.scripts || {};
  this.scripts.commit = "cz";
  this.scripts.prepare = "husky install";
'

echo "📝 Creating commitlint.config.cjs..."
cat <<EOF > commitlint.config.cjs
module.exports = {
  extends: ['@commitlint/config-conventional'],
};
EOF

echo "🔧 Setting up Husky manually..."

# Install husky and set up Git hooks
npx husky install

# Ensure prepare script is in package.json
npx json -I -f package.json -e '
  this.scripts = this.scripts || {};
  this.scripts.prepare = "husky install";
'

# Create .husky/pre-push manually
mkdir -p .husky

cat <<'EOF' > .husky/pre-push
#!/bin/sh
. "$(dirname "$0")/_/husky.sh"

# Run commitlint against all commits being pushed
branch=$(git rev-parse --abbrev-ref HEAD)
remote=${1:-origin}

commits=$(git log ${remote}/${branch}..HEAD --pretty=format:"%s")

echo "🔍 Checking commits to be pushed..."

for commit in $commits; do
  echo "⏳ Validating: \"$commit\""
  echo "$commit" | npx commitlint --color --edit - || {
    echo "❌ Commit \"$commit\" is invalid. Push rejected."
    exit 1
  }
done

echo "✅ All commit messages are valid."
EOF

chmod +x .husky/pre-push

echo "✅ Conventional commit enforcement is now set up via pre-push hook."
echo "Use 'npm run commit' for guided commits."
