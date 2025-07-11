#!/usr/bin/env php
<?php

use App\Kernel;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use App\WebSocket\WebSocketServer;
use Symfony\Component\Dotenv\Dotenv;
use App\WebSocket\Router\MessageRouter;
use App\Security\WebSocketAuthenticator;
use App\WebSocket\Connection\ConnectionRegistry;

use React\Socket\SocketServer;
use React\Socket\SecureServer;
use React\EventLoop\Loop;

// Configuration du serveur WebSocket sécurisé (wss://)
require dirname(__DIR__) . '/vendor/autoload.php';

// Chargement manuel des variables d'environnement si non définies
if (!isset($_SERVER['APP_ENV'])) {
    (new Dotenv())->loadEnv(dirname(__DIR__) . '/.env');
}

// Afficher DATABASE_URL pour vérifier qu'elle est correctement chargée
echo "🎯 DATABASE_URL = " . $_ENV['DATABASE_URL'] . "\n";

$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'dev', (bool) ($_SERVER['APP_DEBUG'] ?? true));
$kernel->boot();

$container = $kernel->getContainer();

// Vérifier que le conteneur est correctement initialisé
echo "🔹 Conteneur initialisé\n";

try {
    $em = $container->get('doctrine')->getManager();
    echo "🔹 EntityManager obtenu avec succès\n";

    // Tester la connexion à la base de données en exécutant une requête simple
    $em->getConnection()->executeQuery('SELECT 1');
    echo "🔹 Connexion à la base de données réussie\n";
} catch (\Exception $e) {
    echo "❌ Erreur lors de la connexion à la base de données : " . $e->getMessage() . "\n";
    exit(1);
}

// Récupération des services nécessaires à WebSocketServer
$authenticator = $container->get(WebSocketAuthenticator::class);
$router = $container->get(MessageRouter::class);
$registry = $container->get(ConnectionRegistry::class);

// Configuration du serveur WebSocket sécurisé (wss://)
$loop = Loop::get();

$socket = new SocketServer('0.0.0.0:8080', [], $loop);
$secureSocket = new SecureServer($socket, $loop, [
    'local_cert' => __DIR__ . '/../certs/cert.pem',
    'local_pk' => __DIR__ . '/../certs/key.pem',
    'allow_self_signed' => true,
    'verify_peer' => false,
]);

$httpServer = new HttpServer(
    new WsServer(
        new WebSocketServer($router, $authenticator, $registry)
    )
);

$server = new IoServer($httpServer, $secureSocket, $loop);
echo "🧠 WebSocket Server en écoute sur wss://localhost/ws/\n";
$server->run();






// Configuration du serveur WebSocket sans SSL (ws://)
// use App\Kernel;
// use Ratchet\Http\HttpServer;
// use Ratchet\Server\IoServer;
// use Ratchet\WebSocket\WsServer;
// use App\WebSocket\WebSocketServer;
// use Symfony\Component\Dotenv\Dotenv;
// use App\WebSocket\Router\MessageRouter;
// use App\Security\WebSocketAuthenticator;
// use App\WebSocket\Connection\ConnectionRegistry;

// require dirname(__DIR__) . '/vendor/autoload.php';

// // Chargement manuel des variables d'environnement si non définies
// if (!isset($_SERVER['APP_ENV'])) {
//     (new Dotenv())->loadEnv(dirname(__DIR__) . '/.env');
// }

// // Afficher DATABASE_URL pour vérifier qu'elle est correctement chargée
// echo "🎯 DATABASE_URL = " . $_ENV['DATABASE_URL'] . "\n";

// $kernel = new Kernel($_SERVER['APP_ENV'] ?? 'dev', (bool) ($_SERVER['APP_DEBUG'] ?? true));
// $kernel->boot();

// $container = $kernel->getContainer();

// // Vérifier que le conteneur est correctement initialisé
// echo "🔹 Conteneur initialisé\n";

// try {
//     $em = $container->get('doctrine')->getManager();
//     echo "🔹 EntityManager obtenu avec succès\n";

//     // Tester la connexion à la base de données en exécutant une requête simple
//     $em->getConnection()->executeQuery('SELECT 1');
//     echo "🔹 Connexion à la base de données réussie\n";
// } catch (\Exception $e) {
//     echo "❌ Erreur lors de la connexion à la base de données : " . $e->getMessage() . "\n";
//     exit(1);
// }

// // Récupération des services nécessaires à WebSocketServer
// $authenticator = $container->get(WebSocketAuthenticator::class);
// $router = $container->get(MessageRouter::class);
// $registry = $container->get(ConnectionRegistry::class);


// $server = IoServer::factory(
//     new HttpServer(
//         new WsServer(
//             new WebSocketServer($router, $authenticator, $registry)
//         )
//     ),
//     8080 // port WebSocket
// );

// echo "🧠 WebSocket Server en écoute sur wss://localhost/ws/\n";
// $server->run();
