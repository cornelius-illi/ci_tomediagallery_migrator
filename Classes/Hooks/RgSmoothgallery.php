<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Cornelius Illi <cornelius.illi@student.hpi.uni-potsdam.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 * The shell call is
 * /www/typo3/php cli_dispatch.phpsh EXTKEY TASK
 * 
 * @author	Cornelius Illi <Cornelius.Illi@student.hpi.uni-potsdam.de>
 * @package TYPO3
 */

if (!defined('TYPO3_cliMode')) {
	die('Access denied: CLI only.');
}

$_EXTKEY = 'ci_tomediagallery_migrator';

class Tx_CiTomediagalleryMigrator_Cli_RgSmoothgallery extends t3lib_cli {
	
	/**
	 * 
	 * @param String $filename
	 */
	public function execute($filename) {
		$no_error = true;
	
		// @todo implementation
		
		if($no_error) {
			$this->cli_echo(LF.'All "rg_smoothgallery" galleries have been migrated.'.LF);
		} else {
			$this->cli_echo('Execution aborded due to previous errors!'.LF);
		}
	}
}
    
?>