<?php

########################################################################
# Extension Manager/Repository config file for ext "ci_tomediagallery_migrator".
#
# Auto generated 11-01-2012 12:38
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'To MediaGallery Migrator',
	'description' => 'Migrates galleries of old extensions to media-gallery (using FAL)',
	'category' => 'be',
	'shy' => 0,
	'version' => '1.0.0',
	'dependencies' => 'extbase,fluid',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Cornelius Illi',
	'author_email' => 'cornelius.illi@student.hpi.uni-potsdam.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-0.0.0',
			'typo3' => '4.4.0-4.5.99',
			'extbase' => '1.2.0-2.0.0',
			'fluid' => '1.2.0-2.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:7:{s:16:"ext_autoload.php";s:4:"ac98";s:12:"ext_icon.gif";s:4:"8c8e";s:17:"ext_localconf.php";s:4:"03e7";s:29:"Classes/Cli/ArchiveSyslog.php";s:4:"2d99";s:23:"Classes/Cli/Factory.php";s:4:"3e7c";s:35:"Classes/Scheduler/ContentUpates.php";s:4:"6b07";s:40:"Classes/Scheduler/FormhandlerChanges.php";s:4:"1cdf";}',
	'suggests' => array(
	),
);

?>