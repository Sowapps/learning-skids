#!/usr/bin/env bash

# Call with dot => . ./scripts/debug.sh

# Change working directory to this file's folder
cd "$(dirname "$0")" || exit

export XDEBUG_MODE=debug
export XDEBUG_SESSION=1
export XDEBUG_CONFIG="client_host=192.168.0.42 discover_client_host=no"

echo "XDEBUG_CONFIG => $XDEBUG_CONFIG";
echo "XDEBUG Session enabled";

#xdebug.mode = debug
#xdebug.start_with_request = trigger
#xdebug.discover_client_host = true
#xdebug.client_port = 9003
#xdebug.idekey = PHPSTORM_DEV

