<?php
$db = new PDO('sqlite:' . __DIR__ . '/../database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$migration = '2026_05_17_122629_create_safe_currencies_table';
$batch = 21;
$insert = $db->prepare('INSERT INTO migrations (migration, batch) VALUES (:migration, :batch)');
$insert->execute([':migration' => $migration, ':batch' => $batch]);
echo "Inserted migration: $migration with batch $batch\n";
