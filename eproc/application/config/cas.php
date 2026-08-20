<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['cas_server_url'] = 'https://sso.ui.ac.id/cas';

$config['phpcas_path'] = APPPATH . 'third_party/phpCAS';

$config['cas_disable_server_validation'] = TRUE;

$config['cas_debug'] = APPPATH . 'logs/cas_debug.log';
