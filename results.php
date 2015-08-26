<?php

    require("vendor/autoload.php");
    require("tincan.php");

    require("questions.php");
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

    $corrects = corrections($questions);
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

    $corrects = corrections($questions);
    $total = sizeof($questions);
    if ($corrects > 0 && $total > 0) {
        $scaled = $corrects / $total;
    } else {
        $scaled = 0;
    }

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

    $response = $lrs->saveStatement($statement);
    if ($response->success) {
        print "Statement sent successfully!";
    } else {
        print "Error statement not sent: " . $response->content;
    }

/** Not using Tin Can PHP API
$actor = create_actor($user);
$verb = create_verb($questions);
$object = create_object($activity, $questions);
$result = create_result($questions);

$statement = array(
    'timestamp' => date(DATE_ATOM, time()),
    'version' => '1.0.0',
    'actor' => $actor,
    'verb' => $verb,
    'object' => $object,
    'result' => $result
);
$json_statement = json_encode($statement);

echo $json_statement;

function create_actor($user) {
    $account = array(
        'name' => $user['username'],
        'homePage' => $user['url']
    );

    $actor = array (
        'account' => $account,
        'name' => $user['username'],
        'objectType' => 'Agent'
    );

    return $actor;
}

function create_verb($questions) {
    $corrects = corrections($questions);
    if (sizeof($questions) > 0 && $corrects > (sizeof($questions) / 2) ) {
        $verb = array(
            'id' => 'http://adlnet.gov/expapi/verbs/passed',
            'display' => array ('en-GB' => 'passed')
        );
    } else {
        $verb = array(
            'id' => 'http://adlnet.gov/expapi/verbs/failed',
            'display' => array ('en-GB' => 'failed')
        );
    }
    return $verb;
}

function create_object($activity, $questions) {
    $definition = array(
        'name' => array('en-GB' => $activity['name']),
        'description' => array('en-GB' => $activity['objectives'])
    );

    $object = array (
        'id' => $activity['url'],
        'definition' => $definition,
        'objectType' => 'Activity'
    );

    return $object;
}

function create_result($questions) {
    $corrects = corrections($questions);
    $total = sizeof($questions);
    if ($corrects > 0 && $total > 0) {
        $scaled = $corrects / $total;
    } else {
        $scaled = 0;
    }

    $score = array(
        'min' => 0,
        'max' => $total,
        'scaled' => $scaled,
        'raw' => $corrects
    );

    $result = array(
        'score' => $score,
        'success' => ($total > 0 && $corrects > ($total / 2)),
        'completion' => true,
        'response' => get_responses($questions)
    );

    return $result;
}

 **/

// Util functions

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
        case 'choice':
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

