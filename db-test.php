<?php
require 'vendor/autoload.php';
use \Illuminate\Database\Capsule\Manager as DBManager;
        $manager = new DBManager();

        $manager->addConnection(array(
          'driver'    => 'mysql',
          'host'      => '192.168.5.34',
          'database'  => 'ACL',
          'username'  => 'root',
          'password'  => '273273',
          'charset'   => 'utf8',
          'collation' => 'utf8_general_ci',
          'prefix'    => ''
          ));

$manager->setAsGlobal();
        $manager->bootEloquent();
$users =$manager::table('roles')->first();
var_dump($users);