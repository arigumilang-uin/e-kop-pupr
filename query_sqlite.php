<?php
$db = new PDO('sqlite:' . __DIR__ . '/database/database.sqlite');
$stmt = $db->query("SELECT * FROM pengaturan_khusus");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
