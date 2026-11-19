<?php

namespace App\Services;

use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

class XlsxService
{
    public static function readRows(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new RuntimeException('File XLSX tidak dapat dibuka atau rusak.');
        }

        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml !== false) {
            $sx = new SimpleXMLElement($sharedXml);
            foreach ($sx->si as $si) {
                if (isset($si->t)) {
                    $sharedStrings[] = (string) $si->t;
                } else {
                    $text = '';
                    foreach ($si->r as $r) {
                        $text .= (string) $r->t;
                    }
                    $sharedStrings[] = $text;
                }
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($sheetXml === false) {
            throw new RuntimeException('Worksheet utama (sheet1.xml) tidak ditemukan pada file XLSX.');
        }

        $sheet = new SimpleXMLElement($sheetXml);
        $rows = [];

        if (!isset($sheet->sheetData->row)) {
            return $rows;
        }

        foreach ($sheet->sheetData->row as $rowNode) {
            $rowData = [];
            $maxCol = 0;

            foreach ($rowNode->c as $cell) {
                $ref = (string) $cell['r'];
                $colLetters = preg_replace('/[0-9]+/', '', $ref);
                $colIndex = self::columnLettersToIndex($colLetters);
                $maxCol = max($maxCol, $colIndex);

                $type = (string) $cell['t'];
                $value = '';

                if ($type === 's') {
                    $idx = (int) $cell->v;
                    $value = $sharedStrings[$idx] ?? '';
                } elseif ($type === 'inlineStr') {
                    $value = (string) ($cell->is->t ?? '');
                } else {
                    $value = (string) ($cell->v ?? '');
                }

                $rowData[$colIndex] = trim($value);
            }

            if ($rowData !== []) {
                $normalized = [];
                for ($i = 0; $i <= $maxCol; $i++) {
                    $normalized[] = $rowData[$i] ?? '';
                }
                $rows[] = $normalized;
            }
        }

        return $rows;
    }

    public static function createWorkbook(array $rows): string
    {
        $tmpPath = tempnam(sys_get_temp_dir(), 'osis_xlsx_') . '.xlsx';
        $zip = new ZipArchive();
        if ($zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Gagal membuat file XLSX.');
        }

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
</Types>');

        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>');

        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
</Relationships>');

        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Data Pemilih" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>');

        $sheetRowsXml = '';
        foreach ($rows as $rIdx => $cols) {
            $rowNum = $rIdx + 1;
            $sheetRowsXml .= "<row r=\"{$rowNum}\">";
            foreach ($cols as $cIdx => $val) {
                $colRef = self::indexToColumnLetters($cIdx) . $rowNum;
                $escaped = htmlspecialchars((string) $val, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                $sheetRowsXml .= "<c r=\"{$colRef}\" t=\"inlineStr\"><is><t>{$escaped}</t></is></c>";
            }
            $sheetRowsXml .= '</row>';
        }

        $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <sheetData>' . $sheetRowsXml . '</sheetData>
</worksheet>');

        $zip->close();

        return $tmpPath;
    }

    private static function columnLettersToIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $index = 0;
        for ($i = 0; $i < strlen($letters); $i++) {
            $index = $index * 26 + (ord($letters[$i]) - ord('A') + 1);
        }
        return max(0, $index - 1);
    }

    private static function indexToColumnLetters(int $index): string
    {
        $letters = '';
        $index++;
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letters = chr(65 + $mod) . $letters;
            $index = (int) (($index - $mod) / 26);
        }
        return $letters;
    }
}
