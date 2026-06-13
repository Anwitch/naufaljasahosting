#!/usr/bin/env sh
set -eu

fail() {
    printf 'FAIL: %s\n' "$1" >&2
    exit 1
}

grep -q 'COPY portal\.html /var/www/html/index\.html' Dockerfile \
    || fail 'Dockerfile must serve the portal at /'

grep -q 'COPY portal\.html /var/www/html/portal\.html' Dockerfile \
    || fail 'Dockerfile must also serve the portal at /portal.html for back links'

grep -q "\$basePath = '/webgis_app';" webgis_app/core_config/settings.php \
    || fail 'Default APP_BASE_PATH must match the Docker route /webgis_app'

if grep -R "app_url('../portal\.html')" webgis_app/panel_admin webgis_app/panel_user >/dev/null; then
    fail 'Panel portal links must not use app_url() with ../portal.html'
fi

if grep -R "href=\"../portal\.html\"" webgis_app >/dev/null; then
    fail 'webgis_app portal links must use root_url("portal.html") instead of relative parent paths'
fi

printf 'Route consistency checks passed.\n'
