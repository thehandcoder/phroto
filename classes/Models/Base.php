<?php
/**
 * Base.php
 *
 */

namespace Models;

/**
 *
 * This is the base model class. Any see models should extend this class
 * and should use call this constructor.
 *
 * <code>
 *  class SomeModel extends Base
 * </code>
 *
 * @author  Mike Marcotte, Ben Brown
 *
 *
 */
abstract class Base {

  /**
   * A generic holder for data that can be access by the magic getter
   * @var array
   */
  protected $data = array();

  /**
   * The cache object to be used by children.  Should be instance of
   * @var \utilities\Cache
   */
  protected $cache;

  /**
   * The config object to be used by children.  Should be instance of
   * @var \utilities\Config
   */
  protected $config;

  /**
   * The datebase to use
   * @var \MongoDb
   */
  private static $server;

  /**
   * The datebase to use
   * @var \MongoDb
   */
  private $db;

  /**
   * The Gridfs accessor
   * @var \MongoGridFs
   */
  private static $gfs;

  /**
   * @access private
   * @var \Logger The logger object.  It is an instance of log4php
   */
  protected $logger;

  /**
   * Setup the basic accessors
   */
  public function __construct() {
    $this->db = 'trivia';

    if (!(self::$server instanceof \utilities\Mongo)) {
      self::$server = new \Mongo();
    }
  }

  /**
   * Return the database for this item.  You should be able to access the
   * db object by using the $db property.
   *
   * @return Database
   *
   * @todo Change the return to be an instace of \Utilities\Mongo
   */
  protected function getDb() {
    return self::$server->$db;
  }

  public function load($id){
      $collection = self::$server->{$this->db}->{$this->getCollectionName()};
  }

  public function save(){
      $collection = self::$server->{$this->db}->{$this->getCollectionName()};
      $results = $collection->save($this->data);
      if (!isset($this->data['_id'])) {
        $this->data['_id'] = $results['_id'];
      }
  }

  /**
   * Magic getter to pull varibles from the data array.  Checks for a getter firest.
   * If getter doesn't exist it pulls value from data array
   *
   * @param  string $varName The name of the property to get
   * @return mixed           The value of the requested property
   */
  public function __get($varName) {
    if (is_callable(array($this, 'get' . ucfirst($varName)))) {
      return $this->{'get' . ucfirst($varName)}();
    } else {
      if (isset($this->data[$varName])) {
        return $this->data[$varName];
      }
    }
  }

  /**
   * Magic isset to check and see if a variable is stored within
   * the store the data array
   *
   * @param string $varName Name of the variable being set
   * @return bool
   */
  public function __isset($varName) {
    return isset($this->data[$varName]);

  }


  /**
   * Magic setter to store varibles into the data array.  Checks for a setter firest.
   * If setter doesn't exist it puts value into data array
   *
   * @param string $varName Name of the variable being set
   * @param mixed  $value   Value to be stored
   */
  public function __set($varName, $value) {
    if (is_callable(array($this, 'set' . ucfirst($varName)))) {
      $this->{'set' . ucfirst($varName)}($value);
    } else {
      $this->data[$varName] = $value;
    }
  }

  public function getCollectionName() {
    $name = self::getClassName();
    $name = str_replace('\\', '', $name);
    $name = str_replace('Models', '', $name);
    return $name;
  }

  /**
   * Get the class name of the called class instead of this class
   * @return string Called class name
   */
  public static function getClassName() {
    return get_called_class();
  }

  public function setData(array $data) {
    $this->data = $data;
  }
}
