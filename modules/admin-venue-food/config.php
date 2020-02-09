<?php

return [
    '__name' => 'admin-venue-food',
    '__version' => '0.0.3',
    '__git' => 'git@github.com:getmim/admin-venue-food.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/admin-venue-food' => ['install','update','remove'],
        'theme/admin/venue/food' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'admin' => NULL
            ],
            [
                'lib-form' => NULL
            ],
            [
                'lib-formatter' => NULL
            ],
            [
                'lib-pagination' => NULL
            ],
            [
                'venue-food' => NULL
            ],
            [
                'admin-venue' => NULL 
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'AdminVenueFood\\Controller' => [
                'type' => 'file',
                'base' => 'modules/admin-venue-food/controller'
            ]
        ],
        'files' => []
    ],
    'routes' => [
        'admin' => [
            'adminVenueFood' => [
                'path' => [
                    'value' => '/venue/food'
                ],
                'method' => 'GET',
                'handler' => 'AdminVenueFood\\Controller\\Food::index'
            ],
            'adminVenueFoodEdit' => [
                'path' => [
                    'value' => '/venue/food/(:id)',
                    'params' => [
                        'id'  => 'number'
                    ]
                ],
                'method' => 'GET|POST',
                'handler' => 'AdminVenueFood\\Controller\\Food::edit'
            ],
            'adminVenueFoodRemove' => [
                'path' => [
                    'value' => '/venue/food/(:id)/remove',
                    'params' => [
                        'id'  => 'number'
                    ]
                ],
                'method' => 'GET',
                'handler' => 'AdminVenueFood\\Controller\\Food::remove'
            ]
        ]
    ],
    'adminUi' => [
        'sidebarMenu' => [
            'items' => [
                'venue' => [
                    'label' => 'Venue',
                    'icon' => '<i class="fas fa-map-marker-alt"></i>',
                    'priority' => 0,
                    'children' => [
                        'food' => [
                            'label' => 'Food',
                            'icon'  => '<i></i>',
                            'route' => ['adminVenueFood'],
                            'perms' => 'manage_venue_food'
                        ]
                    ]
                ]
            ]
        ]
    ],
    'libForm' => [
        'forms' => [
            'admin.venue.edit' => [
                'food' => [
                    'label' => 'Food',
                    'type' => 'checkbox-group',
                    'rules' => []
                ]
            ],
            'admin.venue-food.edit' => [
                'name' => [
                    'label' => 'Name',
                    'type' => 'text',
                    'rules' => [
                        'required' => true,
                        'unique' => [
                            'model' => 'VenueFood\\Model\\VenueFood',
                            'field' => 'name',
                            'self' => [
                                'service' => 'req.param.id',
                                'field' => 'id'
                            ]
                        ]
                    ]
                ]
            ],
            'admin.venue-food.index' => [
                'q' => [
                    'label' => 'Search',
                    'type' => 'search',
                    'nolabel' => true,
                    'rules' => []
                ]
            ]
        ]
    ]
];