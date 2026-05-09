<?php
declare(strict_types=1);

function j_recovery_table(): array
{
    return [
        [0.0, 8.26], [-0.002, 8.27], [-0.004, 8.28], [-0.006, 8.3], [-0.008, 8.31],
        [-0.01, 8.32], [-0.012, 8.34], [-0.016, 8.37], [-0.018, 8.38], [-0.02, 8.39],
        [-0.022, 8.41], [-0.024, 8.42], [-0.026, 8.43], [-0.028, 8.45], [-0.03, 8.46],
        [-0.032, 8.48], [-0.034, 8.49], [-0.036, 8.5], [-0.038, 8.52], [-0.04, 8.53],
        [-0.042, 8.55], [-0.044, 8.56], [-0.046, 8.57], [-0.048, 8.59], [-0.05, 8.6],
        [-0.052, 8.62], [-0.054, 8.63], [-0.056, 8.65], [-0.058, 8.66], [-0.06, 8.68],
        [-0.062, 8.69], [-0.064, 8.71], [-0.066, 8.72], [-0.068, 8.73], [-0.07, 8.75],
        [-0.072, 8.76], [-0.074, 8.78], [-0.076, 8.79], [-0.078, 8.81], [-0.08, 8.83],
        [-0.082, 8.84], [-0.084, 8.86], [-0.086, 8.87], [-0.088, 8.89], [-0.09, 8.9],
        [-0.092, 8.92], [-0.094, 8.93], [-0.096, 8.95], [-0.098, 8.97], [-0.1, 8.98],
        [-0.102, 9.0], [-0.104, 9.01], [-0.106, 9.03], [-0.108, 9.05], [-0.11, 9.06],
        [-0.112, 9.08], [-0.114, 9.1], [-0.116, 9.11], [-0.118, 9.13], [-0.12, 9.15],
        [-0.122, 9.16], [-0.124, 9.18], [-0.126, 9.2], [-0.128, 9.21], [-0.13, 9.23],
        [-0.132, 9.25], [-0.134, 9.26], [-0.136, 9.28], [-0.138, 9.3], [-0.14, 9.32],
        [-0.142, 9.33], [-0.144, 9.35], [-0.146, 9.37], [-0.148, 9.39], [-0.15, 9.41],
        [-0.152, 9.42], [-0.154, 9.44], [-0.156, 9.46], [-0.158, 9.48], [-0.16, 9.5],
        [-0.162, 9.51], [-0.164, 9.53], [-0.166, 9.55], [-0.168, 9.57], [-0.17, 9.59],
        [-0.172, 9.61], [-0.174, 9.63], [-0.176, 9.65], [-0.178, 9.66], [-0.18, 9.68],
        [-0.182, 9.7], [-0.184, 9.72], [-0.186, 9.74], [-0.188, 9.76], [-0.19, 9.78],
        [-0.192, 9.8], [-0.194, 9.82], [-0.196, 9.84], [-0.198, 9.86], [-0.2, 9.88],
        [-0.22, 10.08], [-0.24, 10.29], [-0.26, 10.5], [-0.28, 10.73], [-0.3, 10.97],
        [-0.32, 11.23], [-0.34, 11.5], [-0.36, 11.78], [-0.38, 12.08], [-0.4, 12.38],
        [-0.42, 12.71], [-0.44, 13.06], [-0.46, 13.43], [-0.48, 13.83], [-0.5, 14.24],
    ];
}

function num(array $data, string $key, float $default = 0): float
{
    $value = $data[$key] ?? $default;
    if ($value === '' || $value === null) {
        return $default;
    }
    return (float)$value;
}

function parse_free_pattern(string $pattern, int $years): array
{
    $parts = array_map('trim', explode(',', $pattern));
    $free = [];
    for ($i = 0; $i < $years; $i++) {
        $free[$i] = isset($parts[$i]) && is_numeric($parts[$i]) ? max(0, (float)$parts[$i]) : 0.0;
    }
    return $free;
}

function rent_factor(float $area, int $years, float $escalationRate, array $freeMonths): float
{
    $factor = 0.0;
    for ($year = 1; $year <= $years; $year++) {
        $paidMonths = max(0, 12 - ($freeMonths[$year - 1] ?? 0));
        $factor += $area * pow(1 + $escalationRate, $year - 1) * 365 / 12 * $paidMonths;
    }
    return $factor;
}

function rent_total(float $area, float $dailyPrice, int $years, float $escalationRate, array $freeMonths): float
{
    return $dailyPrice * rent_factor($area, $years, $escalationRate, $freeMonths);
}

function interpolate_y_from_rate(float $rate): float
{
    $table = j_recovery_table();
    if ($rate >= $table[0][0]) {
        return $table[0][1];
    }
    $last = end($table);
    if ($rate <= $last[0]) {
        return $last[1];
    }
    for ($i = 0, $count = count($table) - 1; $i < $count; $i++) {
        [$r1, $y1] = $table[$i];
        [$r2, $y2] = $table[$i + 1];
        if ($rate <= $r1 && $rate >= $r2) {
            $span = $r2 - $r1;
            if (abs($span) < 0.0000001) {
                return $y1;
            }
            return $y1 + (($rate - $r1) / $span) * ($y2 - $y1);
        }
    }
    return $last[1];
}

function interpolate_rate_from_y(float $targetYears): float
{
    $table = j_recovery_table();
    if ($targetYears <= $table[0][1]) {
        return $table[0][0];
    }
    $last = end($table);
    if ($targetYears >= $last[1]) {
        return $last[0];
    }
    for ($i = 0, $count = count($table) - 1; $i < $count; $i++) {
        [$r1, $y1] = $table[$i];
        [$r2, $y2] = $table[$i + 1];
        if ($targetYears >= $y1 && $targetYears <= $y2) {
            $span = $y2 - $y1;
            if (abs($span) < 0.0000001) {
                return $r1;
            }
            return $r1 + (($targetYears - $y1) / $span) * ($r2 - $r1);
        }
    }
    return $last[0];
}

function calculate_pricing(array $input): array
{
    $years = max(1, (int)num($input, 'leaseYears', 3));
    $approvedArea = num($input, 'approvedArea');
    $approvedPrice = num($input, 'approvedPrice');
    $contractArea = num($input, 'contractArea', $approvedArea);
    $contractPrice = num($input, 'contractPrice');
    $approvedPropertyFee = num($input, 'approvedPropertyFee', 12);
    $contractPropertyFee = num($input, 'contractPropertyFee', $approvedPropertyFee);
    $approvedEscalation = num($input, 'approvedEscalation', 5) / 100;
    $contractEscalation = num($input, 'contractEscalation', 0) / 100;
    $fitoutCost = num($input, 'fitoutCost');
    $partitionCost = num($input, 'partitionCost');
    $interestRate = num($input, 'interestRate', 5) / 100;
    $specialItems = num($input, 'specialItems');
    $targetJYears = num($input, 'targetJYears', 10);

    $costArea = max(1, num($input, 'costArea', $contractArea ?: $approvedArea));
    $costPremium = ($fitoutCost * (1 + $interestRate * $years) + $partitionCost) / $costArea / 365 / $years;
    $effectiveApprovedPrice = $approvedPrice + $costPremium;

    $approvedFree = parse_free_pattern((string)($input['approvedFreePattern'] ?? '1,1,1'), $years);
    $contractFree = parse_free_pattern((string)($input['contractFreePattern'] ?? '2,1,0'), $years);

    $approvedRent = rent_total($approvedArea, $effectiveApprovedPrice, $years, $approvedEscalation, $approvedFree);
    $contractRent = rent_total($contractArea, $contractPrice, $years, $contractEscalation, $contractFree);
    $approvedProperty = $approvedArea * $approvedPropertyFee * 12 * $years;
    $contractProperty = $contractArea * $contractPropertyFee * 12 * $years;
    $rentDiff = $contractRent - $approvedRent;
    $propertyDiff = $contractProperty - $approvedProperty;
    $breakRate = $approvedRent == 0 ? 0 : ($rentDiff + $propertyDiff + $specialItems) / $approvedRent;
    $jYears = interpolate_y_from_rate($breakRate);

    $targetBreakRate = interpolate_rate_from_y($targetJYears);
    $targetContractRent = $approvedRent * (1 + $targetBreakRate) - $propertyDiff - $specialItems;
    $contractFactor = rent_factor($contractArea, $years, $contractEscalation, $contractFree);
    $targetContractPrice = $contractFactor == 0 ? 0 : $targetContractRent / $contractFactor;

    return [
        'approvedArea' => $approvedArea,
        'approvedPrice' => $approvedPrice,
        'costPremium' => $costPremium,
        'effectiveApprovedPrice' => $effectiveApprovedPrice,
        'contractArea' => $contractArea,
        'contractPrice' => $contractPrice,
        'approvedRent' => $approvedRent,
        'contractRent' => $contractRent,
        'approvedProperty' => $approvedProperty,
        'contractProperty' => $contractProperty,
        'rentDiff' => $rentDiff,
        'propertyDiff' => $propertyDiff,
        'specialItems' => $specialItems,
        'breakRate' => $breakRate,
        'jYears' => $jYears,
        'targetJYears' => $targetJYears,
        'targetBreakRate' => $targetBreakRate,
        'targetContractPrice' => $targetContractPrice,
    ];
}

