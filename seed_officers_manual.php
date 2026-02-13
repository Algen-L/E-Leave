<?php
// seed_officers_manual.php

$host = '127.0.0.1';
$db   = 'sdo-lt';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

$officers = [
    [
        'username' => 'cid_chief',
        'name' => 'CID Chief Officer',
        'full_name' => 'Juan Dela Cruz (CID)',
        'role' => 'cid_chief',
        'office_station' => 'CID Office',
        'position' => 'Chief Education Supervisor',
    ],
    [
        'username' => 'sgod_chief',
        'name' => 'SGOD Chief Officer',
        'full_name' => 'Maria Clara (SGOD)',
        'role' => 'sgod_chief',
        'office_station' => 'SGOD Office',
        'position' => 'Chief Education Supervisor',
    ],
    [
        'username' => 'ao_officer',
        'name' => 'Admin Officer',
        'full_name' => 'Jose Rizal (AO)',
        'role' => 'ao',
        'office_station' => 'Office of the SDS',
        'position' => 'Administrative Officer V',
    ],
    [
        'username' => 'asds_officer',
        'name' => 'ASDS Officer',
        'full_name' => 'Andres Bonifacio (ASDS)',
        'role' => 'asds',
        'office_station' => 'Office of the ASDS',
        'position' => 'Asst. Schools Division Superintendent',
    ],
    [
        'username' => 'sds_officer',
        'name' => 'SDS Officer',
        'full_name' => 'Gabriela Silang (SDS)',
        'role' => 'sds',
        'office_station' => 'Office of the SDS',
        'position' => 'Schools Division Superintendent',
    ],
];

$passwordHash = password_hash('password123', PASSWORD_BCRYPT);
$now = date('Y-m-d H:i:s');

$stmt = $pdo->prepare("INSERT INTO users (username, name, gmail, password, full_name, role, office_station, position, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

foreach ($officers as $officer) {
    // Check if exists
    $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $check->execute([$officer['username']]);
    if ($check->rowCount() > 0) {
        echo "User {$officer['username']} already exists. Skipping.\n";
        continue;
    }

    try {
        $stmt->execute([
            $officer['username'],
            $officer['name'],
            $officer['username'] . '@deped.gov.ph',
            $passwordHash,
            $officer['full_name'],
            $officer['role'],
            $officer['office_station'],
            $officer['position'],
            1, // is_active (true)
            $now,
            $now
        ]);
        echo "Created user: {$officer['username']}\n";
    } catch (PDOException $e) {
        echo "Error creating {$officer['username']}: " . $e->getMessage() . "\n";
    }
}

echo "Seeding completed manually.\n";
