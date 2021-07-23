<?php
class gecpsbasicModuleFrontController extends ModuleFrontController
{
  public $params = array();

  public function initContent()
  {
    //parent::initContent();
    $this->initParams();
	   $this->checkAction();
    $this->setTemplate('module:gecps/views/templates/front/basic.tpl');


  }

  public function checkAction(){
    if (isset($_GET['action'])) {
      if (method_exists($this,$_GET['action'])) {
        $this->{$_GET['action']}();
      }
    }
    if (isset($_POST['action'])) {
      if (method_exists($this,$_POST['action'])) {
        $this->{$_POST['action']}();
      }
    }

  }

  public function initParams(){
    $this->params = array(

    );
    if (extension_loaded('memcached')) {
      $m = new Memcached();
      $m->flush();
    }
    if (extension_loaded('apcu')) {
      apc_clear_cache();
      apc_clear_cache('user');
      apc_clear_cache('opcode');
      echo json_encode(array('success' => true));
    }


    //echo $this->module->l('test5','basic','es-ES');
  }
}
