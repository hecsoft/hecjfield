<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Fieldtest
 * @author     Hervé CYR <herve.cyr@laposte.net>
 * @copyright  Copyright (C) 2016. Tous droits réservés.
 * @license    GNU General Public License version 2 ou version ultérieure ; Voir LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Class FieldtestFrontendHelper
 *
 * @since  1.6
 */
class FieldtestFrontendHelper
{
	/**
	 * Get an instance of the named model
	 *
	 * @param   string  $name  Model name
	 *
	 * @return null|object
	 */
	public static function getModel($name)
	{
		$model = null;

		// If the file exists, let's
		if (file_exists(JPATH_SITE . '/components/com_fieldtest/models/' . strtolower($name) . '.php'))
		{
			require_once JPATH_SITE . '/components/com_fieldtest/models/' . strtolower($name) . '.php';
			$model = JModelLegacy::getInstance($name, 'FieldtestModel');
		}

		return $model;
	}
}
