<?php
/**
 *
 */
namespace Controllers;

abstract class Base {

  /**
   * Holds a view object
   * @var \utilities\View
   */
  protected $view;
  protected $protected;
  
  /**
   * Assign a new view to th $view property
   */
  public function __construct() {
    $baseViewPath      = realpath(pathinfo(__FILE__, PATHINFO_DIRNAME)  . '/../../views');
    $this->view        = new \Utilities\View($baseViewPath);
    $pathFromClass     = str_replace('Controllers', '', str_replace('\\', '/', ucwords(self::getClassName())));
    $this->view->title = 'Trivia';

    $this->view->addToPath($pathFromClass);
    $this->view->setLayout($baseViewPath . '/layout.phtml'); 
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