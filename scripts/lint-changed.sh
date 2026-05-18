#!/bin/zsh

# lint-changed.sh: Lint changed files, then run the fast PHPStan profile.
# Usage: ./scripts/lint-changed.sh [--staged|--committed]

set -euo pipefail

MODE="committed"
if [[ $# -ge 1 && "$1" == "--staged" ]]; then
  MODE="staged"
fi

if [[ "$MODE" == "staged" ]]; then
  CHANGED_FILES=($(git diff --cached --name-only))
else
  CHANGED_FILES=($(git diff --name-only HEAD))
fi

PHP_FILES=()
PHPSTAN_SOURCE_FILES=()
PHPSTAN_TEST_FILES=()
JS_FILES=()
PRETTIER_FILES=()
PHPSTAN_MAX_CHANGED_FILES=${PHPSTAN_MAX_CHANGED_FILES:-25}

for file in "${CHANGED_FILES[@]}"; do
  if [[ ! -f $file ]]; then
    continue
  fi
  if [[ $file == *.php ]]; then
    PHP_FILES+="$file"

    if [[ $file == tests/* || $file == packages/*/tests/* ]]; then
      PHPSTAN_TEST_FILES+="$file"
    else
      PHPSTAN_SOURCE_FILES+="$file"
    fi
  fi
  if [[ $file == *.js || $file == *.jsx || $file == *.ts || $file == *.tsx ]]; then
    JS_FILES+="$file"
  fi
  if [[ $file == *.js || $file == *.jsx || $file == *.ts || $file == *.tsx || $file == *.css || $file == *.json || $file == *.yml || $file == *.md || $file == *.blade.php ]]; then
    PRETTIER_FILES+="$file"
  fi
done

if [[ ${#PHP_FILES[@]} -gt 0 ]]; then
  echo "Running Pint on changed PHP files..."
  ./vendor/bin/pint --parallel "${PHP_FILES[@]}"
else
  echo "No changed PHP files for Pint."
fi

if [[ ${#PRETTIER_FILES[@]} -gt 0 ]]; then
  echo "Running Prettier on changed files..."
  npx prettier --write "${PRETTIER_FILES[@]}"
else
  echo "No changed files for Prettier."
fi

if [[ ${#JS_FILES[@]} -gt 0 ]]; then
  echo "Running ESLint on changed JS/TS files..."
  npx eslint "${JS_FILES[@]}" --max-warnings=0
else
  echo "No changed JS/TS files for ESLint."
fi

if [[ ${#PHP_FILES[@]} -eq 0 ]]; then
  echo "No changed PHP files for PHPStan."
elif [[ ${#PHP_FILES[@]} -gt $PHPSTAN_MAX_CHANGED_FILES ]]; then
  echo "Running PHPStan profiles for ${#PHP_FILES[@]} changed PHP files..."

  if [[ ${#PHPSTAN_SOURCE_FILES[@]} -gt 0 ]]; then
    ./vendor/bin/phpstan analyse --memory-limit=-1 --configuration=phpstan.fast.neon
  fi

  if [[ ${#PHPSTAN_TEST_FILES[@]} -gt 0 ]]; then
    ./vendor/bin/phpstan analyse --memory-limit=-1 --configuration=phpstan.tests.neon
  fi
else
  echo "Running PHPStan on changed PHP files..."
  ./vendor/bin/phpstan analyse --memory-limit=-1 --configuration=phpstan.fast.neon "${PHP_FILES[@]}"
fi

echo "Lint/preflight complete."
