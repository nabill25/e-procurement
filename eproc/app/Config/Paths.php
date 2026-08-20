<?php

namespace Config;

/**
 * -----------------------------------------------------------------------
 * SYSTEM DIRECTORY NAME
 * -----------------------------------------------------------------------
 * This variable must contain the name of your "system" directory. Include
 * the path if the directory is not in the same directory as this file.
 */
class Paths
{
    /**
     * -------------------------------------------------------------------
     * SYSTEM DIRECTORY NAME
     * -------------------------------------------------------------------
     * If you want this front controller to use a different "system"
     * directory than the default one you can set its name here. The
     * directory name, as well as the path to it can be changed here.
     */
    public string $systemDirectory = __DIR__ . '/../../vendor/codeigniter4/framework/system';

    /**
     * -------------------------------------------------------------------
     * APPLICATION DIRECTORY NAME
     * -------------------------------------------------------------------
     * If you want this front controller to use a different "application"
     * directory than the default one you can set its name here. The
     * directory name, as well as the path to it can be changed here.
     */
    public string $appDirectory = __DIR__ . '/..';

    /**
     * -------------------------------------------------------------------
     * WRITABLE DIRECTORY NAME
     * -------------------------------------------------------------------
     * If you want this front controller to use a different writable
     * directory than the default one you can set its name here.
     */
    public string $writableDirectory = __DIR__ . '/../../writable';

    /**
     * -------------------------------------------------------------------
     * TESTS DIRECTORY NAME
     * -------------------------------------------------------------------
     * If you want this front controller to use a different "tests"
     * directory than the default one you can set its name here.
     */
    public string $testsDirectory = __DIR__ . '/../../tests';

    /**
     * -------------------------------------------------------------------
     * VIEW DIRECTORY NAME
     * -------------------------------------------------------------------
     * If you want to change the default view directory,
     * you may do so here.
     */
    public string $viewDirectory = __DIR__ . '/../Views';
}
