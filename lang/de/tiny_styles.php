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
 * @copyright Academic Moodle Cooperation {@link http://www.academic-moodle-cooperation.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actions'] = 'Aktionen';
$string['back_overview'] = 'Zur Übersicht';
$string['bootstrapclass'] = 'CSS-Stil / Bootstrap-Klasse';
$string['bootstrapclass_help'] = 'Wählen Sie aus, welcher Stil angewendet werden soll: ein manuell definierter Inline-CSS-Stil oder eine vordefinierte Bootstrap-Klasse.';
$string['boxes'] = 'Boxen';
$string['bulkactionmustselect'] = 'Bitte wählen Sie mindestens ein Element.';
$string['categories'] = 'Kategorien';
$string['categoriesorder'] = 'Reihenfolge von Kategorien erfolgreich aktualisiert';
$string['category'] = 'Kategorie';
$string['category_saved'] = 'Kategorie gespeichert';
$string['categorydeleted'] = 'Kategorie gelöscht';
$string['categoryhelp'] = 'Kategorie';
$string['categoryhelp_help'] = 'Wählen Sie die Kategorie aus, zu der dieses Element gehört.';
$string['clearsearch'] = "Auswahl zurücksetzen";
$string['close'] = 'Schließen';
$string['confirmdeletebulk'] = 'Möchten Sie die ausgewählten Elemente wirklich löschen?';
$string['confirmdeletecategory'] = 'Möchten Sie diese Kategorie wirklich löschen?';
$string['confirmdeleteelement'] = 'Möchten Sie dieses Element wirklich löschen?';
$string['create_element'] = 'Element hinzufügen';
$string['createcategory'] = 'Kategorie erstellen';
$string['cssclasseshelp'] = 'CSS-Klassen';
$string['cssclasseshelp_help'] = 'Wählen Sie eine vordefinierte Klasse aus oder definieren Sie manuell eine per CSS.';
$string['delete'] = 'Löschen';
$string['deleteaction'] = 'Löschen';
$string['deletecategory'] = 'Kategorie löschen';
$string['description'] = 'Beschreibung';
$string['descriptiondisp'] = 'Beschreibung anzeigen';
$string['details'] = 'Details anzeigen';
$string['divider'] = 'Trennlinie';
$string['duplicateaction'] = 'Duplizieren';
$string['edit'] = 'Bearbeiten';
$string['editcategory'] = 'Kategorie bearbeiten';
$string['editelement'] = 'Element bearbeiten';
$string['editor:clearstyle'] = 'Gestaltungsvorlagen entfernen';
$string['elementcancel'] = 'Eingabe/ Bearbeitung von Gestaltungsvorlage abgebrochen';
$string['elementcreated'] = 'Element erstellt';
$string['elementdeleted'] = 'Element(e) wurden erfolgreich gelöscht';
$string['elementduplicated'] = 'Element(e) wurden erfolgreich dupliziert';
$string['elementhidden'] = 'Element(e) wurden erfolgreich ausgeblendet';
$string['elements'] = 'Elemente bearbeiten';
$string['elements_updated'] = 'Elemente aktualisiert';
$string['elementsettings'] = 'Element-Einstellungen';
$string['elementsheading'] = 'Elemente';
$string['elementsheadingname'] = 'Gestaltungsvorlagen';
$string['elementshown'] = 'Element(e) wurden erfolgreich sichtbar gemacht';
$string['elementsorder'] = 'Reihenfolge von Elementen erfolgreich aktualisiert';
$string['elementstitle'] = 'Kategorie-Elemente';
$string['elementupdated'] = 'Element aktualisiert';
$string['error:invalidaction'] = 'Ungültige Aktion angegeben';
$string['error:missingelements'] = 'No elements selected';
$string['error:missingparam'] = 'Missing parameters';
$string['error:recordnotfound'] = 'Datensatz nicht gefunden';
$string['error:name'] = 'Der Name muss mindestens 3 Zeichen lang sein.';
$string['examplefile'] = 'Beispiel-Datei';
$string['exportdata'] = 'Gestaltungsvorlagen exportieren';
$string['hideaction'] = 'Verbergen';
$string['iconhelp'] = 'Icon';
$string['iconhelp_help'] = 'Wählen Sie ein Symbol, das im Editor zusammen mit der Kategorie angezeigt wird. Sofern Sie keines auswählen, wird ein Standard-Symbol hinterlegt.';
$string['import'] = 'Importieren';
$string['importdata'] = 'Gestaltungsvorlagen importieren';
$string['importdatainfo'] = 'Laden Sie eine JSON-Datei hoch, die Gestaltungsvorlagen-Konfigurationen für den TinyMCE-Editor enthalten.';
$string['importfailed'] = 'Fehler beim Importieren der Konfiguration aus der Datei.';
$string['importinstructions'] = 'Laden Sie eine JSON-Datei mit gültigem Format hoch, die Gestaltungsvorlagen-Konfigurationen für das TinyMCE-Plugin enthält.';
$string['importjsoncategories'] = 'Ungültiges JSON-Format: Der Abschnitt „categories“ fehlt oder ist fehlerhaft formatiert.';
$string['importjsonfile'] = 'JSON-Konfigurationsdatei';
$string['importjsonfile_help'] = 'Laden Sie eine JSON-Datei hoch, die Gestaltungsvorlagen-Konfigurationen enthält.';
$string['importsuccess'] = 'Gestaltungsvorlagen erfolgreich importiert.';
$string['inline'] = 'Inline';
$string['instructions_0_toggle'] = 'Anleitung zur Vorbereitung der Konfigurationsdatei für den Import';
$string['instructions_1_usage_heading'] = 'Verwenden Sie die Beispiel-Datei als Vorlage';
$string['instructions_1_1_usage_text'] = 'Die Beispiel-JSON-Datei kann direkt zum Bearbeiten verwendet und erweitert werden.';
$string['instructions_2_structure_heading'] = 'Aufbau';
$string['instructions_2_1_structure_text'] = 'Die JSON-Datei für TinyMCE-Gestaltungsvorlagen muss einer bestimmten Struktur folgen. Diese besteht aus einem Array für die Kategorie, welche weitere Arrays für Elemente enthält. Die Kategorien und Elemente werden in der Reihenfolge importiert, in der sie in der Datei definiert sind.';
$string['instructions_3_cat_intro'] = 'Folgende Optionen sind für <strong>Kategorien</strong> verfügbar:';
$string['instructions_3_1_cat_name'] = 'Name der Kategorie, ist im Editor sichtbar';
$string['instructions_3_2_cat_description'] = 'Beschreibung der Kategorie, ist aktuell nur für Administrator/innen sichtbar';
$string['instructions_3_2_2_cat_symbol'] = 'ein Symbol für die Kategorie';
$string['instructions_3_3_cat_menumode'] = 'die Elemente einer Kategorie können dargestellt werden als <code>submenu</code> (Submenü mit dem Namen der Kategorie als Menüeintrag), <code>inline</code> (direkt im Editor angezeigt) oder die Kategorie kann als <code>divider</code> verwendet werden (um Inhalte voneinander zu trennen, hier können keine Elemente hinzugefügt werden)';
$string['instructions_3_4_cat_enabled'] = 'entweder <code>1</code> (aktiviert) or <code>0</code> (deaktiviert), standardmäßig: 0';
$string['instructions_4_elem_intro'] = 'Folgende Optionen sind für <strong>Elemente</strong> verfügbar:';
$string['instructions_4_1_elem_name'] = 'Name des Elements, ist im Editor sichtbar';
$string['instructions_4_2_elem_custom'] = 'entweder <code>1</code> (wenn ein manuell definierter Stil mit gültigem CSS-Code verwendet wird) oder <code>0</code> (wenn eine vordefinierte Bootstrap-Klasse verwendet wird)';
$string['instructions_4_3_elem_type'] = '<code>inline</code> (für die Gestaltung von Wörtern oder kurzen Texten) oder <code>block</code> (für Absätze oder längere Texte)';
$string['instructions_4_4_elem_cssclasses'] = 'das können Bootstrap-Klassen sein (alerts und badges, z.B. <code>alert alert-info</code> oder <code>badge bg-primary</code>) oder gültiger CSS-Code (z.B. <code>color: red; font-weight: bold;</code>), welcher als <a href="https://developer.mozilla.org/en-US/docs/Learn_web_development/Core/Styling_basics/Getting_started#inline_styles" target="_blank">Inline-Styles</a> hinzugefügt wird';
$string['instructions_4_5_elem_enabled'] = 'entweder <code>1</code> (aktiviert) or <code>0</code> (deaktiviert), standardmäßig: 0';
$string['instructions_5_example_heading'] = 'Beispiel';
$string['instructions_5_1_example_note'] = 'Nachfolgend finden Sie ein Beispiel für eine ausgefüllte JSON-Datei:';
$string['instructions_5_2_example_code'] = "\"categories\": [\n    {\n        \"name\": \"Kategorie 1\",\n        \"description\": \"Beschreibung der Kategorie 1\",\n        \"symbol\": \"Wählen Sie ein Symbol aus\",\n        \"menumode\": \"submenu\",\n        \"enabled\": 1,\n        \"elements\": [\n            {\n                \"name\": \"Element A\",\n                \"type\": \"block\",\n                \"cssclasses\": \"alert alert-info\",\n                \"enabled\": 1,\n                \"custom\": 0\n            },\n            {\n                \"name\": \"Element B\",\n                \"type\": \"inline\",\n                \"cssclasses\": \"color: red; font-weight: bold;\",\n                \"enabled\": 1,\n                \"custom\": 1\n            },\n        ]\n    },\n    {\n        \"name\": \"Kategorie n\",\n        ...\n    },\n]";
$string['instructions_6_good_heading'] = 'Wissenswertes';
$string['instructions_6_1_good_list'] = '<ul> <li>Kategorien können mehrfach importiert werden. Dies verhindert unbeabsichtigte Löschungen oder Änderungen.</li> <li>Alle Felder können später über die Administrations-Seite des Plugins bearbeitet werden.</li><li>Kategorien und Elemente behalten ihre Sortierreihenfolge automatisch beim Export und Import. Die im JSON gespeicherte Reihenfolge wird direkt übernommen.</li><li>Die exportierte JSON-Datei enthält die <strong>ID</strong> jedes Elements. Diese wird beim Erstellen einer Import-JSON jedoch nicht benötigt.</li></ul>';
$string['invalidelelmentid'] = 'Ungültige Element-ID';
$string['invalidfiletype'] = 'Ungültiger Dateityp. Bitte laden Sie eine JSON-Datei hoch.';
$string['invalidid'] = 'Ungültige Kategorie-ID';
$string['invalidjson'] = 'Die Datei enthält ungültige JSON-Daten.';
$string['invalidjsonstructure'] = 'Ungültige JSON-Struktur. Die Datei muss Kategorien (category), Elemente (element) und Kategorie-Zugehörigkeit (cat_elements) enthalten.';
$string['jsonfilehelp'] = 'Um das korrekte JSON-Format sicherzustellen, können Sie die aktuellen Kategorien exportieren und die JSON-Dateistruktur als Vorlage verwenden.';
$string['labels'] = 'Label';
$string['manualconfig'] = 'Manueller Stil';
$string['manualdefault'] = 'Bitte geben Sie hier gültigen CSS-Code ein. Beispiel: <code>color: red; font-weight: bold;</code><br>Technischer Hinweis: Die manuellen Stile werden den ausgewählten HTML-Elementen als <a href="https://developer.mozilla.org/de/docs/Learn_web_development/Core/Styling_basics/Getting_started#inline-styles" target="_blank">Inline-Styles</a> hinzugefügt.';
$string['menuitem_styles'] = 'Gestaltungsvorlagen';
$string['menumode'] = 'Menümodus';
$string['menumodehdr'] = 'Darstellung der Kategorie im Menü';
$string['menumodehelp'] = 'Darstellung';
$string['menumodehelp_help'] = 'Wählen Sie aus, ob die Elemente direkt im Menü angezeigt oder in einem Untermenü im Editor organisiert werden sollen. Alternativ können Sie auch eine Trennlinie erstellen, um Kategorien visuell voneinander zu trennen.';
$string['menumodetype'] = 'Menümodus';
$string['menumodetype_help'] = '<p>Wählen Sie den Modus aus, wie Elemente im Menü der Gestaltungsvorlagen angezeigt werden sollen:</p><ul><li><strong>Untermenü</strong>: Die Elemente werden als Untermenü mit dem Titel der Kategorie als übergeordnetem Knoten angezeigt.</li><li><strong>Inline</strong>: Die Elemente der Kategorie werden nacheinander direkt im Menü der Gestaltungsvorlagen angezeigt.</li><li><strong>Trennlinie</strong>: Möglichkeit, um Inhalte im Menü durch eine Linie getrennt voneinander anzuzeigen. Es handelt sich hierbei um keine Kategorie, der Elemente hinzugefügt werden können!</li></ul>';
$string['movedown'] = 'Runter';
$string['moveup'] = 'Hoch';
$string['name'] = 'Name';
$string['no_selection'] = 'Keine Auswahl';
$string['nocategories'] = 'Keine Kategorien vorhanden';
$string['noelements'] = 'Keine Elemente gefunden.';
$string['nofileuploaded'] = 'Es wurde keine Datei hochgeladen.';
$string['noiconselected'] = 'Keine Elemente ausgewählt';
$string['noiconsfound'] = 'Keine Symbole gefunden';
$string['pluginname'] = 'TinyMCE-Gestaltungsvorlagen';
$string['preview'] = 'Vorschau';
$string['privacy:metadata'] = 'Das Plugin TinyMCE Gestaltungsvorlagen speichert keine personenbezogenen Daten.';
$string['searchplaceholder'] = 'Suchen';
$string['selectall'] = 'Alle auswählen';
$string['selectanicon'] = 'Verfügbare Symbole';
$string['selectdefault'] = 'Auswählen...';
$string['selectedicon'] = 'Ausgewähltes Symbol';
$string['selecticon'] = 'Symbol';
$string['selectjsonfile'] = 'JSON-Datei zum Importieren auswählen:';
$string['showaction'] = 'Anzeigen';
$string['styles:use'] = 'TinyMCE Gestaltungsvorlagen verwenden';
$string['submenu'] = 'Untermenü';
$string['submit'] = 'Absenden';
$string['tiny_styles_admin'] = 'Gestaltungsvorlagen';
$string['tiny_styles_button'] = 'Gestaltungsvorlagen';
$string['type'] = 'Anzeigeart';
$string['type:inline']  = 'Inline';
$string['type:paragraph'] = 'Block';
$string['typehelp'] = 'Anzeigeart';
$string['typehelp_help'] = 'Wählen Sie aus, wie die Gestaltungsvorlage auf Text angewandt werden soll:<ul><li><strong>Inline</strong>: für Wörter oder kurze Texte</li><li><strong>Block</strong>: für Absätze oder längere Texte</li></ul>';
$string['view'] = 'Ansehen';
$string['withselection'] = 'Mit Auswahl...';
