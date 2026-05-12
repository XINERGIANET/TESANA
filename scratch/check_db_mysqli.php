<?php
$mysqli = new mysqli("127.0.0.1", "u109698536_tesana", "Tesana#2024", "u109698536_tesana");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

$sql = "SELECT id, name FROM clients WHERE document = '08804479'";
if ($result = $mysqli->query($sql)) {
    while ($row = $result->fetch_row()) {
        printf ("ID: %s Name: %s\n", $row[0], $row[1]);
        $client_id = $row[0];
        
        $sql2 = "SELECT id, date FROM attendances WHERE client_id = $client_id ORDER BY date DESC LIMIT 1";
        if ($res2 = $mysqli->query($sql2)) {
            $row2 = $res2->fetch_row();
            printf ("Last Attendance ID: %s Date: %s\n", $row2[0], $row2[1]);
        }
    }
    $result->free_result();
}

$mysqli->close();
