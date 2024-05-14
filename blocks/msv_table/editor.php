<?php  defined('C5_EXECUTE') or die("Access Denied.");

$value = '[[null,null]]';
$metadata = '[]';

if ($table_data) {
    $value = $table_data;
}

if ($table_metadata) {
    $metadata = $table_metadata;
}

$mergecells = json_decode($metadata, true);

$newmergecells = [];

foreach($mergecells as $mc) {
    if (isset($mc['rowspan']) || isset($mc['colspan'])) {
        $newmergecells[] = $mc;
    }
}

$mergedata = json_encode($newmergecells);

?>

<style>
    .htCore td, .htCore th {
        font-family: arial, sans-serif !important;
    }

    .htCore .htBold{
        font-weight: bold;
    }

    .htCore td.highlighted{
        background: yellow;
    }

    .htCore td.italic{
        font-style: italic;
    }

    .htCore .highlight {
        color: inherit !important;
    }

    .wtHolder {
        height: auto !important;
    }
    .htContextMenu {
        z-index: 100000 !important;
    }
    .ht_master .wtHolder {
        overflow: auto;
    }


</style>

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
        var ht = $("#<?php echo $uniqueid; ?>_tabledata").handsontable('getInstance');
        var rowList = ht.getData(0,0, ht.countRows() -2, ht.countCols() - 2);
        $("#<?php echo $uniqueid; ?>_table_data").val(JSON.stringify(rowList));

        var meta = [];

        var spaninfo =  ht.getPlugin('mergeCells').mergedCellsCollection.mergedCells;

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

        $("#<?php echo $uniqueid; ?>_table_metadata").val(JSON.stringify(meta));

        return true;
    };

    function defaultRenderer(instance, td, row, col, prop, value, cellProperties) {
        Handsontable.renderers.HtmlRenderer.apply(this, arguments);

<!--        --><?php //if ($template != 'no_headers.php') { ?>
//        if (row == 0) {
//            td.style.background = '#EEE';
//            td.style.fontWeight = 'bold';
//        }
//        <?php //} ?>

        if (row == instance.countRows() - 1 || col == instance.countCols() - 1) {
            td.style.background = '#AAA';
        }

        if (!value) {
            td.innerHTML = '';
        }
    }

    Handsontable.renderers.registerRenderer('defaultRenderer', defaultRenderer); //maps function to lookup string

    var maxwidth = $("#<?php echo $uniqueid; ?>_tabledata").width();

    $("#<?php echo $uniqueid; ?>_tabledata").handsontable({
        data: <?php echo $value; ?>,
        startRows: 1,
        startCols: 2,
        minRows: 1,
        minCols: 2,
        maxRows: 1000,
        maxCols: 500,
        rowHeaders: false,
        colHeaders: false,
        minSpareRows: 1,
        minSpareCols: 1,
        manualColumnResize: true,
        manualRowResize: true,
        modifyColWidth: function(width, col){
            if(width > maxwidth){
                return maxwidth * 0.65
            }
        },
        preventOverflow: 'horizontal',
        cells: function (row, col, prop) {
            var cellProperties = {};
            cellProperties.renderer = "defaultRenderer"; //uses lookup map
            return cellProperties;
        },
        afterContextMenuShow :function(key, options){
            var sel = this.getSelected()[0];
            var i =sel[0], j =sel[1];
            var cell = this.getCell(i,j);
            if($(cell).hasClass('htBold')){
                $('.htContextMenu .htCore tr td div').filter(function() {
                    if($(this).text() == "Bold"){
                        $(this).append('<span class="selected">✓</span>');
                    }
                });
            }

            if($(cell).hasClass('italic')){
                $('.htContextMenu .htCore tr td div').filter(function() {
                    if($(this).text() == "Italic"){
                        $(this).append('<span class="selected">✓</span>');
                    }
                });
            }

            if($(cell).hasClass('highlighted')){
                $('.htContextMenu .htCore tr td div').filter(function() {
                    if($(this).text() == "Highlight"){
                        $(this).append('<span class="selected">✓</span>');
                    }
                });
            }
        },
        contextMenu: {
            callback: function(key, options) {
                /*
                 * For bold font
                 */
                if(key === 'bold'){
                    htformatting(this, 'htBold');
                }

                /*
                 * For highlight cell
                 */
                if (key === 'highlighted'){
                    htformatting(this, 'highlighted');
                }

                /*
                * For italic font
                */
                if (key === 'italic'){
                    htformatting(this, 'italic');
                }
            },
            items: {
                "row_above": {},
                "row_below": {},
                "col_left": {},
                "col_right": {},
                "hsep2": "---------",
                "remove_row": {name:'<?php echo t('Remove row(s)');?>'},
                "remove_col": {name:'<?php echo t('Remove columns(s)');?>'},
                "hsep3": "---------",
                "alignment" : {},
                "mergeCells" : {},
                "hsep4": "---------",
                "undo": {},
                "redo": {},
                "hsep5": "---------",
                "bold": {"name": "<?php echo t('Bold');?>"},
                "italic": {"name": "<?php echo t('Italic');?>"},
                "highlighted": {"name": "<?php echo t('Highlight');?>"}

            }
        },
        cell: <?php echo $metadata; ?>,
        mergeCells:  <?php echo $mergedata; ?>

    });

    function htformatting(ht, classname) {
        var sel = ht.getSelected()[0];

        var i, j, istart, iend, jstart, jend ;
        if(sel[0] > sel[2] ){
            istart = sel[2] ; iend = sel[0] ;
        }else{
            istart = sel[0] ; iend = sel[2] ;
        }

        if(sel[1] > sel[3] ){
            jstart = sel[3] ; jend = sel[1] ;
        }else{
            jstart = sel[1] ; jend = sel[3] ;
        }


        for(i = istart; i < iend+1; i++){
            for(j = jstart; j < jend+1; j++){
                var cell = ht.getCell(i,j);
                var jcell = $(cell);

                if (!jcell.hasClass('htmulticell')) {
                    if(jcell.hasClass(classname)){
                        jcell.removeClass(classname);
                        ht.setCellMeta(i, j, 'className', ht.getCellMeta(i, j).className.replace(classname, ''));
                    }else{
                        jcell.addClass(classname);
                        var existingClasses =  ht.getCellMeta(i,j).className;

                        if (typeof existingClasses === 'undefined') {
                            existingClasses = '';
                        }

                        ht.setCellMeta(i,j,'className', existingClasses + ' ' + classname);
                    }

                    if (jcell.attr('colspan') || jcell.attr('rowspan')) {
                        jcell.addClass('htmulticell');
                    }
                }
            }
        }

        $('.htmulticell').removeClass('htmulticell');
    }

</script>
