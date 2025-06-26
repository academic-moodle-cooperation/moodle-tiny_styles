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
$string['privacy:metadata'] = 'Das Plugin TinyMCE Gestaltungsvorlagen speichert keine personenbezogenen Daten.';

// Hauptmenüpunkt
$string['menuitem_styles'] = 'Gestaltungsvorlagen';
$string['tiny_styles_button'] = 'Gestaltungsvorlagen';

// Symbolleisten-Schaltflächen Tooltips oder Texte
$string['boxes'] = 'Boxen';
$string['labels'] = 'Label';

// Einstellungen
$string['tiny_styles_admin'] = 'TinyMCE-Gestaltungsvorlagen';

// Kategorien
$string['categories'] = 'Kategorien';
$string['createcategory'] = 'Kategorie erstellen';
$string['name'] = 'Name';
$string['description'] = 'Beschreibung';
$string['presentation'] = 'Präsentationsart';
$string['actions'] = 'Aktionen';
$string['view'] = 'Ansehen';
$string['elements'] = 'Elemente bearbeiten';
$string['moveup'] = 'Hoch';
$string['movedown'] = 'Runter';
$string['editcategory'] = 'Kategorie bearbeiten';
$string['deletecategory'] = 'Kategorie löschen';
$string['nocategories'] = 'Keine Kategorien vorhanden';
$string['category_saved'] = 'Kategorie gespeichert';
$string['presentationhdr'] = 'Präsentation der Kategorie';

$string['descriptiondisp'] = 'Beschreibung anzeigen';
$string['presentationtype'] = 'Präsentationsart';

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
$string['create_element'] = 'Element hinzufügen';
$string['selectall'] = 'Alle auswählen';
$string['details'] = 'Details anzeigen';
$string['manualconfig'] = 'Manuelle Konfiguration';
$string['manualdefault'] = 'Bitte geben Sie gültigen Inline-CSS-Code ein. Beispiel: <code>color: red; font-weight: bold;</code><br>Mehr über Inline-Stile erfahren Sie <a href="https://www.freecodecamp.org/news/inline-style-in-html/" target="_blank" rel="noopener">hier</a>.';

// Noch zu definieren
$string['elementsettings'] = 'Element-Einstellungen';
$string['back_overview'] = 'Zur Übersicht';
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
$string['exportdata'] = 'Gestaltungsvorlagen exportieren';
$string['importdata'] = 'Gestaltungsvorlagen importieren';
$string['importsuccess'] = 'Gestaltungsvorlagen erfolgreich importiert.';
$string['import'] = 'Importieren';
$string['close'] = 'Schließen';

$string['nofileuploaded'] = 'Es wurde keine Datei hochgeladen.';
$string['invalidjson'] = 'Ungültige JSON-Datei.';

$string['selectjsonfile'] = 'JSON-Datei zum Importieren auswählen:';
$string['jsonfilehelp'] = 'Um das korrekte JSON-Format sicherzustellen, können Sie die aktuellen Kategorien exportieren und die JSON-Dateistruktur als Vorlage verwenden.';
$string['invalidfiletype'] = 'Ungültiger Dateityp. Bitte laden Sie eine JSON-Datei hoch.';
$string['invalidjsonstructure'] = 'Ungültige JSON-Struktur. Die Datei muss Kategorien (category), Elemente (element) und Kategorie-Zugehörigkeit (cat_elements) enthalten.';

$string['withselection'] = 'Mit Auswahl...';
$string['selectdefault'] = 'Auswählen...';
$string['showaction'] = 'Anzeigen';
$string['hideaction'] = 'Verbergen';
$string['duplicateaction'] = 'Duplizieren';
$string['deleteaction'] = 'Löschen';

$string['submenu'] = 'Untermenü';
$string['inline'] = 'Inline';
$string['divider'] = 'Trennlinie';

$string['selecticon'] = 'Symbol';
$string['noiconselected'] = 'Keine Elemente ausgewählt';
$string['selectanicon'] = 'Ein Symbol auswählen';
$string['selectedicon'] = 'Ausgewähltes Symbol';

$string['bulkactionmustselect'] = 'Bitte wählen Sie mindestens ein Element.';

$string['categoryhelp'] = 'Kategorie';
$string['typehelp'] = 'Typ';
$string['cssclasseshelp'] = 'CSS-Klassen';
$string['categoryhelp_help'] = 'Wählen Sie die Kategorie aus, zu der dieses Element gehört.';
$string['typehelp_help'] = 'Wählen Sie aus, ob diese Gestaltungsvorlage als Inline- oder Blockelement angewendet werden soll.';
$string['cssclasseshelp_help'] = 'Wählen Sie eine vordefinierte Klasse aus oder definieren Sie manuell eine per CSS.';

$string['iconhelp'] = 'Icon';
$string['presentationhelp'] = 'Darstellung';
$string['iconhelp_help'] = 'Wählen Sie ein Symbol, das im Editor zusammen mit der Kategorie angezeigt wird. Sofern Sie keines auswählen, wird ein Standard-Symbol hinterlegt.';
$string['presentationhelp_help'] = 'Wählen Sie aus, ob die Elemente direkt im Menü angezeigt oder in einem Untermenü im Editor organisiert werden sollen. Alternativ können Sie auch eine Trennlinie erstellen, um Kategorien visuell voneinander zu trennen.';
$string['elementshown'] = 'Element(e) wurden erfolgreich sichtbar gemacht';
$string['elementhidden'] = 'Element(e) wurden erfolgreich ausgeblendet';
$string['elementduplicated'] = 'Element(e) wurden erfolgreich dupliziert';
$string['elementdeleted'] = 'Element(e) wurden erfolgreich gelöscht';

$string['importdatainfo'] = 'Laden Sie eine JSON-Datei hoch, die Gestaltungsvorlagen-Konfigurationen für den TinyMCE-Editor enthalten.';
$string['importjsonfile'] = 'JSON-Konfigurationsdatei';
$string['importinstructions'] = 'Laden Sie eine JSON-Datei mit gültigem Format hoch, die Gestaltungsvorlagen-Konfigurationen für das TinyMCE-Plugin enthält.';
$string['importsuccess'] = 'Stildatei erfolgreich importiert.';
$string['importfailed'] = 'Fehler beim Importieren der Konfiguration aus der Datei.';
$string['invalidjson'] = 'Die Datei enthält ungültige JSON-Daten.';
$string['importjsonfile_help'] = 'Laden Sie eine JSON-Datei hoch, die Gestaltungsvorlagen-Konfigurationen enthält.';

$string['examplefiles_heading'] = 'Beispielfiles herunterladen';
$string['examplefiles_description'] = 'Laden Sie Beispiel-JSON-Dateien und Anleitungen herunter, um das Importformat besser zu verstehen.';
$string['examplefiles_label'] = 'Beispielfiles herunterladen';
$string['download_button'] = 'Herunterladen';

$string['searchplaceholder'] = 'Suchen';
$string['noiconsfound'] = 'Keine Symbole gefunden';

$string['instr_structure_heading'] = 'Struktur';
$string['instr_structure_text'] = 'Die Datei example.json ist in ein Kategorie-Array unterteilt, wobei jede Kategorie ein Element-Array enthält. Dieses Format muss jede importierte JSON-Datei einhalten.';
$string['instr_visualized_label'] = 'Visualisierung:';
$string['instr_visualized_code'] = "Kategorien: [ kategorie_1, kategorie_2 ... kategorie_n ]\n\nkategorie_1: [ element_a, element_b ... element_n ]\nkategorie_2: [ element_x, element_y ...\n→ mit den Elementen, die Stilinformationen enthalten";

$string['instr_usage_heading'] = 'Verwendung der JSON-Datei';
$string['instr_usage_text'] = 'Die Beispiel-JSON-Datei kann direkt bearbeitet und durch Kopieren erweitert werden.<br><strong>Hinweis:</strong> Die JSON-Syntax und Formatierung muss korrekt eingehalten werden, damit die Datei funktioniert.';

$string['instr_fill_heading'] = 'Wie man die example.json ausfüllt:';
$string['instr_fill_note'] = '(Siehe unten für Erklärungen zu enabled, type, etc.)';
$string['instr_fill_code'] = "\"categories\": [\n    {\n        \"name\": \"Geben Sie hier einen mindestens 3 Zeichen langen Namen ein.\",\n        \"description\": \"Geben Sie hier eine kurze Beschreibung der Kategorie ein.\",\n        \"showdesc\": \"eine der folgenden Optionen wählen: helptext/tooltip/never\",\n        \"presentation\": \"eine der folgenden Optionen wählen: submenu/inline/divider\",\n        \"enabled\": 1,\n        \"elements\": [\n            {\n                \"name\": \"Einen beschreibenden Namen hier einfügen\",\n                \"type\": \"inline oder block wählen\",\n                \"cssclasses\": \"gültige CSS-Klasse z. B. alert alert-danger\",\n                \"enabled\": 1,\n                \"custom\": 0\n            },\n            ... weitere Elemente ...\n        ]\n    },\n    ... weitere Kategorien ...\n]";

$string['instr_expl_heading'] = 'Erklärungen';
$string['instr_expl_intro'] = 'Die Felder Name und Beschreibung sollten selbsterklärend sein.';
$string['instr_fields_title'] = 'Weitere Felder sind:';

$string['instr_cat_heading'] = 'Kategorie:';
$string['instr_cat_list'] = '<ul>
<li><strong>showdesc:</strong> wie die Beschreibung den Nutzern angezeigt wird, oder gar nicht</li>
<li><strong>presentation:</strong> wie die Elemente im Editor angezeigt werden (submenu / inline / divider)</li>
<li><strong>enabled:</strong> entweder <code>1</code> (aktiviert) oder <code>0</code> (deaktiviert), Standard: <code>0</code></li>
</ul>';

$string['instr_elem_heading'] = 'Element:';
$string['instr_elem_list'] = '<ul>
<li><strong>type:</strong> <code>inline</code> oder <code>block</code><br><em>inline für kurze Texte oder Wörter<br>block für Absätze oder längere Textblöcke</em></li>
<li><strong>cssclasses:</strong> CSS-Stil für den Text<br><em>z. B. Bootstrap-Klassen oder Inline-CSS wie <code>color: red; font-weight: bold;</code></em></li>
<li><strong>enabled:</strong> gleiches Verhalten wie bei Kategorien</li>
<li><strong>custom:</strong> <code>1</code>, wenn Inline-CSS verwendet wird</li>
</ul>';

$string['instr_good_heading'] = 'Wissenswertes';
$string['instr_good_list'] = '<ul>
<li>Doppelte Kategorien können mehrfach importiert werden. Dies verhindert unbeabsichtigte Löschungen oder Änderungen.<br><strong>Empfehlung:</strong> Verwenden Sie die Beispiel-JSON als Grundlage für Importe.</li>
<li>Alle Felder können später über die Moodle-Administrationsoberfläche bearbeitet werden.</li>
<li>Icons müssen derzeit manuell über die Kategorienverwaltung in Moodle zugewiesen werden.</li>
<li>Weitere Beispiele finden Sie in der vollständigen exportierten JSON-Datei.</li>
</ul>';
