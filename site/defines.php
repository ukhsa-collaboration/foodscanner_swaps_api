<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

# switch to turn on/off randomization. If enabled, scanning the same barcode twice will get a different order
# of swaps AFTER the first 3 (which will always be the top 3).
define('RANDOMIZATION_ENABLED', true);


# Specify the error codes for specific situations.
define('ERROR_CODE_BARCODE_NOT_FOUND', -100);
define('ERROR_CODE_AUTHENTICATION_FAILED', -101);
define('ERROR_CODE_SERVER_MISSING_ENVIRONMENT_VARIABLE', -102);
define('ERROR_CODE_MISSING_COMPUTE_INSTANCE', -103);
define('ERROR_CODE_COMPUTE_INSTANCE_NOT_READY', -104);
define('ERROR_CODE_MISSING_PARAMETER', -105);


# Specify the AMI ID to deploy the compute docker container to.
# Currently set to ubuntu 20.04
# Don't forget that AMI IDs are region specific.
define('COMPUTE_AMI_ID', "ami-05c424d59413a2876");