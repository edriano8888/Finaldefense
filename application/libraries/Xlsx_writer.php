<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Xlsx_writer
 *
 * Minimal, dependency-free Excel (.xlsx) writer used by all report exports.
 * Unlike CSV, an .xlsx file can store column widths, so every column is
 * auto-sized to fit its header and data (no more "#####" or cut-off headers).
 *
 * Row kinds:
 *   title   - large bold report title (ignored for width calculation)
 *   meta    - "Label: value" lines under the title
 *   section - bold green section heading (ignored for width calculation)
 *   header  - table header row (white bold text on brand green)
 *   data    - normal bordered data row
 *   total   - bold highlighted totals row
 *   blank   - empty spacer row
 */
class Xlsx_writer {

    const MIN_WIDTH = 10;
    const MAX_WIDTH = 60;

    private $rows = [];
    private $widths = [];
    private $max_cols = 0;
    private $freeze_row = 0;
    private $filter_range = null;
    private $sheet_name = 'Report';

    public function __construct($params = []) {
        if (!empty($params['sheet_name'])) {
            $this->set_sheet_name($params['sheet_name']);
        }
    }

    public function set_sheet_name($name) {
        // Excel sheet names: max 31 chars, no []:*?/\
        $name = preg_replace('/[\[\]\:\*\?\/\\\\]/', '', (string)$name);
        $this->sheet_name = substr($name !== '' ? $name : 'Report', 0, 31);
        return $this;
    }

    public function row_count() {
        return count($this->rows);
    }

    public function add_title($text)          { return $this->add_row([$text], 'title'); }
    public function add_section($text)        { return $this->add_row([$text], 'section'); }
    public function add_meta($label, $value)  { return $this->add_row([$label, $value], 'meta'); }
    public function add_header(array $cells)  { return $this->add_row($cells, 'header'); }
    public function add_total(array $cells)   { return $this->add_row($cells, 'total'); }
    public function add_blank()               { return $this->add_row([], 'blank'); }

    public function add_row(array $cells, $kind = 'data') {
        $cells = array_values($cells);
        foreach ($cells as &$c) {
            if (is_string($c)) {
                $c = trim(preg_replace('/\s+/', ' ', strip_tags($c)));
            } elseif (is_bool($c)) {
                $c = $c ? 'Yes' : 'No';
            } elseif ($c === null) {
                $c = '';
            }
        }
        unset($c);

        $this->rows[] = ['cells' => $cells, 'kind' => $kind];
        $this->max_cols = max($this->max_cols, count($cells));

        // Title / section / meta labels span visually across columns, so they
        // must not force the first column to become very wide.
        if (in_array($kind, ['header', 'data', 'total'], true)) {
            foreach ($cells as $i => $c) {
                $len = $this->display_length($c);
                if ($kind === 'header') {
                    $len += 4; // bold text + filter dropdown arrow
                } else {
                    $len += 2;
                }
                $this->widths[$i] = max($this->widths[$i] ?? 0, $len);
            }
        } elseif ($kind === 'meta') {
            // Only the label column participates; values may be long sentences.
            $len = $this->display_length($cells[0] ?? '') + 2;
            $this->widths[0] = max($this->widths[0] ?? 0, $len);
        }
        return $this;
    }

    /** Freeze everything above (and including) the given 1-based row. */
    public function freeze_header($row = 1) {
        $this->freeze_row = max(0, (int)$row);
        return $this;
    }

    /** Add Excel filter dropdowns to a header row spanning the table below it. */
    public function auto_filter($header_row = 1) {
        $this->filter_range = (int)$header_row;
        return $this;
    }

    /**
     * Stream the workbook to the browser and stop execution.
     */
    public function send($filename) {
        $filename = preg_replace('/\.(csv|xls|xlsx)$/i', '', (string)$filename) . '.xlsx';
        $filename = preg_replace('/[^A-Za-z0-9_\-\.]+/', '_', $filename);

        $tmp = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new ZipArchive();
        if ($zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            show_error('Unable to generate the Excel report.', 500);
            return;
        }

        $zip->addFromString('[Content_Types].xml', $this->content_types_xml());
        $zip->addFromString('_rels/.rels', $this->rels_xml());
        $zip->addFromString('docProps/app.xml', $this->app_xml());
        $zip->addFromString('docProps/core.xml', $this->core_xml());
        $zip->addFromString('xl/workbook.xml', $this->workbook_xml());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbook_rels_xml());
        $zip->addFromString('xl/styles.xml', $this->styles_xml());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->sheet_xml());
        $zip->close();

        while (ob_get_level()) { @ob_end_clean(); }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($tmp));
        header('Cache-Control: max-age=0, no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        readfile($tmp);
        @unlink($tmp);
        exit;
    }

    // ------------------------------------------------------------------
    // Internals
    // ------------------------------------------------------------------

    private function display_length($value) {
        if (is_float($value)) {
            $value = number_format($value, 2);
        } elseif (is_int($value)) {
            $value = number_format($value);
        }
        $value = (string)$value;
        return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
    }

    private function col_letter($index) {
        $index++;
        $letters = '';
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letters = chr(65 + $mod) . $letters;
            $index = (int)(($index - $mod) / 26);
        }
        return $letters;
    }

    private function esc($value) {
        $value = (string)$value;
        // Strip characters that are illegal in XML 1.0
        $value = preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}]/u', '', $value);
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    /**
     * Style indexes (see styles_xml cellXfs order).
     */
    private function style_for($kind, $value) {
        $is_int = is_int($value);
        $is_num = $is_int || is_float($value);
        switch ($kind) {
            case 'title':   return 2;
            case 'section': return 3;
            case 'header':  return 1;
            case 'meta':    return null; // handled per column
            case 'total':   return $is_num ? ($is_int ? 8 : 9) : 7;
            case 'data':
            default:        return $is_num ? ($is_int ? 5 : 6) : 4;
        }
    }

    private function sheet_xml() {
        $cols = max(1, $this->max_cols);

        $xml  = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
              . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';

        $xml .= '<sheetViews><sheetView workbookViewId="0" showGridLines="0">';
        if ($this->freeze_row > 0) {
            $xml .= '<pane ySplit="' . $this->freeze_row . '" topLeftCell="A' . ($this->freeze_row + 1)
                  . '" activePane="bottomLeft" state="frozen"/>';
        }
        $xml .= '</sheetView></sheetViews>';
        $xml .= '<sheetFormatPr defaultRowHeight="18"/>';

        $xml .= '<cols>';
        for ($i = 0; $i < $cols; $i++) {
            $w = $this->widths[$i] ?? self::MIN_WIDTH;
            $w = max(self::MIN_WIDTH, min(self::MAX_WIDTH, $w));
            $xml .= '<col min="' . ($i + 1) . '" max="' . ($i + 1) . '" width="' . $w . '" customWidth="1"/>';
        }
        $xml .= '</cols>';

        $xml .= '<sheetData>';
        $last_table_row = 0;
        foreach ($this->rows as $r => $row) {
            $rn = $r + 1;
            $attrs = '';
            if ($row['kind'] === 'title')   { $attrs = ' ht="28" customHeight="1"'; }
            if ($row['kind'] === 'section') { $attrs = ' ht="24" customHeight="1"'; }
            if ($row['kind'] === 'header')  { $attrs = ' ht="24" customHeight="1"'; }

            $xml .= '<row r="' . $rn . '"' . $attrs . '>';
            foreach ($row['cells'] as $i => $value) {
                $ref = $this->col_letter($i) . $rn;

                if ($row['kind'] === 'meta') {
                    $s = $i === 0 ? 10 : 11;
                } else {
                    $s = $this->style_for($row['kind'], $value);
                }

                if ((is_int($value) || is_float($value)) && is_finite((float)$value)) {
                    $xml .= '<c r="' . $ref . '" s="' . $s . '"><v>' . $value . '</v></c>';
                } elseif ($value === '' && in_array($row['kind'], ['title', 'section', 'blank'], true)) {
                    continue;
                } else {
                    $xml .= '<c r="' . $ref . '" s="' . $s . '" t="inlineStr"><is><t xml:space="preserve">'
                          . $this->esc($value) . '</t></is></c>';
                }
            }

            // Pad header / data / total rows so borders form a complete grid.
            if (in_array($row['kind'], ['header', 'data', 'total'], true)) {
                for ($i = count($row['cells']); $i < $cols; $i++) {
                    $s = $row['kind'] === 'header' ? 1 : ($row['kind'] === 'total' ? 7 : 4);
                    $xml .= '<c r="' . $this->col_letter($i) . $rn . '" s="' . $s . '"/>';
                }
                if ($this->filter_range !== null && $rn >= $this->filter_range) {
                    $last_table_row = $rn;
                }
            }
            $xml .= '</row>';
        }
        $xml .= '</sheetData>';

        if ($this->filter_range !== null && $last_table_row >= $this->filter_range) {
            $xml .= '<autoFilter ref="A' . $this->filter_range . ':' . $this->col_letter($cols - 1) . $last_table_row . '"/>';
        }

        $xml .= '<pageMargins left="0.4" right="0.4" top="0.5" bottom="0.5" header="0.3" footer="0.3"/>';
        $xml .= '<pageSetup orientation="landscape" fitToWidth="1" fitToHeight="0"/>';
        $xml .= '</worksheet>';
        return $xml;
    }

    private function styles_xml() {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<numFmts count="2">'
            .   '<numFmt numFmtId="164" formatCode="#,##0"/>'
            .   '<numFmt numFmtId="165" formatCode="#,##0.00"/>'
            . '</numFmts>'
            . '<fonts count="5">'
            .   '<font><sz val="11"/><color rgb="FF1E293B"/><name val="Calibri"/><family val="2"/></font>'
            .   '<font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/><family val="2"/></font>'
            .   '<font><b/><sz val="16"/><color rgb="FF0F6B3E"/><name val="Calibri"/><family val="2"/></font>'
            .   '<font><b/><sz val="12"/><color rgb="FF0F6B3E"/><name val="Calibri"/><family val="2"/></font>'
            .   '<font><b/><sz val="11"/><color rgb="FF0F172A"/><name val="Calibri"/><family val="2"/></font>'
            . '</fonts>'
            . '<fills count="4">'
            .   '<fill><patternFill patternType="none"/></fill>'
            .   '<fill><patternFill patternType="gray125"/></fill>'
            .   '<fill><patternFill patternType="solid"><fgColor rgb="FF0F6B3E"/><bgColor indexed="64"/></patternFill></fill>'
            .   '<fill><patternFill patternType="solid"><fgColor rgb="FFE8F5EE"/><bgColor indexed="64"/></patternFill></fill>'
            . '</fills>'
            . '<borders count="2">'
            .   '<border><left/><right/><top/><bottom/><diagonal/></border>'
            .   '<border>'
            .     '<left style="thin"><color rgb="FFD6DEE6"/></left>'
            .     '<right style="thin"><color rgb="FFD6DEE6"/></right>'
            .     '<top style="thin"><color rgb="FFD6DEE6"/></top>'
            .     '<bottom style="thin"><color rgb="FFD6DEE6"/></bottom>'
            .     '<diagonal/>'
            .   '</border>'
            . '</borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="12">'
            // 0 default
            .   '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            // 1 header
            .   '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1">'
            .     '<alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            // 2 title
            .   '<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment vertical="center"/></xf>'
            // 3 section
            .   '<xf numFmtId="0" fontId="3" fillId="0" borderId="0" xfId="0" applyFont="1" applyAlignment="1"><alignment vertical="center"/></xf>'
            // 4 data text
            .   '<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyAlignment="1">'
            .     '<alignment vertical="center" wrapText="1"/></xf>'
            // 5 data integer
            .   '<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1">'
            .     '<alignment horizontal="right" vertical="center"/></xf>'
            // 6 data decimal
            .   '<xf numFmtId="165" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1" applyAlignment="1">'
            .     '<alignment horizontal="right" vertical="center"/></xf>'
            // 7 total text
            .   '<xf numFmtId="0" fontId="4" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1">'
            .     '<alignment vertical="center"/></xf>'
            // 8 total integer
            .   '<xf numFmtId="164" fontId="4" fillId="3" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1">'
            .     '<alignment horizontal="right" vertical="center"/></xf>'
            // 9 total decimal
            .   '<xf numFmtId="165" fontId="4" fillId="3" borderId="1" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1">'
            .     '<alignment horizontal="right" vertical="center"/></xf>'
            // 10 meta label
            .   '<xf numFmtId="0" fontId="4" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            // 11 meta value
            .   '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '</cellXfs>'
            . '<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            . '</styleSheet>';
    }

    private function content_types_xml() {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            . '<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            . '</Types>';
    }

    private function rels_xml() {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            . '</Relationships>';
    }

    private function app_xml() {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties">'
            . '<Application>Instroom</Application></Properties>';
    }

    private function core_xml() {
        $now = gmdate('Y-m-d\TH:i:s\Z');
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" '
            . 'xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" '
            . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            . '<dc:creator>Instroom</dc:creator>'
            . '<dcterms:created xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:created>'
            . '<dcterms:modified xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:modified>'
            . '</cp:coreProperties>';
    }

    private function workbook_xml() {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="' . $this->esc($this->sheet_name) . '" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private function workbook_rels_xml() {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }
}
