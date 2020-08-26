<?php
use app\library\components\ExcelFromHtml;
use \yii\helpers\ArrayHelper;
/**
 * @var $type
 * @var $subscriptionModels
 * @var $totalAmount
 * @var $model
 * @var $dataSet
 * @var $searchModel
 */
$modelStats = ArrayHelper::index($model,'date');
$subscriptionColumn = '';
$totalAmountSubscriptions = 0;
$totalCount = $dataSet['total_count'] ? $dataSet['total_count'] : 0;
$totalActiveCount = $dataSet['total_active_count'] ?  $dataSet['total_active_count'] : 0;
$totalRegistrationCount = $dataSet['registration_count'] ? $dataSet['registration_count'] : 0;
$totalRenewCount = $dataSet['renew_count'] ? $dataSet['renew_count'] : 0;
$totalCancelledCount = $dataSet['cancelled_count'] ? $dataSet['cancelled_count'] : 0;
$totalExpiredCount = $dataSet['expired_count'] ? $dataSet['expired_count'] : 0;

foreach ($subscriptionModels as $subscriptionModel) {
    $subscriptionId = $subscriptionModel->id;
    $tempArr[$subscriptionId] = $totalAmount[$subscriptionId] ? number_format($totalAmount[$subscriptionId]['amount_sum']*$totalAmount[$subscriptionId]['quantity_sum'],'2','.',',') : 0;
    $subscriptionColumn .= "<td>$tempArr[$subscriptionId]</td>";
    $totalAmountSubscriptions += $totalAmount[$subscriptionId]['amount_sum']*$totalAmount[$subscriptionId]['quantity_sum'];
}

$totalAmountSubscriptionsF = number_format($totalAmountSubscriptions, 2, '.',',');
$htmlString = "<table class='table table-bordered'>";
$htmlString .= "<thead>";
$htmlString .= "<tr>";
$htmlString .= "<th width='100'>Ngày</th>";
foreach ($subscriptionModels as $subscriptionModel) {
    $htmlString .= " <th>Doanh thu gói $subscriptionModel->name</th>";
}
$htmlString .= "<th>Doanh thu mua lẻ</th>
      <th>Doanh thu truy thu cước (retry)</th>
      <th>Tổng Doanh thu</th>
      <th>Doanh thu lũy kế THÁNG</th>
      <th>So với cùng kỳ THÁNG trước</th>
      <th>Thuê bao ĐK mới</th>
      <th>Hủy dịch vụ</th>
      <th>Tổng TB lũy kế</th>
      <th>Tổng TB sử dụng</th>
      <th>Thuê bao PSC</th>
      <th>Tỷ lệ trừ cước thành công</th>";
$htmlString .=" </tr>";
$htmlString .=" </thead>";
$htmlString .=" <tbody>";

foreach ($modelStats as $item) {
    $htmlString .= "<tr>";
    $htmlString .= "<td>$item->date</td>
          $subscriptionColumn
          <td>0</td>
          <td>0</td>
          <td>$totalAmountSubscriptionsF</td>
          <td>0</td>
          <td>0</td>
          <td>$totalRegistrationCount</td>
          <td>$totalCancelledCount</td>
          <td>0</td>
          <td>$totalActiveCount</td>
          <td>0</td>
          <td>0</td>";
    $htmlString .= "</tr>";
}
$htmlString .= "</tbody>";
$htmlString .= "<tfoot>";
$htmlString .= "<tr>";
$htmlString .="<td>Tổng</td>
      $subscriptionColumn
      <td>0</td>
      <td>0</td>
      <td>$totalAmountSubscriptionsF</td>
      <td>0</td>
      <td>0</td>
      <td>$totalRegistrationCount</td>
      <td>$totalCancelledCount</td>
      <td>0</td>
      <td>$totalActiveCount</td>
      <td>0</td>
      <td>0</td>";
$htmlString .= "</tr>";
$htmlString .= "</tfoot>";
$htmlString .= "</table>";
echo $htmlString;
