<?php

function getControllers($container, array $names) {
    foreach ($names as $name) {
        $container[$name] = function($container) use($name) {
            $namespace = "App\\Controllers\\$name";
            return new $namespace($container);
        };
    }

    return $container;
}