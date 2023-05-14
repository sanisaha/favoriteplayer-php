<?php include 'DBConnect.php';
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
// echo phpinfo();

// echo 'testing our local api';
$objectDB = new DbConnect;
$connection = $objectDB->connect();
// var_dump($connection);

//reads the raw data from request body

print_r(file_get_contents('php://input'));

$player = file_get_contents('php://input');
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case "POST":
    $player = json_decode(file_get_contents('php://input'));
    $sql = "INSERT INTO players(id, name, position, club) VALUES(null, :name, :position, :club)";
    $stmt = $connection->prepare($sql);
    $stmt->bindParam(':name', $player->name);
    $stmt->bindParam(':position', $player->position);
    $stmt->bindParam(':club', $player->club);

    if ($stmt->execute()) {
    $response = ['status' => 1, 'message'=>'Record created successfully'];
    } else {
    $response = ['status'=> 0, 'message'=> 'Failed to create record'];
    }
    echo json_encode($response) ;
    break;

    case 'GET':
        $sql = "SELECT * FROM players";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if((isset($path[3])) && is_numeric($path[3])){
            $sql .= " WHERE id = :id";
            $stmt = $connection->prepare($sql);
            $stmt->bindParam(':id', $path[3]);
            $stmt->execute();
            $players = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

                
        echo json_encode($players);
        break;

    
    

        case "PUT": 
            $player = json_decode( file_get_contents('php://input') );
            $sql = "UPDATE players SET name= :name, position =:position, club =:club WHERE id = :id";
            $stmt = $connection->prepare($sql);
            $stmt->bindParam(':id', $player->id);
            $stmt->bindParam(':name', $player->name);
            $stmt->bindParam(':position', $player->position);
            $stmt->bindParam(':club', $player->club);

            if($stmt->execute()) {
                $response = ['status' => 1, 'message' => 'Record updated successfully.'];
            } else {
                $response = ['status' => 0, 'message' => 'Failed to update record.'];
            }
            echo json_encode($response);
            break;

        case "DELETE": 
            $sql = "DELETE FROM players WHERE id = :id";
            $path = explode('/', $_SERVER['REQUEST_URI']);
    
            $stmt = $connection->prepare($sql);
            $stmt->bindParam(':id', $path[3]);
            if($stmt->execute()) {
                $response = ['status' => 1, 'message' => 'Record deleted successfully.'];
            } else {
                $response = ['status' => 0, 'message' => 'Failed to delete record.'];
            }
            echo json_encode($response);
            break;

   }
?>


