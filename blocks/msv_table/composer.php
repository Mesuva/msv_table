<?php  defined('C5_EXECUTE') or die("Access Denied.");
$json = Core::make('helper/json');
$template = '';
if ($b) {
    $template = $b->getBlockFilename();
}
?>

<div class="control-group">
    <label class="control-label"><?php echo $label?></label>
    <?php if($description): ?>
        <i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?php echo $description?>"></i>
    <?php endif; ?>
    <div class="controls">
        <textarea id="<?php echo $bID; ?>_table_data" name="<?php echo $view->field('table_data'); ?>"  style="display:  none;  "><?php  echo $table_data; ?></textarea>
        <textarea id="<?php echo $bID; ?>_table_metadata" name="<?php echo $view->field('table_metadata'); ?>" style="display:  none;  "><?php  echo $table_metadata; ?></textarea>
        <div id="<?php echo $bID; ?>_tabledata"></div>
    </div>
</div>

<?php $this->inc('editor.php'); ?>