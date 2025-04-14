<?php
// Diese Datei ist Teil von Moodle - https://moodle.org/
//
// Moodle ist freie Software: Sie können sie weiterverbreiten und/oder modifizieren
// unter den Bedingungen der GNU General Public License, veröffentlicht von
// der Free Software Foundation, entweder Version 3 der Lizenz oder
// (nach Ihrer Wahl) einer späteren Version.
//
// Moodle wird in der Hoffnung verbreitet, dass es nützlich ist,
// jedoch OHNE JEGLICHE GEWÄHRLEISTUNG; sogar ohne die implizite Gewährleistung der
// MARKTGÄNGIGKEIT oder EIGNUNG FÜR EINEN BESTIMMTEN ZWECK. Siehe die
// GNU General Public License für weitere Details.
//
// Sie sollten eine Kopie der GNU General Public License zusammen mit Moodle erhalten haben.
// Falls nicht, siehe <https://www.gnu.org/licenses/>.

/**
 * Plugin-Strings werden hier definiert.
 *
 * @package     tiny_styles
 * @category    string
 * @copyright   2025 Karri Pajarinen <pajarinenk66@univie.ac.at>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 oder später
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
