<?php

function phpmodel($table)
{
   $relations_key = array_keys($table['relations']);
   $relations = '';
   if (count($relations_key) > 0) {
      $relations .= '[';
      $t = 0;
      foreach ($relations_key as $key) {
         if ($t == 0 || $t == count($relations_key)) {
            $relations .= '';
         } else {
            $relations .= ',';
         }
         $relations .= "'$key'=>[";
         $f = 0;
         foreach ($table['relations'][$key] as $id => $value) {
            if ($f == 0 || $f == count($table['relations'][$key])) {
               $relations .= '';
            } else {
               $relations .= ',';
            }
            $relations .= "'$id'" . '=>'
               . "'$value'";
            ++$f;
         }
         $relations .= ",'callback'" . '=>' . ucfirst($key) . "::class" . ']';
         ++$t;
      }
      $relations .= ']';
   }
   $nullable = array_column(array_filter($table["data"], fn ($r) => !(isset($r["sql_attribute"]) && (str_contains($r['sql_attribute'], 'NOT NULL') || str_contains($r['sql_attribute'], 'PRIMARY')))), "name");
   $fillable = array_column(array_filter($table["data"], fn ($r) => !isset($r["fillable"])), "name");
   return php_w('
namespace App\Model;

use The\Model;

class ' . ucfirst($table['name']) . ' extends Model {

    public $model = ' . json_encode(array_column($table['data'], 'name')) . ';
    public $name = "' . $table['name'] . '";
    public $nullable = ' . json_encode($nullable) . ';
    protected $enable = ' . (in_array("enable", array_column($table['data'], 'name')) ? 'true' : 'false') . ';
    protected $table = "' . $table['table'] . '";
    protected $relations = ' . ($relations == '' ? '""' : $relations) . ';
    protected $fillable = ' . json_encode($fillable) . ';

}');
}
function phpdefaultController(array $table, array $curd, string $key = '')
{
   return php_w('
namespace App\Controller\\' . ucfirst($key) . ';
use App\Model\{
  ' . ucfirst($table['name']) . '
};

class ' . ucfirst($key) . ucfirst($table['name']) . 'Controller {

' . (in_array("a", $curd) ? '
    public static function all() {
        if (isset($_GET["latest"])) {
            return ' . ucfirst($table['name']) . '::wherec([["updated_at", ">", $_GET["latest"]]])->get();
        }
        return ' . ucfirst($table['name']) . '::all();
    }
' : '') .
      (in_array("r", $curd) ? '
    public static function show($id) {
        return ' . ucfirst($table['name']) . '::find($id);
    }' : '') .
      (in_array("c", $curd) ? '
    public static function store() {
        return ' . ucfirst($table['name']) . '::create($_POST)->getInserted();
    }
    ' : '') .
      (in_array("u", $curd) ? '
    public static function update($id) {
        ' . ucfirst($table['name']) . '::where(["id"=>[$id]])->update($_POST);
        return ' . ucfirst($table['name']) . '::find($id);
    }
    ' : '') .
      (in_array("p", $curd) ? '
    public static function upsert() {
        return ' . ucfirst($table['name']) . '::upsert($_POST["' . $table['table'] . '"])->getsInserted();
    }
    ' : '') .
      (in_array("d", $curd) ? '

    public static function delete($id) {
        ' . ucfirst($table['name']) . '::delete(["id"=>$id]);
        return $id;
    }' : '') . '

}');
}
function phpphotoController(array $table, array $curd, string $key = '')
{
   return php_w('
namespace App\Controller\\' . ucfirst($key) . ';
use The\{FileAct, Img, Response};
use App\Model\{
  ' . ucfirst($table['name']) . '
};

class ' . ucfirst($key) . ucfirst($table['name']) . 'Controller {

' . (in_array("a", $curd) ? '
    public static function all() {
        if (isset($_GET["latest"])) {
            return ' . ucfirst($table['name']) . '::wherec([["updated_at", ">", $_GET["latest"]]])->get();
        }
        return ' . ucfirst($table['name']) . '::all();
    }
' : '') .
      (in_array("r", $curd) ? '

    public static function show($id) {
        return ' . ucfirst($table['name']) . '::find($id);
    }' : '') .
      (in_array("c", $curd) ? '
    public static function store() {
        $file = FileAct::init($_FILES[' . '"photo"' . '])->public("")->fileupload($_FILES[' . '"photo"' . '], $_POST[' . '"name"' . ']);
        return ' . ucfirst($table['name']) . '::create($file)->getInserted();
    }
    ' : '') .
      (in_array("u", $curd) ? '
    public static function update($id) {
      $file = FileAct::init($_FILES[' . '"photo"' . '])->public("")->fileupload($_FILES[' . '"photo"' . '], $_POST[' . '"name"' . ']);
      Photo::where(["id" => [$id]])->update($file);
        return ' . ucfirst($table['name']) . '::find($id);
    }
    ' : '') .
      (in_array("p", $curd) ? '
    public static function upsert() {
      if (isset($_POST["' . 'dir' . '"])) {
         if ($_POST["' . 'dir' . '"] !== "") {
             $files = FileAct::init($_FILES["' . 'photo' . '"])->public($_POST["' . 'dir' . '"])->ups()->files;
             ' . (isset($table['type']['version']) && count($table['type']['version']) > 0 ? ('foreach ($files as $file) {
                 ' . (implode('', array_map(fn ($key, $value) => 'Img::webpImage(source: $file[' . '"path"' . '], destination: $file[' . '"dir"' . '] . DIRECTORY_SEPARATOR . "' . $key . '/" . $file[' . '"name"' . '], x: ' . $value['width'] . ', quality: ' . $value['quality'] . ');
                 ', array_keys($table['type']['version']), array_values($table['type']['version']))))  . '}') : "") . '
             return Photo::upsert($files)->getsInserted();
         }
     }
     return Response::bad_req("It seem you Missed Directory");
    }
    ' : '') .
      (in_array("d", $curd) ? '

    public static function delete($id) {
      $' . $table['name'] . ' = ' . ucfirst($table['name']) . '::find($id)->array();
      if ($' . $table['name'] . ') {
          is_file($' . $table['name'] . '["path"]) ? unlink($' . $table['name'] . '["path"]) : "";
          ' . ucfirst($table['name']) . '::delete(["id" => $id]);
      }
      return $id;
    }' : '') . '

}');
}
function phpController(array $table, array $curd, string $key = '')
{
   if (isset($table["type"])) {
      if ($table["type"]['name'] == "file") {
      } elseif ($table["type"]['name'] == "photo") {
         return phpphotoController($table, $curd, $key);
      }
   } else {
      return phpdefaultController($table, $curd, $key);
   }
}

function phpset($table, $json)
{
   $GLOBALS['For'] = [];
   foreach ($table as $item) {
      $model = fopen_dir(__DIR__ . "/../php/App/" . ucfirst('model/') . ucfirst($item['name']) . '.php');
      $model_write = phpModel($item);
      fwrite($model, $model_write);
      if (isset($item['crud']['roles'])) {
         foreach ($item['crud']['roles'] as $key => $value) {
            phpwritec($item, $value, $key, $key);
         }
      }
      if (isset($item['crud']['isuper'])) {
         phpwritec($item, $item['crud']['isuper'], 'isuper', 'isuper');
      }
      if (isset($item['crud']['islogin'])) {
         phpwritec($item, $item['crud']['islogin'], 'islogin');
      }
      if (isset($item['crud']['public'])) {
         phpwritec($item, $item['crud']['public'], 'public');
      }
   }
   if (isset($GLOBALS['For']['roles'])) {
      foreach ($GLOBALS['For']['roles'] as $key => $value) {
         phproterc($key, $value);
      }
   }
   if (isset($GLOBALS['For']['isuper'])) {
      phproterc('isisuper', $GLOBALS['For']['isuper']);
   }
   if (isset($GLOBALS['For']['islogin'])) {
      phproterc('islogin', $GLOBALS['For']['islogin']);
   }
   if (isset($GLOBALS['For']['ipublic'])) {
      phproterc('ipublic', $GLOBALS['For']['ipublic']);
   }
   phpenv($json);
   templatecopy("php", "php");
}

function phpwritec($item, $value, $key, $role = '')
{
   if ($role == '') {
      if (!isset($GLOBALS['For'][$key])) {
         $GLOBALS['For'][$key] = ['path' => $key, "controller" => [], 'child' => []];
      }
      $GLOBALS['For'][$key]["child"][] = ['path' => "/" . $item['name'], "crud" => ["class" => ucfirst($key) . ucfirst($item['name']) . ucfirst("controller"), "crud" => $value]];
      $GLOBALS['For'][$key]["controller"][] = 'use App\\' . ucfirst('controller\\') . ucfirst($key) . '\\' . ucfirst($key) . ucfirst($item['name']) . 'Controller;';
   } else {
      if (!isset($GLOBALS['For']['roles'])) {
         $GLOBALS['For']['roles'] = [];
      }
      if (!isset($GLOBALS['For']['roles'][$key])) {
         $GLOBALS['For']['roles'][$key] = ["path" => '/' . $key];
         $GLOBALS['For']["roles"][$key]["child"][] = ['path' => "/" . $item['name'], "crud" => ["class" => ucfirst($key) . ucfirst($item['name']) . ucfirst("controller"), "crud" => $value]];
         $GLOBALS['For']["roles"][$key]["controller"][] = 'use App\\' . ucfirst('controller\\') . ucfirst($key) . '\\' . ucfirst($key) . ucfirst($item['name']) . 'Controller;';
      } else {
         $GLOBALS['For']["roles"][$key]["child"][] = ['path' => "/" . $item['name'], "crud" => ["class" => ucfirst($key) . ucfirst($item['name']) . ucfirst("controller"), "crud" => $value]];
         $GLOBALS['For']["roles"][$key]["controller"][] = 'use App\\' . ucfirst('controller\\') . ucfirst($key) . '\\' . ucfirst($key) . ucfirst($item['name']) . 'Controller;';
      }
   }
   $controller_write = phpController($item, $value, $key);
   $controller = fopen_dir(__DIR__ . "/../php/App/" . ucfirst('controller/') . ucfirst($key) . '/' . ucfirst($key) . ucfirst($item['name']) . 'Controller.php');
   fwrite($controller, $controller_write);
}

function phproterc($key, $route)
{
   if ($key != "ipublic" && $key != "islogin") {
      $route['roles'] = [$key];
   }
   $controller = $route['controller'];
   unset($route['controller']);
   $route = var_export($route, true);
   $route_controller = implode("\n", $controller);
   $routx = fopen_dir(__DIR__ . "/../php/" . ucfirst('routes/pre/api/') . ucfirst($key) . '.php');
   fwrite($routx, preg_replace("/'class' => '(.+?)'/", '"class" => ${1}::class', "<?php\n\n$route_controller \n\n$$key = $route;\n\n"));
}

function phpenv($json)
{
   $env = fopen_dir(__DIR__ . "/../php/env.php");
   fwrite($env, php_wrapper(implode("", array_map(function ($key, $value) {
      return "
      define('$key', " . json_encode($value) . ");";
   }, array_keys($json["env"]), $json["env"]))));
}
function templatecopy(string $folder, string $destination)
{
   foreach (scanfullfolder(__DIR__ . "/template/$folder") as $file) {
      $pre = __DIR__ . '/../' . $destination;
      $target = str_replace(__DIR__ . "/template/$folder", "",  $file);
      $dir = "";
      foreach (explode("/", $target) as $x) {
         if ($x !== "") {
            $dir .= "/" . $x;
            if (!($pre . $target === $pre . $dir) && !is_dir($pre . $dir)) {
               mkdir($pre . $dir);
            }
         }
      }
      if (!is_file($pre . $target)) {
         copy($file, $pre . $target);
      }
   }
}
