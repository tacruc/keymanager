<?php
/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\KeyManager\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
	   ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
	   ['name' => 'key#public_server_key', 'url' => '/{filename}.asc', 'verb' => 'GET'],
	   ['name' => 'key#index', 'url' => '/keys', 'verb' => 'GET'],
	   ['name' => 'key#show', 'url' => '/keys/{id}', 'verb' => 'GET'],
	   ['name' => 'key#create', 'url' => '/keys', 'verb' => 'POST'],
	   ['name' => 'key#delete', 'url' => '/keys/{id}', 'verb' => 'DELETE'],
	   ['name' => 'key#revoke', 'url' => '/keys/{id}/revoke', 'verb' => 'POST'],
	   ['name' => 'key#generate_revoke_certificate', 'url' => '/keys/{id}/revoke', 'verb' => 'GET'],
	   ['name' => 'key#set_default', 'url' => '/keys/default/{id}', 'verb' => 'GET']

    ]
];
