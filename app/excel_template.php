<?php
declare(strict_types=1);

require_once __DIR__ . '/pricing.php';

const PRICING_TEMPLATE = APP_ROOT . '/app/templates/pricing_template.xlsx';
const PRICING_OUTPUT_DIR = APP_ROOT . '/storage/pricing';

function pricing_template_status(): array
{
    return [
        'exists' => is_file(PRICING_TEMPLATE),
        'path' => PRICING_TEMPLATE,
        'updated_at' => is_file(PRICING_TEMPLATE) ? date('Y-m-d H:i:s', (int)filemtime(PRICING_TEMPLATE)) : null,
        'libreoffice' => libreoffice_binary(),
    ];
}

function libreoffice_binary(): ?string
{
    $candidates = [
        '/usr/bin/libreoffice',
        '/usr/local/bin/libreoffice',
        '/Applications/LibreOffice.app/Contents/MacOS/soffice',
        'libreoffice',
    ];
    foreach ($candidates as $candidate) {
        if ($candidate === 'libreoffice') {
            $found = trim((string)shell_exec('command -v libreoffice 2>/dev/null'));
            if ($found !== '') {
                return $found;
            }
            continue;
        }
        if (is_executable($candidate)) {
            return $candidate;
        }
    }
    return null;
}

function upload_pricing_template(array $file): void
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        json_response(['error' => '模板上传失败，请重新选择 xlsx 文件。'], 422);
    }
    $name = (string)($file['name'] ?? '');
    if (!str_ends_with(strtolower($name), '.xlsx')) {
        json_response(['error' => '请上传 .xlsx 格式的Excel模板。'], 422);
    }
    @mkdir(dirname(PRICING_TEMPLATE), 0775, true);
    if (!move_uploaded_file((string)$file['tmp_name'], PRICING_TEMPLATE)) {
        json_response(['error' => '模板保存失败，请检查目录权限。'], 500);
    }
}

function column_index(string $letters): int
{
    $letters = strtoupper($letters);
    $index = 0;
    for ($i = 0, $len = strlen($letters); $i < $len; $i++) {
        $index = $index * 26 + (ord($letters[$i]) - 64);
    }
    return $index;
}

function cell_sort_key(string $cell): int
{
    preg_match('/^([A-Z]+)(\d+)$/', strtoupper($cell), $m);
    return ((int)$m[2]) * 1000 + column_index($m[1]);
}

function set_sheet_cell(DOMDocument $dom, DOMXPath $xpath, string $cell, mixed $value): void
{
    $nodes = $xpath->query("//x:c[@r='{$cell}']");
    $c = $nodes && $nodes->length ? $nodes->item(0) : null;
    if (!$c) {
        $rowNum = (int)preg_replace('/\D/', '', $cell);
        $rowNodes = $xpath->query("//x:row[@r='{$rowNum}']");
        $row = $rowNodes && $rowNodes->length ? $rowNodes->item(0) : null;
        if (!$row) {
            $sheetData = $xpath->query('//x:sheetData')->item(0);
            $row = $dom->createElementNS('http://schemas.openxmlformats.org/spreadsheetml/2006/main', 'row');
            $row->setAttribute('r', (string)$rowNum);
            $sheetData->appendChild($row);
        }
        $c = $dom->createElementNS('http://schemas.openxmlformats.org/spreadsheetml/2006/main', 'c');
        $c->setAttribute('r', $cell);
        $inserted = false;
        foreach (iterator_to_array($row->childNodes) as $child) {
            if ($child instanceof DOMElement && $child->tagName === 'c' && cell_sort_key($cell) < cell_sort_key($child->getAttribute('r'))) {
                $row->insertBefore($c, $child);
                $inserted = true;
                break;
            }
        }
        if (!$inserted) {
            $row->appendChild($c);
        }
    }

    while ($c->firstChild) {
        $c->removeChild($c->firstChild);
    }

    if (is_numeric($value)) {
        $c->removeAttribute('t');
        $v = $dom->createElementNS('http://schemas.openxmlformats.org/spreadsheetml/2006/main', 'v', (string)(float)$value);
        $c->appendChild($v);
        return;
    }

    $c->setAttribute('t', 'inlineStr');
    $is = $dom->createElementNS('http://schemas.openxmlformats.org/spreadsheetml/2006/main', 'is');
    $t = $dom->createElementNS('http://schemas.openxmlformats.org/spreadsheetml/2006/main', 't');
    $t->appendChild($dom->createTextNode((string)$value));
    $is->appendChild($t);
    $c->appendChild($is);
}

function read_sheet_cell(DOMXPath $xpath, string $cell, array $sharedStrings = []): mixed
{
    $nodes = $xpath->query("//x:c[@r='{$cell}']");
    if (!$nodes || !$nodes->length) {
        return null;
    }
    $c = $nodes->item(0);
    $type = $c instanceof DOMElement ? $c->getAttribute('t') : '';
    if ($type === 'inlineStr') {
        return $xpath->evaluate('string(x:is/x:t)', $c);
    }
    $raw = $xpath->evaluate('string(x:v)', $c);
    if ($type === 's') {
        return $sharedStrings[(int)$raw] ?? $raw;
    }
    return is_numeric($raw) ? (float)$raw : $raw;
}

function load_shared_strings(ZipArchive $zip): array
{
    $xml = $zip->getFromName('xl/sharedStrings.xml');
    if ($xml === false) {
        return [];
    }
    $dom = new DOMDocument();
    $dom->loadXML($xml);
    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
    $strings = [];
    foreach ($xpath->query('//x:si') as $si) {
        $strings[] = $xpath->evaluate('string(.//x:t)', $si);
    }
    return $strings;
}

function write_inputs_to_workbook(string $path, array $input): void
{
    $zip = new ZipArchive();
    if ($zip->open($path) !== true) {
        throw new RuntimeException('无法打开Excel模板。');
    }
    $xml = $zip->getFromName('xl/worksheets/sheet1.xml');
    if ($xml === false) {
        $zip->close();
        throw new RuntimeException('模板中未找到第一个工作表。');
    }
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = false;
    $dom->loadXML($xml);
    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

    $map = [
        'H4' => $input['roomCode'] ?? '',
        'I4' => $input['contractRoomCode'] ?? ($input['roomCode'] ?? ''),
        'H5' => num($input, 'approvedArea'),
        'I5' => num($input, 'contractArea'),
        'H6' => num($input, 'approvedPrice'),
        'I6' => num($input, 'contractPrice'),
        'H7' => num($input, 'approvedPropertyFee', 12),
        'I7' => num($input, 'contractPropertyFee', 12),
        'H8' => '三年递增' . num($input, 'approvedEscalation', 5) . '%',
        'I8' => num($input, 'contractEscalation', 0) . '%',
        'H9' => (string)($input['approvedFreePattern'] ?? '1,1,1'),
        'I9' => (string)($input['contractFreePattern'] ?? '2,1,0'),
        'H10' => num($input, 'leaseYears', 3) . '年',
        'I10' => num($input, 'leaseYears', 3) . '年',
        'H14' => 0,
        'I14' => num($input, 'specialItems'),
        'H17' => num($input, 'costArea', num($input, 'contractArea')),
        'H19' => num($input, 'fitoutCost'),
        'H20' => num($input, 'partitionCost'),
    ];
    foreach ($map as $cell => $value) {
        set_sheet_cell($dom, $xpath, $cell, $value);
    }

    $zip->addFromString('xl/worksheets/sheet1.xml', $dom->saveXML());
    force_workbook_recalculation($zip);
    $zip->close();
}

function force_workbook_recalculation(ZipArchive $zip): void
{
    $workbookXml = $zip->getFromName('xl/workbook.xml');
    if ($workbookXml !== false) {
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($workbookXml);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $calcPr = $xpath->query('//x:calcPr')->item(0);
        if (!$calcPr) {
            $calcPr = $dom->createElementNS('http://schemas.openxmlformats.org/spreadsheetml/2006/main', 'calcPr');
            $dom->documentElement->appendChild($calcPr);
        }
        if ($calcPr instanceof DOMElement) {
            $calcPr->setAttribute('calcMode', 'auto');
            $calcPr->setAttribute('fullCalcOnLoad', '1');
            $calcPr->setAttribute('forceFullCalc', '1');
        }
        $zip->addFromString('xl/workbook.xml', $dom->saveXML());
    }
    $index = $zip->locateName('xl/calcChain.xml');
    if ($index !== false) {
        $zip->deleteIndex($index);
    }
}

function recalculate_with_libreoffice(string $path): bool
{
    $binary = libreoffice_binary();
    if (!$binary) {
        return false;
    }
    $dir = dirname($path);
    $cmd = escapeshellarg($binary) . ' --headless --convert-to xlsx --outdir ' . escapeshellarg($dir) . ' ' . escapeshellarg($path) . ' 2>&1';
    shell_exec($cmd);
    return true;
}

function read_workbook_outputs(string $path): array
{
    $zip = new ZipArchive();
    if ($zip->open($path) !== true) {
        throw new RuntimeException('无法读取测算结果Excel。');
    }
    $shared = load_shared_strings($zip);
    $xml = $zip->getFromName('xl/worksheets/sheet1.xml');
    $zip->close();
    if ($xml === false) {
        throw new RuntimeException('模板中未找到第一个工作表。');
    }
    $dom = new DOMDocument();
    $dom->loadXML($xml);
    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
    $breakRate = read_sheet_cell($xpath, 'J15', $shared);
    return [
        'roomCode' => read_sheet_cell($xpath, 'H4', $shared),
        'approvedArea' => read_sheet_cell($xpath, 'H5', $shared),
        'contractArea' => read_sheet_cell($xpath, 'I5', $shared),
        'approvedPrice' => read_sheet_cell($xpath, 'H6', $shared),
        'contractPrice' => read_sheet_cell($xpath, 'I6', $shared),
        'approvedRent' => read_sheet_cell($xpath, 'H12', $shared),
        'contractRent' => read_sheet_cell($xpath, 'I12', $shared),
        'rentDiff' => read_sheet_cell($xpath, 'J12', $shared),
        'approvedProperty' => read_sheet_cell($xpath, 'H13', $shared),
        'contractProperty' => read_sheet_cell($xpath, 'I13', $shared),
        'propertyDiff' => read_sheet_cell($xpath, 'J13', $shared),
        'specialDiff' => read_sheet_cell($xpath, 'J14', $shared),
        'breakRate' => is_numeric($breakRate) ? (float)$breakRate : null,
    ];
}

function run_excel_pricing(array $input): array
{
    if (!is_file(PRICING_TEMPLATE)) {
        json_response(['error' => '尚未上传价格测算Excel模板。'], 500);
    }
    @mkdir(PRICING_OUTPUT_DIR, 0775, true);
    $id = date('Ymd_His') . '_' . bin2hex(random_bytes(4));
    $relative = 'storage/pricing/pricing_' . $id . '.xlsx';
    $outputPath = APP_ROOT . '/' . $relative;
    if (!copy(PRICING_TEMPLATE, $outputPath)) {
        throw new RuntimeException('无法复制价格测算模板。');
    }
    write_inputs_to_workbook($outputPath, $input);
    $recalculated = recalculate_with_libreoffice($outputPath);
    $outputs = read_workbook_outputs($outputPath);
    if (!$recalculated || $outputs['breakRate'] === null || $outputs['breakRate'] === '') {
        $fallback = calculate_pricing($input);
        $outputs['breakRate'] = $fallback['breakRate'];
        $outputs['fallback'] = $fallback;
    }
    $outputs['jYears'] = interpolate_y_from_rate((float)$outputs['breakRate']);
    $outputs['download'] = $relative;
    $outputs['recalculated'] = $recalculated;
    return $outputs;
}
