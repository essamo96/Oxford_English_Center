<?php
$json = json_decode(file_get_contents('image_migration_report.json'), true);
$updates = $json['paths_needing_update'];
$code = "<?php\n\$updates = [\n";
foreach ($updates as $u) {
    $code .= "    ['table' => '{$u['table']}', 'id' => {$u['record_id']}, 'column' => '{$u['column']}', 'value' => '{$u['new_db_value']}'],\n";
}
$code .= "];\n";
file_put_contents('updates_array.php', $code);
echo "Done";
