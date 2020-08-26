<?php
use app\library\components\ExcelFromHtml;

/**
 * @var $monthTotal
 * @var $dataSets
 * @var $subscriptionModels
 * @var $type
 * @var $searchModel
 */
$currentMonth = date('m/Y', strtotime('01-'.$searchModel->searchMonth));
$lastMonth = date('m/Y', strtotime('01-'.$searchModel->searchMonth.' - 1 month'));
$monthOfLastYear = date('m/Y', strtotime('01-'.$searchModel->searchMonth.' - 1 year'));
$htmlString = "<table class='table table-bordered'>
                <tr>
                    <th><strong>Nội dung</strong></th>
                    <th><strong>Tháng $lastMonth</strong></th>
                    <th><strong>Tháng $currentMonth</strong></th>
                    <th><strong>So với tháng $lastMonth (%)</strong></th>
                    <th><strong>So với tháng $monthOfLastYear (%)</strong></th>
                </tr>
                <tr>
                    <td colspan='5'><strong>1. Doanh thu</strong></td>
                </tr>";
$totalMonth = 0;
$totalMonthLast = 0;
$totalPercent = 0;
$totalPercentLastYear = 0;
foreach ($subscriptionModels as $k => $subscriptionModel) {
    $subscriptionId = $subscriptionModel['id'];
    $subscriptionName = $subscriptionModel['name'];
    // get total of week now all subscription
    $totalMonth += $monthTotal[$subscriptionId]['month_now'];

    // get total of week last all subscription
    $totalMonthLast += $monthTotal[$subscriptionId]['month_last'];

    // get total amount of subscription in month now
    $totalAmountMonthNow = $monthTotal[$subscriptionId]['month_now'] ? $monthTotal[$subscriptionId]['month_now'] : 0;

    // get total amount of subscription in month last
    $totalAmountMonthLast = $monthTotal[$subscriptionId]['month_last'] ?  $monthTotal[$subscriptionId]['month_last'] : 0;

    // get total amount of subscription in month last
    $totalAmountMonthLastYear = $monthTotal[$subscriptionId]['month_last_year'] ?  $monthTotal[$subscriptionId]['month_last_year'] : 0;

    // get percent of subscription in 2 month
    $percent = ($totalAmountMonthNow && $totalAmountMonthLast) ? round((($totalAmountMonthNow - $totalAmountMonthLast)/$totalAmountMonthLast), 2) : 0 ;

    // get percent of subscription in 2 month in 2 year
    $percentLastYear = ($totalAmountMonthNow && $totalAmountMonthLastYear) ? round((($totalAmountMonthNow - $totalAmountMonthLastYear)/$totalAmountMonthLastYear), 2) : 0 ;

    // get total percent of subscription in 2 month
    $totalPercent += $percent;

    // get total percent of subscription in 2 month in 2 year
    $totalPercentLastYear += $percentLastYear;
    $htmlString .= "<tr>
                                <td>$subscriptionName</td>
                                <td>".number_format($totalAmountMonthLast, '2','.', ',')."</td>
                                <td>".number_format($totalAmountMonthNow, '2','.', ',')."</td>
                                <td>$percent%</td>
                                <td>$percentLastYear%</td>
                            </tr>";
}
// total stats of current month
$totalMonthRegistration = $dataSets['totalStatsMonth']['registration_count'] ? $dataSets['totalStatsMonth']['registration_count'] : 0;
$totalMonthActive = $dataSets['totalStatsMonth']['total_active_count'] ? $dataSets['totalStatsMonth']['total_active_count'] : 0;
$totalMonthCancelled = $dataSets['totalStatsMonth']['cancelled_count'] ? $dataSets['totalStatsMonth']['cancelled_count'] : 0;
$totalMonthRenew = $dataSets['totalStatsMonth']['renew_count'] ? $dataSets['totalStatsMonth']['renew_count'] : 0;
$totalMonthExpired = $dataSets['totalStatsMonth']['expired_count'] ? $dataSets['totalStatsMonth']['expired_count'] : 0;

// total stats of last month
$totalLastMonthRegistration = $dataSets['totalStatsLastMonth']['registration_count'] ? $dataSets['totalStatsMonth']['registration_count'] : 0;
$totalLastMonthActive = $dataSets['totalStatsLastMonth']['total_active_count'] ? $dataSets['totalStatsMonth']['total_active_count'] : 0;
$totalLastMonthCancelled = $dataSets['totalStatsLastMonth']['cancelled_count'] ? $dataSets['totalStatsMonth']['cancelled_count'] : 0;
$totalLastMonthRenew = $dataSets['totalStatsLastMonth']['renew_count'] ? $dataSets['totalStatsMonth']['renew_count'] : 0;
$totalLastMonthExpired = $dataSets['totalStatsLastMonth']['expired_count'] ? $dataSets['totalStatsMonth']['expired_count'] : 0;

// total of current month last year
$totalMonthLastYearRegistration = $dataSets['totalStatsMonthLastYear']['registration_count'] ? $dataSets['totalStatsMonthLastYear']['registration_count'] : 0;
$totalMonthLastYearActive = $dataSets['totalStatsMonthLastYear']['total_active_count'] ? $dataSets['totalStatsMonthLastYear']['total_active_count'] : 0;
$totalMonthLastYearCancelled = $dataSets['totalStatsMonthLastYear']['cancelled_count'] ? $dataSets['totalStatsMonthLastYear']['cancelled_count'] : 0;
$totalMonthLastYearRenew = $dataSets['totalStatsMonthLastYear']['renew_count'] ? $dataSets['totalStatsMonthLastYear']['renew_count'] : 0;
$totalMonthLastYearExpired = $dataSets['totalStatsMonthLastYear']['expired_count'] ? $dataSets['totalStatsMonthLastYear']['expired_count'] : 0;

// percent of current month and last month
$percentRegistration = ($totalMonthRegistration > 0 && $totalLastMonthRegistration > 0 ) ? ($totalMonthRegistration - $totalLastMonthRegistration) / $totalLastMonthRegistration : 0;
$percentActive = ($totalMonthActive > 0 && $totalLastMonthActive > 0 ) ? ($totalMonthActive - $totalLastMonthActive) / $totalLastMonthActive : 0;
$percentCancelled = ($totalMonthCancelled > 0 && $totalLastMonthCancelled > 0 ) ? ($totalMonthCancelled - $totalLastMonthCancelled) / $totalLastMonthCancelled : 0;
$percentRenew = ($totalMonthRenew > 0 && $totalLastMonthRenew > 0 ) ? ($totalMonthRenew - $totalLastMonthRenew) / $totalLastMonthRenew : 0;
$percentExpired = ($totalMonthExpired > 0 && $totalLastMonthExpired > 0 ) ? ($totalMonthExpired - $totalLastMonthExpired) / $totalLastMonthExpired : 0;

// percent of current month and current month of last year
$percentRegistrationLastYear = ($totalMonthRegistration > 0 && $totalMonthLastYearRegistration > 0 ) ? ($totalMonthRegistration - $totalMonthLastYearRegistration) / $totalMonthLastYearRegistration : 0;
$percentActiveLastYear = ($totalMonthActive > 0 && $totalMonthLastYearActive > 0 ) ? ($totalMonthActive - $totalMonthLastYearActive) / $totalMonthLastYearActive : 0;
$percentCancelledLastYear = ($totalMonthCancelled > 0 && $totalMonthLastYearCancelled > 0 ) ? ($totalMonthCancelled - $totalMonthLastYearCancelled) / $totalMonthLastYearCancelled : 0;
$percentRenewLastYear = ($totalMonthRenew > 0 && $totalMonthLastYearRenew > 0 ) ? ($totalMonthRenew - $totalMonthLastYearRenew) / $totalMonthLastYearRenew : 0;
$percentExpiredLastYear = ($totalMonthExpired > 0 && $totalMonthLastYearExpired > 0 ) ? ($totalMonthExpired - $totalMonthLastYearExpired) / $totalMonthLastYearExpired : 0;
$htmlString .= "
                <tr>
                    <td><strong>Tổng</strong></td>
                    <td>".number_format($totalMonthLast, '2','.', ',')."</td>
                    <td>".number_format($totalMonth, '2','.', ',')."</td>
                    <td>$totalPercent%</td>
                    <td>$totalPercentLastYear%</td>
                </tr>
                <tr>
                    <td colspan='5'><strong>2. Sản lượng</strong></td>
                </tr>
                <tr>
                    <td>Thuê bao mới</td>
                    <td>$totalLastMonthRegistration</td>
                    <td>$totalMonthRegistration</td>
                    <td>$percentRegistration%</td>
                    <td>$percentRegistrationLastYear%</td>
                </tr>
                <tr>
                    <td>Thuê bao kích hoạt</td>
                     <td>$totalLastMonthActive</td>
                    <td>$totalMonthActive</td>
                    <td>$percentActive%</td>
                    <td>$percentActiveLastYear%</td>
                </tr>
                <tr>
                    <td>Thuê bao hủy dịch vụ</td>
                    <td>$totalLastMonthCancelled</td>
                    <td>$totalMonthCancelled</td>
                    <td>$percentCancelled%</td>
                    <td>$percentCancelledLastYear%</td>
                </tr>
                <tr>
                    <td>Thuê bao gia hạn</td>
                    <td>$totalLastMonthRenew</td>
                    <td>$totalMonthRenew</td>
                    <td>$percentRenew%</td>
                    <td>$percentRenewLastYear%</td>
                </tr>
                <tr>
                    <td>Thuê bao hết hạn</td>
                    <td>$totalLastMonthExpired</td>
                    <td>$totalMonthExpired</td>
                    <td>$percentExpired%</td>
                    <td>$percentExpired%</td>
                </tr>";
$htmlString .= ' </table>';
echo $htmlString;
