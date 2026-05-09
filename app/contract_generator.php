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
            if ($startIndex === null && $position <= $cursor + $length) {
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
                contract_underline_text_node($node);
            } elseif ($index === $startIndex) {
                $node->nodeValue = mb_substr($value, 0, $startOffset) . $replace;
                contract_underline_text_node($node);
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
    $run = $textNode->parentNode;
    while ($run instanceof DOMNode && $run->localName !== 'r') {
        $run = $run->parentNode;
    }
    if (!$run instanceof DOMElement) {
        return;
    }

    $namespace = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
    $runProps = null;
    foreach ($run->childNodes as $child) {
        if ($child instanceof DOMElement && $child->localName === 'rPr') {
            $runProps = $child;
            break;
        }
    }
    if (!$runProps instanceof DOMElement) {
        $runProps = $run->ownerDocument->createElementNS($namespace, 'w:rPr');
        $run->insertBefore($runProps, $run->firstChild);
    }
    foreach ($runProps->childNodes as $child) {
        if ($child instanceof DOMElement && $child->localName === 'u') {
            $child->setAttributeNS($namespace, 'w:val', 'single');
            return;
        }
    }
    $underline = $run->ownerDocument->createElementNS($namespace, 'w:u');
    $underline->setAttributeNS($namespace, 'w:val', 'single');
    $runProps->appendChild($underline);
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
        foreach ($replacements as $search => $replace) {
            contract_replace_text_nodes($textNodes, (string)$search, (string)$replace);
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
    $phaseTwoStart = contract_date_object(contract_value($input, 'rentPeriod2Start', $leaseStart->modify('+' . max(12, $leaseMonths - 12) . ' months')->format('Y-m-d')));
    $monthlyRent1 = (float)contract_value($input, 'monthlyRent1', '11680');
    $monthlyRent2 = (float)contract_value($input, 'monthlyRent2', '12264');
    $firstMonths = max(1, min(4, (int)contract_value($input, 'firstRentMonths', '3')));

    $rows = [];
    $periodStart = $leaseStart;
    $monthsRemaining = $leaseMonths;
    $first = true;

    while ($monthsRemaining > 0) {
        $months = $first ? min($firstMonths, $monthsRemaining) : min(3, $monthsRemaining);
        $periodEnd = $periodStart->modify('+' . $months . ' months')->modify('-1 day');
        if ($periodEnd > $leaseEnd) {
            $periodEnd = $leaseEnd;
        }

        $amount = 0.0;
        $monthCursor = $periodStart;
        for ($i = 0; $i < $months; $i++) {
            $amount += $monthCursor >= $phaseTwoStart ? $monthlyRent2 : $monthlyRent1;
            $monthCursor = $monthCursor->modify('+1 month');
        }

        $dueDate = $first
            ? contract_date_object(contract_value($input, 'firstPayDate', $leaseStart->modify('-10 days')->format('Y-m-d')))
            : $periodStart->modify('-10 days');
        $net = $amount / 1.09;
        $tax = $amount - $net;
        $rows[] = [
            contract_display_date($dueDate),
            contract_display_date($periodStart),
            '～',
            contract_display_date($periodEnd),
            number_format($amount, 2),
            number_format($tax, 2),
            number_format($net, 2),
        ];

        $periodStart = $periodEnd->modify('+1 day');
        $monthsRemaining -= $months;
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
    for ($i = $rows->length - 1; $i >= 1; $i--) {
        $table->removeChild($rows->item($i));
    }

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
    }

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
    $fitoutStart1 = contract_cn_date(contract_value($input, 'fitoutStart1', '2025-12-28'), '2025年12月28日');
    $fitoutEnd1 = contract_cn_date(contract_value($input, 'fitoutEnd1', '2026-02-27'), '2026年2月27日');
    $fitoutStart2 = contract_cn_date(contract_value($input, 'fitoutStart2', '2026-12-28'), '2026年12月28日');
    $fitoutEnd2 = contract_cn_date(contract_value($input, 'fitoutEnd2', '2027-01-27'), '2027年1月27日');
    $deliveryDate = contract_cn_date(contract_value($input, 'deliveryDate', '2025-12-27'), '2025年12月27日');
    $rentPeriod1Start = contract_cn_date(contract_value($input, 'rentPeriod1Start', '2025-12-28'), '2025年12月28日');
    $rentPeriod1End = contract_cn_date(contract_value($input, 'rentPeriod1End', '2027-12-27'), '2027年12月27日');
    $rentPeriod2Start = contract_cn_date(contract_value($input, 'rentPeriod2Start', '2027-12-28'), '2027年12月28日');
    $rentPeriod2End = contract_cn_date(contract_value($input, 'rentPeriod2End', '2028-12-27'), '2028年12月27日');

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

    $replacements = [
        '上海雅瑄科技有限公司' => $tenantName,
        '91310120MAC6YKQU52' => contract_value($input, 'creditCode', '91310120MAC6YKQU52'),
        '上海市奉贤区南桥镇西闸公路566号' => contract_value($input, 'registeredAddress', '上海市奉贤区南桥镇西闸公路566号'),
        '杨应新' => contract_value($input, 'legalRepresentative', '杨应新'),
        '13918141990' => contract_value($input, 'tenantPhone', '13918141990'),
        '【2025】年【11】月【5】日' => '【' . $signYear . '】年【' . $signMonth . '】月【' . $signDay . '】日',
        '上海市宝山区罗店路388弄33号B座706室' => $propertyAddress,
        'B座706室' => $roomCode,
        '租赁期限为36个月' => '租赁期限为' . $leaseMonths . '个月',
        '自2025年12月28日起至2028年12月27日止' => '自' . $leaseStart . '起至' . $leaseEnd . '止',
        '自2025年12月28日起至2026年2月27日' => '自' . $fitoutStart1 . '起至' . $fitoutEnd1,
        '自2026年12月28日起至2027年1月27日' => '自' . $fitoutStart2 . '起至' . $fitoutEnd2,
        '2025年12月27日向承租方交付标的房屋' => $deliveryDate . '向承租方交付标的房屋',
        '自2025年12月28日起至2027年12月27日止的租金为人民币:11680元/月' => '自' . $rentPeriod1Start . '起至' . $rentPeriod1End . '止的租金为人民币:' . (int)$monthlyRent1 . '元/月',
        '自2027年12月28日起至2028年12月27日止的租金为人民币:12264元/月' => '自' . $rentPeriod2Start . '起至' . $rentPeriod2End . '止的租金为人民币:' . (int)$monthlyRent2 . '元/月',
        '承租方应于本合同签订之日起30日内，向出租方支付首期房屋租金（“首期租金”）人民币（大写）：叁万伍仟零肆拾元整 （￥：35040.00元）（含9%增值税）。' =>
            '承租方应于本合同签订之日起30日内，向出租方支付首期房屋租金（“首期租金”）人民币（大写）：' . contract_money_upper($firstRent) . ' （￥：' . contract_money($firstRent) . '元）（含9%增值税）。',
        '承租方应于本合同签订之日起30日内向出租方支付保证金人民币（大写）：叁万伍仟零肆拾元整 （￥：35040.00元），作为承租方履行本合同之担保，保证金不计利息，出租方向承租方开具收据。标的房屋租金调整的，保证金亦同时相应调整。' =>
            '承租方应于本合同签订之日起30日内向出租方支付保证金人民币（大写）：' . contract_money_upper($deposit) . ' （￥：' . contract_money($deposit) . '元），作为承租方履行本合同之担保，保证金不计利息，出租方向承租方开具收据。标的房屋租金调整的，保证金亦同时相应调整。',
        '物业管理费金额为2880元/月' => '物业管理费金额为' . (int)$propertyFee . '元/月',
        '该房屋计租面积为240m²' => '该房屋计租面积为' . $area . 'm²',
        '出租方联络地址为：【上海市宝山区罗店路388弄MAX科技园】' => '出租方联络地址为：【' . contract_value($input, 'lessorNoticeAddress', '上海市宝山区罗店路388弄MAX科技园') . '】',
        '承租方联络地址为：【上海市宝山区罗店路388弄33号B座706室】' => '承租方联络地址为：【' . $noticeAddress . '】',
        '电话：【021-56590771】         联系人：【葛英林】' => '电话：【' . contract_value($input, 'lessorPhone', '021-56590771') . '】         联系人：【' . contract_value($input, 'lessorContact', '葛英林') . '】',
        '电话：【13918141990】          联系人：【杨应新】' => '电话：【' . contract_value($input, 'tenantPhone', '13918141990') . '】          联系人：【' . $contactPerson . '】',
        '应向国家缴纳税款1250元/㎡' => '应向国家缴纳税款' . $taxPerSqm . '元/㎡',
        '2025年12月5日' => contract_cn_date(contract_value($input, 'firstPayDate', date('Y-m-d')), '2025年12月5日'),
        '35,040.00' => number_format($firstRent, 2),
        '32,146.79' => number_format($firstRent / 1.09, 2),
        '2,893.21' => number_format($firstRent - $firstRent / 1.09, 2),
    ];

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
