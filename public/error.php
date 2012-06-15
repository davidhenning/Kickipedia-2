<?php

if($e->getCode() === 400) {
    header('HTTP/1.0 400 Bad Request', true, 400);

    if($_SERVER['CONTENT_TYPE'] === 'application/json') {
        header('Content-type: application/json');
        header('HTTP/1.0 400 Bad Request', true, 400);

        $aError = array(
            'status' => 400,
            'time' => date('Y-m-d H:i:s'),
            'request' => array(
                'method' => 'PUT',
                'url' => request_uri()
            ),
            'response' => array(
                'error' => str_ireplace('exception', '', get_class($e)),
                'message' => $e->getMessage(),
                'backtrace' => $e->getTrace()
            )
        );

        echo json_encode($aError);
        exit();
    }
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>MongoAppKit Error</title>
        <link href="/assets/stylesheets/error.css" media="screen, projection" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <h1>MongoAppKit encountered an error:</h1>
        <h2><?php echo str_ireplace('exception', '', get_class($e)); ?></h2>
        <div class="error">
            <?php echo $e->getMessage(); ?>
        </div>
        
        <h3>Backtrace:</h3>
        <div class="detail">
            <div class="backtrace"><?php echo nl2br($e->getTraceAsString()); ?></div>
        </div>

        <h3>Request:</h3>
        <div class="detail">
            <div>
                <?php 
                    $aGetParams = $_GET;
                    array_shift($aGetParams);
                    foreach($aGetParams as $key => $value) {
                        echo "<p><strong>{$key}:</strong> {$value}</p>";
                    }
                ?>
            </div>
        </div>

        <h3>Server:</h3>
        <div class="detail">       
            <div>
                <?php
                    foreach($_SERVER as $key => $value) {
                        echo "<p><strong>{$key}:</strong> {$value}</p>";
                    }
                ?>
            </div>
        </div>        
    </body>
</html>