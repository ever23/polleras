<?php

return [
    'class' => '\\Cc\\Mvc\\PDO',
    'param' => ['mysql:host=localhost;dbname=pollera;charset=utf8', 'root', '']
];
/** Sqlite con PDO
 * return [
 *   'class' => '\\Cc\\Mvc\\PDO',
 *   'param' => ['sqlite:'.dirname(__FILE__) . '/../testdb.db']
 * ];
 */

/** Mysql con MySQLi
 * return [
 *   'class' => '\\Cc\\Mvc\\MySQLi',
 *   'param' => ['localhost', 'root', '', 'testDB']
 * ];
 */
 
/** Mysql con PDO
 * return [
 *   'class' => '\\Cc\\Mvc\\PDO',
 *   'param' => ['mysql:host=localhost;dbname=testDB;charset=utf8', 'root', '']
 * ];
 */

/** PosgreSql con PDO
 * return [
 *   'class' => '\\Cc\\Mvc\\PDO',
 *   'param' => ['pgsql:host=localhost;port=5432;dbname=testDB;', 'root', '']
 * ];
 */
