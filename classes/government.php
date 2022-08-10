<?php
include_once __DIR__ . "/person.php";
include_once __DIR__ . "/position.php";
class Government {
    static function getAllDepartments($link) {
        return mysqli_fetch_all(mysqli_query($link, "SELECT * FROM departments"), MYSQLI_ASSOC);
    }

    static function getAllPositions($link) {
        return mysqli_fetch_all(mysqli_query($link, "SELECT * FROM positions"), MYSQLI_ASSOC);
    }

    static function printGovernment($link) {
        $positions = Government::getAllPositions($link);
        echo "
        <nav>
            <div class=\"nav nav-tabs\" id=\"nav-tabs\" role=\"tablist\">";
        $departments = Government::getAllDepartments($link);
        $i = 1;
        foreach($departments as $d) {
            if($i == 1) {
                echo "<button class=\"nav-link active\" id=\"nav-{$i}-tab\" data-bs-toggle=\"tab\" data-bs-target=\"#nav-{$i}\" type=\"button\" role=\"tab\">{$d['name']}</button>";
            } else {
                echo "<button class=\"nav-link\" id=\"nav-{$i}-tab\" data-bs-toggle=\"tab\" data-bs-target=\"#nav-{$i}\" type=\"button\" role=\"tab\">{$d['name']}</button>";
            }
            $i++;
        }
        echo"
            </div>
        </nav>";

        echo "<div class=\"tab-content\" id=\"nav-tabContent\">";
        $i = 1;
        foreach($departments as $d) {
            if($i == 1) {
                echo "<div class=\"tab-pane fade show active\" id=\"nav-{$i}\" role=\"tabpanel\" tabindex=\"0\">";
            } else {
                echo "<div class=\"tab-pane fade\" id=\"nav-{$i}\" role=\"tabpanel\" tabindex=\"0\">";
            }
            echo "
            <table class=\"table table-dark\">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Incumbent</th>";
            if($d['ID'] > 1) {
                echo "<th>Appointer</th>";
            }
            echo "
                    <th>Annual Income</th>
                </tr>
                </thead>
                <tbody>";
            foreach($positions as $p) {
                if(in_array($d['ID'], json_decode($p['posgroup']))) {
                    echo "<tr>";
                    echo "<td>{$p['name']}</td>";
                    if($p['holderID'] != null) {
                        $hname = Person::getDisplayName($link, $p['holderID']);
                        echo "<td>{$hname}</td>";
                    } else {
                        echo "<td><i>Vacant</i></td>";
                    }
                    if($p['ID'] > 0 && $p['ID'] != 66 && $p['appointer'] > -1) {
                        $appointer = Position::getPositionName($link, $p['appointer']);
                        echo "<td>{$appointer}</td>";
                    } else if($p['appointer'] == -1) {
                        echo "<td>The House of Commons</td>";
                    }
                    $pay = $p['pay'];
                    echo "<td>{$pay}</td>";
                    echo "</tr>";
                }
            }
                echo "
                </tbody>
            </table>
            ";
            echo "</div>";
            $i++;
        }
        echo "</div>";
    }
}
?>