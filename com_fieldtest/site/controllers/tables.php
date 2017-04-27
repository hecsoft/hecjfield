<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Fieldtest
 * @author     Hervé CYR <herve.cyr@laposte.net>
 * @copyright  Copyright (C) 2016. Tous droits réservés.
 * @license    GNU General Public License version 2 ou version ultérieure ; Voir LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';

/**
 * Tables list controller class.
 *
 * @since  1.6
 */
class FieldtestControllerTables extends FieldtestController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional
	 * @param   array   $config  Configuration array for model. Optional
	 *
	 * @return object	The model
	 *
	 * @since	1.6
	 */
	public function &getModel($name = 'Tables', $prefix = 'FieldtestModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
}
