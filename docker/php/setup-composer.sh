#!/bin/bash
set -e

# If we experience "Unknown package has no name defined" try removing the ./vendor folder
composer -V -vvv || rm -rf vendor;

# Make sure we're using v2.2 of Composer as it's marked as LTS
composer self-update --2.2 || composer self-update --2 || composer self-update;
composer self-update --2.2;
