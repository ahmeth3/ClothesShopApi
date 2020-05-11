<?php

require_once './UserDbMethods.php';

function isTheseParametersAvailable($params)
{
    $available = true;
    $missingparams = "";
    foreach ($params as $param) {
        if (!isset($_POST[$param]) || strlen($_POST[$param]) <= 0) {
            $available = false;
            $missingparams = $missingparams . ", " . $param;
        }
    }
    if (!$available) {
        $response = array();
        $response['error'] = true;
        $response['message'] = 'Parameters ' . substr($missingparams, 1, strlen($missingparams)) . ' missing';
        echo json_encode($response);
        die();
    }
}

$response = array();
if (isset($_GET['apicall'])) {
    switch ($_GET['apicall']) {
        case 'createUser':
            isTheseParametersAvailable(array('email', 'password'));

            $db = new UserDbMethods();

            $result = $db->createUser(
                $_POST['email'],
                $_POST['password']
            );

            if ($result) {
                $response['error'] = false;
                $response['message'] = 'User added successflly';

                $response['users'] = $db->getUsers();
            } else {
                $response['error'] = true;
                $response['message'] = 'Some error occurred while creating new user';
            }
            break;

        case 'getUsers':
            $db = new UserDbMethods();

            $response['error'] = false;
            $response['message'] = 'Request successfully completed';
            $response['users'] = $db->getUsers();
            break;

        case 'updateUser':
            isTheseParametersAvailable(array('id', 'email', 'password'));

            $db = new UserDbMethods();

            $result = $db->updateUser(
                $_POST['id'],
                $_POST['email'],
                $_POST['password']
            );

            if ($result) {
                $response['error'] = false;
                $response['message'] = 'User updated successfully';
                $response['users'] = $db->getUsers();
            } else {
                $response['error'] = true;
                $response['message'] = 'Some error occurred while updating user';
            }

            break;

        case 'deleteUser':
            if (isset($_GET['id'])) {
                $db = new UserDbMethods();

                if ($db->deleteUser($_GET['id'])) {
                    $response['error'] = false;
                    $response['message'] = 'User deleted successfully';
                    $response['users'] = $db->getUsers();
                } else {
                    $response['error'] = true;
                    $response['message'] = 'Nothing to delete, provide a correct Id';
                }
            } else {
                $response['error'] = true;
                $response['message'] = 'There is no Id in url';
            }

            break;
    }
} else {
    $response['error'] = true;
    $response['message'] = 'Invalid API Call';
}
echo json_encode($response);
