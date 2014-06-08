<?php

return function (CM_Config_Node $config) {
    $config->Harvest_Api_Client->account = 'cargomedia';
    $config->Harvest_Api_Client->email = 'ff@cargomedia.ch';
    $config->Harvest_Api_Client->password = 'cargomedia123!';

    $config->Harvest_Cli->defaultProject = 2355141;
};
