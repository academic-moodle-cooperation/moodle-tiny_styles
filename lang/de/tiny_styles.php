<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package tiny_styles
 * @author Karri Pajarinen
 * @copyright 2025 Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'TinyMCE-Gestaltungsvorlagen';
$string['privacy:metadata'] = 'Tiny Styles speichert keine personenbezogenen Daten';

// Hauptmenüpunkt
$string['menuitem_styles'] = 'Stile';
$string['tiny_styles_button'] = 'Stile';

// Symbolleisten-Schaltflächen Tooltips oder Texte
$string['boxes'] = 'Boxen';
$string['labels'] = 'Label';

// Einstellungen
$string['tiny_styles_admin'] = 'Tiny Styles';
$string['managecategories'] = 'Kategorien verwalten';
$string['manageelements'] = 'Elemente verwalten';
$string['createelement'] = 'Neues Element erstellen';
$string['createcategory'] = 'Neue Kategorie erstellen';

// Kategorien
$string['categories'] = 'Kategorien';
$string['createcategory'] = 'Kategorie erstellen';
$string['name'] = 'Name';
$string['description'] = 'Beschreibung';
$string['presentation'] = 'Darstellung';
$string['actions'] = 'Aktionen';
$string['view'] = 'Ansehen';
$string['elements'] = 'Elemente bearbeiten';
$string['moveup'] = 'Hoch';
$string['movedown'] = 'Runter';
$string['editcategory'] = 'Kategorie bearbeiten';
$string['deletecategory'] = 'Kategorie löschen';
$string['nocategories'] = 'Keine Kategorien vorhanden';
$string['category_saved'] = 'Kategorie gespeichert';
$string['presentationhdr'] = 'Darstellung der Kategorie';

$string['descriptiondisp'] = 'Beschreibung anzeigen';
$string['presentationtype'] = 'Darstellungstyp';

// Elemente
$string['elementstitle'] = 'Kategorie-Elemente';
$string['elementsheading'] = 'Elemente';
$string['type'] = 'Typ';
$string['bootstrapclass'] = 'Bootstrap-Klasse / CSS-Stil';
$string['edit'] = 'Bearbeiten';
$string['delete'] = 'Löschen';
$string['noelements'] = 'Keine Elemente gefunden.';
$string['no_selection'] = 'Keine Auswahl';
$string['category'] = 'Kategorie';

$string['submit'] = 'Absenden';
$string['type'] = 'Typ';
$string['create_element'] = 'Element erstellen';
$string['selectall'] = 'Alle auswählen';
$string['details'] = 'Details anzeigen';
$string['manualconfig'] = 'Manuelle Konfiguration';
$string['manualdefault'] = 'Bitte geben Sie gültigen Inline-CSS-Code ein. Beispiel: <code>color: red; font-weight: bold;</code><br>Mehr über Inline-Stile erfahren Sie <a href="https://www.freecodecamp.org/news/inline-style-in-html/" target="_blank" rel="noopener">hier</a>.';

// Noch zu definieren
$string['elementsettings'] = 'Element-Einstellungen';
$string['back_overview'] = 'Zurück zur Übersicht';
$string['editelement'] = 'Element bearbeiten';
$string['invalidelelmentid'] = 'Ungültige Element-ID';

$string['elements_updated'] = 'Elemente aktualisiert';
$string['elementcreated'] = 'Element erstellt';
$string['elementupdated'] = 'Element aktualisiert';
$string['error_nametooshort'] = 'Der Name ist zu kurz';
$string['elementcancel'] = 'Stilformular abgebrochen';

$string['confirmdeleteelement'] = 'Möchten Sie dieses Element wirklich löschen?';
$string['confirmdeletecategory'] = 'Möchten Sie diese Kategorie wirklich löschen?';
$string['elementdeleted'] = 'Element gelöscht';
$string['categorydeleted'] = 'Kategorie gelöscht';
$string['preview'] = 'Vorschau';

$string['errorname'] = 'Der Name muss mindestens 3 Zeichen lang sein.';

// export import
$string['exportdata'] = 'Daten exportieren';
$string['importdata'] = 'Daten importieren';
$string['importsuccess'] = 'Import erfolgreich';
$string['import'] = 'Importieren';
$string['close'] = 'Schließen';

$string['nofileuploaded'] = 'Es wurde keine Datei hochgeladen.';
$string['invalidjson'] = 'Ungültige JSON-Datei.';

$string['selectjsonfile'] = 'JSON-Datei zum Importieren auswählen:';
$string['jsonfilehelp'] = 'Um das korrekte JSON-Format sicherzustellen, können Sie die aktuellen Kategorien exportieren und die JSON-Dateistruktur als Vorlage verwenden.';
$string['invalidfiletype'] = 'Ungültiger Dateityp. Bitte laden Sie eine JSON-Datei hoch.';
$string['invalidjsonstructure'] = 'Ungültige JSON-Struktur. Die Datei muss Kategorien, Elemente und cat_elements enthalten.';

$string['reorder'] = 'Reorder';

$string['withselection'] = 'Mit Auswahl: ';
$string['selectdefault'] = 'Auswählen';
$string['showaction'] = 'Anzeigen';
$string['hideaction'] = 'Ausblenden';
$string['duplicateaction'] = 'Duplizieren';
$string['deleteaction'] = 'Löschen';

$string['submenu'] = 'Untermenü';
$string['inline'] = 'Inline';
$string['divider'] = 'Trenner';

$string['symbol'] = 'Symbol';
$string['selecticon'] = 'Symbol auswählen';
$string['noiconselected'] = 'Keine Elemente ausgewählt';
$string['selectanicon'] = 'Ein Symbol auswählen';
$string['selectedicon'] = 'Ausgewähltes Symbol';

$string['bulkactionmustselect'] = 'Bitte wählen Sie mindestens ein Element.';

$string['category_help'] = 'Wählen Sie die Kategorie aus, zu der dieses Element gehört.';
$string['type_help'] = 'Wählen Sie, ob dieser Stil als Inline- oder Blockelement angewendet werden soll.';
$string['cssclasses_help'] = 'Wählen Sie aus vordefinierten Klassen oder verwenden Sie ein benutzerdefiniertes Stylesheet.';

$string['categoryhelp'] = 'Kategorie';
$string['typehelp'] = 'Typ';
$string['cssclasseshelp'] = 'CSS-Klassen';
$string['categoryhelp_help'] = 'Wähle die Kategorie aus, zu der dieses Element gehört.';
$string['typehelp_help'] = 'Wähle, ob dieser Stil als Inline- oder Blockelement angewendet werden soll.';
$string['cssclasseshelp_help'] = 'Wähle ein vordefiniertes Stil-Layout oder verwende ein eigenes Stylesheet.';

$string['iconhelp'] = 'Icon';
$string['presentationhelp'] = 'Darstellung';
$string['iconhelp_help'] = 'Wähle ein Icon, das im Editor zusammen mit der Kategorie angezeigt wird.';
$string['presentationhelp_help'] = 'Wählen Sie aus, ob die Stilelemente direkt im Styles-Menü angezeigt oder in einem Untermenü im Editor organisiert werden sollen.';

$string['elementshown'] = 'Element(e) wurden erfolgreich sichtbar gemacht';
$string['elementhidden'] = 'Element(e) wurden erfolgreich ausgeblendet';
$string['elementduplicated'] = 'Element(e) wurden erfolgreich dupliziert';
$string['elementdeleted'] = 'Element(e) wurden erfolgreich gelöscht';

$string['importdatainfo'] = 'Laden Sie JSON-Dateien hoch, die Stilkonfigurationen für den TinyMCE-Editor enthalten.';
$string['importjsonfile'] = 'JSON-Konfigurationsdatei';
$string['importinstructions'] = 'Laden Sie eine JSON-Datei mit gültigem Format hoch, die Stilkonfigurationen für das TinyMCE-Plugin enthält.';
$string['importsuccess'] = 'Stildatei erfolgreich importiert.';
$string['importfailed'] = 'Fehler beim Importieren der Konfiguration aus der Datei.';
$string['invalidjson'] = 'Die Datei enthält ungültige JSON-Daten.';
$string['importjsonfile_help'] = 'Laden Sie eine JSON-Datei hoch, die die Stilkonfigurationen enthält.';

$string['examplefiles_heading'] = 'Beispielfiles herunterladen';
$string['examplefiles_description'] = 'Laden Sie Beispiel-JSON-Dateien und Anleitungen herunter, um das Importformat besser zu verstehen.';
$string['examplefiles_label'] = 'Beispielfiles herunterladen';
$string['download_button'] = 'Herunterladen';

$string['searchplaceholder'] = 'Suchen';
$string['noiconsfound'] = 'Keine Symbole gefunden';
