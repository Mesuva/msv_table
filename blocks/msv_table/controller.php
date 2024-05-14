<?php
namespace Concrete\Package\MsvTable\Block\MsvTable;
use \Concrete\Core\Block\BlockController;
use Concrete\Core\Support\Facade\Application;

class Controller extends BlockController {

	protected $btName = 'Table';
	protected $btTable = 'btMSVTable';

	protected $btInterfaceWidth = "700";
	protected $btInterfaceHeight = "450";

	protected $btCacheBlockOutput = true;
	protected $btCacheBlockOutputOnPost = true;
	protected $btCacheBlockOutputForRegisteredUsers = false;

	protected $btSupportsInlineEdit = true;
	protected $btSupportsInlineAdd = true;
    protected $btDefaultSet = 'basic';

	public function getSearchableContent() {
		$content = array();
		$content[] = $this->table_data;
		return implode(' - ', $content);
	}

	public function getBlockTypeName()
	{
		return t($this->btName);
	}

	public function getBlockTypeDescription() {
		return t("An inline editable table block");
	}

	public function save($args) {

		if ($args['table_data'] == '[[null],[null]]' || $args['table_data'] == '[[null]]') {
			$args['table_data'] = '';
			$args['table_metadata'] = '';
		}

		parent::save($args);
	}

	public function add(){
        $app = Application::getFacadeApplication();
        $uniqueid = $app->make('helper/validation/identifier')->getString(10);
        $this->set('uniqueid', $uniqueid);
        $this->set('table_data', '');
        $this->set('table_metadata', '');
		$this->requireAsset('handsontable');
	}

	public function edit() {
        $app = Application::getFacadeApplication();
        $uniqueid = $app->make('helper/validation/identifier')->getString(10);
        $this->set('uniqueid', $uniqueid);
		$this->requireAsset('handsontable');
	}

	public function composer() {
		$this->requireAsset('handsontable');
	}

}
