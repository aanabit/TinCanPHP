<?php

    require("vendor/autoload.php");
    // require("tincan.php");

    require("config.php");
    require("dbo_lib.php");
    require("question.class.php");

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
        if ($question->isCorrect($answer)) {
            $corrects++;
        }
    }
    if ($corrects > 0 && $total > 0) {
        $scaled = $corrects / $total;
    } else {
        $scaled = 0;
    }


echo 'Corrects: '.$corrects;
echo '<br />Total: '.$total;

/** Using Tin Can PHP API **/

    $lrs = new TinCan\RemoteLRS(
        $tincan_data['endpoint'],
        '1.0.1',
        $user['username'],
        $user['pass']
    );

    // Actor Element

    $actor = new TinCan\Agent();
    $actor
        ->setName($user['name'])
        ->setMbox($user['username'])
        ->setAccount([]);
    $actor
        ->getAccount()
        ->setName($user['username'])
        ->setHomePage($user['url']);

    // Verb element

    $verb = new TinCan\Verb();
    $verb->setDisplay([]);

    if (sizeof($questions) > 0 && $corrects > (sizeof($questions) / 2)) {
        $verb
            ->setId('http://adlnet.gov/expapi/verbs/passed')
            ->getDisplay()
            ->set('en-GB', 'passed');
    } else {
        $verb
            ->setId('http://adlnet.gov/expapi/verbs/failed')
            ->getDisplay()
            ->set('en-GB', 'failed');
    }

    // Object Element

    $object = new TinCan\Activity();
    $object
        ->setId($activity['url'])
        ->setDefinition([]);
    $object->getDefinition()
        ->getName()
        ->set('en-GB', $activity['name']);
    $object->getDefinition()
        ->getDescription()
        ->set('en-GB', $activity['objectives']);

    $result = new TinCan\Result();
    $result
        ->setCompletion(true)
        ->setResponse(get_responses($questions))
        ->setSuccess(($total > 0 && $corrects > ($total / 2)))
        ->setScore([]);
    $result->getScore()
        ->setMin(0)
        ->setMax($total)
        ->setRaw($corrects)
        ->setScaled($scaled);

    $statement = new TinCan\Statement(
        [
            'actor' => $actor,
            'verb' => $verb,
            'object' => $object,
            'result' => $result
        ]
    );

    // Send the statement

/**
    $response = $lrs->saveStatement($statement);
    if ($response->success) {
        print "Statement sent successfully!";
    } else {
        print "Error statement not sent: " . $response->content;
    }
**/

// Util functions
/**
function corrections($questions) {
    $corrects = 0;
    foreach ($questions as $question) {
        if (is_correct($question)) {
            $corrects++;
        }
    }
    return $corrects;
}

function is_correct($question) {
    $answer = $_POST['q'.$question['id']];

    switch ($question['type']) {
        case 'cloze':
//            var_dump($question);
//            echo '<br />';
//            var_dump($answer);
//            echo '<br />';
            // TODO: More than one correct answer
            foreach ($question['values'] as $value) {
                if ($question['correct']) {
                    if (!in_array($value['value'], $answer)) {
                        echo 'inCorrect';
                        return false;
                    }
                } else {
                    if (in_array($value['value'], $answer)) {
                        echo 'inCorrect';
                        return false;
                    }
                }
            }
            echo 'Correct';
            return true;
            break;
        case 'choice':
            foreach ($question['values'] as $value) {
                if ($answer == $value['value']) {
                    return $value['correct'];
                }
            }
            break;
        case 'select':
            foreach ($question['values'] as $value) {
                if ($answer == $value['value']) {
                    return $value['correct'];
                }
            }
            break;
        case 'shortanswer':
            return in_array($answer, $question['values']);
            break;
    }
}
 **/

function get_responses($questions) {
    $responses = '';
    foreach ($questions as $question) {
        $responses .= $_POST['q'.$question['id']].', ';
    }
    if (strlen($responses) > 0) {
        return substr($responses, 0, -2);
    } else {
        return $responses;
    }
}

