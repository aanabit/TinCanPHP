<?php

    require("config.php");
    require("dbo_lib.php");
    require("question.class.php");
    require("myTinCanAPI.class.php");

    $questions = array();

// We do not need the totals to show just one question
//    $corrects = 0;
//    $total = 0;

    $page = 0;
    if (!empty($_GET['page'])) {
        $page = $_GET['page'];
    }
    $dbo = new DBO ($servername, $username, $password, $dbname);

    $sql = "SELECT * FROM questions";
    $dbo->query ($sql) or die ($dbo->ShowError ());
    $num_questions = $dbo->emaitza_kopurua();

    $sql = "SELECT * FROM questions LIMIT ".$page.", 1";
    $dbo->query ($sql) or die ($dbo->ShowError ());
    while ($questionDB = $dbo->emaitza()) {
        $question = new question($questionDB);
        $total++;

        $qid = 'q'.$question->getId();
        $answer = $_POST[$qid];

        $passed = $question->isCorrect($answer);
//        if ($passed) {
//            $corrects++;
//        }

        $myTC = new MyTinCanAPI();
        $myTC->createObject($question->getBefore());
        $myTC->createVerb($passed);
        if (is_array($answer)) {
            $answer = implode(',',$answer);
        }
        $myTC->createResult($passed, $answer);
        $myTC->sendStatement();
    }
//    if ($corrects > 0 && $total > 0) {
//        $scaled = $corrects / $total;
//    } else {
//        $scaled = 0;
//    }

    $page++;

// Different for the last question
    if ($page == $num_questions) {
        $lastTC = new MyTinCanAPI();
        $lastTC->createObject($activity['name']);
        $lastTC->createLastVerb();
        $lastTC->sendStatement();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
    <meta http-equiv="pragma" content="no-cache" />

    <title>TIN CAN Test</title>
    <link rel="stylesheet" href="./src/css/bootstrap.min.css">
    <link rel="stylesheet" href="./src/css/app.css">
    <link rel="stylesheet" href="./src/css/tincan.css">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12 text-center">
            <h1>Tin Can Test</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form name="tincan_test" action="results.php?page=<?php echo $page; ?>" method="post">
                <div class="well">
<?php               if ($page == $num_questions) {
                        echo '<h3>Test finished</h3>';
                    } else {
                        try {
                            $sql = "SELECT * FROM questions ORDER BY id LIMIT " . $page . ", 1";
                            $dbo->query($sql) or die ($dbo->ShowError());
                            while ($questionDB = $dbo->emaitza()) {
                                $question = new question($questionDB);
                                echo $question->render();
                            }
                        } catch (PDOException $e) {
                            echo $e->getMessage();
                        }   ?>
                        <input type="hidden" value="<?php echo $questions;?>" />
                        <div class="submit text-right"><input type="submit" value="Next" class="btn btn-primary" /></div>
<?php               }
                    $conn = null;
?>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="./src/js/jquery-1.11.3.min.js"></script>
</body>
</html>