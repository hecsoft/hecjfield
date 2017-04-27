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

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

// Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_fieldtest', JPATH_SITE);
$doc = JFactory::getDocument();

$doc->addStyleSheet("//code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css");
/**/
?>

<div class="table-edit front-end-edit">
	<?php if (!empty($this->item->id)): ?>
		<h1>Edit <?php echo $this->item->id; ?></h1>
	<?php else: ?>
		<h1>Add</h1>
	<?php endif; ?>

	<form id="form-table"
		  action="<?php echo JRoute::_('index.php?option=com_fieldtest&view=filexplorer'); ?>"
		  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
		
		<div ><?php echo $this->form->getInput('documents'); ?></div>
	
		<input type="hidden" name="option" value="com_fieldtest"/>
		<input type="hidden" name="task"   value="filexplorer"/>
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
