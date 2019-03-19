<?php
$connection = 'mysql:host=localhost;dbname=test;charset=utf8mb4';
$pdo = new PDO($connection, 'root', '');

if ('POST' === $_SERVER['REQUEST_METHOD']) {
    $data = json_decode(file_get_contents("php://input"), true);
    if (null === $data) {
        die("Input data corrupt");
    }
    $pdo->prepare('INSERT DELAYED INTO counters 
      (`id`, `country`, `event`, `date`, `counter`) 
      VALUES (null, :country, :event, DATE(NOW()), 1) ON DUPLICATE KEY UPDATE `counter` = `counter` + 1')
        ->execute([
            'country' => $data['country'],
            'event' => $data['event']
        ]);
    echo "OK";
} elseif ('GET' === $_SERVER['REQUEST_METHOD']) {
    $result = $pdo->query("
    SELECT 
        `date`,
        c.country, 
        CONCAT(event, 's') as event, 
        SUM(counter) as counter
    FROM counters AS  c
    RIGHT JOIN (
	     SELECT country
	     FROM counters
	     GROUP BY country
	     ORDER BY sum(counter) DESC
	     LIMIT 5
	   ) AS top_country ON c.country = top_country.country
    WHERE `date` >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY c.country, event, `date`");

    if ('json' === $_GET['format']) {
        header('Content-Type: application/json');
        echo json_encode($result->fetchAll(PDO::FETCH_ASSOC));
    } elseif ('csv' === $_GET['format']) {
        header('Content-Type: text/csv');
        $stream = fopen('php://output', 'w');
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($stream, $row);
        }
        fclose($stream);
    }
}