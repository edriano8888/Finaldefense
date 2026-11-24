<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['protocol']    = getenv('SMTP_PROTOCOL') ?: 'smtp';
$config['smtp_host']   = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
$config['smtp_port']   = getenv('SMTP_PORT') ?: 587;
$config['smtp_user']   = getenv('SMTP_USER') ?: '';
$config['smtp_pass']   = getenv('SMTP_PASS') ?: '';
$config['smtp_crypto'] = getenv('SMTP_CRYPTO') ?: 'tls';
$config['mailtype']    = 'html';
$config['charset']     = 'utf-8';
$config['newline']     = "\r\n";
$config['crlf']        = "\r\n";
$config['wordwrap']    = TRUE;


