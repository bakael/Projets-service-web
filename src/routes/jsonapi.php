<?php
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Message\ResponseInterface as Response;

    require '../vendor/autoload.php';
   
    //tu y trouvera toutes les methodes

    
    function message ($code,$status,$message,$type=null,$object=null) {
        if ($object == null) {
            return array("code" => $code, "status" => $status, "message" => $message);
        } else {
            return array("code" => $code, "status" => $status, "message" => $message, $type => $object);
        }        
    }

    $app = new \Slim\App;
    /**
     * route - CREATE - add new neighbour - POST method
     */
    $app->post
    (
        '/api/voisin', 
        function (Request $request, Response $old_response) {
            try {
                $params = $request->getQueryParams();                
                $nom = $params['nom'];
                $adress = $params['adress'];
                $about = $params['about'];
                $tel = $params['tel'];

                $sql = "insert into voisins (nom,adress,about,tel) values (:nom,:adress,:about,:tel)";

                $db_access = new DBAccess ();
                $db_connection = $db_access->getConnection();

                $statement = $db_connection->prepare($sql);                
                $statement->bindParam(':nom', $nom);
                $statement->bindParam(':adress', $adress);
                $statement->bindParam(':about', $about);
                $statement->bindParam(':tel', $tel);
                $statement->execute();
                
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(200, 'OK', "The neighbour has been created successfully.")));
            } catch (Exception $exception) {
                
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(500, 'KO', $exception->getMessage())));
            }

            return $response;
        }
    );

    /**
     * route - READ - get neighbour by id - GET method
     */
    $app->get
    (
        '/api/voisin/{id}', 
        function (Request $request, Response $old_response) {
            try {
                $id = $request->getAttribute('id');                

                $sql = "select * from voisins where id = :id";

                $db_access = new DBAccess ();
                $db_connection = $db_access->getConnection();

                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();

                $statement = $db_connection->prepare($sql);
                $statement->execute(array(':id' => $id));
                if ($statement->rowCount()) {
                    $voisin = $statement->fetch(PDO::FETCH_OBJ);                    
                    $body->write(json_encode(message(200, 'OK', "Process Successed.", "voisin", $voisin)));
                }
                else
                {
                    $body->write(json_encode(message(513, 'KO', "The neighbour with id = '".$id."' has not been found or has already been deleted.")));
                }

                $db_access->releaseConnection();
            } catch (Exception $exception) {
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(500, 'KO', $exception->getMessage())));
            }
            
            return $response;
        }
    );

    /**
     * route - READ - get all neighbours - GET method
     */
    $app->get
    (
        '/api/voisins', 
        function (Request $request, Response $old_response) {
            try {
                $sql = "Select * From voisins";
                $db_access = new DBAccess ();
                $db_connection = $db_access->getConnection();
    
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();

                $statement = $db_connection->query($sql);
                if ($statement->rowCount()) {
                    $voisins = $statement->fetchAll(PDO::FETCH_OBJ);                    
                    $body->write(json_encode(message(200, 'OK', "Process Successed.", "voisins", $voisins)));
                } else {
                    $body->write(json_encode(message(512, 'KO', "No neighbour has been recorded yet.")));
                }

                $db_access->releaseConnection();
            } catch (Exception $exception) {
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(500, 'KO', $exception->getMessage())));
            }
    
            return $response;
        }
    );

    /**
     * route - UPDATE - update a neighbour by id - PUT method
     */
    $app->put
    (
        '/api/voisin/{id}', 
        function (Request $request, Response $old_response) {
            try {

                $id = $request->getAttribute('id');

                $params = $request->getQueryParams();
                $nom = $params['nom'];
                $adress = $params['adress'];
                $about = $params['about'];

                $sql = "update voisins set nom = :nom, adress = :adress, about = :about where id = :id";

                $db_access = new DBAccess ();
                $db_connection = $db_access->getConnection();

                $statement = $db_connection->prepare($sql);
                $statement->bindParam(':nom', $nom);
                $statement->bindParam(':adress', $adress);
                $statement->bindParam(':about', $about);
                $statement->bindParam(':id', $id);
                $statement->execute();

                $db_access->releaseConnection();

                $response = $old_response->withHeader('Content-Type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(200, 'OK', "The neighbour has been updated successfully.")));
            } catch (Exception $exception) {
                $response = $old_response->withHeader('Content-Type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(500, 'KO', $exception->getMessage())));
            }

            return $response;
        }
    );

    /**
     * route - DELETE - delete a neighbour by id - DELETE method
     */
    $app->delete
    (
        '/api/voisin/{id}', 
        function (Request $request, Response $old_response) {
            try {
                $id = $request->getAttribute('id');

                $sql = "delete from voisins where id = :id";

                $db_access = new DBAccess ();
                $db_connection = $db_access->getConnection();

                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();

                $statement = $db_connection->prepare($sql);
                $statement->execute(array(':id' => $id));

                $body->write(json_encode(message(200, 'OK', "The neighbour has been deleted successfully.")));
                $db_access->releaseConnection();
            } catch (Exception $exception) {
                $response = $old_response->withHeader('Content-type', 'application/json');
                $body = $response->getBody();
                $body->write(json_encode(message(500, 'KO', $exception->getMessage())));
            }

            return $response;
        }
    );

    $app->run();
?>