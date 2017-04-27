<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Fieldtest
 * @author     Hervé CYR <herve.cyr@laposte.net>
 * @copyright  Copyright (C) 2016. Tous droits réservés.
 * @license    GNU General Public License version 2 ou version ultérieure ; Voir LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::register('FieldtestFrontendHelper', JPATH_COMPONENT . '/helpers/fieldtest.php');

// Execute the task.
$controller = JControllerLegacy::getInstance('Fieldtest');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
