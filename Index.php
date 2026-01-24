<?php

use Src\User;
use Src\Server;

require_once 'Src/Server.php';
require_once 'Src/User.php';

$server = new Server();
$user = new User();

$body = json_decode(file_get_contents('php://input'), true);

switch ($server->getMethod()) {
    case 'GET':

        if (isset($body['id'])) {
            $responseData = $user->getUser($body['id']);
        } else {
            $responseData = $user->getUsers();
        }

        if (count($responseData) > 0) {
            $server->returnResponse(200, $responseData);
        } else {
            $server->returnResponse();
        }

        break;
    case 'POST':

        $name = $body['name'] ?? null;
        $password = $body['password'] ?? null;

        if (is_null($name) || is_null($password)) {
            $server->returnResponse(409, ['error' => 'Not Found Data']);
            break;
        }

        //saveUser Lê e cria usuario
        //Objeto caso crie e false caso duplicata
        $potentialUser = $user->saveUser($name, $password);

        if (!$potentialUser) {
            $server->returnResponse(400, ['error' => 'Error saving, user exist']);
            break;
        }

        $server->returnResponse(201, $potentialUser);

        break;
    case 'PUT':

        (int)$id = $body['id'] ?? null;
        $name = $body['name'] ?? null;
        $password = $body['password'] ?? null;

        if (is_null($name) || is_null($password) || is_null($id)) {
            $server->returnResponse(409, ['error' => 'Not Found Data']);
            break;
        }

        $potentialUser = $user->modifyUser($id, $name, $password);

        if (!$potentialUser) {
            $server->returnResponse(409, ['error' => 'Id Not Found']);
            break;
        }

        $server->returnResponse(200, $potentialUser);
        break;
    case 'PATCH':

        (int)$id = $body['id'] ?? null;
        $name = $body['name'] ?? null;
        $password = $body['password'] ?? null;

        if (is_null($name) || is_null($password) || is_null($id)) {
            $server->returnResponse(409, ['error' => 'Not Found Data']);
            break;
        }

        $potentialUser = $user->partialModifyUser($id, $name, $password);

        if (!$potentialUser) {
            $server->returnResponse(409, ['error' => 'Id Not Found']);
            break;
        }

        $server->returnResponse(200, $potentialUser);
        break;
    case 'DELETE':

        (int)$id = $body['id'] ?? null;

        if (is_null($id)) {
            $server->returnResponse(409, ['error' => 'Not Found Data']);
            break;
        }

        $userDeleted = $user->deleteUser($id);

        if (!$userDeleted) {
            $server->returnResponse(409, ['error' => 'Id Not Found']);
            break;
        }

        $server->returnResponse(200, $userDeleted);

        break;
}