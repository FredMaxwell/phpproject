<?php
function mergeIntervals($intervals) {
    if (empty($intervals)) {
        return [];
    }

    usort($intervals, function($a, $b) {
        return $a[0] - $b[0];
    });

    $merged = [];
    $current = $intervals[0];

    foreach ($intervals as $interval) {
        if ($interval[0] <= $current[1]) {
            $current[1] = max($current[1], $interval[1]);
        } else {
            $merged[] = $current;
            $current = $interval;
        }
    }

    $merged[] = $current;

    return $merged;
}

$intervals = [[1, 4], [3, 6], [8, 10]];
$result = mergeIntervals($intervals);

print_r($result);