<?php

function calculateWaste(float $side, array $materials): float {
    sort($materials);
    foreach ($materials as $material) {
        if ($side <= $material) {
            $waste = $material - $side;
            return round((abs($waste) < 1e-9) ? 0.0 : $waste, 2);
        }
    }
    return round($side, 2);
}

function wasteCost(float $waste, float $side, float $wastePrice): float {
    return $waste * $side * $wastePrice;
}

function totalCost(float $totalSides, float $price, float $cost): float {
    return $totalSides * $price + $cost;
}

function determineWaste(float $verticalSide, float $horizontalSide, float $price, float $courtesy, array $materials, float $wastePrice, bool $isChoice, bool $isHorizontalCut = null): array {
    $totalSides = $verticalSide * $horizontalSide;
    $verticalSideWithoutCourtesy = $verticalSide;
    $horizontalSideWithoutCourtesy = $horizontalSide;

    $verticalSide += $courtesy;
    $horizontalSide += $courtesy;

    $maxMaterial = max($materials);
    $verticalSideDivision = intval($verticalSide / $maxMaterial);
    $horizontalSideDivision = intval($horizontalSide / $maxMaterial);

    $remainingVerticalSide = $verticalSide - ($maxMaterial * $verticalSideDivision);
    $remainingHorizontalSide = $horizontalSide - ($maxMaterial * $horizontalSideDivision);

    $verticalWaste = calculateWaste($remainingVerticalSide, $materials);
    $horizontalWaste = calculateWaste($remainingHorizontalSide, $materials);

    $verticalCost = wasteCost($verticalWaste, $horizontalSideWithoutCourtesy, $wastePrice);
    $horizontalCost = wasteCost($horizontalWaste, $verticalSideWithoutCourtesy, $wastePrice);

    $totalVerticalCost = totalCost($totalSides, $price, $verticalCost);
    $totalHorizontalCost = totalCost($totalSides, $price, $horizontalCost);

    if ($isChoice) {
        if ($isHorizontalCut) {
            return [
                "verticalSide" => $verticalSide,
                "horizontalSide" => $horizontalSide,
                "waste" => $horizontalWaste,
                "wasteCost" => $horizontalCost,
                "totalCost" => $totalHorizontalCost
            ];
        } else {
            return [
                "verticalSide" => $verticalSide,
                "horizontalSide" => $horizontalSide,
                "waste" => $verticalWaste,
                "wasteCost" => $verticalCost,
                "totalCost" => $totalVerticalCost
            ];
        }
    } else {
        if ($totalVerticalCost <= $totalHorizontalCost) {
            return [
                "verticalSide" => $verticalSide,
                "horizontalSide" => $horizontalSide,
                "waste" => $verticalWaste,
                "wasteCost" => $verticalCost,
                "totalCost" => $totalVerticalCost
            ];
        } else {
            return [
                "verticalSide" => $verticalSide,
                "horizontalSide" => $horizontalSide,
                "waste" => $horizontalWaste,
                "wasteCost" => $horizontalCost,
                "totalCost" => $totalHorizontalCost
            ];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $verticalSide = floatval($data['verticalSide']);
    $horizontalSide = floatval($data['horizontalSide']);
    $price = floatval($data['price']);
    $courtesy = floatval($data['courtesy']);
    $materials = array_map('floatval', explode(',', $data['materials']));
    $wastePrice = floatval($data['wastePrice']);
    $isChoice = isset($data['isChoice']) ? filter_var($data['isChoice'], FILTER_VALIDATE_BOOLEAN) : false;
    $isHorizontalCut = isset($data['isHorizontalCut']) ? filter_var($data['isHorizontalCut'], FILTER_VALIDATE_BOOLEAN) : false;

    $result = determineWaste($verticalSide, $horizontalSide, $price, $courtesy, $materials, $wastePrice, $isChoice, $isHorizontalCut);
    echo json_encode($result);
}
