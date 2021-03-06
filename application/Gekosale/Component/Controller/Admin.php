<?php
/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.pl
 *
 * Copyright (c) 2008-2013 WellCommerce sp. z o.o.. Zabronione jest usuwanie informacji o licencji i autorach.
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 * 
 * $Revision: 438 $
 * $Author: gekosale $
 * $Date: 2011-08-27 11:29:36 +0200 (So, 27 sie 2011) $
 * $Id: controller.class.php 438 2011-08-27 09:29:36Z gekosale $ 
 */

namespace Gekosale\Component\Controller;

abstract class Admin extends \Gekosale\Component\Controller
{
	
	public $form;
	
	protected $registry;
	
	protected $designPath;
	
	protected $printLayout = true;
	
	protected $_id = 0;
	
	protected $model = NULL;
	
	protected $formModel = NULL;

	public function __construct ($registry, $designPath = NULL)
	{
		$this->registry = $registry;
		$this->id = $this->registry->core->getParam();
		$this->model = \Gekosale\App::getModel($this->getName());
		$this->formModel = \Gekosale\App::getFormModel($this->getName());
	}

	public function setDesignPath ($path)
	{
		$this->designPath = $path;
	}

	public function getDesignPath ()
	{
		return $this->designPath;
	}
	


	public function renderLayout ($Data = Array(), $action = NULL)
	{
		if (NULL === $action){
			$action = $this->registry->router->getAction();
		}
		
		$this->registry->template->assign('sticky', 'help/' . $this->registry->router->getCurrentController() . '/' . $action . '.tpl');
		$this->registry->template->assign('stickyid', 'sticky-' . $this->registry->router->getCurrentController() . '-' . $action);
		$this->registry->template->assign('xajax', $this->registry->xajax->getJavascript());
		$this->registry->xajax->processRequest();
		foreach ($Data as $name => $value){
			$this->registry->template->assign($name, $value);
		}
		$this->registry->template->display($this->loadTemplate($action . '.tpl'));
	}

	public function loadTemplate ($fileName)
	{
		return $this->getDesignPath() . $fileName;
	}

}