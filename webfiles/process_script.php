<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the parameter 'param' is set in the GET request
if (isset($_GET['param'])) {
    $receivedNewspaper = htmlspecialchars($_GET['param']);
} else {
    echo "No parameter received.";
    exit;
}

$pythonScript = '/home/future4/llm/sentiment-analysis2.py';
exec("python3.7 $pythonScript $receivedNewspaper 2>&1", $output, $returnCode);

if ($returnCode === 0) {
    // Join the output array into a single string
    $jsonString = implode("\n", $output);
    
    // Remove any non-JSON content before and after the JSON data
    $jsonString = preg_replace('/^.*?(\{.*\}).*?$/s', '$1', $jsonString);
    
    // Decode the JSON string
    $data = json_decode($jsonString, true);
    
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        echo "<p>Error decoding JSON: " . htmlspecialchars(json_last_error_msg()) . "</p>";
        echo "<p>Raw JSON string:</p>";
        echo "<pre>" . htmlspecialchars($jsonString) . "</pre>";
    } else {
        echo "<h1>Sentiment Analysis Results for " . htmlspecialchars($receivedNewspaper) . "</h1>\n";
        echo "<a href='index.html'>Go back to latest news entries</a>\n";
        
        echo "<h2>Sentiment Analysis: Select another newspaper:</h2>\n<ul>\n";
        echo "<li><a href='sentiment-analysis-taz.html'>taz</a></li>\n";
        echo "<li><a href='sentiment-analysis-spiegel.html'>Spiegel</a></li>\n";
        echo "<li><a href='sentiment-analysis-zeit.html'>Zeit</a></li>\n";
        echo "<li><a href='sentiment-analysis-faz.html'>FAZ</a></li>\n";
        echo "<li><a href='sentiment-analysis-sueddeutsche.html'>Süddeutsche</a></li>\n";
        echo "</ul>\n";

        echo "<h2>Political Analysis: Select another newspaper:</h2>\n<ul>\n";
        echo "<li><a href='political-analysis-taz.html'>Taz</a></li>\n";
        echo "<li><a href='political-analysis-spiegel.html'>Spiegel</a></li>\n";
        echo "<li><a href='political-analysis-zeit.html'>Zeit</a></li>\n";
        echo "<li><a href='political-analysis-faz.html'>FAZ</a></li>\n";
        echo "<li><a href='political-analysis-sueddeutsche.html'>Süddeutsche</a></li>\n";
        echo "</ul>\n";

        echo "<p><strong>Overall Sentiment:</strong> " . htmlspecialchars($data['overall_sentiment']) . "</p>\n";
        
        foreach ($data['subtopics'] as $subtopic) {
            echo "<div class='subtopic'>\n";
            echo "<h2>" . htmlspecialchars($subtopic['topic']) . "</h2>\n";
            echo "<p class='sentiment'>Sentiment: " . htmlspecialchars($subtopic['sentiment']) . "</p>\n";
            echo "<div class='comments'>\n<h3>Comments:</h3>\n<ul>\n";
            
            foreach ($subtopic['comments'] as $comment) {
                echo "<li>" . htmlspecialchars($comment) . "</li>\n";
            }
            
            echo "</ul>\n</div>\n</div>\n";
        }
    }
} else {
    echo "<p>Error running Python script. Return code: " . htmlspecialchars($returnCode) . "</p>";
    echo "<p>Output: <pre>" . htmlspecialchars(implode("\n", $output)) . "</pre></p>"; // Display output for debugging
}
?>
