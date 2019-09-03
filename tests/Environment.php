<?php

trait Environment
{
    private static $_loadedEnvVars = false;

    public function loadEnvVars()
    {
        if (self::$_loadedEnvVars) {
            return;
        }

        $env = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES);

        foreach ($env as $var) {
            putenv($var);
        }

        self::$_loadedEnvVars = true;
    }
}
