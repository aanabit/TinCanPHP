<form name="tincan_test" action="results.php" method="post">

<?php

//$db = db_connect();
//$questions = db_getQuestions($db);

foreach($questions as $question) {

    echo '<div id="q'.$question['id'].'" class="question">';
    echo $question['before'].' ';

    switch ($question['type']) {
        case 'cloze':
            echo '<div class="answers">';
            $i = 1;

            foreach ($question['values'] as $value) {
                echo '<div class="answer">';
                echo '<input type="radio" name="q'.$question['id'].'" id="q'.$question['id'].'_'.$i.'" value="'.$value['value'].'" />';
                echo '<label for="q'.$question['id'].'_'.$i.'">'.$value['value'].'</label>';
                echo '</div>';
                $i++;
            }
            echo '</div>';
            break;
        case 'choice':
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