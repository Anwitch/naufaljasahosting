#!/usr/bin/env sh
set -eu

fail() {
    printf 'FAIL: %s\n' "$1" >&2
    exit 1
}

for file in $(find . -name '*.php' -type f | sort); do
    first_bytes="$(LC_ALL=C od -An -tx1 -N3 "$file" | tr -d ' \n')"
    [ "$first_bytes" != "efbbbf" ] || fail "$file starts with a UTF-8 BOM"
done

for file in webgis_app/index.php webgis_app/login.php webgis_app/logout.php; do

    first_five="$(LC_ALL=C od -An -c -N5 "$file" | tr -d ' \n')"
    [ "$first_five" = "<?php" ] || fail "$file must start with <?php before any output"
done

printf 'PHP header safety checks passed.\n'
