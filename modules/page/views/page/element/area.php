<?php defined('SYSPATH') OR die('No direct access allowed.');

echo "\n<!-- Element Area  $id ($name) -->\n";
if (Page::$adminmode)
{
?>
<p class="page-area-title"><?php echo __('Element Area #:num - :name', array(':num' => $id, ':name' => $name)) ?></p>
<div class="page-area">
<?php
}
echo $content;
if (Page::$adminmode)
{
?>
<div class="page-element-control">
	<p class="title"><span class="fam-add inline-sprite"></span><?php echo __('Add New Element') ?></p>
	<?php echo Form::open() ?>
	<?php echo Form::hidden('area', $id); ?>
	<select name="type" style="float:left;margin-right:5px;">
		<?php
		foreach (Model_Page_Element::$type_maps AS $type_id => $type_name)
		{
			echo "<option value='{$type_id}'>". __(ucfirst($type_name)) ."</option>";
		}
		?>
	</select>
	<?php echo Form::submit('add', __('Add Element'), array('class' => 'submit')); ?>
	</form>
	<div style="clear:left;"></div>
</div>

</div>
<?php
}
echo "\n<!-- End Content Area $id ($name) -->\n";
