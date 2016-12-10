<?php
require_once(dirname(__FILE__).'/Table.php');

class SGPB_SubscribersView extends SGPB_Table
{
	public function __construct()
	{
		global $wpdb;
		parent::__construct('');

		$this->setRowsPerPage(SG_APP_POPUP_TABLE_LIMIT);
		$this->setTablename($wpdb->prefix.'sg_subscribers');
		$this->setColumns(array(
			'id',
			'firstName',
			'lastName',
			'email',
			'subscriptionType'
		));
		$this->setDisplayColumns(array(
			'bulk'=>'<input class="subs-bulk" type="checkbox">',
			'id' => 'ID',
			'firstName' => 'First name',
			'lastName' => 'Last name',
			'email' => 'Email',
			'subscriptionType' => 'Subscription type'
		));
		$this->setSortableColumns(array(
			'id' => array('id', false),
			'firstName' => array('firstName', true),
			$this->setInitialSort(array(
				'id' => 'DESC'
			))
		));
	}

	public function customizeRow(&$row)
	{
		$row[5] = $row[4];
		$row[4] = $row[3];
		$row[3] = $row[2];
		$row[2] = $row[1];
		$row[1] = $row[0];
		$id = $row[0];
		$row[0] = '<input type="checkbox" class="subs-delete-checkbox" data-delete-id="'.$id.'">';
	}

	public function customizeQuery(&$query)
	{
		$searchQuery = '';
		global $wpdb;
		if(isset($_POST['s']) && !empty($_POST['s']))
		{
			$searchCriteria = $_POST['s'];
			$searchQuery = " WHERE title LIKE '%$searchCriteria%' ";
		}
		$query .= $searchQuery;
	}
}
