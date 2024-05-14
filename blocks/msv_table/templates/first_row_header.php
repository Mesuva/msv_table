<?php  defined('C5_EXECUTE') or die("Access Denied.");
use \Michelf\Markdown;
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
			$table = '<div class="table-responsive">';
			$table .= '<table class="table table-bordered">';

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

					//$val =  trim($th->entities($val));// add in comment to display HTML content like image
					$empty = '';

					if ($val == '') {
						$empty = ' empty';
					}

					$val = nl2br($val);
					$val = Markdown::defaultTransform($val);
					$val = str_replace(array('<p>', '</p>'),'', $val);

                    $colspan = 0;

                    if (isset($metadata[$rowcount][$colcount]->colspan)) {
                        $colspan = $metadata[$rowcount][$colcount]->colspan;
                    }

					if ($colspan > 1) {
						for($i = $colcount + 1; $i < $colspan + $colcount; $i++ ) {
							$redundantcells[$rowcount][$i] = true;
						}
					}

                    $rowspan = 0;
                    if (isset($metadata[$rowcount][$colcount]->rowspan)) {
                        $rowspan = $metadata[$rowcount][$colcount]->rowspan;
                    }

					if ($rowspan > 1 && $rowcount != 0) {

						for($i = $rowcount + 1; $i < $rowspan + $rowcount ; $i++ ) {
							$redundantcells[$i][$colcount] = true;

							for($j = $colcount + 1; $j < $colspan + $colcount; $j++ ) {
								$redundantcells[$i][$j] = true;
							}

						}

					}


					if (!isset($redundantcells[$rowcount][$colcount])) {

                        $className = '';
                        if (isset($metadata[$rowcount][$colcount]->className)) {
                            $className = $metadata[$rowcount][$colcount]->className;
                        }

						if ($rowcount == 0) {
							$table .= '<' . $ct . ($colspan > 1 ? ' colspan="' . $colspan . '"' : '') .  ' class="col' . $colcount . $empty . ' ' .  $className . '">' . $val . '</' . $ct . '>';
						} else{
							$table .= '<' . $ct . ($colspan > 1 ? ' colspan="' . $colspan . '" ': '') .  ($rowspan > 1 ? ' rowspan="' . $rowspan . '"' : '') . ' class="col' . $colcount . $empty . ' ' . $className  . '">' . $val . '</' . $ct . '>';
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
			$table .= '</div>';
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
	.table_display .htBold{
	        font-weight: bold;
	}

	.table_display td.highlighted{
		background: yellow;
	}

	.table_display td.italic{
		font-style: italic;
	}
</style>






