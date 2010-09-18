<?php

/**
 * Site Users Plugin
 * Archive
 *
 * Author:   Timo Besenreuther
 *           EZdesign.de
 * Created:  2010-09-18
 * Modified: 2010-09-18
 */

class Piwik_SiteUsers_Archive {
	
	/* Current archive processing variables */
	private $idsite;
	private $period;
	private $startDate;
	private $endDate;
	
	/** Current archive processing object
	 * @var Piwik_ArchiveProcessing */
	private $archiveProcessing;
	
	private static $instance;
	/** Get singleton instance
	 * @return Piwik_SiteUsers_Archive */
	public static function getInstance() {
		if (self::$instance == null) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	/** Log user action */
	public static function log($userName, $action, $idsite, $idvisit) {
		$model = Piwik_SiteUsers_Model::getInstance();
		
		switch ($action) {
		case 'login':
			$model->logLogin($userName, $idsite, $idvisit);
			break;
		case 'logout':
			$model->logLogout($userName, $idvisit);
			break;
		}
	}
	
	/** Get data table from archive
     * @return Piwik_DataTable */
    public static function getDataTable($name, $idsite, $period, $date) {
    	Piwik::checkUserHasViewAccess($idsite);
    	$name = 'SiteUsers_'.$name;
		$archive = Piwik_Archive::build($idsite, $period, $date);
		$dataTable = $archive->getDataTable($name);
		return $dataTable;
    }
	
	/** Build archive for a single day */
	public static function archiveDay(Piwik_ArchiveProcessing $archive) {
		$self = self::getInstance();
		$self->extractArchiveProcessing($archive);
		
		$model = Piwik_SiteUsers_Model::getInstance();
		$data = $model->dayAnalyzeLogins($self->idsite, $self->startDate, $self->endDate);
		$self->archiveDataArray('logins', $data);
	}
	
	/** Build DataTable from array and archive it */
	private function archiveDataArray($keyword, &$data) {
		$dataTable = new Piwik_DataTable();
		foreach ($data as &$row) {
			$dataTable->addRow(new Piwik_DataTable_Row(
				array(Piwik_DataTable_Row::COLUMNS => $row))
			);
		}
		$name = 'SiteUsers_'.$keyword;
		$this->archiveProcessing->insertBlobRecord($name, $dataTable->getSerialized());
		destroy($dataTable);
	}
	
	/** Build archive for a period */
	public static function archivePeriod(Piwik_ArchiveProcessing $archive) {
		$archive->archiveDataTable(array(
			'SiteUsers_logins'
		));
	}
	
	/** Extract values from ArchiveProcessing */
	private function extractArchiveProcessing(Piwik_ArchiveProcessing $archive) {
		$this->archiveProcessing = $archive;
		$this->idsite = intval($archive->idsite);
		$this->period = $archive->period;
		$this->startDate = $archive->getStartDatetimeUTC();
		$this->endDate = $archive->getEndDatetimeUTC();
	}
	
}