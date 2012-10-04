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
require_once (PATH_t3lib . 'class.t3lib_tcemain.php');
require_once (PATH_t3lib . 'class.t3lib_befunc.php');

class Tx_CiMaintainance_Cli_Factory extends t3lib_cli {
	
	protected $hooks;
	protected $config;
	protected $oldPluginElements;
	
	function __construct() {
		// Running parent class constructor
        parent::__construct(); // parent::previously t3lib_cli();
        
        $this->tce = t3lib_div::makeInstance('t3lib_TCEmain');
        $this->tce->stripslashes_values = 0;
        
		$this->cli_options = array_merge($this->cli_options, array());
		$this->cli_help = array_merge(
			$this->cli_help,
			array(
				'name' => 'To-Media-Gallery-Migrator',
				'synopsis' => $this->extKey . '[goof_fotoboek_pi1] filename ###OPTIONS###',
				'description' => 'CLI-Script to migrate Fotogalleries to Media-Gallery',
				'examples' => 'typo3/cli_dispatch.phpsh',
				'author' => '(c) 2012 - Cornelius Illi'
			)
		);
     }
	
	/**
     * CLI engine
     *
     * @param array Command line arguments
     * @return string
     */
    function cli_main($argv) {
        $oldListType = trim( strtolower( (string)$this->cli_args['_DEFAULT'][1] ) );
        $filename = trim( (string)$this->cli_args['_DEFAULT'][2] );
        
        if(is_null($oldListType)) {
        	$this->cli_echo('No list_type specified!'.LF);
        	exit(0);
        }
        
        if(is_null($filename)) {
        	$this->cli_echo('No configuration-file specified!'.LF);
        	exit(0);
        } 	
        
      	switch ($oldListType) {
            case 'goof_fotoboek_pi1':
      	   		$this->importConfigurationFile($filename);
		        $this->createSysFolders();
		        $this->fetchOldPluginElements($oldListType);
		        
		        foreach($this->oldPluginElements as $row) {
		        	$fileCollectionId = $this->createFileCollection( $row );
		        	$this->updatePluginElement( $row, $fileCollectionId );
		        	//$ffTools = t3lib_div::makeInstance("t3lib_flexformtools");
		        	//$ffTools->cleanFlexFormXML('tt_content', 'pi_flexform',$row);        	
		        }
		        
		        // @todo: cli_option --forceDelete delete old t3lib_BEfunc::BEenableFields('tt_content',1);
            break;
            
    		default:
    			$this->cli_validateArgs();
            	$this->cli_help();
            exit;
        }
    }
    
    private function updatePluginElement($row, $fileCollectionId) {
    	$data['tt_content'][ $row['uid'] ] = array (
    		'list_type' => 'mediagallery_gallery'
    	);
    	$this->tce->start($data, Array() );
    	$this->tce->process_datamap();

    	var_dump($row['pi_flexform']);
    	$new = t3lib_BEfunc::getRecord('tt_content', $row['uid']);
    	var_dump($new);
    	exit(0);
    	
    	/**
    	 * 		pi_flexform updated automatically ?
    	 * 		load flexform-xml
    	 * 			insert file-collection-id
    	 */
       
    }
    
    private function fetchOldPluginElements($oldListType) {
    	$where = "CType='list' AND list_type='".$oldListType."'";
    	$where .= t3lib_BEfunc::deleteClause('tt_content').t3lib_BEfunc::BEenableFields('tt_content');
    	
    	$res = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
    			'uid,pid,header,tx_gooffotoboek_path,pi_flexform',
    			'tt_content',
    			$where);
    	if(is_null($res) || count($res) === 0 ) {
    		$this->cli_echo('Could not fetch any old plugin-elements for list_type: '.$oldListType.LF);
    		exit(0);
    	} else {
    		$this->oldPluginElements = $res;
    	}
    }
    
    private function importConfigurationFile($filename) {  	
    	if( t3lib_div::validPathStr($filename) ) {
    		if(!t3lib_div::isAbsPath($filename)) {
    			$filename = t3lib_div::getFileAbsFileName($filename);
    		}
    	} else {
    		$this->cli_echo('The specified filename "'.$filename.'" is not valid!'.LF);
    		exit(0);
    	}
    	
    	if( file_exists($filename) ) {
    		$contents = file_get_contents($filename);
    		$json = json_decode($contents, true);
    		
    		if(is_null($json)) {
    			$this->cli_echo('JSON in specified config-file ("'.$filename.'") cannot be decoded! Please check!'.LF);
    			exit(0);
    		} else {
    			$this->config = $json;
    		}
    	}
    }
    
    private function createSysFolders() {
    	// checking pre-conditions
    	if( !array_key_exists("sys_folder_label", $this->config ) || empty($this->config["sys_folder_label"]) ) {
    		$this->cli_echo('"sys_folder_label" not specified in config-file!'.LF);
    		exit(0);
    	}
    	
    	if( !array_key_exists("entry_points", $this->config) || empty($this->config["entry_points"]) ) {
    		$this->cli_echo('"entry_points" not specified in config-file!'.LF);
    		exit(0);
    	}
    	
    	// create one sys-folder for each key and set its UID as value
    	foreach($this->config["entry_points"] as $key => $value) {
    		$pid = intval($key);
    		if($pid === 0) {
    			$this->cli_echo('Skipping value "'.$key.'". Not a valid UID.'.LF);
    			continue;
    		} 
    		
    		$this->createSysFolderWithPid($pid);
    	}
    }
    
    private function createSysFolderWithPid($pid) {
    	// pre-condition: if folder exists - return
    	$where = "title='".$this->config["sys_folder_label"]. "' AND pid=".$pid;
    	$where .= t3lib_BEfunc::deleteClause('pages').t3lib_BEfunc::BEenableFields('pages');
    	
    	$existingFolder = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow("uid","pages",$where);    	
    	if($existingFolder) {
    		$this->config["entry_points"][$pid] = $existingFolder['uid'];
    		$this->cli_echo('Found existing folder with uid:'.$existingFolder['uid'].LF);
    		return;
    	}
    	
    	$tempUid = 'NEW'.uniqid();
    	$tstamp = time();
    	
    	$data = array(
    		'pages' => array(
    			$tempUid => array (
    				'pid' => $pid,
    				'title' => $this->config["sys_folder_label"],
    				'doktype' => 254,
    				'hidden' => 0,
    				'crdate' => $tstamp,
    				'tstamp' => $tstamp
    			)		
    		)		
    	);
    	 	
    	$this->tce->start($data, Array() );
    	$this->tce->process_datamap();
    	
    	if (count($this->tce->errorLog) !== 0) {
    		var_dump($this->tce->errorLog);
    		exit(0);
    	}
    	
    	$folderUid = $this->tce->substNEWwithIDs[$tempUid];
    	
    	if(!$folderUid) {
    		$this->cli_echo('New folder could not be created!'.LF);
    		exit(0);
    	} else {
    		$this->config["entry_points"][$pid] = $folderUid;
    		$msg = 'New folder "'.$this->config["sys_folder_label"];
    		$msg .= ' ('.$folderUid.')" created on page '.$pid.LF;
    		$this->cli_echo($msg);
    	}
    }
       
    private function createFileCollection($row) {
    	$tempUid = 'NEW'.uniqid();
    	$pid = $this->findNextPidFor($row['pid']);
    	$path = $this->parsePath( $row["tx_gooffotoboek_path"] );
    	$title = $this->createFileCollectionTitle($row['header']);
    	$data = array(
    			'sys_file_collection' => array(
    					$tempUid => array (
    							'pid' => $pid,
    							'type' => 'folder',
    							'storage' => 1,
    							'folder' => $path,
    							'title' => $title
    					)
    			)
    	);
    	
    	$this->tce->start($data, Array() );
    	$this->tce->process_datamap();
    	
    	if (count($this->tce->errorLog) !== 0) {
    		var_dump($this->tce->errorLog);
    		exit(0);
    	}
    	 
    	return $this->tce->substNEWwithIDs[$tempUid];
    }
    
    private function createFileCollectionTitle($row) {
    	$header = trim( $row['header'] );
    	if( !empty($header) ) {
    		// return header, if not empty
    		return $header;
    	} else {
			// if header is empty, use last part of path as title, e.g
			// fileadmin/hpi/FG_ITS/fotogalerien/ausfluege/"hochseilgarten"
    		$pathSegments = t3lib_div::trimExplode('/', $row["tx_gooffotoboek_path"], true);
    		return $pathSegments[-1];
    	}
    }
    
    private function parsePath($path) {
    	$pos = strripos($path,'fileadmin');
    	if($pos === false) return $path;
    	return substr($path, $pos);
    }
    
    private function findNextPidFor($pid) {
    	if( array_key_exists($pid, $this->config['entry_points'] ) ) return $pid;
    	$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow('pid','pages','uid='.$pid);
    	if(!$row) {
    		$this->cli_echo('Failed to find next page up in hierarchy.'.LF);
    		exit(0);
    	}
    	return $this->findNextPidFor( $row['pid'] );
    }
}
$factory = t3lib_div::makeInstance('Tx_CiMaintainance_Cli_Factory');
/* @var $factory Tx_CiMaintainance_Cli_Factory */
$factory->cli_main($_SERVER['argv']);