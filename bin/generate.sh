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
#
# clean up
#
rm -rf master.zip
rm -rf _s_to_wp_theme-master
rm -rf ${THEME_SLUG}.zip
rm -rf ${THEME_SLUG}/bin

cd ${THEME}

perl -pi -e "s/THEME_SLUG/${THEME_SLUG}/g" $(grep -rl THEME_SLUG)
perl -pi -e "s/THEME_NAME/${THEME_NAME}/g" $(grep -rl THEME_NAME)

