<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
    <meta http-equiv="pragma" content="no-cache" />

    <title>TIN CAN Test</title>
    <link rel="stylesheet" href="./src/css/bootstrap.min.css">
    <link rel="stylesheet" href="./src/css/app.css">
    <link rel="stylesheet" href="./src/css/tincan.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <h1>Tin Can Test</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <form name="tincan_test" action="results.php" method="post">
                    <div class="well">
<?php
                        require("dbo_lib.php");
                        require("question.class.php");

                        $questions_tmp = array();
                        $args = $_POST;

                        foreach ($args as $key => $value) {
                            if (strpos($key, 'qtype_') !== false) {
                                $index = intval(substr($key, 6));
                                $questions_tmp[$index]['type'] = $value;
                            } elseif (strpos($key, 'w_') > 2) {
                                $index = intval(substr($key, 2, strpos($key, 'w_') - 2));
                                $questions_tmp[$index]['values'][] = $value;
                            } elseif (strpos($key, 'g_') > 2) {
                                $index = intval(substr($key, 2, strpos($key, 'w_') - 2));
                                $questions_tmp[$index]['values'][] = $value;
                                $questions_tmp[$index]['corrects'][] = $value;
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

                        try {
                            $dbo = new DBO ($servername, $username, $password, $dbname);

                            foreach ($questions_tmp as $key => $value) {
                                $question = new question($key, $value);
                                $question->insertQuestion($dbo);
                            }

                            $sql = "SELECT * FROM questions ORDER BY id LIMIT 1";
                            $dbo->query($sql) or die ($dbo->ShowError());
                            while ($questionDB = $dbo->emaitza()) {
                                $question = new question($questionDB);
                                echo $question->render();
                            }
                        } catch(PDOException $e) {
                            echo $e->getMessage();
                        }
                        $conn = null;
?>
                        <input type="hidden" value="<?php echo $questions;?>" />
                        <div class="submit text-right"><input type="submit" value="Next" class="btn btn-primary" /></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="./src/js/jquery-1.11.3.min.js"></script>
</body>
</html>