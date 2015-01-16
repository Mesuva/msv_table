<?php  defined('C5_EXECUTE') or die("Access Denied.");

$value = '[[null,null]]';
$metadata = '[]';

if ($table_data) {
    $value = $table_data;
}

if ($table_metadata) {
    $metadata = $table_metadata;
}
?>


<script type="text/javascript">

    $('.cancel-inline').click(function(){
        ConcreteEvent.fire('EditModeExitInline');
        Concrete.getEditMode().scanBlocks();
    });

    $('.save-inline').click(function(){
        changeAction();
        $('#ccm-block-form').submit();
        ConcreteEvent.fire('EditModeExitInlineSaved');
        ConcreteEvent.fire('EditModeExitInline', {
            action: 'save_inline'
        });
    });

    $('.ccm-panel-detail-form-actions button').click(function(){
        changeAction();
    });

    var changeAction = function(changes, source) {
        var ht = $("#<?php echo $bID; ?>_tabledata").handsontable('getInstance');
        var rowList = ht.getData(0,0, ht.countRows() -2, ht.countCols() - 2);
        $("#<?php echo $bID; ?>_table_data").val(JSON.stringify(rowList));

        var meta = [];

        var spaninfo =  ht.mergeCells.mergedCellInfoCollection;

        for(i = 0; i < ht.countRows() - 1; i++ ) {

            for(j = 0; j < ht.countCols() - 1; j++) {
                meta.push({row:i, col:j, className:ht.getCellMeta(i, j).className});
            }
        }

        for (i = 0; i < spaninfo.length; i++) {
            var cellmeta = spaninfo[i];

            if (cellmeta.rowspan > 1 || cellmeta.colspan > 1) {
                $.each(meta, function() {
                    if (this.row == cellmeta.row && this.col == cellmeta.col ) {
                        this.rowspan = cellmeta.rowspan;
                        this.colspan = cellmeta.colspan;
                    }
                });
            }

        }

        $("#<?php echo $bID; ?>_table_metadata").val(JSON.stringify(meta));

        return true;
    };

    function defaultRenderer(instance, td, row, col, prop, value, cellProperties) {
        Handsontable.renderers.TextRenderer.apply(this, arguments);

        <?php if ($template != 'no_headers.php') { ?>
        if (row == 0) {
            td.style.background = '#EEE';
            td.style.fontWeight = 'bold';
        }
        <?php } ?>

        if (row == instance.countRows() - 1 || col == instance.countCols() - 1) {
            td.style.background = '#666';
        }
    }

    Handsontable.renderers.registerRenderer('defaultRenderer', defaultRenderer); //maps function to lookup string

    $("#<?php echo $bID; ?>_tabledata").handsontable({
        data: <?php echo $value; ?>,
        startRows: 1,
        startCols: 2,
        minRows: 1,
        minCols: 2,
        maxRows: 400,
        maxCols: 200,
        rowHeaders: false,
        colHeaders: false,
        minSpareRows: 1,
        minSpareCols: 1,
        mergeCells: true,
        cells: function (row, col, prop) {
            var cellProperties = {};
            cellProperties.renderer = "defaultRenderer"; //uses lookup map
            return cellProperties;
        },
        contextMenu: {
            items: {
                "row_above": {},
                "row_below": {},
                "col_left": {},
                "col_right": {},
                "hsep2": "---------",
                "remove_row": {name:'Remove row(s)'},
                "remove_col": {name:'Remove columns(s)'},
                "hsep3": "---------",
                "alignment" : {},
                "mergeCells" : {},
                "hsep4": "---------",
                "undo": {},
                "redo": {}
            }
        },
        cell: <?php echo $metadata; ?>,
        mergeCells:  <?php echo $metadata; ?>

    });


</script>

<style>
    .handsontable {
        z-index: 1000;
    }
</style>



 