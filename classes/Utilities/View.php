<?php
/**
 * View.php
 *
 * A basic view object for rendering final output
 *
 * @author Ben Brown
 */

namespace Utilities;

class View {
  /**
   *
   * @var array
   */
  private $data = array();

  /**
   * Template name
   * @var string
   */
  private $template;

  /**
   * Path to the template directory
   * @var string
   */
  private $path;

  /**
   * The layout file to use
   * @var string
   */
  private $layout;

  /**
   * The default path as set in the bootstrap
   * @var string
   */
  private static $defaultPath;

  /**
   * Brave brave Sir Robin ran away
   * @param string $path Path to view files
   */
  public function __construct($path = null) {

    if (isset(self::$defaultPath)) {
      $this->setPath(self::$defaultPath);
    }

    if (!empty($path)) {
      $this->setPath($path);
    }
  }

  /**
   * Magic isset function to check and see if variables are in the data
   * array.
   *
   * @param  string $varName The name of the property/data to get
   * @return bool            True if the specified piece of data exists
   */
  public function __isset($varName) {
    return !empty($this->data[$varName]);
  }

  /**
   * Set the template path
   *
   * @param string $template The name of the template
   */
  public function setTemplate ($template) {
    $this->template = (string) $template;
  }

  /**
   * Set the template path
   *
   * @param string $template The name of the template
   */
  public function setLayout ($layout) {
    $this->layout = (string) $layout;
  }

  /**
   * Add aditional directories to the path
   *
   * @param string $path Add items to the path to use sub directory for templates
   */
  public function addToPath($path){
    $this->setPath($this->path . $path);
  }

  /**
   * Set the path for the view.  Check to make sure that path
   * exits before setting.  Throw an exception if it doesn't
   *
   * @param string $path Path to the views
   */
  public function setPath ($path) {
    // Only look at strings for safty sake
    $path = (string) $path;

    // Make sure it always ends in a /
    if (substr($path, -1) != '/') {
      $path .= '/';
    }

    if (is_dir($path)) {
      $this->path = $path;
    } else {
      throw new \Exception('Path (' . $path . ') does not exist');
    }
  }

  /**
   * Static method for setting a default path during the bootstrap process
   *
   * @param string $defaultPath Path to the views
   */
  public static function setDefaultPath($defaultPath) {
    self::$defaultPath = (string) $defaultPath;
  }

  /**
   * Render the view for final display.  Extracts all the data into the method
   * scope and includes the template file based on the paths
   * @return [type] [description]
   */
  public function render($template = null, $parameters = null) {
    if (!isset($this->path)) {
      throw new \Exception('You must set a path prior to render');
    }

    if( null == $template || empty( $template ) ) {
      $template = $this->template;
    }

    if (!isset($template)) {
      throw new \Exception('You must set a template prior to render');
    }

    if (!empty($parameters)) {
      $this->data = array_merge($this->data, (array) $parameters);
    }

    extract($this->data);
	
    ob_start();
    require($this->path . $template . '.phtml');
    $content = ob_get_clean();

    if (isset($this->layout)) {
   		require($this->layout);     
    } else {
      echo $content;
    }
  }

  /**
   * Magic getter to pull varibles from the data array.  Checks for a getter firest.
   * If getter doesn't exist it pulls value from data array
   * If data array doesn't exist returns false
   *
   * @param  string $varName The name of the property to get
   * @return mixed           The value of the requested property
   */
  public function __get($varName) {
    if (is_callable(array($this, 'get' . ucfirst($varName)))) {
      return $this->{'get' . ucfirst($varName)}();
    } else {
      if( isset($this->data[$varName]) ) {
        return $this->data[$varName];
      } else {
        return false;
      }
    }
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


/**
 * Method for rendring and return rendered templates.  Used as an include in other documents
 * @param  string  $include Path to the include.  This path is based on the current view path
 * @param  boolean $echo    Should the data be echoed to the current buffer or shoudl it be returned
 * @return string|void      Nothing if the content is echoed, the rendered template otherwise
 */
  public function partial($include, $echo = true) {
    ob_start();
    include $this->path . $include;
    $results = ob_get_clean();
    if ($echo) {
        echo $results;
    } else {
      return $results;
    }
  }

}