<?php


    $questions_tmp = array();

    $args = $_POST;

    foreach ($args as $key => $value) {
        if (strpos($key, 'qtype_') !== false) {
            $index = intval(substr($key, 6));
            $questions_tmp[$index]['type'] = $value;
        } elseif (strpos($key, 'a_') > 2) {
            $index = intval(substr($key, 2, strpos($key, 'a_') - 2));
            $questions_tmp[$index]['values'][] = $value;
        } elseif (strpos($key, 'ck_') > 2) {
            $index = intval(substr($key, 2, strpos($key, 'a_') - 2));
            $questions_tmp[$index]['corrects'][] = $value;
        } elseif (strpos($key, 'q_') !== false) {
            $index = intval(substr($key, 2));
            $questions_tmp[$index]['before'] = $value;

        }
    }

    $questions = array();
    foreach ($questions_tmp as $key => $value) {
        $questions[] = new question($key, $value);
    }

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        foreach($questions as $question) {
            $question->insertQuestion($conn);
        }
    }
    catch(PDOException $e) {
        echo $e->getMessage();
    }
    $conn = null;

function get_question($id, $question_tmp) {
    $question = array();
    $question['id'] = $id;
    $question['before'] = $question_tmp['before'];
    $question['after'] = '';
    switch ($question_tmp['type']) {
        case 'yesno':
            $question['type'] = 'cloze';
            if (in_array('Yes', $question_tmp['values'])) {
                $question['values'] = array(
                    array('value' => 'Yes', 'correct' => true),
                    array('value' => 'No', 'correct' => false)
                );
            } else {
                $question['values'] = array(
                    array('value' => 'Yes', 'correct' => false),
                    array('value' => 'No', 'correct' => true)
                );

            }
            break;
        case 'cloze':
            $question['type'] = 'cloze';
            $len = sizeof($question_tmp['values']);
            $values = array();
            for ($i = 1; $i <= $len; $i++) {
                if (in_array($i, $question_tmp['corrects'])) {
                    $values[] = array('value' => $question_tmp['values'][$i-1], 'correct' => true);
                } else {
                    $values[] = array('value' => $question_tmp['values'][$i-1], 'correct' => false);
                }
            }
            $question['values'] = $values;
            break;
        case 'choice':
            $question['type'] = 'choice';
            $len = sizeof($question_tmp['values']);
            $values = array();
            for ($i = 1; $i <= $len; $i++) {
                if (in_array($i, $question_tmp['corrects'])) {
                    $values[] = array('value' => $question_tmp['values'][$i-1], 'correct' => true);
                } else {
                    $values[] = array('value' => $question_tmp['values'][$i-1], 'correct' => false);
                }
            }
            $question['values'] = $values;
            break;
    }
    return $question;
}



/**
$questions = array(
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


/**

//    $db = db::getConnection();
//    $sql = "SELECT * FROM questions";

//    $result = $db->query($sql);

//var_dump($result);

 ***/

/**
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "quizez";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "INSERT INTO questions (type, before_text, after_text, values_text)
    VALUES ('type', 'Before', 'After', 'Values')";
    $conn->exec($sql);
    echo "New record created successfully";
}
catch(PDOException $e)
{
    echo $sql . "<br>" . $e->getMessage();
}

$conn = null;
 **/

// echo 'aurretik';

// $db = myDB::getInstance();

// echo 'ondoren';

// $db->exec('');

// echo 'inserted';