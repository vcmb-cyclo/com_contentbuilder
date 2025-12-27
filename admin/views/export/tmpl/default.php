<?php
/**
 * @package     ContentBuilder
 * @author      Markus Bopp
 * @link        https://www.crosstec.org
 * @copyright   Copyright (C) 2025 by XDA+GIL
 * @license     GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;
use PhpOffice\PhpSpreadsheet\Shared\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


@ob_end_clean();

require_once (JPATH_COMPONENT_ADMINISTRATOR .'/classes/contentbuilder_helpers.php');
//require_once __DIR__ .'/../../../classes/PhpSpreadsheet/Spreadsheet.php';
require __DIR__ . '/../../../librairies/PhpSpreadsheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Joomla\CMS\Factory;

//Font::setAutoSizeMethod(Font::AUTOSIZE_METHOD_EXACT);

$database = Factory::getDbo();

$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()->setCreator("ContentBuilder")->setLastModifiedBy("ContentBuilder");

// Create "Sheet 1" tab as the first worksheet.
// https://phpspreadsheet.readthedocs.io/en/latest/topics/worksheets/adding-a-new-worksheet
$spreadsheet->removeSheetByIndex(0);

$worksheet1 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, substr($this->data->title ?? 'default', 0, 31));
$spreadsheet->addSheet($worksheet1, 0);

// LETTER -> A4.
$worksheet1->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

// Freeze first line.
$worksheet1->freezePane('A2');

$worksheet1->getDefaultRowDimension()->setRowHeight(-1); // auto-hauteur

// First row in grey.
// Appliquer le style à la première ligne
$style = $worksheet1->getStyle('1:1');

// Fond gris
$style->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()
    ->setARGB('c0c0c0');

// Centrage horizontal et vertical
$style->getAlignment()
    ->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
    ->setVertical(PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

// 1 -- Labels.
$labels = $this->data->visible_labels;
$colreserved = 0;

// Case of show_id_column true -> First column reserved.
$col_id = 0;
$reserved_labels = [];
if ($this->data->show_id_column) {
    $col_id = ++$colreserved;
    array_push($reserved_labels, Text::_('COM_CONTENTBUILDER_ID'));
}

// Case of state true -> column reserved.
$col_state = 0;
if ($this->data->list_state) {
    $col_state = ++$colreserved;
    array_push($reserved_labels, Text::_('COM_CONTENTBUILDER_EDIT_STATE'));
}

// Case of publish true -> column reserved.
$col_publish = 0;
if ($this->data->list_publish) {
    $col_publish = ++$colreserved;
    array_push($reserved_labels, Text::_('PUBLISH'));
}

$labels = array_merge($reserved_labels, $labels);

$col = 1;
foreach ($labels as $label) {
    $cell = [$col++, 1];
    $worksheet1->setCellValue($cell, $label);
    $worksheet1->getStyle($cell)->getFont()->setBold(true);
}

// 2 -- Data.
$row = 2;
foreach ($this->data->items as $item) {
    $i = 1; // Colonne de départ
    
    // Si on veut mettre l'ID
    if ($col_id > 0) {
        $worksheet1->setCellValue([$i++, $row], $item->colRecord);
    }

    // Si on veut mettre la colonne d'état.
    if ($col_state > 0) {
        // Sécuriser la requête
        $recordId = $database->quote($item->colRecord);
        $sql = "SELECT title, color 
                FROM `#__contentbuilder_list_states` 
                WHERE id = (SELECT state_id 
                            FROM `#__contentbuilder_list_records` 
                            WHERE record_id = $recordId)";
        $database->setQuery($sql);
        $result = $database->loadRow();

        if ($result !== null) {
            if (empty($result[1]) || !preg_match('/^[0-9A-F]{6}$/i', $result[1])) {
                $result[1] = 'FFFFFF'; // Blanc par défaut
            }

            // Convertir $i en lettre de colonne
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $cell = $columnLetter . $row; // Ex. 'B2'
            
            // Retrait de la couleur dans l'export.
            /*
            if ($result[1] !== 'FFFFFF') { // !== pour cohérence avec chaînes
                $worksheet1->getStyle($cell)->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $result[1]]
                    ]
                ]);
            }*/
            $worksheet1->setCellValue([$i++, $row], $result[0]);
        }
        else {
            $i++;
        }
    }

    // Si on veut mettre la colonne d'état.
    if ($col_publish > 0) {
        $i++;
    }
 
    // Les autres colonnes.
    foreach($this->data->visible_cols as $id) {
        $worksheet1->setCellValue([$i++, $row], $item->{"col$id"});          
    }

    $row++; // Passer à la ligne suivante pour chaque item
}

$spreadsheet->getDefaultStyle()->getAlignment()->setWrapText(true);
//$worksheet1->setTitle("export-" . date('Y-m-d_Hi') . ".xlsx");

// Name file.
// Récupérer le fuseau horaire du client (via POST, GET, ou autre)
$input = Factory::getApplication()->input;
$userTimezone = $input->get('user_timezone', null, 'string');

// Si aucun fuseau horaire client n'est fourni, utiliser celui de Joomla
if (!$userTimezone) {
    $config = Factory::getConfig();
    $userTimezone = $config->get('offset', 'UTC');
}

// Créer la date avec le fuseau horaire
$date = Factory::getDate('now', $userTimezone);

$query = $database->getQuery(true)
    ->select($database->quoteName('name'))
    ->from($database->quoteName('#__facileforms_forms'))
    ->where($database->quoteName('id') . ' = ' . (int) $this->data->reference_id);

$database->setQuery($query);
$name = $database->loadResult() ?: 'Formulaire_inconnu';


$filename = "CB_export_" . $name. '_' .$date->format('Y-m-d_Hi', true) . ".xlsx";


$spreadsheet->setActiveSheetIndex(0);

foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
    // Auto-size toutes les colonnes utilisées
    foreach ($worksheet->getColumnDimensions() as $colDim) {
        $colDim->setAutoSize(true);
    }

    // Forcer le calcul
    $worksheet->calculateColumnWidths();

    // Limite max + petite correction pour enlever le padding excessif restant
    foreach ($worksheet->getColumnDimensions() as $colDim) {
        $calculatedWidth = $colDim->getWidth();
        
        if ($calculatedWidth > 70) {
            $colDim->setAutoSize(false);
            $colDim->setWidth(70);
        } else {
            // Optionnel : réduire légèrement pour un ajustement encore plus serré
            // Testez avec 0.85 à 1.0 selon vos polices
            $colDim->setWidth(max(3, $calculatedWidth - 0.9));
        }
    }
}



// Nettoyer tout buffer avant sortie
while (ob_get_level() > 0) {
    ob_end_clean();
}

// Headers corrects pour forcer le téléchargement Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0, no-cache, must-revalidate');
header('Pragma: public');
header('Expires: 0');

ob_end_clean();
ob_start();



$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$objWriter->save('php://output');

exit;