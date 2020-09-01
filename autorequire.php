<?php /** @noinspection PhpIncludeInspection */

function autorequire($dir) {
    $files = glob($dir . '/*.php');

    foreach ($files as $file) {
        require_once($file);
    }
}

require_once 'application/models/Entity.php';
require_once 'application/repositories/CommonRepository.php';
require_once 'application/controllers/controller_helper.php';

autorequire('application');
autorequire('application/controllers');
autorequire('application/models');
autorequire('application/repositories');
autorequire('application/services');
autorequire('application/utils');