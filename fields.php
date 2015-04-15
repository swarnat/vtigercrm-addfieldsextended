<?php
//
// Created by Stefan Warnat
//
ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_STRICT & ~E_DEPRECATED);

// Turn on debugging level
global $Vtiger_Utils_Log;
$Vtiger_Utils_Log = true;

include_once('vtlib/Vtiger/Menu.php');
include_once('vtlib/Vtiger/Module.php');

$adb->dieOnError = true;

/**
 * @param $fromModule Select the module, which should get the new field
 * @param $fieldName fieldName (must be a-zA-Z0-9_)
 * @param $fieldLabel label of the new field
 * @param $options Array with Options for this Picklist
 */
function addPicklist($modules, $fieldName, $fieldLabel, $options) {
    if(!is_array($modules)) {
        $modules = array($modules);
    }

    $initValues = false;
    foreach($modules as $index => $targetModuleName) {

        // Welches Label soll das Feld bekommen?
        //$fieldLabel = 'Preisliste';

        // -------- ab hier nichts mehr anpassen !!!!
        $module = Vtiger_Module::getInstance($targetModuleName);

        $field = \Vtiger_Field::getInstance('pr_cost', $module);
        $blocks = Vtiger_Block::getAllForModule($module);
        $block = $blocks[0];

        $field1 = new Vtiger_Field();
        $field1->name = $fieldName;
        $field1->label= $fieldLabel;
        $field1->table = $module->basetable;
        $field1->column = $fieldName.$index;
        $field1->columntype = 'VARCHAR(100)';
        $field1->uitype = 16;

        $field1->typeofdata = 'V~O';
        $block->addField($field1);

        if($initValues === false) {
            $field1->setPicklistValues( $options );
            $initValues = true;
        }

    }
}

/**
 * @param $fromModule Select the module, which should get the new field
 * @param $targetModuleNameArray Which modules should be selectable
 * @param $fieldName fieldName (must be a-zA-Z0-9_)
 * @param $fieldLabel label of the new field
 *
 */
function addRelatedField($fromModule, $targetModuleNameArray, $fieldName, $fieldLabel) {
    // Welches Modul soll bearbeitet werden?
    $targetModuleName = $fromModule;

    // Ein noch nicht verwendeter Feldname für diese Relation. Wird gleichzeitig der Spaltenname
    //$fieldname = 'pricebook';

    // Zu welchem Block soll Feld hinzugefügt werden. (Wert muss aus vtiger_blocks gelesen werden und ist meistens UNGLEICH des Blocktitels)
    // Wenn Wert ungekannt, einfach auf null belassen. Dann wird Feld in den ersten BLock eingefügt
    $blockName = null;

    // Welches Label soll das Feld bekommen?
    //$fieldLabel = 'Preisliste';

    // Welche Module sollen ausgewählt werden können?
    $relatedModules = $targetModuleNameArray;

    // -------- ab hier nichts mehr anpassen !!!!
    $module = Vtiger_Module::getInstance($targetModuleName);

    if($blockName === null) {
        $blocks = Vtiger_Block::getAllForModule($module);
        $block = $blocks[0];
    } else {
        $block = Vtiger_Block::getInstance ($blockName, $module);
    }

    $field1 = new Vtiger_Field();
    $field1->name = $fieldName;
    $field1->label= $fieldLabel;
    $field1->table = $module->basetable;
    $field1->column = $fieldName;
    $field1->columntype = 'VARCHAR(100)';
    $field1->uitype = 10;
    $field1->typeofdata = 'V~O';
    $block->addField($field1);
    $field1->setRelatedModules($relatedModules);
}

if($_POST['submit'] == 'ok') {

	addRelatedField('Accounts', array('Invoice'), 'invoiceid', 'Invoice');

    addPicklist(array('Leads', 'Accounts', 'Contacts'),  'testfield', 'Fieldlabel', array('value1', 'value2', 'value3'));
} else {
    ?>
    Input 'ok' to start
    <form method="POST" action="#">
        <input type="text" name="submit" value=""><input type="submit" name="absenden" value="Start">
    </form>
    <?php
}
