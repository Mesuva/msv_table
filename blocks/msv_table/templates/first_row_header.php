<?php  defined('C5_EXECUTE') or die("Access Denied.");
$nh = Core::make('helper/navigation');
$json = Core::make('helper/json');
$th = Core::make('helper/text');
?>

<?php

$header = true;

if (!empty($table_data)): ?>

	<div class="table_display">

		<?php

 		$data = false;
		$metadata = false;

		$json_as_object = $json->decode($table_data);

		if(is_array($json_as_object) || is_object($json_as_object)){
			$array = array();
			foreach($json_as_object as $key => $value){
				$array[$key] = $value;
			}
			$data = $array;
		}

		$json_as_object = $json->decode($table_metadata);

		if(is_array($json_as_object) || is_object($json_as_object)){
			$array = array();
			foreach($json_as_object as $key => $value){
				$array[$value->row][$value->col] = $value;
			}
			$metadata = $array;
		}


		$table = '';

		if (empty($data)) {
			$table = '';
		} else {
			$custom_css_class = '';
			$b = Block::getByID($bID, Page::getCurrentPage(), $this->block->getAreaHandle());
			if ($b && $b->getCustomStyle() && $b->getCustomStyle()->getStyleSet()) {
				$custom_css_class = $b->getCustomStyle()->getStyleSet()->getCustomClass();
			}

			$table = '<table class="table ' . $custom_css_class . ' ">';

			if ($header) {
				$table .= '<thead>';
			}

			$rowcount = 0;

			$redundantcells = array();

			foreach($data as $row) {
				$colcount = 0;


				if ($rowcount ==1 && $header) {
					$table .= '<tbody><tr>';
				} else {
					$table .= '<tr>';
				}



				foreach($row as $val) {

					if (($rowcount == 0 || $colcount == 0) && $header) {
						$ct = 'th'  ;
					} else {
						$ct = 'td';
					}

					$val =  trim($th->entities($val));
					$empty = '';

					if ($val == '') {
						$empty = ' empty';
					}

					if ($metadata[$rowcount][$colcount]->colspan > 1) {

						for($i = $colcount + 1; $i < $metadata[$rowcount][$colcount]->colspan + $colcount; $i++ ) {
							$redundantcells[$rowcount][$i] = true;
						}
					}


					if ($metadata[$rowcount][$colcount]->rowspan > 1 && $rowcount != 0) {

						for($i = $rowcount + 1; $i < $metadata[$rowcount][$colcount]->rowspan + $rowcount ; $i++ ) {
							$redundantcells[$i][$colcount] = true;

							for($j = $colcount + 1; $j < $metadata[$rowcount][$colcount]->colspan + $colcount; $j++ ) {
								$redundantcells[$i][$j] = true;
							}

						}

					}


					if (!isset($redundantcells[$rowcount][$colcount])) {

						if ($rowcount == 0) {
							$table .= '<' . $ct . ($metadata[$rowcount][$colcount]->colspan > 1 ? ' colspan="' . $metadata[$rowcount][$colcount]->colspan . '"' : '') .  ' class="col' . $colcount . $empty . ' ' .  $metadata[$rowcount][$colcount]->className . '">' . $val . '</' . $ct . '>';
						} else{
							$table .= '<' . $ct . ($metadata[$rowcount][$colcount]->colspan > 1 ? ' colspan="' . $metadata[$rowcount][$colcount]->colspan . '" ': '') .  ($metadata[$rowcount][$colcount]->rowspan > 1 ? 'rowspan="' . $metadata[$rowcount][$colcount]->rowspan . '"' : '') . ' class="col' . $colcount . $empty . ' ' . $metadata[$rowcount][$colcount]->className  . '">' . $val . '</' . $ct . '>';
						}

					}

					$colcount++;
				}


				$table .= '</tr>';

				if ($rowcount == 0 && $header) {
					$table .= '</thead>';
				}

				$rowcount++;
			}

			if ($rowcount > 0 && $header) {
				$table .= '</tbody>';
			}

			$table .= '</table>';
		}

		echo $table;

		?>
	</div>

<?php  endif; ?>

<style>
	.table_display .htRight {
		text-align: right;
	}

	.table_display .htCenter {
		text-align: center;
	}

	.table_display .htJustify {
		text-align: justify;
	}

	.table_display .htBottom {
		vertical-align: bottom;
	}

	.table_display .htMiddle {
		vertical-align: middle;
	}
</style>






