<?php
namespace Tualo\Office\DAV\Classes\FS;
use Tualo\Office\Basic\TualoApplication as App;
use Sabre\DAV;
use Tualo\Office\DAV\Classes\FS\DSFile;


class DSDirectory extends DAV\Collection {

  private $path='';
  private $db;
  private $table_name;

  function __construct($table_name,$path='') {
    $this->db = App::get('session')->getDB();
    $this->table_name = $table_name;
    $this->path = $path;

  }

  function getChildren() {

    $children = array();
    // Loop through the directory, and create objects for each node
    //print_r($this->db->direct('select name from ds_files where table_name={table_name} ',['table_name'=>$this->table_name]));
    foreach($this->db->direct('select name from ds_files where table_name={table_name} ',['table_name'=>$this->table_name]) as $node) {
      $children[] = $this->getChild($node);
    }

    return $children;

  }

  function getChild($node) {

      $path = $this->path . '/' . $node['name'];

      // We have to throw a NotFound exception if the file didn't exist
      /*if (!file_exists($path)) {
        throw new DAV\Exception\NotFound('The file with name: ' . $name . ' could not be found');
      }*/

      // Some added security
      //if ($name[0]=='.')  throw new DAV\Exception\NotFound('Access denied');
      /*
      if (strpos($node['name'],'/')>=0) {
          return new DSDirectory($this->table_name,$path);
      } else {
        */
        //print_r([$this->table_name,$path]);
      return new DSFile($this->table_name,$path);

      //}

  }

  function childExists($name) {
    $res = $this->db->singleRow('select name from ds_files where table_name={table_name} and name={name}',
      [
        'table_name'=>$this->table_name,
        'name'=>$this->path.''.$name
      ]
    );
    return $res!==false;
  }

  function getName() {

      return basename($this->table_name);

  }

}