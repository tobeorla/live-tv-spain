#!/bin/bash
set -x # Debug
set -e # Exit with nonzero exit code if anything fails

if [ "$TRAVIS_EVENT_TYPE" != "cron" ] && [ "$TRAVIS_EVENT_TYPE" != "pull_request" ]; then
    echo "Not Cron|PR. Skipping deploy..."
    exit 0
fi


# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

git config --global user.email "$GH_USER_EMAIL"
git config --global user.name "$GH_USER_NAME"

set +x
git remote set-url origin https://tobeorla:$GH_TOKEN@github.com/tobeorla/live-tv-spain
set -x

## UPLOAD NEW HOSTS
git checkout $TRAVIS_BRANCH
git add README.md
git add tv-spain.json
git commit -m "Update Status: $(date +%d-%m-%Y)"

git push origin $TRAVIS_BRANCH 2>/dev/null

