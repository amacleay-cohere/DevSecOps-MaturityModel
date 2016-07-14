<?php
include_once "head.php";
?>
    <body>
<?php
include_once "navi.php";
?>

<?php
include_once "data.php";
function printDimensionUsefull($dimensions)
{

    $all = array();
    foreach ($dimensions as $dimension => $subdimensions) {
        foreach ($subdimensions as $subdimension => $element) {

            for ($i = 1; $i < 5; $i++) {
                if (array_key_exists($all, $i)) {
                    $all[$i] = array();
                }
                if (array_key_exists($all[$i], $subdimension)) {
                    $all[$subdimension][$i] = array();
                    $all[$subdimension][$i]['usefulness'] = array();
                    $all[$subdimension][$i]['hardnessOfImplementation'] = array();
                }
                foreach ($element as $elementName => $elementImplementation) {
                    if ($elementImplementation['level'] != $i) continue;
                    $all[$subdimension][$i]['usefulness'][] = $elementImplementation['usefulness'];

                    $knowledge = getKnowledge($elementImplementation);

                    $all[$subdimension][$i]['hardnessOfImplementation'][] = $knowledge;
                    $all[$subdimension][$i]['hardnessOfImplementation'][] = $elementImplementation['hardnessOfImplementation']["time"];
                    $all[$subdimension][$i]['hardnessOfImplementation'][] = $elementImplementation['hardnessOfImplementation']["time"];
                    $all[$subdimension][$i]['hardnessOfImplementation'][] = $elementImplementation['hardnessOfImplementation']["resources"];
                }

            }
        }
    }
    ?>

    <table class="table table-nonfluid">
        <tr>
            <th></th>
    <?php
    foreach (reset($all) as $level => $levelElements) {
        echo "<th colspan='2'>Ebene $level</th>";
    }
    echo "</tr><tr><th>Dimension</th>";


    foreach (reset($all) as $level => $levelElements) {
        echo "<th>Nutzen</th>";
        echo "<th>Schwere</th>";
    }
    echo "</tr>";
    foreach ($all as $level => $levelElements) {
        echo "<tr>";
        echo "<td>$level</td>";
        foreach ($levelElements as $subdimension => $elements) {
            $std = stats_standard_deviation($elements["usefulness"]);

            $avg = 0;
            foreach ($elements['usefulness'] as $element) {
                $avg += $element;
            }
            $avg = $avg / count($elements['usefulness']);

            $stdImp = stats_standard_deviation($elements['hardnessOfImplementation']);

            $avgImp = 0;
            foreach ($elements['hardnessOfImplementation'] as $element) {
                $avgImp += $element;
            }

            $avgImp = $avgImp / count($elements['hardnessOfImplementation']);

            $colors = array("#81F781", "#BEF781", "#F3F781", "#F7BE81", "#FA5858");
            echo "<td style='background-color: ".getColor($avg, $colors)."'>" . number_format((float)$avg, 2, '.', '') . "<br />&sigma;=" . number_format((float)$std, 2, '.', '') . "</td><td style='background-color: ".getColorForHardnessOfImplementation($avgImp, $colors)."'>" . number_format((float)$avgImp, 2, '.', '') . "<br />&sigma;=" . number_format((float)$stdImp, 2, '.', '') . "</td>";
        }
        echo "</tr>";
    }
}

function getColor($val, $colors) {

    return getColorForHardnessOfImplementation($val,  array_reverse($colors));
}
function getColorForHardnessOfImplementation($val, $colors)
{
    if($val < 1.49) {
        return $colors[0];
    }
    if($val < 2.49) {
        return $colors[1];
    }
    if($val < 3.49) {
        return $colors[2];
    }
    if($val < 4.49) {
        return $colors[3];
    }
    return $colors[4];
}

if (!function_exists('stats_standard_deviation')) {
    /**
     * This user-land implementation follows the implementation quite strictly;
     * it does not attempt to improve the code or algorithm in any way. It will
     * raise a warning if you have fewer than 2 values in your array, just like
     * the extension does (although as an E_USER_WARNING, not E_WARNING).
     *
     * @param array $a
     * @param bool $sample [optional] Defaults to false
     * @return float|bool The standard deviation or false on error.
     */
    function stats_standard_deviation(array $a, $sample = false)
    {
        $n = count($a);
        if ($n === 0) {
            trigger_error("The array has zero elements", E_USER_WARNING);
            return false;
        }
        if ($sample && $n === 1) {
            trigger_error("The array has only 1 element", E_USER_WARNING);
            return false;
        }
        $mean = array_sum($a) / $n;
        $carry = 0.0;
        foreach ($a as $val) {
            $d = ((double)$val) - $mean;
            $carry += $d * $d;
        };
        if ($sample) {
            --$n;
        }
        return sqrt($carry / $n);
    }
}

printDimensionUsefull($dimensions);