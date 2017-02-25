<?php

/**
 *	This represents an archived collection of search analytics.
 *	@author Nathan Glasl <nathan@silverstripe.com.au>
 */

class ExtensibleSearchArchive extends DataObject {

	private static $db = array(
		'StartingDate' => 'Date',
		'EndingDate' => 'Date'
	);

	private static $has_one = array(
		'ExtensibleSearchPage' => 'ExtensibleSearchPage'
	);

	private static $has_many = array(
		'HistorySummary' => 'ExtensibleSearchArchived'
	);

	private static $default_sort = 'ID DESC';

	private static $summary_fields = array(
		'TitleSummary'
	);

	private static $field_labels = array(
		'TitleSummary' => 'Date Range'
	);

	public function canCreate($member = null) {

		return false;
	}

	public function canDelete($member = null) {

		return false;
	}

	/**
	 *	The archive date range.
	 *
	 *	@return string
	 */

	public function getTitle() {

		$starting = date('F Y', strtotime($this->StartingDate));
		$ending = date('F Y', strtotime($this->EndingDate));
		return "{$starting} to {$ending}";
	}

	public function getCMSFields() {

		$fields = parent::getCMSFields();

		// Remove any fields that are not required in their default state.

		$fields->removeByName('StartingDate');
		$fields->removeByName('EndingDate');
		$fields->removeByName('ExtensibleSearchPageID');
		$fields->removeByName('HistorySummary');

		// Instantiate the archived collection of search analytics.

		$fields->addFieldToTab('Root.Main', GridField::create(
			'HistorySummary',
			_t('ExtensibleSearch.Summary','Summary'),
			$this->HistorySummary(),
			$summaryConfiguration = GridFieldConfig_Base::create()
		)->setModelClass('ExtensibleSearchArchived'));

		// Instantiate an export button.

		$summaryConfiguration->addComponent(new GridFieldExportButton());

		// Update the custom summary fields to be sortable.

		$summaryConfiguration->getComponentByType('GridFieldSortableHeader')->setFieldSorting(array(
			'FrequencyPercentage' => 'Frequency'
		));
		$summaryConfiguration->removeComponentsByType('GridFieldFilterHeader');

		// Allow extension customisation.

		$this->extend('updateExtensibleSearchArchiveCMSFields', $fields);
		return $fields;
	}

	/**
	 *	The archive date range as HTML.
	 *
	 *	@return html
	 */

	public function getTitleSummary() {

		$starting = date('F Y', strtotime($this->StartingDate));
		$ending = date('F Y', strtotime($this->EndingDate));

		// The following is required so HTML isn't automatically escaped.

		$output = HTMLText::create();
		$output->setValue("<strong>{$starting}</strong> to <strong>{$ending}</strong>");
		return $output;
	}

}
