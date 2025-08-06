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

$string['pluginname'] = 'TinyMCE-Gestaltungsvorlagen';

$string['actions'] = 'Aktionen';
$string['back_overview'] = 'Zur Übersicht';
$string['bootstrapclass'] = 'CSS-Stil / Bootstrap-Klasse';
$string['bootstrapclass_help'] = 'Wählen Sie aus, welcher Stil angewendet werden soll: ein manuell definierter Inline-CSS-Stil oder eine vordefinierte Bootstrap-Klasse.';
$string['boxes'] = 'Boxen';
$string['bulkactionmustselect'] = 'Bitte wählen Sie mindestens ein Element.';
$string['categories'] = 'Kategorien';
$string['category'] = 'Kategorie';
$string['category_saved'] = 'Kategorie gespeichert';
$string['categorydeleted'] = 'Kategorie gelöscht';
$string['categoryhelp'] = 'Kategorie';
$string['categoryhelp_help'] = 'Wählen Sie die Kategorie aus, zu der dieses Element gehört.';
$string['clearsearch'] = "Auswahl zurücksetzen";
$string['close'] = 'Schließen';
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
$string['elementcancel'] = 'Eingabe/ Bearbeitung von Gestaltungsvorlage abgebrochen';
$string['elementcreated'] = 'Element erstellt';
$string['elementdeleted'] = 'Element(e) wurden erfolgreich gelöscht';
$string['elementduplicated'] = 'Element(e) wurden erfolgreich dupliziert';
$string['elementhidden'] = 'Element(e) wurden erfolgreich ausgeblendet';
$string['elements'] = 'Elemente bearbeiten';
$string['elements_updated'] = 'Elemente aktualisiert';
$string['elementsettings'] = 'Element-Einstellungen';
$string['elementsheading'] = 'Elemente';
$string['elementshown'] = 'Element(e) wurden erfolgreich sichtbar gemacht';
$string['elementstitle'] = 'Kategorie-Elemente';
$string['elementupdated'] = 'Element aktualisiert';
$string['error_nametooshort'] = 'Der Name ist zu kurz';
$string['errorname'] = 'Der Name muss mindestens 3 Zeichen lang sein.';
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
$string['importjsonfile'] = 'JSON-Konfigurationsdatei';
$string['importjsonfile_help'] = 'Laden Sie eine JSON-Datei hoch, die Gestaltungsvorlagen-Konfigurationen enthält.';
$string['importsuccess'] = 'Gestaltungsvorlagen erfolgreich importiert.';
$string['inline'] = 'Inline';
$string['instr_cat_heading'] = 'Kategorie:';
$string['instr_cat_list'] = '<ul> <li><strong>menumode:</strong> wie die Elemente im Editor angezeigt werden (submenu / inline / divider)</li> <li><strong>enabled:</strong> entweder <code>1</code> (aktiviert) oder <code>0</code> (deaktiviert), Standard: <code>0</code></li> </ul>';
$string['instr_elem_heading'] = 'Element:';
$string['instr_elem_list'] = '<ul> <li><strong>type:</strong> <code>inline</code> oder <code>block</code><br><em>inline für kurze Texte oder Wörter<br>block für Absätze oder längere Textblöcke</em></li> <li><strong>cssclasses:</strong> CSS-Stil für den Text<br><em><a href="https://developer.mozilla.org/de/docs/Learn_web_development/Core/Styling_basics/Getting_started#inline-styles" target="_blank">Inline CSS</a> (z.B. <code>color: red; font-weight: bold;</code>) oder Bootstrap-Klassen</em></li> <li><strong>enabled:</strong> entweder <code>1</code> (aktiviert) oder <code>0</code> (deaktiviert), Standard: <code>0</code></li> <li><strong>custom:</strong> <code>1</code>, wenn Inline-CSS verwendet wird</li> </ul>';
$string['instr_expl_heading'] = 'Erklärungen';
$string['instr_expl_intro'] = 'Die Felder Name und Beschreibung sollten selbsterklärend sein.';
$string['instr_fields_title'] = 'Weitere Optionen sind:';
$string['instr_fill_code'] = "\"categories\": [\n    {\n        \"name\": \"Geben Sie hier einen mindestens 3 Zeichen langen Namen ein.\",\n        \"description\": \"Geben Sie hier eine kurze Beschreibung der Kategorie ein.\",\n        \"showdesc\": \"eine der folgenden Optionen wählen: helptext/tooltip/never\",\n        \"menumode\": \"eine der folgenden Optionen wählen: submenu/inline/divider\",\n        \"enabled\": 1,\n        \"elements\": [\n            {\n                \"name\": \"Einen beschreibenden Namen hier einfügen\",\n                \"type\": \"inline oder block wählen\",\n                \"cssclasses\": \"gültige CSS-Klasse z.B. alert alert-danger\",\n                \"enabled\": 1,\n                \"custom\": 0\n            },\n            ... weitere Elemente ...\n        ]\n    },\n    ... weitere Kategorien ...\n]";
$string['instr_fill_heading'] = 'Wie man die JSON-Datei definiert:';
$string['instr_fill_note'] = '(Siehe unten für Erklärungen zu den verschiedenen Optionen.)';
$string['instr_good_heading'] = 'Wissenswertes';
$string['instr_good_list'] = '<ul> <li>Kategorien können mehrfach importiert werden. Dies verhindert unbeabsichtigte Löschungen oder Änderungen.<br><strong>Empfehlung:</strong> Verwenden Sie die Beispiel-Datei als Grundlage für Importe.</li> <li>Alle Felder können später über die Moodle-Administrationsoberfläche bearbeitet werden.</li> <li>Icons müssen derzeit manuell über die Kategorienverwaltung in Moodle zugewiesen werden.</li> </ul>';
$string['instr_structure_heading'] = 'Struktur';
$string['instr_structure_text'] = 'Die Datei muss mit einem Kategorie-Array aufgebaut sein, wobei jede Kategorie ein Element-Array enthält.<br><strong>Hinweis:</strong>Die JSON-Syntax und Formatierung muss korrekt eingehalten werden, damit die Datei funktioniert.';
$string['instr_usage_heading'] = 'Verwendung der JSON-Datei';
$string['instr_usage_text'] = 'Die Beispiel-JSON-Datei kann direkt bearbeitet und durch kopieren erweitert werden.';
$string['instr_visualized_code'] = "Kategorien: [ kategorie_1, kategorie_2 ... kategorie_n ]\n\nkategorie_1: [ element_a, element_b ... element_n ]\nkategorie_2: [ element_x, element_y ...\n→ mit den Elementen, die Stilinformationen enthalten";
$string['instr_visualized_label'] = 'Visualisierung:';
$string['instructions_heading'] = 'Anleitung für die JSON-Datei';
$string['instructions_toggle'] = 'Anleitung zur Vorbereitung der Konfigurationsdatei für den Import.';
$string['invalidelelmentid'] = 'Ungültige Element-ID';
$string['invalidfiletype'] = 'Ungültiger Dateityp. Bitte laden Sie eine JSON-Datei hoch.';
$string['invalidjson'] = 'Die Datei enthält ungültige JSON-Daten.';
$string['invalidjsonstructure'] = 'Ungültige JSON-Struktur. Die Datei muss Kategorien (category), Elemente (element) und Kategorie-Zugehörigkeit (cat_elements) enthalten.';
$string['jsonfilehelp'] = 'Um das korrekte JSON-Format sicherzustellen, können Sie die aktuellen Kategorien exportieren und die JSON-Dateistruktur als Vorlage verwenden.';
$string['labels'] = 'Label';
$string['manualconfig'] = 'Manueller Stil';
$string['manualdefault'] = 'Bitte geben Sie hier gültigen CSS-Code ein. Beispiel: <code>color: red; font-weight: bold;</code><br>Technischer Hinweis: Die manuellen Stile werden den ausgewählten HTML-Elementen als <a href="https://developer.mozilla.org/de/docs/Learn_web_development/Core/Styling_basics/Getting_started#inline-styles" target="_blank">Inline-Styles</a> hinzugefügt.';
$string['menuitem_styles'] = 'Gestaltungsvorlagen';
$string['movedown'] = 'Runter';
$string['moveup'] = 'Hoch';
$string['name'] = 'Name';
$string['no_selection'] = 'Keine Auswahl';
$string['nocategories'] = 'Keine Kategorien vorhanden';
$string['noelements'] = 'Keine Elemente gefunden.';
$string['nofileuploaded'] = 'Es wurde keine Datei hochgeladen.';
$string['noiconselected'] = 'Keine Elemente ausgewählt';
$string['noiconsfound'] = 'Keine Symbole gefunden';
$string['menumode'] = 'Menümodus';
$string['menumodehdr'] = 'Darstellung der Kategorie im Menü';
$string['menumodehelp'] = 'Darstellung';
$string['menumodehelp_help'] = 'Wählen Sie aus, ob die Elemente direkt im Menü angezeigt oder in einem Untermenü im Editor organisiert werden sollen. Alternativ können Sie auch eine Trennlinie erstellen, um Kategorien visuell voneinander zu trennen.';
$string['menumodetype'] = 'Menümodus';
$string['menumodetype_help'] = '<p>Wählen Sie den Modus aus, wie Elemente im Menü der Gestaltungsvorlagen angezeigt werden sollen:</p><ul><li><strong>Untermenü</strong>: Die Elemente werden als Untermenü mit dem Titel der Kategorie als übergeordnetem Knoten angezeigt.</li><li><strong>Inline</strong>: Die Elemente der Kategorie werden nacheinander direkt im Menü der Gestaltungsvorlagen angezeigt.</li><li><strong>Trennlinie</strong>: Möglichkeit, um Inhalte im Menü durch eine Linie getrennt voneinander anzuzeigen. Es handelt sich hierbei um keine Kategorie, der Elemente hinzugefügt werden können!</li></ul>';
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
$string['styles:use'] = 'TinyMCE benutzerdefinierte Stile verwenden';
$string['submenu'] = 'Untermenü';
$string['submit'] = 'Absenden';
$string['tiny_styles_admin'] = 'Gestaltungsvorlagen';
$string['tiny_styles_button'] = 'Gestaltungsvorlagen';
$string['type'] = 'Anzeigeart';
$string['typehelp'] = 'Anzeigeart';
$string['typehelp_help'] = 'Wählen Sie aus, wie die Gestaltungsvorlage auf Text angewandt werden soll:<ul><li><strong>Inline</strong>: für Wörter oder kurze Texte</li><li><strong>Block</strong>: für Absätze oder längere Texte</li></ul>';$string['type:inline'] = 'Inline';
$string['type:paragraph'] = 'Block';
$string['type:inline']  = 'Inline';
$string['view'] = 'Ansehen';
$string['withselection'] = 'Mit Auswahl...';
