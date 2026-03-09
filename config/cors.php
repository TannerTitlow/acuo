<?php

return [

  'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],
  'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:5173')],
  'allowed_methods' => ['*'],
  'allowed_headers' => ['*'],
  'supports_credentials' => true,  // critical — must be true for cookie auth

];
