<?php

class question {
    private $id;
    private $type;
    private $before;
    private $after;
    private $values = array();

    function __construct() {
        $numArgs = func_num_args();
        switch ($numArgs) {
            case 1:
                $this->newQuestionDB(func_get_args());
                break;
            case 2:
                $this->newQuestion(func_get_args());
                break;
        }
    }

    public function newQuestionDB($args) {
        $arg = $args[0];

        $this->id = $arg['id'];
        $this->type = $arg['type'];
        $this->before = $arg['before_text'];
        $this->after = $arg['after_text'];
        $this->values = json_decode($arg['values_text'], true);
    }

    public function newQuestion($args) {
        $this->id = $args[0];

        $question_tmp = $args[1];
        $this->before = $question_tmp['before'];
        $this->after = '';
        switch ($question_tmp['type']) {
            case 'yesno':
                $this->type = 'yesno';
                if (in_array('Yes', $question_tmp['values'])) {
                    $this->values = array(
                        array('value' => 'Yes', 'correct' => true),
                        array('value' => 'No', 'correct' => false)
                    );
                } else {
                    $this->values = array(
                        array('value' => 'Yes', 'correct' => false),
                        array('value' => 'No', 'correct' => true)
                    );

                }
                break;
            case 'cloze':
                $this->type = 'cloze';
                $len = sizeof($question_tmp['values']);
                $values = array();
                for ($i = 1; $i <= $len; $i++) {
                    if (in_array($i, $question_tmp['corrects'])) {
                        $values[] = array('value' => $question_tmp['values'][$i-1], 'correct' => true);
                    } else {
                        $values[] = array('value' => $question_tmp['values'][$i-1], 'correct' => false);
                    }
                }
                $this->values = $values;
                break;
            case 'choice':
                $this->type = 'choice';
                $len = sizeof($question_tmp['values']);
                $values = array();
                for ($i = 1; $i <= $len; $i++) {
                    if (in_array($i, $question_tmp['corrects'])) {
                        $values[] = array('value' => $question_tmp['values'][$i-1], 'correct' => true);
                    } else {
                        $values[] = array('value' => $question_tmp['values'][$i-1], 'correct' => false);
                    }
                }
                $this->values = $values;
                break;
            case 'short':
                $this->type = 'short';
                $len = sizeof($question_tmp['values']);
                $values = array();
                for ($i = 1; $i <= $len; $i++) {
                    $values[] = $question_tmp['values'][$i-1];
                }
                $this->values = $values;
                break;
            case 'short':
                $this->type = 'short';
                $len = sizeof($question_tmp['values']);
                $values = array();
                for ($i = 1; $i <= $len; $i++) {
                    $values[] = $question_tmp['values'][$i-1];
                }
                $this->values = $values;
                break;
            case 'fillthegap':
                $this->type = 'fillthegap';
                $len = sizeof($question_tmp['values']);
                $values = array();
                for ($i = 1; $i <= $len; $i++) {
                    if (in_array($question_tmp['values'][$i-1], $question_tmp['corrects'])) {
                        $values[] = array('value' => $question_tmp['values'][$i-1], 'correct' => true);
                    } else {
                        $values[] = array('value' => $question_tmp['values'][$i-1], 'correct' => false);
                    }
                }
                $this->values = $values;
                break;
        }
        return $this;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getType() {
        return $this->type;
    }

    public function setBefore($before) {
        $this->before = $before;
    }

    public function getBefore() {
        return $this->before;
    }

    public function setAfter($after) {
        $this->after = $after;
    }

    public function getAfter() {
        return $this->after;
    }

    public function setValues($values) {
        $this->values = $values;
    }

    public function getValues() {
        return $this->values;
    }

    public function insertQuestion($conn) {
        $sql = "INSERT INTO questions (type, before_text, after_text, values_text)
                    VALUES ('".$this->type."', '".$this->before."', '".$this->after."', '".json_encode($this->values)."')";
//        $conn->exec($sql);
        $conn->query($sql);
    }

    public function getResponsePattern() {
        if ($this->getType() == "short") {
            return $this->values;
        }

        $pattern = '';
        foreach ($this->values as $value) {
            if ($value['correct']) {
                $pattern .= $value['value'].'[,]';
            }
        }
        return array(substr($pattern, 0, -3));
    }

    public function render() {
        $html = '<div id="q'.$this->id.'" class="question">';
        $html .= '<h3>'.$this->before.'</h3>';

        switch ($this->type) {
            case 'cloze':
                $html .= '<div class="answers">';
                $i = 1;

                $multiple = $this->isMultiple();

                foreach ($this->values as $value) {
                    $html .= '<div class="answer">';
                    if ($multiple) {
                        $html .= '<input type="checkbox" name="q'.$this->id.'[]" id="q'.$this->id.'_'.$i.'" value="'.$value['value'].'" />';
                    } else {
                        $html .= '<input type="radio" name="q'.$this->id.'" id="q'.$this->id.'_'.$i.'" value="'.$value['value'].'" />';
                    }
                    $html .= '<label for="q'.$this->id.'_'.$i.'">'.$value['value'].'</label>';
                    $html .= '</div>';
                    $i++;
                }
                $html .= '</div>';
                break;
            case 'choice':
            case 'yesno':
                $html .= '<div class="answers">';
                $i = 1;

                foreach ($this->values as $value) {
                    $html .= '<div class="answer">';
                    $html .= '<input type="checkbox" name="q'.$this->id.'[]" id="q'.$this->id.'_'.$i.'" value="'.$value['value'].'" />';
                    $html .= '<label for="q'.$this->id.'_'.$i.'">'.$value['value'].'</label>';
                    $html .= '</div>';
                    $i++;
                }
                $html .= '</div>';
                break;
            case 'select':
                $html .= '<select name="q'.$this->id.'" id="q'.$this->id.'">';
                foreach ($this->values as $value) {
                    $html .= '<option name="q'.$this->id.'" value="'.$value['value'].'" >'.$value['value'].'</option>';
                }
                $html .= '</select>';
                break;
            case 'short':
                $html .= '<input type="text" name="q'.$this->id.'" />';
                break;
            case 'fillthegap':
                $html .= '<div class="answers">';
                foreach ($this->values as $value) {
                    if ($value['correct']) {
                        $html .= '<input type="text" name="q'.$this->id.'" id="q'.$this->id.'" />';
                    } else {
                        $html .= $value['value'];
                    }
                }
                $html .= '</div>';
                break;
        }

        $html .= $this->after;
        $html .= '</div>';

        return $html;
    }

    public function isCorrect($answer) {
        switch ($this->type) {
            case 'cloze':
                if (!$this->isMultiple()) {
                    $answer = array($answer);
                }
                foreach ($this->values as $value) {
                    if ($value['correct'] == true) {
                        if (!in_array($value['value'], $answer)) {
                            return false;
                        }
                    } else {
                        if (in_array($value['value'], $answer)) {
                            return false;
                        }
                    }
                }
                return true;
                break;
            case 'choice':
            case 'yesno':
                foreach ($this->values as $value) {
                    if ($answer == $value['value']) {
                        return $value['correct'];
                    }
                }
                break;
            case 'select':
                foreach ($this->values as $value) {
                    if ($answer == $value['value']) {
                        return $value['correct'];
                    }
                }
                break;
            case 'short':
                return in_array($answer, $this->values);
                break;
            case 'fillthegap':
                foreach ($this->values as $value) {
                    if ($answer == $value['value']) {
                        return $value['correct'];
                    }
                }
                break;
        }
        return false;
    }

    public function isMultiple() {
        $correct = false;

        foreach ($this->values as $value) {
            if ($value['correct']) {
                if ($correct) {
                    return true;
                }
                $correct = true;
            }
        }
        return false;
    }

}