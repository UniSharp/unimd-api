<?php

if (!class_exists('JWTAuth')) {
    class_alias('Tymon\JWTAuth\Facades\JWTAuth', 'JWTAuth');
}

if (!class_exists('JWTFactory')) {
    class_alias('Tymon\JWTAuth\Facades\JWTFactory', 'JWTFactory');
}
