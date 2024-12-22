<?php
$pythonScript = "/home/future4/llm/sentiment-analysis1.py ";
exec("python3.7 $pythonScript", $output, $returnCode);

if ($returnCode === 0) {
    echo implode("\n", $output);
} else {
    echo "Error running Python script. Return code: $returnCode";
}
?>