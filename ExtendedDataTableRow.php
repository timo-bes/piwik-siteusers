<?php

/**
 * Extended DataTable_Row
 *
 * Author:   Timo Besenreuther
 *           EZdesign.de
 * Created:  2010-08-30
 * Modified: 2010-08-31
 */

class Piwik_SiteUsers_ExtendedDataTableRow
		extends Piwik_DataTable_Row {
	
	public function sumRow(Piwik_SiteUsers_ExtendedDataTableRow $rowToSum) {
		foreach ($rowToSum->getColumns() as $columnToSumName => $columnToSumValue) {
			if ($columnToSumName != 'label' && $columnToSumName != 'iduser') {
				if ($columnToSumName == 'username') {
					// take the latest username
					$newValue = $columnToSumValue;
				} else {
					// sum other columns
					$thisColumnValue = $this->getColumn($columnToSumName);
					$newValue = $this->sumRowArray($thisColumnValue, $columnToSumValue);
				}
				$this->setColumn($columnToSumName, $newValue);
			}
		}
	}
	
}

?>