<?php 
/*-------------------------- Get Data From Controller --------------------*/
use Core\Classes\View;
/*-------------------------------------------------------------------------*/?>


<!DOCTYPE html>
<html dir="LTR" lang="en">
<head>
    <meta charset="UTF-8"/>

    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <!-- stylesheet -->
    <link rel="stylesheet" href="<?= base_url('assets/node_modules/bootstrap/dist/css/bootstrap.min.css')?>"/>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?ver='.microtime())?>">
    <!-- fonts --> 
    <link href="https://fonts.googleapis.com/css2?family=Inter" rel="stylesheet">
    <!-- title -->
    <title><?= View::getData('page_title', 'CodeMediator | Home') ?></title>
</head>
<body>



<!-- @Header -->
<header class="header">
    <div class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container-fluid">
            <!-- brand/logo -->
            <a class="navbar-brand mt-1 mb-1" href="<?= base_url()?>">CodeMediator</a>
            <!-- navigation button -->
            <button class='navbar-toggler'  type='button' data-toggle='collapse' data-target='#navbar-navigation' ariacontrols='navbar-navigation' aria-expanded='false' aria-label='Toggle navigation'   >
                <span class='navbar-toggler-icon'></span>
            </button>
            <!-- navigation list -->
            <div class="navbar-collapse collapse" id="navbar-navigation">
                <ul class="navbar-nav mr-auto  my-2 my-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="<?= base_url("about-us") ?>">about us</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url("contact-us") ?>">contact us</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url("privacy-policy") ?>">privacy policy</a></li>
                </ul>
                <hr class="collapse d-block d-lg-none "/>
                <ul class="navbar-nav navbar-list my-2 my-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="https://github.com/rochdi-wafik/CodeMediator/blob/main/Docs/Readme.MD">Documentation</a></li>
                    <li class="nav-item collapse d-md-block"><a class="nav-link"> |</a></li>
                    <li class="nav-item"><a class="nav-link active" href="https://github.com/rochdi-wafik/CodeMediator">Github</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>
