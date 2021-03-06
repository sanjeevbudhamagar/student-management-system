<?php
/**
 * Created by PhpStorm.
 * User: Pratik
 * Date: 9/15/2016
 * Time: 7:42 PM
 */

session_start();
$role = $_SESSION['role'];
if($role == "Receptionist") {

?>
    <!DOCTYPE html>
    <html>
    <head lang="en">
        <meta charset="UTF-8">
        <title>Student Management</title>
        <script src="../js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="../css/bootstrap.min.css"/>
        <link rel="stylesheet" href="../css/style.css"/>
    </head>
    <body>
    <?php
    include 'layout/header.php';


    $class_id = $_GET["id"];
    $month = date("F");
    $year = date("o");

    function getAttendanceDay($class_id, $month, $year, $connection)
    {

        $select = "select DISTINCT day from attendance where class_id = '$class_id' && month = '$month' && year = '$year'";
        $attendance = $connection->query($select);
        return $attendance;

    }


    function getAttendance($student_id, $day, $month, $year, $connection)
    {
        $select = "select status from attendance where `student_id` = '$student_id' && `day` = '$day' && MONTH = '$month' && YEAR = '$year'";
//    echo $select."</br>";
        $attendance = $connection->query($select);
        while ($row = $attendance->fetch_assoc()) {
            $a = $row["status"];
            return $a;
        }
    }

    ?>


    <div class="container">
        <div class="col-md-2 vm">
            <ul>
                <?php
                $class = getClass($connection);
                while ($row = $class->fetch_assoc()) {
                    ?>
                    <li style="position: relative">
                        <a href="teacher.php?id=<?php echo $row["id"]; ?>">
                            <?php
                            $status = checkAttendance($row["id"], $connection);
                            if($status == "done"){
                                ?>
                                <div style="left: 0; top: 0; height: 35px; width: 10px; position: absolute; color: #ffffff; background-color: green;"></div>
                            <?php
                            }else{
                                ?>
                                <div style="left: 0; top: 0; height: 35px;  width: 10px; position: absolute; color: #ffffff; background-color: red;"></div>
                            <?php
                            }
                            ?>
                            <?php echo $row["class"]; ?></a>
                    </li>
                <?php
                }
                ?>
            </ul>
        </div>
        <div class="col-md-10" style="overflow-x: scroll;">
            <legend>Attendance of <?php echo getClassName($class_id, $connection); ?><span
                    class="pull-right"><?php echo $month . " " . $year; ?></span></legend>

            <form action="../controller/attendanceController.php?id=<?php echo $class_id; ?>" method="post">
                <table class="table table table-responsive table-bordered table-striped table-fixed" id="attendance-table">
                    <thead>
                    <th>Roll no.</th>
                    <th></th>
                    <?php
                    $status = checkAttendance($class_id, $connection);
                    if($status != 'done'){
                        ?>
                        <th><?php echo date("j"); ?></th>
                    <?php
                    }
                    ?>

                    <?php
                    $day = getAttendanceDay($class_id, $month, $year, $connection);
                    $day_array = array();
                    $i = 0;
                    while ($row = $day->fetch_assoc()) {
                        $day_array[$i] = $row["day"];
                        $i++;
                    }
                    //                echo count($day_array);
                    for ($i = count($day_array) - 1; $i >= 0; $i--) {
                        ?>
                        <th><?php echo $day_array[$i]; ?></th>
                    <?php
                    }
                    ?>
                    </thead>
                    <tbody style="background-color: blanchedalmond;">
                    <?php
                    $classStudent = getClassStudents(getClassName($class_id, $connection), $connection);
                    while ($row = $classStudent->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?php
                        $studentID = $row['id'];
                        echo $row["roll_number"]; ?></td>
                            <th>
                                <a href="studentAttendance.php?id=<?php echo $studentID; ?>">
                                <?php
                        echo $row["first_name"] . " " . $row["last_name"]; ?></th>
                            </a>
                            <?php
                            if($status != 'done'){
                            ?>
                            <td>
                                <select name="<?php echo $row["id"]; ?>">
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="leave">Leave</option>
                                </select>
                            </td>
                            <?php
                    }
                            ?>
                            <?php
                            for ($i = count($day_array) - 1; $i >= 0; $i--) {
                                $attendance = getAttendance($studentID, $day_array[$i], $month, $year, $connection);
                                ?>
                                <td><?php echo $attendance; ?></td>
                            <?php
                            }
                            ?>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>


                </table>
                <input class="btn btn-primary" value="Done" type="submit"/>
            </form>
        </div>
    </div>

    <script>
        $('#attendance-table').dataTable();
    </script>
    </body>
    </html>

<?php
}else{
    session_unset();
    header("Location: logout.php");
}
?>