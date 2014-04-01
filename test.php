<?php

$s = 'Удар по воротам;гол;штрафной удар';

$k = 'штрафной удар по воротам';

$kw = array_map('trim', explode(';', $s));

echo '<pre>'; print_r($kw); echo '</pre>';

$r = array();

foreach ($kw as $v) {

  if (!preg_match('/\s+/', $v)) {
    $r[$v] = preg_match('/\b' . $v . '\b/iu', $k);
  } else {
    $r[$v] = mb_stristr($k, $v);
  }

}

echo '<pre>'; var_dump($r); echo '</pre>';

exit;


class Data {

    public $child;

    public $name;

    function __construct($name, $child = null) {
      $this->name = $name;
      $this->child = $child;
    }

    function show() {
      $obj = $this;
      while ($obj !== null) {
        echo "{$obj->name}<br/>";
        $obj = $obj->child;
      }
    }

}


$a = new Data('A', new Data('B', new Data('C', new Data('D'))));

$tmp;

while($a) {
  if (!$tmp) {
    $tmp = new Data($a->name);
  }

  if ($a->child) {
    $tmp = new Data($a->child->name, $tmp);
  }

  $a = $a->child;
}

$tmp->show();