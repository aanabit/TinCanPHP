<?php
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
