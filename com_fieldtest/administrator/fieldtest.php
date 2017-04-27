<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Fieldtest
 * @author     Hervé CYR <herve.cyr@laposte.net>
 * @copyright  Copyright (C) 2016. Tous droits réservés.
 * @license    GNU General Public License version 2 ou version ultérieure ; Voir LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_fieldtest'))
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

$controller = JControllerLegacy::getInstance('Fieldtest');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
