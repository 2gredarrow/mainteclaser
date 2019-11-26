<?php

$ip = $_SERVER['REMOTE_ADDR'];
$result = file_get_contents('http://ip-api.com/json/' . $ip, false, null);
$response = json_decode($result);

$address = "$response->city, $response->country, $response->countryCode, $response->zip, $response->regionName, $response->isp, $response->org";

$servername = "localhost";
$username = "root";
$password = "10calci.";

try {
    $conn = new PDO("mysql:host=$servername;dbname=maintec", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully";

    $sql = "Select hits as conto from ips where ip=:ipp";
    $sth = $conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->bindValue(':ipp', $ip);
    $sth->execute();
    $result = $sth->fetchColumn();
    if ($result == 0) {
        $sql = "insert into ips (ip,hits,location) values(:ipp,1,:addr)";
        $sth = $conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->bindValue(':ipp', $ip);
        $sth->bindValue(':addr', $address);
        $sth->execute();
    } else {
        $sql = "update ips set hits=:phit, visit_date=CURRENT_TIMESTAMP where ip=:ipp";
        $sth = $conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        $sth->bindValue(':phit', $result + 1);
        $sth->bindValue(':ipp', $ip);
        $sth->execute();
    }

} catch (PDOException $e) {
      //echo "Connection failed: " . $e->getMessage();
}
