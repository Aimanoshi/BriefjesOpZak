<?php

    // General variables
    $basePath = __DIR__ . '/../';

    require_once $basePath . '/vendor/autoload.php';

    $router = new \Bramus\Router\Router();

    $loader = new \Twig\Loader\FilesystemLoader($basePath . '/resources/templates');
    $twig = new \Twig\Environment($loader);

    $router->before('GET|POST', '/.*', function () {
        session_start();
    });

    $router->get('/', 'MessageController@home');

    $router->get('/login', 'AuthController@showLogin');
    $router->post('/login', 'AuthController@login');

    $router->get('/register', 'AuthController@showRegister');
    $router->post('/register', 'AuthController@register');


    //alle organistaie + hun detail // detail van all kanaal per organistaie + kunnen aboneren
    $router->get('/organisatie', 'MessageController@organisaties');
    $router->get('/organisatie/(\d+)/detail', 'MessageController@showOrganisatieDetail');
    $router->post('/organisatie/(\d+)/detail', 'MessageController@organisatieDetail');
    $router->get('/organisatie/kanaal/(\d+)', 'MessageController@kanaalDetail');
    $router->get('/organisatie/kanaal/(\d+)/aboneer', 'MessageController@aboneer');
    $router->get('/organisatie/kanaal/(\d+)/verwijder', 'MessageController@deleteKanaal');
    $router->get('/kanaal', 'MessageController@geabonneerdKanaal');
    $router->post('/kanaal', 'MessageController@verlaatKanaal');
    $router->get('/kanaal/(\d+)/berichten', 'MessageController@kanaalBerichten');


    //create organisatie + kanaal
    $router->get('/organisatie/create', 'MessageController@showOrganisatieCreate');
    $router->post('/organisatie/create', 'MessageController@organisatieCreate');
    $router->get('/organisatie/(\d+)/kanaal/create', 'MessageController@showKanaalCreate');
    $router->post('/organisatie/(\d+)/kanaal/create', 'MessageController@kanaalCreate');

    //add messages
    $router->get('/kanaal/(\d+)/voegbericht', 'MessageController@showVoegBerichten');
    $router->post('/kanaal/(\d+)/voegbericht', 'MessageController@voegBerichten');

    $router->get('/logout', 'AuthController@logout');

    //abonnees
    $router->get('/abonnees', 'MessageController@showAbonnees');
    $router->post('/abonnees', 'MessageController@abonnees');

    $router->run();