<?php

$activity = array (
    'url' => 'http://localhost:8888/tincan_php/quiz.php',
    'name' => 'TIN CAN Test activity',
    'objectives' => 'Testing Tin Can',
    'type' => 'choices'
);

/**
 * $questions = array(
    array(
        'id' => 1,
        'type' => 'cloze',
        'before' => 'He __ a student',
        'after' => '',
        'values' => array(
            array('value' => 'am', 'correct' => false),
            array('value' => 'are', 'correct' => false),
            array('value' => 'is', 'correct' => true)
        )
    ),
    array(
        'id' => 2,
        'type' => 'choice',
        'before' => 'Mike',
        'after' => ' a nurse',
        'values' => array(
            array('value' => "'s", 'correct' => true),
            array('value' => "'re", 'correct' => false),
            array('value' => "'m", 'correct' => false)
        )
    ),
    array(
        'id' => 3,
        'type' => 'shortanswer',
        'before' => 'I ',
        'after' => ' a happy person',
        'values' => array('am')
    )
);
**/


$questions = array();

    $args = $_POST;
    var_dump($args);

    $q_type = $_POST['question_type'];
    $q_text = $_POST['question'];
    switch ($q_type) {
        case 'yesno':
            $q_yesno = $_POST['yesno_radio'];
            if ($q_yesno == "Yes") {
                $values = array(
                    array('value' => 'Yes', 'correct' => true),
                    array('value' => 'No', 'correct' => false)
                );
            } else {
                $values = array(
                    array('value' => 'Yes', 'correct' => false),
                    array('value' => 'No', 'correct' => true)
                );
            }

            $question = array(
                    'id' => 1,
                    'type' => 'choice',
                    'before' => $q_text,
                    'after' => '',
                    'values' => $values
                )
            );

            break;
        case 'cloze':
            break;
        case 'shortanswer':
            break;
    }

    $questions[] = $question;

//    $db = db_connect();
//    db_insertQuestion($question, $db);
