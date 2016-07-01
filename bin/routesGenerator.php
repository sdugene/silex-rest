<?php
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
    isc.table_name,
    GROUP_CONCAT(isc.column_name SEPARATOR ',') as columns,
    id.id,
    isc.privileges,
    isc.column_comment,
    fk.foreign_keys
  FROM information_schema.columns isc
  LEFT JOIN (
      SELECT i.TABLE_NAME as table_name, CONCAT('{\"',GROUP_CONCAT(CONCAT(k.REFERENCED_TABLE_NAME,'\":{\"column\":\"',k.COLUMN_NAME,'\",\"referenced\":\"',k.REFERENCED_COLUMN_NAME,'\"}') SEPARATOR ',\"'),'}') as foreign_keys
    FROM information_schema.TABLE_CONSTRAINTS i
    LEFT JOIN information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME
    WHERE i.CONSTRAINT_TYPE = 'FOREIGN KEY' AND i.table_schema = DATABASE() GROUP BY table_name ORDER BY i.table_name
  ) as fk ON fk.table_name = isc.table_name
  LEFT JOIN (
      SELECT column_name as id, table_name
    FROM information_schema.columns
    WHERE table_schema = DATABASE() AND extra = 'auto_increment'
  ) as id ON id.table_name = isc.table_name
  WHERE
    isc.table_schema = DATABASE()
    AND isc.column_name != id.id
  GROUP BY isc.table_name
  ORDER BY isc.table_name;
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
            'idColumn' => $table['id'],
            'attributes' => explode(',',$table['columns']),
            'foreignKeys' => [],
            'methods' => [
                "get" => "getById",
                "getAll" => "getAll",
                "search" => "search",
                "getWithJoin" => "getByIdWithJoin",
                "getAllWithJoin" => "getAllWithJoin",
                "searchWithJoin" => "searchWithJoin"
            ]
        ];

        if (!is_null($table['foreign_keys'])) {
            $routes[$key]['foreignKeys'] = json_decode($table['foreign_keys'], true);
        }

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
    echo 'Routes creation done
';
} elseif (!empty($routes)) {
    echo 'Creation ERROR : no route found
 ';
} else {
    echo 'Creation ERROR : I can\'t write '.ROOT . 'resources/routes/routes.json
 ';
}