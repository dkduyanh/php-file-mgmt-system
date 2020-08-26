<?php

namespace app\library\google;

//$analytics = initializeAnalytics();
//$response = getReport($analytics);
//printResults($response);

class Analytics
{
    protected $_analytics;
    /**
     * Initializes an Analytics Reporting API V4 service object.
     *
     * @return An authorized Analytics Reporting API V4 service object.
     */
    function initialize()
    {

        // Use the developers console and download your service account
        // credentials in JSON format. Place them in this directory or
        // change the key file location if necessary.
        $KEY_FILE_LOCATION = DATA_DIR . '/keys/danet-243707-ebcf349e93ba.json'; //service-account-credentials.json';

        // Create and configure a new client object.
        $client = new \Google_Client();
        $client->setApplicationName("Hello Analytics Reporting");
        $client->setAuthConfig($KEY_FILE_LOCATION);
        $client->setScopes(['https://www.googleapis.com/auth/analytics.readonly']);
        $this->_analytics = new \Google_Service_AnalyticsReporting($client);

        return $this;
    }


    /**
     * Queries the Analytics Reporting API V4.
     *
     * @param service An authorized Analytics Reporting API V4 service object.
     * @return The Analytics Reporting API V4 response.
     */
    function getReport()
    {

        // Replace with your view ID, for example XXXX.
        $VIEW_ID = "126245998";

        // Create the DateRange object.
        $dateRange = new \Google_Service_AnalyticsReporting_DateRange();
        $dateRange->setStartDate("7daysAgo");
        $dateRange->setEndDate("today");

        // Create the Metrics object.
        $sessions = new \Google_Service_AnalyticsReporting_Metric();
        $sessions->setExpression("ga:sessions");
        $sessions->setAlias("sessions");

        //Create the browser dimension.
        $browser = new \Google_Service_AnalyticsReporting_Dimension();
        $browser->setName("ga:browser");

        // Create the ReportRequest object.
        $request = new \Google_Service_AnalyticsReporting_ReportRequest();
        $request->setViewId($VIEW_ID);
        $request->setDateRanges($dateRange);
        $request->setMetrics(array($sessions));
        $request->setDimensions(array($browser));

        $body = new \Google_Service_AnalyticsReporting_GetReportsRequest();
        $body->setReportRequests(array($request));
        return $this->_analytics->reports->batchGet($body);
    }


    /**
     * Parses and prints the Analytics Reporting API V4 response.
     *
     * @param An Analytics Reporting API V4 response.
     */
    static function printResults($reports)
    {
        //if(!is_array($reports)) $reports = [$reports];
        for ($reportIndex = 0; $reportIndex < count($reports); $reportIndex++) {
            $report = $reports[$reportIndex];
            $header = $report->getColumnHeader();
            $dimensionHeaders = $header->getDimensions();
            $metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
            $rows = $report->getData()->getRows();

            for ($rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
                $row = $rows[$rowIndex];
                $dimensions = $row->getDimensions();
                $metrics = $row->getMetrics();
                for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
                    print($dimensionHeaders[$i] . ": " . $dimensions[$i] . "\n");
                }

                for ($j = 0; $j < count($metrics); $j++) {
                    $values = $metrics[$j]->getValues();
                    for ($k = 0; $k < count($values); $k++) {
                        $entry = $metricHeaders[$k];
                        print($entry->getName() . ": " . $values[$k] . "\n");
                    }
                }
            }
        }
    }

    public function getRealtime()
    {
        $optParams = array(
            'dimensions' => 'rt:medium');

        try {
            return $results = $this->_analytics->data_realtime->get(
                'ga:56789',
                'rt:activeUsers',
                $optParams);
            // Success.
        } catch (apiServiceException $e) {
            // Handle API service exceptions.
            $error = $e->getMessage();
            o($error);
        }
    }

    public function printRealtime($results)
    {
        $this->printReportInfo($results);
        $this->printQueryInfo($results);
        $this->printProfileInfo($results);
        $this->printColumnHeaders($results);
        $this->printDataTable($results);
        $this->printTotalsForAllResults($results);
    }

    public function printDataTable(&$results) {
        $table = '';
        if (count($results->getRows()) > 0) {
            $table .= '<table>';

            // Print headers.
            $table .= '<tr>';

            foreach ($results->getColumnHeaders() as $header) {
                $table .= '<th>' . $header->name . '</th>';
            }
            $table .= '</tr>';

            // Print table rows.
            foreach ($results->getRows() as $row) {
                $table .= '<tr>';
                foreach ($row as $cell) {
                    $table .= '<td>'
                        . htmlspecialchars($cell, ENT_NOQUOTES)
                        . '</td>';
                }
                $table .= '</tr>';
            }
            $table .= '</table>';

        } else {
            $table .= '<p>No Results Found.</p>';
        }
        print $table;
    }

    function printColumnHeaders(&$results) {
        $html = '';
        $headers = $results->getColumnHeaders();

        foreach ($headers as $header) {
            $html .= <<<HTML
                <pre>
                Column Name       = {$header->getName()}
                Column Type       = {$header->getColumnType()}
                Column Data Type  = {$header->getDataType()}
                </pre>
HTML;
        }
        print $html;
    }

    function printQueryInfo(&$results) {
        $query = $results->getQuery();
        $html = <<<HTML
            <pre>
            Ids         = {$query->getIds()}
            Metrics     = {$query->getMetrics()}
            Dimensions  = {$query->getDimensions()}
            Sort        = {$query->getSort()}
            Filters     = {$query->getFilters()}
            Max Results = {$query->getMax_results()}
            </pre>
HTML;

        print $html;
    }

    function printProfileInfo(&$results) {
        $profileInfo = $results->getProfileInfo();

        $html = <<<HTML
            <pre>
            Account ID               = {$profileInfo->getAccountId()}
            Web Property ID          = {$profileInfo->getWebPropertyId()}
            Internal Web Property ID = {$profileInfo->getInternalWebPropertyId()}
            Profile ID               = {$profileInfo->getProfileId()}
            Profile Name             = {$profileInfo->getProfileName()}
            Table ID                 = {$profileInfo->getTableId()}
            </pre>
HTML;

        print $html;
    }

    function printReportInfo(&$results) {
        $html = <<<HTML
              <pre>
            Kind                  = {$results->getKind()}
            ID                    = {$results->getId()}
            Self Link             = {$results->getSelfLink()}
            Total Results         = {$results->getTotalResults()}
            </pre>
HTML;

        print $html;
    }

    function printTotalsForAllResults(&$results) {
        $totals = $results->getTotalsForAllResults();

        foreach ($totals as $metricName => $metricTotal) {
            $html .= "Metric Name  = $metricName\n";
            $html .= "Metric Total = $metricTotal";
        }

        print $html;
    }
}