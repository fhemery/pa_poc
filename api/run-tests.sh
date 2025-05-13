#!/bin/bash

# Colors for better output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Default values
WATCH_MODE=false
TEST_PATH="tests"
FILTER=""

# Parse command line arguments
while [[ $# -gt 0 ]]; do
  key="$1"
  case $key in
    -w|--watch)
      WATCH_MODE=true
      shift
      ;;
    -f|--filter)
      FILTER="--filter=$2"
      shift
      shift
      ;;
    *)
      TEST_PATH="$1"
      shift
      ;;
  esac
done

# Function to run the tests
run_tests() {
  echo -e "${YELLOW}Running tests: $TEST_PATH $FILTER${NC}"
  echo "-------------------------------------------"
  
  # Run the tests inside the Docker container
  if docker compose exec php-fpm bin/phpunit $FILTER $TEST_PATH; then
    echo -e "${GREEN}Tests passed successfully!${NC}"
    return 0
  else
    echo -e "${RED}Tests failed!${NC}"
    return 1
  fi
}

# Run tests once if not in watch mode
if [ "$WATCH_MODE" = false ]; then
  run_tests
  exit $?
fi

# Watch mode
echo -e "${YELLOW}Watching for changes in src/ and tests/ directories...${NC}"
echo -e "${YELLOW}Press Ctrl+C to stop watching.${NC}"

# Initial test run
run_tests

# Install inotify-tools if not available
if ! command -v inotifywait &> /dev/null; then
  echo -e "${YELLOW}inotify-tools is not installed. Please install it with:${NC}"
  echo "sudo apt-get install inotify-tools"
  exit 1
fi

# Watch for changes
while true; do
  inotifywait -r -e modify,create,delete ./www/src ./www/tests
  echo -e "\n${YELLOW}Changes detected, running tests...${NC}"
  run_tests
done
