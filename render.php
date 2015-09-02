<form name="tincan_test" action="results.php" method="post">

<?php

$questions = array();

$dbo = new DBO ($servername, $username, $password, $dbname);
$sql = "SELECT * FROM questions";

$dbo->query ($sql) or die ($dbo->ShowError ());
while ($question = $dbo->emaitza ()) {
    $question['before'] = $question['before_text'];
    $question['after'] = $question['after_text'];
    $question['values'] = json_decode($question['values_text'], true);

    $questions[] = $question;
}

function is_multiple($values) {
    $correct = false;

    foreach ($values as $value) {
        if ($value['correct']) {
            if ($correct) {
                return true;
            }
            $correct = true;
        }
    }
    return $false;
}

foreach($questions as $question) {
    echo '<div id="q'.$question['id'].'" class="question">';
    echo $question['before'].' ';

    switch ($question['type']) {
        case 'cloze':
            echo '<div class="answers">';
            $i = 1;

            $multiple = is_multiple($question['values']);
            foreach ($question['values'] as $value) {
                echo '<div class="answer">';
                if ($multiple) {
                    echo '<input type="checkbox" name="q'.$question['id'].'[]" id="q'.$question['id'].'_'.$i.'" value="'.$value['value'].'" />';
                } else {
                    echo '<input type="radio" name="q'.$question['id'].'" id="q'.$question['id'].'_'.$i.'" value="'.$value['value'].'" />';
                }
                echo '<label for="q'.$question['id'].'_'.$i.'">'.$value['value'].'</label>';
                echo '</div>';
                $i++;
            }
            echo '</div>';
            break;
        case 'choice':
            echo '<div class="answers">';
            $i = 1;

            foreach ($question['values'] as $value) {
                echo '<div class="answer">';
                echo '<input type="checkbox" name="q'.$question['id'].'[]" id="q'.$question['id'].'_'.$i.'" value="'.$value['value'].'" />';
                echo '<label for="q'.$question['id'].'_'.$i.'">'.$value['value'].'</label>';
                echo '</div>';
                $i++;
            }
            echo '</div>';
            break;
        case 'select':
            echo '<select name="q'.$question['id'].'" id="q'.$question['id'].'">';
            $i = 0;
            foreach ($question['values'] as $value) {
                echo '<option name="q'.$question['id'].'" value="'.$value['value'].'" >'.$value['value'].'</option>';
            }
            echo '</select>';
            break;
        case 'shortanswer':
            echo '<input type="text" name="q'.$question['id'].'" />';
            break;
    }

    echo ' '.$question['after'];
    echo '</div>';
}
?>

    <input type="hidden" value="<?php echo $questions;?>" />
    <div class="buttons"><input type="submit" value="Send" /></div>

</form>

