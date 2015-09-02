<?php

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

