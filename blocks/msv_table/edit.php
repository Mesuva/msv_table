<?php  defined('C5_EXECUTE') or die("Access Denied.");
$json = Core::make('helper/json');

$template = '';

if ($b) {
    $template = $b->getBlockFilename();
}
?>

<ul class="ccm-inline-toolbar">
  <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-cancel">
  <button  class="btn cancel-inline"><?php echo t('Cancel'); ?></button>
  </li>

  <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save">
    <button class="btn btn-primary save-inline"><?php echo t('Save'); ?></button>
  </li>

</ul>


<textarea id="<?php echo $bID; ?>_table_data" name="table_data"  style="display:  none;  "><?php  echo htmlspecialchars($table_data, ENT_NOQUOTES); ?></textarea>
<textarea id="<?php echo $bID; ?>_table_metadata" name="table_metadata" style="display:  none;  "><?php  echo $table_metadata; ?></textarea>
<div id="<?php echo $bID; ?>_tabledata"></div>


<?php $this->inc('editor.php', array('template'=>$template)); ?>