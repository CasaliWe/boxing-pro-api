<?php

// **********AUTOLOAD**********
require __DIR__.'/../vendor/autoload.php';

// **********VARIÁVEIS DE AMBIENTES**********
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

// **********CONEXÃO COM O DB**********
require __DIR__.'/db.php';


// **********BASE URL GERAL**********
$base_url = $_ENV['BASE_URL'];


// **********CONTET METATAGS**********
$titulo_site = $_ENV['TITULO_SITE_SEO'];
$descricao_site = $_ENV['DESCRICAO_SITE_SEO'];