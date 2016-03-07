<?php
/**
 * Created by IntelliJ IDEA.
 * User: Tonik
 * Date: 7. 3. 2016
 * Time: 18:37
 */

namespace SoftwareStudio\DatabaseMigration\entity;


class MigrationItem {

	/** @var \SplFileInfo */
	private $file;

	/** @var int */
	private $orderNumber;
	/**
	 * MigrationItem constructor.
	 * @param $file
	 */
	public function __construct( \SplFileInfo $folder, $filename, $orderNumber ) {

		$this->file = new \SplFileInfo($folder->getPathname() . '/' .$filename);
		$this->orderNumber = (int)$orderNumber;
	}

	/**
	 * @return \SplFileInfo
	 */
	public function getFile() { return $this->file; }

	/**
	 * @return int
	 */
	public function getOrderNumber() { return $this->orderNumber; }




}