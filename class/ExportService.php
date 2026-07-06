<?php

class ExportService
{
    public static function sendCsv($filename, array $headers, array $rows)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . self::safeFilename($filename, 'csv') . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($output, $headers);
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
        exit;
    }

    public static function sendPdf($title, array $headers, array $rows, $filename = 'report')
    {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . self::safeFilename($filename, 'pdf') . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo self::buildPdf($title, $headers, $rows);
        exit;
    }

    private static function safeFilename($name, $ext)
    {
        $name = preg_replace('/[^a-zA-Z0-9_-]+/', '_', $name);
        return trim($name, '_') . '.' . $ext;
    }

    private static function buildPdf($title, array $headers, array $rows)
    {
        $lines = [];
        $lines[] = $title;
        $lines[] = 'Generated: ' . date('Y-m-d H:i:s');
        $lines[] = '';
        $lines[] = implode(' | ', $headers);

        foreach ($rows as $row) {
            $lines[] = implode(' | ', array_map(static function ($value) {
                return str_replace(["\r", "\n"], ' ', (string) $value);
            }, $row));
        }

        $content = "BT\n/F1 10 Tf\n";
        $y = 800;
        foreach ($lines as $line) {
            $text = self::escapePdfText($line);
            $content .= "1 0 0 1 40 {$y} Tm ({$text}) Tj\n";
            $y -= 14;
            if ($y < 40) {
                break;
            }
        }
        $content .= "ET";

        $objects = [];
        $objects[] = "1 0 obj<< /Type /Catalog /Pages 2 0 R >>endobj\n";
        $objects[] = "2 0 obj<< /Type /Pages /Kids [3 0 R] /Count 1 >>endobj\n";
        $objects[] = "3 0 obj<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>endobj\n";
        $objects[] = '4 0 obj<< /Length ' . strlen($content) . " >>stream\n{$content}\nendstream endobj\n";
        $objects[] = "5 0 obj<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>endobj\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object;
        }

        $xrefPos = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefPos}\n%%EOF";

        return $pdf;
    }

    private static function escapePdfText($text)
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}
