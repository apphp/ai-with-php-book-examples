<?php

$memoryStart = memory_get_usage();
$microtimeStart = microtime(true);
ob_start();
//////////////////////////////

include('rubix-multiple-linear-regression-code.php');

//////////////////////////////
$result = ob_get_clean();
$microtimeEnd = microtime(true);
$memoryEnd = memory_get_usage();

$features = isset($_GET['features']) && is_array($_GET['features']) ? $_GET['features'] : [];
verify_fields($features, ['0', '1', '2'], ['0', '1']);

?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Linear Regression with PHP</h1>
</div>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
    <h2 class="h4">Multiple Linear Regression with Rubix</h2>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-0">
            <a href="<?=create_href('ml-algorithms', 'linear-regression', 'rubix-multiple-linear-regression')?>" class="btn btn-sm btn-outline-primary">Show Code</a>
        </div>
    </div>
</div>

<div>
    <p>
        Involves two or more independent variables. For example, predicting house prices based on
        factors like size, number of rooms, and location (distance to city center).
    </p>
</div>

<div>
    <?php
        $testData = [
            'rooms,size,distance',
            '4,1800,3,  // First house',
            '2,1200,8   // Second house'
        ];
        echo create_dataset_and_test_data_links(__DIR__ . '/houses2.csv', $testData);
    ?>
</div>

<div class="container-fluid px-2">
    <div class="row justify-content-start p-0">
        <div class="col-md-12 col-lg-7 px-1 pe-5">
            <p><b>Chart:</b></p>
            <?php
                echo Chart::drawMultiLinearRegression(
                    samples:  $dataset->samples(),
                    labels: $dataset->labels(),
                    features: $features,
                    titles: ['Number of rooms', 'Square footage (sq.ft)', 'Location (km to center)'],
                    targetLabel: 'Price ($)',
                    mainTraceLabel: 'Dataset',
                    customTraceLabel: 'Prediction',
                    predictionSamples: $newSamples,
                    predictionResults: $predictions,
                );
            ?>
        </div>
        <div class="col-md-12 col-lg-5 p-0 m-0">
            <div>
                <div class="mt-1">
                    <b>Features:</b>
                </div>
                <form action="<?= APP_SEO_LINKS ? create_href('ml-algorithms', 'linear-regression', 'rubix-multiple-linear-regression-code-run') : 'index.php'; ?>" type="GET">
                    <?= !APP_SEO_LINKS ? create_form_fields('ml-algorithms', 'linear-regression', 'rubix-multiple-linear-regression-code-run') : '';?>
                    <?=create_form_features(['Rooms' => 0, 'Size' => 1, 'Location' => 2], $features);?>
                    <div class="form-check form-check-inline float-end p-0 m-0 me-1">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Re-generate</button>
                    </div>
                </form>
            </div>

            <hr>

            <div class="pb-1">
                <b>Result:</b>
                <span class="float-end">Memory: <?= memory_usage($memoryEnd, $memoryStart); ?> Mb</span>
                <span class="float-end me-2">Time running: <?= running_time($microtimeEnd, $microtimeStart); ?> sec.</span>
            </div>
            <code class="code-result">
                <pre><?= $result; ?></pre>
            </code>
        </div>

    </div>

</div>

