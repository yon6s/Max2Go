<?php
declare(strict_types=1);

const CONTRACT_TEMPLATE = APP_ROOT . '/app/templates/contract_template.docx';
const CONTRACT_OUTPUT_DIR = APP_ROOT . '/storage/contracts';

function contract_value(array $input, string $key, string $default = ''): string
{
    $value = trim((string)($input[$key] ?? ''));
    return $value !== '' ? $value : $default;
}

function contract_money(float $amount): string
{
    return number_format($amount, 2, '.', '');
}

function contract_money_upper(float $amount): string
{
    $digits = ['零', '壹', '贰', '叁', '肆', '伍', '陆', '柒', '捌', '玖'];
    $units = ['', '拾', '佰', '仟'];
    $sections = ['', '万', '亿'];
    $integer = (int)floor(abs($amount) + 0.00001);
    $decimal = (int)round((abs($amount) - $integer) * 100);

    if ($integer === 0) {
        $result = '零元';
    } else {
        $result = '';
        $sectionIndex = 0;
        $needZero = false;
        while ($integer > 0) {
            $section = $integer % 10000;
            if ($section === 0) {
                $needZero = $result !== '';
            } else {
                $sectionText = '';
                $zero = false;
                for ($i = 0; $i < 4; $i++) {
                    $num = $section % 10;
                    if ($num === 0) {
                        $zero = $sectionText !== '';
                    } else {
                        if ($zero) {
                            $sectionText = '零' . $sectionText;
                            $zero = false;
                        }
                        $sectionText = $digits[$num] . $units[$i] . $sectionText;
                    }
                    $section = intdiv($section, 10);
                }
                if ($needZero) {
                    $result = '零' . $result;
                    $needZero = false;
                }
                $result = $sectionText . $sections[$sectionIndex] . $result;
            }
            $integer = intdiv($integer, 10000);
            $sectionIndex++;
        }
        $result .= '元';
    }

    $jiao = intdiv($decimal, 10);
    $fen = $decimal % 10;
    if ($jiao === 0 && $fen === 0) {
        return $result . '整';
    }
    if ($jiao > 0) {
        $result .= $digits[$jiao] . '角';
    }
    if ($fen > 0) {
        $result .= $digits[$fen] . '分';
    }
    return $result;
}

function contract_date_parts(string $date, array $fallback): array
{
    if (preg_match('/^(\\d{4})-(\\d{1,2})-(\\d{1,2})$/', $date, $matches)) {
        return [(int)$matches[1], (int)$matches[2], (int)$matches[3]];
    }
    return $fallback;
}

function contract_cn_date(string $date, string $fallback): string
{
    [$year, $month, $day] = contract_date_parts($date, [0, 0, 0]);
    if ($year === 0) {
        return $fallback;
    }
    return $year . '年' . $month . '月' . $day . '日';
}

function contract_iso_date(string $date, string $fallback): string
{
    return preg_match('/^\\d{4}-\\d{1,2}-\\d{1,2}$/', $date) ? $date : $fallback;
}

function contract_date_object(string $date): DateTimeImmutable
{
    return new DateTimeImmutable(contract_iso_date($date, date('Y-m-d')));
}

function contract_display_date(DateTimeImmutable $date): string
{
    return $date->format('Y') . '年' . (int)$date->format('n') . '月' . (int)$date->format('j') . '日';
}

function contract_replace_text_nodes(DOMNodeList $textNodes, string $search, string $replace): bool
{
    if ($search === '' || $search === $replace) {
        return false;
    }

    $changed = false;
    $guard = 0;
    while (true) {
        $guard++;
        if ($guard > 50) {
            break;
        }
        $fullText = '';
        foreach ($textNodes as $node) {
            $fullText .= $node->nodeValue;
        }

        $position = mb_strpos($fullText, $search);
        if ($position === false) {
            break;
        }

        $searchLength = mb_strlen($search);
        $endPosition = $position + $searchLength;
        $cursor = 0;
        $startIndex = null;
        $endIndex = null;
        $startOffset = 0;
        $endOffset = 0;

        foreach ($textNodes as $index => $node) {
            $length = mb_strlen($node->nodeValue);
            if ($startIndex === null && $position < $cursor + $length) {
                $startIndex = $index;
                $startOffset = max(0, $position - $cursor);
            }
            if ($endIndex === null && $endPosition <= $cursor + $length) {
                $endIndex = $index;
                $endOffset = max(0, $endPosition - $cursor);
                break;
            }
            $cursor += $length;
        }

        if ($startIndex === null || $endIndex === null) {
            break;
        }

        foreach ($textNodes as $index => $node) {
            if ($index < $startIndex || $index > $endIndex) {
                continue;
            }
            $value = $node->nodeValue;
            if ($startIndex === $endIndex) {
                $node->nodeValue = mb_substr($value, 0, $startOffset) . $replace . mb_substr($value, $endOffset);
            } elseif ($index === $startIndex) {
                $node->nodeValue = mb_substr($value, 0, $startOffset) . $replace;
            } elseif ($index === $endIndex) {
                $node->nodeValue = mb_substr($value, $endOffset);
            } else {
                $node->nodeValue = '';
            }
        }
        $changed = true;
    }

    return $changed;
}

function contract_underline_text_node(DOMNode $textNode): void
{
    // Deprecated. Do not force underlines anymore.
}

function contract_apply_replacements(string $xml, array $replacements): string
{
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = true;
    $dom->formatOutput = false;
    $dom->loadXML($xml);
    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

    foreach ($xpath->query('//w:p') as $paragraph) {
        $textNodes = $xpath->query('.//w:t', $paragraph);
        if ($textNodes->length === 0) {
            continue;
        }
        $textContent = preg_replace('/\s+/', '', $paragraph->textContent);
        
        foreach ($replacements as $search => $replace) {
            if (is_array($replace)) {
                $searchNoSpace = preg_replace('/\s+/', '', $search);
                if (mb_strpos($textContent, $searchNoSpace) !== false) {
                    foreach ($replace as $innerSearch => $innerReplace) {
                        contract_replace_text_nodes($textNodes, (string)$innerSearch, (string)$innerReplace);
                    }
                }
            } else {
                contract_replace_text_nodes($textNodes, (string)$search, (string)$replace);
            }
        }
    }

    return $dom->saveXML();
}

function contract_set_cell_text(DOMXPath $xpath, DOMElement $cell, string $text): void
{
    $textNodes = $xpath->query('.//w:t', $cell);
    if ($textNodes->length === 0) {
        return;
    }
    $textNodes->item(0)->nodeValue = $text;
    for ($i = 1; $i < $textNodes->length; $i++) {
        $textNodes->item($i)->nodeValue = '';
    }
}

function contract_rent_rows(array $input): array
{
    $leaseStart = contract_date_object(contract_value($input, 'leaseStart', '2025-12-28'));
    $leaseMonths = max(1, (int)contract_value($input, 'leaseMonths', '36'));
    $leaseEnd = $leaseStart->modify('+' . $leaseMonths . ' months')->modify('-1 day');
    
    $fitoutStart1 = trim((string)($input['fitoutStart1'] ?? ''));
    $fitoutEnd1 = trim((string)($input['fitoutEnd1'] ?? ''));
    $fitoutStart2 = trim((string)($input['fitoutStart2'] ?? ''));
    $fitoutEnd2 = trim((string)($input['fitoutEnd2'] ?? ''));
    
    $monthlyRent1 = (float)contract_value($input, 'monthlyRent1', '11680');
    $monthlyRent2 = (float)contract_value($input, 'monthlyRent2', '12264');
    $phaseTwoStart = contract_date_object(contract_value($input, 'rentPeriod2Start', '2099-12-31'));
    $firstMonths = (int)contract_value($input, 'firstRentMonths', '3');
    $paymentCycle = (int)contract_value($input, 'paymentCycle', '3');

    $rows = [];
    $currentDate = $leaseStart;
    $first = true;

    while ($currentDate <= $leaseEnd) {
        $targetPaidMonths = $first ? $firstMonths : $paymentCycle;
        
        $d = (int)$currentDate->format('j');
        $m = (int)$currentDate->format('n') + $targetPaidMonths;
        $y = (int)$currentDate->format('Y') + intdiv((int)($m - 1), 12);
        $m = ((int)($m - 1) % 12) + 1;
        $firstDayOfMonth = new DateTimeImmutable(sprintf('%04d-%02d-01', $y, $m));
        $dim = (int)$firstDayOfMonth->format('t');
        if ($d > $dim) $d = $dim;
        $baselineEnd = (new DateTimeImmutable(sprintf('%04d-%02d-%02d', $y, $m, $d)))->modify('-1 day');
        
        $targetPaidDays = $currentDate->diff($baselineEnd)->days + 1;
        $actualPaidDays = 0;
        
        $scanDate = $currentDate;
        while ($actualPaidDays < $targetPaidDays) {
            $dateStr = $scanDate->format('Y-m-d');
            $isFree = false;
            if ($fitoutStart1 !== '' && $fitoutEnd1 !== '' && $dateStr >= $fitoutStart1 && $dateStr <= $fitoutEnd1) {
                $isFree = true;
            }
            if ($fitoutStart2 !== '' && $fitoutEnd2 !== '' && $dateStr >= $fitoutStart2 && $dateStr <= $fitoutEnd2) {
                $isFree = true;
            }
            if (!$isFree) {
                $actualPaidDays++;
            }
            $periodEnd = $scanDate;
            $scanDate = $scanDate->modify('+1 day');
        }
        
        if ($periodEnd > $leaseEnd) {
            $periodEnd = $leaseEnd;
            $actualPaidDays = 0;
            $scanDate = $currentDate;
            while ($scanDate <= $periodEnd) {
                $dateStr = $scanDate->format('Y-m-d');
                $isFree = false;
                if ($fitoutStart1 !== '' && $fitoutEnd1 !== '' && $dateStr >= $fitoutStart1 && $dateStr <= $fitoutEnd1) {
                    $isFree = true;
                }
                if ($fitoutStart2 !== '' && $fitoutEnd2 !== '' && $dateStr >= $fitoutStart2 && $dateStr <= $fitoutEnd2) {
                    $isFree = true;
                }
                if (!$isFree) {
                    $actualPaidDays++;
                }
                $scanDate = $scanDate->modify('+1 day');
            }
            $amount = ($actualPaidDays / $targetPaidDays) * ($targetPaidMonths * $monthlyRent1);
        } else {
            $amount = $targetPaidMonths * $monthlyRent1;
        }

        $dueDate = $first
            ? contract_date_object(contract_value($input, 'firstPayDate', $leaseStart->modify('-10 days')->format('Y-m-d')))
            : $currentDate->modify('-10 days');
            
        $amountRounded = ceil($amount * 100) / 100;
        $netRounded = ceil(($amountRounded / 1.09) * 100) / 100;
        $taxRounded = $amountRounded - $netRounded;

        $rows[] = [
            contract_display_date($dueDate),
            contract_display_date($currentDate),
            '～',
            contract_display_date($periodEnd),
            number_format($amountRounded, 2, '.', ''),
            number_format($taxRounded, 2, '.', ''),
            number_format($netRounded, 2, '.', ''),
        ];

        $currentDate = $periodEnd->modify('+1 day');
        $first = false;
    }

    return $rows;
}

function contract_update_rent_table(string $xml, array $input): string
{
    $dom = new DOMDocument();
    $dom->preserveWhiteSpace = true;
    $dom->formatOutput = false;
    $dom->loadXML($xml);
    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

    $table = $xpath->query('//w:tbl')->item(1);
    if (!$table instanceof DOMElement) {
        return $xml;
    }
    $rows = $xpath->query('./w:tr', $table);
    if ($rows->length < 2) {
        return $xml;
    }

    $templateRow = $rows->item(1);
    $lastRow = $rows->item($rows->length - 1)->cloneNode(true);
    
    for ($i = $rows->length - 1; $i >= 1; $i--) {
        $table->removeChild($rows->item($i));
    }

    $totalAmount = 0.0;
    $totalTax = 0.0;
    $totalNet = 0.0;

    foreach (contract_rent_rows($input) as $rowData) {
        $row = $templateRow->cloneNode(true);
        $cells = $xpath->query('./w:tc', $row);
        foreach ($rowData as $index => $value) {
            $cell = $cells->item($index);
            if ($cell instanceof DOMElement) {
                contract_set_cell_text($xpath, $cell, $value);
            }
        }
        $table->appendChild($row);
        
        $totalAmount += (float)$rowData[4];
        $totalTax += (float)$rowData[5];
        $totalNet += (float)$rowData[6];
    }

    $lastCells = $xpath->query('.//w:tc', $lastRow);
    if ($lastCells->length >= 4) {
        contract_set_cell_text($xpath, $lastCells->item(1), number_format($totalAmount, 2, '.', ''));
        contract_set_cell_text($xpath, $lastCells->item(2), number_format($totalTax, 2, '.', ''));
        contract_set_cell_text($xpath, $lastCells->item(3), number_format($totalNet, 2, '.', ''));
    }
    $table->appendChild($lastRow);

    return $dom->saveXML();
}

function generate_contract_docx(array $input): array
{
    if (!is_file(CONTRACT_TEMPLATE)) {
        throw new RuntimeException('合同模板不存在，请先放置 app/templates/contract_template.docx。');
    }
    if (!is_dir(CONTRACT_OUTPUT_DIR)) {
        mkdir(CONTRACT_OUTPUT_DIR, 0775, true);
    }

    $tenantName = contract_value($input, 'tenantName', '上海雅瑄科技有限公司');
    $signYear = contract_value($input, 'signYear', date('Y'));
    $signMonth = contract_value($input, 'signMonth', '');
    $signDay = contract_value($input, 'signDay', '');
    $leaseStart = contract_cn_date(contract_value($input, 'leaseStart', '2025-12-28'), '2025年12月28日');
    $leaseEnd = contract_cn_date(contract_value($input, 'leaseEnd', '2028-12-27'), '2028年12月27日');
    $fitoutStart1Raw = trim((string)($input['fitoutStart1'] ?? ''));
    $fitoutEnd1Raw = trim((string)($input['fitoutEnd1'] ?? ''));
    $fitoutStart2Raw = trim((string)($input['fitoutStart2'] ?? ''));
    $fitoutEnd2Raw = trim((string)($input['fitoutEnd2'] ?? ''));

    $fitoutStart1 = contract_cn_date($fitoutStart1Raw, '2025年12月28日');
    $fitoutEnd1 = contract_cn_date($fitoutEnd1Raw, '2026年2月27日');
    $monthlyRent1 = (float)contract_value($input, 'monthlyRent1', '11680');
    $monthlyRent2 = (float)contract_value($input, 'monthlyRent2', '12264');
    $firstRent = (float)contract_value($input, 'firstRent', '35040');
    $deposit = (float)contract_value($input, 'deposit', (string)$firstRent);
    $propertyFee = (float)contract_value($input, 'propertyFee', '2880');
    $area = contract_value($input, 'area', '240');
    $leaseMonths = contract_value($input, 'leaseMonths', '36');
    $propertyAddress = contract_value($input, 'propertyAddress', '上海市宝山区罗店路388弄33号B座706室');
    $roomCode = contract_value($input, 'roomCode', 'B座706室');
    $noticeAddress = contract_value($input, 'noticeAddress', $propertyAddress);
    $contactPerson = contract_value($input, 'contactPerson', contract_value($input, 'legalRepresentative', '杨应新'));
    $taxPerSqm = contract_value($input, 'taxPerSqm', '1250');
    $deliveryDate = contract_cn_date(contract_value($input, 'deliveryDate', '2025-12-27'), '2025年12月27日');
    $rentPeriod1Start = contract_cn_date(contract_value($input, 'rentPeriod1Start', '2025-12-28'), '2025年12月28日');
    $rentPeriod1End = contract_cn_date(contract_value($input, 'rentPeriod1End', '2027-12-27'), '2027年12月27日');
    $rentPeriod2Start = contract_cn_date(contract_value($input, 'rentPeriod2Start', '2027-12-28'), '2027年12月28日');
    $rentPeriod2End = contract_cn_date(contract_value($input, 'rentPeriod2End', '2028-12-27'), '2028年12月27日');

    $replacements = [
        '上海雅瑄科技有限公司' => $tenantName,
        '91310120MAC6YKQU52' => contract_value($input, 'creditCode', '91310120MAC6YKQU52'),
        '上海市奉贤区南桥镇西闸公路566号' => contract_value($input, 'registeredAddress', '上海市奉贤区南桥镇西闸公路566号'),
        '杨应新' => contract_value($input, 'legalRepresentative', '杨应新'),
        '13918141990' => contract_value($input, 'tenantPhone', '13918141990'),
        '【2025】' => '【' . $signYear . '】',
        '【11】' => '【' . $signMonth . '】',
        '【5】' => '【' . $signDay . '】',
        '上海市宝山区罗店路388弄33号B座706室' => $propertyAddress,
        'B座706室' => $roomCode,
        '租赁期限为' => ['36' => $leaseMonths],
        '自2025年12月28日起至2028年12月27日止' => [
            '2025年12月28日' => $leaseStart,
            '2028年12月27日' => $leaseEnd,
        ],
        '交付标的房屋' => ['2025年12月27日' => $deliveryDate],
        '自2025年12月28日起至2027年12月27日止的租金为人民币:11680元/月' => [
            '2025年12月28日' => $rentPeriod1Start,
            '2027年12月27日' => $rentPeriod1End,
            '11680' => (int)$monthlyRent1,
        ],
        '自2027年12月28日起至2028年12月27日止的租金为人民币:12264元/月' => [
            '2027年12月28日' => $rentPeriod2Start,
            '2028年12月27日' => $rentPeriod2End,
            '12264' => (int)$monthlyRent2,
        ],
        '首期房屋租金' => [
            '叁万伍仟零肆拾元整' => contract_money_upper($firstRent),
            '35040.00' => contract_money($firstRent),
        ],
        '支付保证金' => [
            '叁万伍仟零肆拾元整' => contract_money_upper($deposit),
            '35040.00' => contract_money($deposit),
        ],
        '物业管理费金额为' => ['2880' => (int)$propertyFee],
        '该房屋计租面积为' => ['240' => $area],
        '出租方联络地址' => ['上海市宝山区罗店路388弄MAX科技园' => contract_value($input, 'lessorNoticeAddress', '上海市宝山区罗店路388弄MAX科技园')],
        '承租方联络地址' => ['上海市宝山区罗店路388弄33号B座706室' => $noticeAddress],
        '联系人：【葛英林】' => [
            '021-56590771' => contract_value($input, 'lessorPhone', '021-56590771'),
            '葛英林' => contract_value($input, 'lessorContact', '葛英林')
        ],
        '联系人：【杨应新】' => [
            '13918141990' => contract_value($input, 'tenantPhone', '13918141990'),
            '杨应新' => $contactPerson
        ],
        '应向国家缴纳税款' => ['1250' => $taxPerSqm],
        '首期租金及首期物业费' => [
            '2025年12月5日' => contract_cn_date(contract_value($input, 'firstPayDate', date('Y-m-d')), '2025年12月5日'),
        ],
    ];

    if ($fitoutStart1Raw === '' || $fitoutEnd1Raw === '') {
        $replacements['自2025年12月28日起至2026年2月27日'] = '';
    } else {
        $replacements['自2025年12月28日起至2026年2月27日'] = [
            '2025年12月28日' => $fitoutStart1,
            '2026年2月27日' => $fitoutEnd1,
        ];
    }

    if ($fitoutStart2Raw === '' || $fitoutEnd2Raw === '') {
        $replacements['，自2026年12月28日起至2027年1月27日'] = '';
    } else {
        $replacements['自2026年12月28日起至2027年1月27日'] = [
            '2026年12月28日' => contract_cn_date($fitoutStart2Raw, '2026年12月28日'),
            '2027年1月27日' => contract_cn_date($fitoutEnd2Raw, '2027年1月27日'),
        ];
    }

    $safeName = preg_replace('/[^\\p{Han}A-Za-z0-9_-]+/u', '', $tenantName) ?: '承租方';
    $filename = 'MAX科技园租赁合同-' . $safeName . '-' . date('Ymd-His') . '.docx';
    $outputPath = CONTRACT_OUTPUT_DIR . '/' . $filename;
    copy(CONTRACT_TEMPLATE, $outputPath);


    $zip = new ZipArchive();
    if ($zip->open($outputPath) !== true) {
        throw new RuntimeException('无法打开合同模板。');
    }

    foreach (['word/document.xml', 'word/footer1.xml', 'word/footer2.xml'] as $part) {
        $xml = $zip->getFromName($part);
        if ($xml === false) {
            continue;
        }
        $xml = contract_apply_replacements($xml, $replacements);
        if ($part === 'word/document.xml') {
            $xml = contract_update_rent_table($xml, $input);
        }
        $zip->addFromString($part, $xml);
    }
    $zip->close();

    return [
        'filename' => $filename,
        'path' => $outputPath,
        'url' => 'api/contract.php?download=' . rawurlencode($filename),
    ];
}

function contract_download_path(string $filename): ?string
{
    $base = basename($filename);
    if ($base === '' || $base !== $filename) {
        return null;
    }
    $path = CONTRACT_OUTPUT_DIR . '/' . $base;
    return is_file($path) ? $path : null;
}
