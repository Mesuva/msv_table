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

<style>
    .table_display .htBold{
        font-weight: bold;
    }

    .table_display td.highlightedRed{
        background: red;
        color: white;
    }

    .table_display td.highlightedGreen{
        background: green;
        color: white;
    }

    .table_display td.highlightedOrange{
        background: orange;
        color: white;
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
        Handsontable.renderers.HtmlRenderer.apply(this, arguments);

        <?php if ($template != 'no_headers.php') { ?>
        if (row == 0) {
            td.style.background = '#EEE';
            td.style.fontWeight = 'bold';
        }
        <?php } ?>

        if (row == instance.countRows() - 1 || col == instance.countCols() - 1) {
            td.style.background = '#AAA';
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
        manualColumnResize: true,
        manualRowResize: true,
        cells: function (row, col, prop) {
            var cellProperties = {};
            cellProperties.renderer = "defaultRenderer"; //uses lookup map
            return cellProperties;
        },
        contextMenu: {
            callback: function(key, options) {
                if(key == 'bold'){
                    //Return index of the currently selected cells as an array [startRow, startCol, endRow, endCol]
                    var sel = this.getSelected() ;
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
                            //var boldValues = '<b>'+this.getDataAtCell(i,j)+'</b>';
                            //this.setDataAtCell(i,j, boldValues);
                            var cell = this.getCell(i,j);
                            $(cell).addClass('htBold');
                            this.setCellMeta(i,j,'className', this.getCellMeta(i,j).className+' htBold');
                        }
                    }
                }

                if(key == 'normalText'){
                    //Return index of the currently selected cells as an array [startRow, startCol, endRow, endCol]
                    var sel = this.getSelected();
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
                            /*var normalValues = this.getDataAtCell(i,j);
                            var regex = /<b\s*[\/]?>/gi;
                            this.setDataAtCell(i,j, normalValues.replace(regex, "").replace(/<[\/]b\s*?>/gi,""));*/
                            var cell = this.getCell(i,j);
                            $(cell).removeClass('htBold');
                            this.setCellMeta(i,j,'className', this.getCellMeta(i,j).className.replace(/htBold/gi,''));
                        }
                    }
                }
            },
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
                "redo": {},
                "hsep5": "---------",
                "bold": {"name": "Bold font"},
                "normalText": {"name": "Normal font"},
                "hsep6": "---------",
                "highlightedMenu": {
                    "name": "Highlight cells",
                    submenu: {
                        items: [
                            {
                                name: "Red",
                                callback: function (key, options) {
                                    if (key == 'highlightedRed'){
                                        //Return index of the currently selected cells as an array [startRow, startCol, endRow, endCol]
                                            var sel = this.getSelected();
                                        var i, j, istart, iend, jstart, jend;

                                        if (sel[0] > sel[2]) {
                                            istart = sel[2];
                                            iend = sel[0];
                                        } else {
                                            istart = sel[0];
                                            iend = sel[2];
                                        }

                                        if (sel[1] > sel[3]) {
                                            jstart = sel[3];
                                            jend = sel[1];
                                        } else {
                                            jstart = sel[1];
                                            jend = sel[3];
                                        }
                                        for (i = istart; i < iend + 1; i++) {
                                            for (j = jstart; j < jend + 1; j++) {
                                                var cell = this.getCell(i,j);
                                                if($(cell).hasClass('highlightedRed')){
                                                    $(cell).removeClass('highlightedRed');
                                                    this.setCellMeta(i,j,'className', this.getCellMeta(i,j).className.replace(/highlightedRed/gi,''));
                                                }else{
                                                    $(cell).addClass('highlightedRed');
                                                    this.setCellMeta(i,j,'className', this.getCellMeta(i,j).className+' highlightedRed');
                                                }
                                            }
                                        }
                                    }
                                },
                                key: "highlightedRed"
                            },
                            {
                                name: "Green",
                                callback: function (key, options) {
                                    if (key == 'highlightedGreen'){
                                        //Return index of the currently selected cells as an array [startRow, startCol, endRow, endCol]
                                        var sel = this.getSelected();
                                        var i, j, istart, iend, jstart, jend;

                                        if (sel[0] > sel[2]) {
                                            istart = sel[2];
                                            iend = sel[0];
                                        } else {
                                            istart = sel[0];
                                            iend = sel[2];
                                        }

                                        if (sel[1] > sel[3]) {
                                            jstart = sel[3];
                                            jend = sel[1];
                                        } else {
                                            jstart = sel[1];
                                            jend = sel[3];
                                        }
                                        for (i = istart; i < iend + 1; i++) {
                                            for (j = jstart; j < jend + 1; j++) {
                                                var cell = this.getCell(i,j);
                                                if($(cell).hasClass('highlightedGreen')){
                                                    $(cell).removeClass('highlightedGreen');
                                                    this.setCellMeta(i,j,'className', this.getCellMeta(i,j).className.replace(/highlightedGreen/gi,''));
                                                }else{
                                                    $(cell).addClass('highlightedGreen');
                                                    this.setCellMeta(i,j,'className', this.getCellMeta(i,j).className+' highlightedGreen');
                                                }
                                            }
                                        }
                                    }
                                },
                                key: "highlightedGreen"
                            },
                            {
                                name: "Orange",
                                callback: function (key, options) {
                                    if (key == 'highlightedOrange'){
                                        //Return index of the currently selected cells as an array [startRow, startCol, endRow, endCol]
                                        var sel = this.getSelected();
                                        var i, j, istart, iend, jstart, jend;

                                        if (sel[0] > sel[2]) {
                                            istart = sel[2];
                                            iend = sel[0];
                                        } else {
                                            istart = sel[0];
                                            iend = sel[2];
                                        }

                                        if (sel[1] > sel[3]) {
                                            jstart = sel[3];
                                            jend = sel[1];
                                        } else {
                                            jstart = sel[1];
                                            jend = sel[3];
                                        }
                                        for (i = istart; i < iend + 1; i++) {
                                            for (j = jstart; j < jend + 1; j++) {
                                                var cell = this.getCell(i,j);
                                                if($(cell).hasClass('highlightedOrange')){
                                                    $(cell).removeClass('highlightedOrange');
                                                    this.setCellMeta(i,j,'className', this.getCellMeta(i,j).className.replace(/highlightedOrange/gi,''));
                                                }else{
                                                    $(cell).addClass('highlightedOrange');
                                                    this.setCellMeta(i,j,'className', this.getCellMeta(i,j).className+' highlightedOrange');
                                                }
                                            }
                                        }
                                    }
                                },
                                key: "highlightedOrange"
                            },
                            {
                                name: "Add Class",
                                // superseeds "global" callback
                                callback: function(key, options) {

                                    if (key == 'highlightedCustomClass'){
                                        var addClassManual = prompt("Please enter class name");
                                        //Return index of the currently selected cells as an array [startRow, startCol, endRow, endCol]
                                        var sel = this.getSelected();
                                        var i, j, istart, iend, jstart, jend;

                                        if (sel[0] > sel[2]) {
                                            istart = sel[2];
                                            iend = sel[0];
                                        } else {
                                            istart = sel[0];
                                            iend = sel[2];
                                        }

                                        if (sel[1] > sel[3]) {
                                            jstart = sel[3];
                                            jend = sel[1];
                                        } else {
                                            jstart = sel[1];
                                            jend = sel[3];
                                        }
                                        for (i = istart; i < iend + 1; i++) {
                                            for (j = jstart; j < jend + 1; j++) {
                                                var cell = this.getCell(i,j);
                                                if (addClassManual != null) {
                                                    //console.log( person );
                                                    $(cell).addClass(addClassManual);
                                                    this.setCellMeta(i,j,'className', this.getCellMeta(i,j).className+' '+addClassManual);
                                                }
                                            }
                                        }
                                    }
                                },
                                key: "highlightedCustomClass"
                            },
                            {
                                name: "Remove Class",
                                //superseeds "global" callback
                                callback: function(key, options) {

                                    if (key == 'highlightedRemoveCustomClass'){
                                        var removeClassManual = prompt("Please enter class name to remove");
                                        //Return index of the currently selected cells as an array [startRow, startCol, endRow, endCol]
                                        var sel = this.getSelected();
                                        var i, j, istart, iend, jstart, jend;

                                        if (sel[0] > sel[2]) {
                                            istart = sel[2];
                                            iend = sel[0];
                                        } else {
                                            istart = sel[0];
                                            iend = sel[2];
                                        }

                                        if (sel[1] > sel[3]) {
                                            jstart = sel[3];
                                            jend = sel[1];
                                        } else {
                                            jstart = sel[1];
                                            jend = sel[3];
                                        }

                                        for (i = istart; i < iend + 1; i++) {
                                            for (j = jstart; j < jend + 1; j++) {
                                                var cell = this.getCell(i,j);
                                                if (removeClassManual != null) {
                                                    //console.log( person );
                                                    $(cell).removeClass(removeClassManual);
                                                    this.setCellMeta(i,j,'className', this.getCellMeta(i,j).className.replace(removeClassManual,''));
                                                }
                                            }
                                        }
                                    }
                                },
                                key: "highlightedRemoveCustomClass"
                            }
                        ]
                    }
                }
            }
        },
        cell: <?php echo $metadata; ?>,
        mergeCells:  <?php echo $metadata; ?>

    });


</script>
