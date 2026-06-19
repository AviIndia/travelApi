<?php

header("Content-Type: application/json");

require_once '../../middleware/auth.php';
require_once '../../config/database.php';
require_once '../../helpers/response.php';

$db = new Database();
$conn = $db->connect();

$data = json_decode(
    file_get_contents("php://input"),
    true
);

try{

    $conn->beginTransaction();

    $sql = $conn->prepare("
        INSERT INTO ai_trip_plans(
            user_id,
            from_location,
            destination,
            start_date,
            end_date,
            travelers,
            budget,
            generated_json
        )
        VALUES(
            ?,?,?,?,?,?,?,?
        )
    ");

    $sql->execute([
        $user->id,
        $data['from_location'],
        $data['destination'],
        $data['start_date'],
        $data['end_date'],
        $data['travelers'],
        $data['budget'],
        json_encode($data['generated_json'])
    ]);

    $trip_id = $conn->lastInsertId();

    if(isset($data['generated_json']['days'])){

        foreach($data['generated_json']['days'] as $day){

            $stmt = $conn->prepare("
                INSERT INTO ai_trip_days(
                    trip_id,
                    day_no,
                    title,
                    description,
                    weather_info,
                    hotel_name,
                    restaurant_name,
                    nearest_hospital,
                    nearest_station,
                    image
                )
                VALUES(
                    ?,?,?,?,?,?,?,?,?,?
                )
            ");

            $stmt->execute([
                $trip_id,
                $day['day_no'],
                $day['title'],
                $day['description'],
                $day['weather_info'] ?? '',
                $day['hotel_name'] ?? '',
                $day['restaurant_name'] ?? '',
                $day['nearest_hospital'] ?? '',
                $day['nearest_station'] ?? '',
                $day['image'] ?? ''
            ]);
        }

    }

    $conn->commit();

    sendResponse(
        true,
        "Trip Saved Successfully",
        [
            "trip_id"=>$trip_id
        ]
    );

}catch(Exception $e){

    $conn->rollBack();

    sendResponse(
        false,
        $e->getMessage()
    );
}