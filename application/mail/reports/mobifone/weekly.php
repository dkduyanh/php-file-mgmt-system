<?php
use app\library\components\ExcelFromHtml;

/**
 * @var $weekTotal
 * @var $dataSets
 * @var $subscriptionModels
 * @var $type
 */

$htmlString = "<table class='table table-bordered'>
                <thead>
                 <tr>
                    <th><strong>Nội dung</strong></th>
                    <th><strong>Tuần N-1</strong></th>
                    <th><strong>Tuần N</strong></th>
                    <th><strong>So với Tuần (N-1) (%)</strong></th>
                    <th><strong>Lũy kế Tháng đến thời gian báo cáo</strong></th>
                </tr>
                </thead>";
$htmlString .= "<tbody>";
$htmlString .= "<tr>
                    <td colspan='5'><strong>1. Doanh thu</strong></td>
                </tr>";
    $totalWeek = 0;
    $totalWeekLast = 0;
    $totalPercent = 0;
    foreach ($subscriptionModels as $k => $subscriptionModel) {
        $subscriptionId = $subscriptionModel['id'];
        $subscriptionName = $subscriptionModel['name'];
        // get total of week now all subscription
        $totalWeek += $weekTotal[$subscriptionId]['week_now'];

        // get total of week last all subscription
        $totalWeekLast += $weekTotal[$subscriptionId]['week_last'];

        // get total amount of subscription in week now
        $totalAmountWeekNow = $weekTotal[$subscriptionId]['week_now'] ? $weekTotal[$subscriptionId]['week_now'] : 0;

        // get total amount of subscription in week last
        $totalAmountWeekLast = $weekTotal[$subscriptionId]['week_last'] ?  $weekTotal[$subscriptionId]['week_last'] : 0;

        // get percent of subscription in 2 week
        $percent = ($totalAmountWeekNow && $totalAmountWeekLast) ? round((($totalAmountWeekNow - $totalAmountWeekLast)/$totalAmountWeekLast), 2) : 0 ;

        // get total percent of subscription in 2 week
        $totalPercent += $percent;
        $htmlString .= "<tr>
                                <td>$subscriptionName</td>
                                <td>".number_format($totalAmountWeekLast, '2','.', ',')."</td>
                                <td>".number_format($totalAmountWeekNow, '2','.', ',')."</td>
                                <td>$percent%</td>
                                <td>0</td>
                            </tr>";
    }
    $totalWeekRegistration = $dataSets['totalStatsWeek']['registration_count'] ? $dataSets['totalStatsWeek']['registration_count'] : 0;
    $totalWeekActive = $dataSets['totalStatsWeek']['total_active_count'] ? $dataSets['totalStatsWeek']['total_active_count'] : 0;
    $totalWeekCancelled = $dataSets['totalStatsWeek']['cancelled_count'] ? $dataSets['totalStatsWeek']['cancelled_count'] : 0;
    $totalWeekRenew = $dataSets['totalStatsWeek']['renew_count'] ? $dataSets['totalStatsWeek']['renew_count'] : 0;
    $totalWeekExpired = $dataSets['totalStatsWeek']['expired_count'] ? $dataSets['totalStatsWeek']['expired_count'] : 0;


    $totalLastWeekRegistration = $dataSets['totalStatsLastWeek']['registration_count'] ? $dataSets['totalStatsWeek']['registration_count'] : 0;
    $totalLastWeekActive = $dataSets['totalStatsLastWeek']['total_active_count'] ? $dataSets['totalStatsWeek']['total_active_count'] : 0;
    $totalLastWeekCancelled = $dataSets['totalStatsLastWeek']['cancelled_count'] ? $dataSets['totalStatsWeek']['cancelled_count'] : 0;
    $totalLastWeekRenew = $dataSets['totalStatsLastWeek']['renew_count'] ? $dataSets['totalStatsWeek']['renew_count'] : 0;
    $totalLastWeekExpired = $dataSets['totalStatsLastWeek']['expired_count'] ? $dataSets['totalStatsWeek']['expired_count'] : 0;

    $percentRegistration = ($totalWeekRegistration > 0 && $totalLastWeekRegistration > 0 ) ? ($totalWeekRegistration - $totalLastWeekRegistration) / $totalLastWeekRegistration : 0;
    $percentActive = ($totalWeekActive > 0 && $totalLastWeekActive > 0 ) ? ($totalWeekActive - $totalLastWeekActive) / $totalLastWeekActive : 0;
    $percentCancelled = ($totalWeekCancelled > 0 && $totalLastWeekCancelled > 0 ) ? ($totalWeekCancelled - $totalLastWeekCancelled) / $totalLastWeekCancelled : 0;
    $percentRenew = ($totalWeekRenew > 0 && $totalLastWeekRenew > 0 ) ? ($totalWeekRenew - $totalLastWeekRenew) / $totalLastWeekRenew : 0;
    $percentExpired = ($totalWeekExpired > 0 && $totalLastWeekExpired > 0 ) ? ($totalWeekExpired - $totalLastWeekExpired) / $totalLastWeekExpired : 0;
$htmlString .= "</tbody>";
    $htmlString .= "<tfoot>
                <tr>
                    <td><strong>Tổng</strong></td>
                    <td>".number_format($totalWeekLast, '2','.', ',')."</td>
                    <td>".number_format($totalWeek, '2','.', ',')."</td>
                    <td>$totalPercent%</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td colspan='5'><strong>2. Sản lượng</strong></td>
                </tr>
                <tr>
                    <td>Thuê bao mới</td>
                    <td>$totalLastWeekRegistration</td>
                    <td>$totalWeekRegistration</td>
                    <td>$percentRegistration%</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Thuê bao kích hoạt</td>
                     <td>$totalLastWeekActive</td>
                    <td>$totalWeekActive</td>
                    <td>$percentActive%</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Thuê bao hủy dịch vụ</td>
                    <td>$totalLastWeekCancelled</td>
                    <td>$totalWeekCancelled</td>
                    <td>$percentCancelled%</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Thuê bao gia hạn</td>
                    <td>$totalLastWeekRenew</td>
                    <td>$totalWeekRenew</td>
                    <td>$percentRenew%</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>Thuê bao hết hạn</td>
                    <td>$totalLastWeekExpired</td>
                    <td>$totalWeekExpired</td>
                    <td>$percentExpired%</td>
                    <td>0</td>
                </tr>
                </tfoot>";
    $htmlString .= ' </table>';
echo $htmlString;
