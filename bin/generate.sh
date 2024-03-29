#!/bin/sh
if [ $# -ne 2 ]; then
    echo "Usage: $0 name slug"
    exit 1
fi

THEME_NAME=$1
THEME_SLUG=$2

THEME=${PWD}/${THEME_SLUG}
ASSETS=${PWD}/${THEME_SLUG}/assets

#
# get "_s" theme
#
curl https://underscores.me/ -d "underscoresme_name=$1&underscoresme_slug=${THEME_SLUG}&underscoresme_sass=1&underscoresme_generate=1" -o ${THEME_SLUG}.zip
unzip -o ${THEME_SLUG}.zip
rm ${THEME_SLUG}.zip
rm ${THEME}/composer.json
#
# SASS
#
mkdir -p ${ASSETS}/sass/frontend/
mv ${THEME}/sass ${ASSETS}/sass/frontend/_s
#
# script
#
mkdir -p ${ASSETS}/scripts/frontend
mv ${THEME}/js/navigation.js ${ASSETS}/scripts/frontend/
mkdir -p ${ASSETS}/scripts/admin
mv ${THEME}/js/customizer.js ${THEME}/assets/scripts/admin
rm -rf ${THEME}/js
#
# https://github.com/iworks/_s_to_wp_theme/archive/refs/heads/master.zip
#
wget https://github.com/iworks/_s_to_wp_theme/archive/refs/heads/master.zip

unzip -o master.zip
cp -r _s_to_wp_theme-master/* ${THEME}
cp -r _s_to_wp_theme-master/.gitignore ${THEME}
#
# clean up
#
rm -rf master.zip
rm -rf _s_to_wp_theme-master
rm -rf ${THEME_SLUG}.zip
mv ${THEME_SLUG}/bin/.eslintrc ${THEME_SLUG}/
rm -rf ${THEME_SLUG}/bin

cd ${THEME}

perl -pi -e "s/THEME_SLUG/${THEME_SLUG}/g" $(grep -rl THEME_SLUG)
perl -pi -e "s/THEME_NAME/${THEME_NAME}/g" $(grep -rl THEME_NAME)

STYLE=assets/sass/frontend/_s/style.scss

perl -pi -e 's/^Theme Name.+$/Theme Name: THEME_NAME/' ${STYLE}
perl -pi -e 's/^Theme URI.+$/Theme URI: THEME_URI/' ${STYLE}
perl -pi -e 's/^Author:.+$/Author: THEME_AUTHOR_NAME/' ${STYLE}
perl -pi -e 's/^Author URI:.+$/Author URI: THEME_AUTHOR_URI/' ${STYLE}
perl -pi -e 's/^Description:.+$/Description: THEME_DESCRIPTION/' ${STYLE}
perl -pi -e 's/^Version:.+$/Version: THEME_VERSION.BUILDTIMESTAMP/' ${STYLE}
perl -pi -e 's/^Tested up to:.+$/Tested up to: THEME_TESTED_WORDPRESS/' ${STYLE}
perl -pi -e 's/^Requires PHP:.+$/Requires: THEME_REQUIRES_PHP/' ${STYLE}
perl -pi -e 's/^Text Domain:.+$/Text Domain: THEME_NAME/' ${STYLE}
perl -pi -e 's/^Tags:.+$/Tags: THEME_TAGS/' ${STYLE}
#perl -pi -e 's/^.+$/: THEME_/' ${STYLE}

