<?php
// config/routes.php
return [
    // Страницы (GET)
    [
        'methods' => ['GET'],
        'uri' => '/',
        'handler' => 'PageController@home'
    ],
    [
        'methods' => ['GET'],
        'uri' => '/login',
        'handler' => 'AuthController@showLoginForm'
    ],
    [
        'methods' => ['GET'],
        'uri' => '/admin',
        'handler' => 'PageController@admin'
    ],
    [
        'methods' => ['GET'],
        'uri' => '/dashboard',
        'handler' => 'PageController@dashboard'
    ],
    
    // API маршруты
    [
        'methods' => ['GET'],
        'uri' => '/api/data',
        'handler' => 'ApiController@getAllData'
    ],
    [
        'methods' => ['GET'],
        'uri' => '/api/data/{id}',
        'handler' => 'ApiController@getData'
    ],
    [
        'methods' => ['POST'],
        'uri' => '/api/data',
        'handler' => 'ApiController@createData'
    ],
    [
        'methods' => ['PUT'],
        'uri' => '/api/data/{id}',
        'handler' => 'ApiController@updateData'
    ],
    [
        'methods' => ['DELETE'],
        'uri' => '/api/data/{id}',
        'handler' => 'ApiController@deleteData'
    ],
    
    // Авторизация (оба метода)
    [
        'methods' => ['GET', 'POST'],
        'uri' => '/auth/login',
        'handler' => 'AuthController@login'
    ],
    [
        'methods' => ['GET', 'POST'],
        'uri' => '/auth/logout',
        'handler' => 'AuthController@logout'
    ],
    
    // Пример с параметрами
    [
        'methods' => ['GET'],
        'uri' => '/user/{id}/profile',
        'handler' => 'PageController@userProfile'
    ]
];
?>