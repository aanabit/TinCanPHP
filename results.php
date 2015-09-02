<?php

    require("config.php");
    require("dbo_lib.php");
    require("question.class.php");
    require("myTinCanAPI.class.php");

    $questions = array();
    $corrects = 0;
    $total = 0;

    $dbo = new DBO ($servername, $username, $password, $dbname);
    $sql = "SELECT * FROM questions";

    $dbo->query ($sql) or die ($dbo->ShowError ());
    while ($questionDB = $dbo->emaitza()) {
        $question = new question($questionDB);
        $total++;

        $qid = 'q'.$question->getId();
        $answer = $_POST[$qid];

        $passed = $question->isCorrect($answer);
        if ($passed) {
            $corrects++;
        }

        $myTC = new MyTinCanAPI();
        $myTC->createVerb($passed);
        if (is_array($answer)) {
            $answer = implode(',',$answer);
        }
        $myTC->createResult($passed, $answer);
        $myTC->sendStatement();
    }
    if ($corrects > 0 && $total > 0) {
        $scaled = $corrects / $total;
    } else {
        $scaled = 0;
    }

echo 'Corrects: '.$corrects;
echo '<br />Total: '.$total;
