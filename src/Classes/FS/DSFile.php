<?php
namespace Tualo\Office\DAV\Classes\FS;
use Tualo\Office\Basic\TualoApplication as App;
use Sabre\DAV;

class DSFile extends DAV\File {

  private $path;
  private $db;
  private $table_name;

  function __construct($table_name,$path='') {
    $this->db = App::get('session')->getDB();
    $this->table_name = $table_name;
    $this->path = $path;

  }

  function getName() {

    return basename($this->path);

  }

  function get() {
    $res = $this->db->singleRow('select * from ds_files_data where file_id in (select id from ds_files where table_name={table_name} and name={name})',
      [
        'table_name'=>$this->table_name,
        'name'=>$this->path
      ]
    );
    list($type,$data) = explode(';base64,',$res['data']);
    return base64_decode($data);
  }

  function getSize() {
    $res = $this->db->singleRow('select size from ds_files where table_name={table_name} and name={name}',
      [
        'table_name'=>$this->table_name,
        'name'=>$this->path
      ]
    );
    return 0; //$res['size'];
  }

  function getETag() {
    $res = $this->db->singleRow('select hash from ds_files where table_name={table_name} and name={name}',
      [
        'table_name'=>$this->table_name,
        'name'=>$this->path
      ]
    );
    return $res['hash'];

  }

}