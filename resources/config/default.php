<?php

return function (CM_Config_Node $config) {
    $config->Harvest_Api_Client->account = 'cargomedia';
    $config->Harvest_Api_Client->email = 'hello@cargomedia.ch';
    $config->Harvest_Api_Client->password = 'my-password';

    $config->Harvest_Cli->defaultProject = 12345;
};
