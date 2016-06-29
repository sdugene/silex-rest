<?php
use Symfony\Component\HttpFoundation\JsonResponse;

define("ROOT", __DIR__ . '/../');
require_once ROOT . 'vendor/autoload.php';

$app = new Silex\Application();

require ROOT . 'resources/config/config.php';

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    "db.options" => $app["db.options"]
));

$sql_priv = "select Select_priv, Insert_priv, Update_priv, Delete_priv from mysql.user WHERE user = '".$app['db.options']['user']."'";
$user_priv = $app["db"]->fetchAssoc($sql_priv);

$id = 1;
$sql = "
  SELECT 
    table_name,
    GROUP_CONCAT(column_name SEPARATOR ',') as columns,
    privileges,
    column_comment
  FROM information_schema.columns
  WHERE
    table_schema = DATABASE()
    AND column_name != 'id'
  GROUP BY table_name
  ORDER BY table_name
";
$tables = $app["db"]->fetchAll($sql);

$routes = [];
foreach ($tables as $table) {
    $privileges = explode(',', $table['privileges']);

    if ($user_priv['Select_priv'] == 'Y') {
        $nameTemp = preg_replace('/_/', ' ', $table['table_name']);
        $key = preg_replace('/[\s]*/', '', ucwords($nameTemp));
        $routes[$key] = [
            'tableName' => $table['table_name'],
            'attributes' => explode(',',$table['columns']),
            'methods' => [
                "get" => "getById",
                "getAll" => "getAll"
            ]
        ];

        if ($user_priv['Insert_priv'] == 'Y') {
            $routes[$key]['methods']['post'] = 'save';
        }

        if ($user_priv['Update_priv'] == 'Y') {
            $routes[$key]['methods']['put'] = 'update';
        }

        if ($user_priv['Delete_priv'] == 'Y') {
            $routes[$key]['methods']['delete'] = 'delete';
        }
    }
}

if (!empty($routes) && file_put_contents(ROOT . 'resources/routes/routes.json', json_encode($routes))) {
    echo 'Routes Creation done';
} elseif (!empty($routes)) {
    echo 'Creation ERROR : no route found';
} else {
    echo 'Creation ERROR : I can\'t write '.ROOT . 'resources/routes/routes.json';
}