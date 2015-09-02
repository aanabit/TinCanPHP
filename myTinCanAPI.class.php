<?php

require("vendor/autoload.php");
require("config.php");

class myTinCanAPI {
    private $lrs;
    private $actor;
    private $verb;
    private $object;
    private $result;

    /** Using Tin Can PHP client API **/

    function __construct() {
        global $tincan_data, $user;

        $this->lrs = new TinCan\RemoteLRS(
            $tincan_data['endpoint'],
            '1.0.1',
            $user['username'],
            $user['pass']
        );
        $this->createActor();
        $this->createObject();
    }

    function createActor() {
        global $user;

        $this->actor = new TinCan\Agent();
        $this->actor
            ->setName($user['name'])
            ->setMbox($user['username'])
            ->setAccount([]);
        $this->actor
            ->getAccount()
            ->setName($user['username'])
            ->setHomePage($user['url']);
    }

    function createVerb($passed) {
        $this->verb = new TinCan\Verb();
        $this->verb->setDisplay([]);

        if ($passed) {
            $this->verb
                ->setId('http://adlnet.gov/expapi/verbs/passed')
                ->getDisplay()
                ->set('en-GB', 'passed');
        } else {
            $this->verb
                ->setId('http://adlnet.gov/expapi/verbs/failed')
                ->getDisplay()
                ->set('en-GB', 'failed');
        }
    }

    function createObject() {
        global $activity;

        $this->object = new TinCan\Activity();
        $this->object
            ->setId($activity['url'])
            ->setDefinition([]);
        $this->object->getDefinition()
            ->getName()
            ->set('en-GB', $activity['name']);
        $this->object->getDefinition()
            ->getDescription()
            ->set('en-GB', $activity['objectives']);
    }

    function createResult($passed, $responses) {
        $this->result = new TinCan\Result();
        $this->result
            ->setCompletion(true)
            ->setResponse($responses)
            ->setSuccess($passed)
            ->setScore([]);
    }

    function createScore($total, $corrects, $scaled) {
        $this->result->getScore()
            ->setMin(0)
            ->setMax($total)
            ->setRaw($corrects)
            ->setScaled($scaled);
    }

    function sendStatement() {
        $statement = new TinCan\Statement(
            [
                'actor' => $this->actor,
                'verb' => $this->verb,
                'object' => $this->object,
                'result' => $this->result
            ]
        );

        $response = $this->lrs->saveStatement($statement);
        if ($response->success) {
            print "Statement sent successfully!\n";
        } else {
            print "Error statement not sent: " . $response->content.'\n';
        }
    }
}