<?php
// Author: Ryan Hewitt - http://www.mesuva.com.au
namespace Concrete\Package\MsvTable;
use Concrete\Core\Package\Package;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Asset\Asset;

class Controller extends Package {

    protected $pkgHandle = 'msv_table';
    protected $appVersionRequired = '8.0';
    protected $pkgVersion = '1.2';

    public function getPackageDescription() {
        return t("A block to quickly enter and display tabular data.");
    }

    public function getPackageName() {
        return t("Table Block");
    }

    public function install() {
        $pkg = parent::install();
        $this->configurePackage($pkg);
    }

    public function configurePackage($pkg) {
        $blk = BlockType::getByHandle('msv_table');
        if(!is_object($blk) ) {
            BlockType::installBlockType('msv_table', $pkg);
        }
    }

    public function on_start() {
        $al = AssetList::getInstance();

        $al->register( 'javascript', 'handsontable', 'js/handsontable.full.min.js', array('version' => '6.2.2', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => false, 'combine' => false), $this );
        $al->register( 'css', 'handsontable', 'css/handsontable.full.min.css', array('version' => '6.2.2', 'position' => Asset::ASSET_POSITION_HEADER, 'minify' => false, 'combine' => false), $this );
        $al->registerGroup('handsontable',

            array(
                array('javascript', 'handsontable'),
                array('css', 'handsontable'),
            )
        );
    }
}
