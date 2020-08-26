<?php
/**
 * Created by PhpStorm.
 * User: TungDev
 * Date: 7/9/2019
 * Time: 2:29 PM
 */

namespace app\library;


use http\Exception\InvalidArgumentException;
use yii\db\Expression;

class Utils
{
    public static function isValidFormatDateTime($date, $format = 'Y-m-d H:i:s'){
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    public static function listDatesInRange($dateFrom, $dateTo = null, $format = 'Y-m-d')
    {
        //if $dateTo is not set
        if($dateTo === null){
            $dateTo = date($format);
        }

        //check if is valid date
        $objDateFrom = \DateTime::createFromFormat($format, $dateFrom);
        if(!$objDateFrom || $objDateFrom->format($format) != $dateFrom){
            throw new \yii\base\InvalidArgumentException($dateFrom.' is invalid');
        }

        $objDateTo = \DateTime::createFromFormat($format, $dateTo);
        if(!$objDateTo || $objDateTo->format($format) != $dateTo){
            throw new \yii\base\InvalidArgumentException($dateFrom.' is invalid');
        }

        $period = new \DatePeriod(
            $objDateFrom,
            new \DateInterval('P1D'),
            $objDateTo
        );
        foreach($period as $key => $value){
            $range[] = $value->format($format);
        }
        $range[] = $objDateTo->format($format);

        return $range;
    }

    public static function allDaysByYear($year, $format = 'Y-m-d'){
        $dateFrom = date($format, strtotime($year.'-01-01'));
        $dateTo = date($format, strtotime($year.'-12-31'));
        return self::listDatesInRange($dateFrom, $dateTo, $format);
    }

    public static function GRBAGenerator($a = 0.8) {
        $r = rand(0, 255);
        $g = rand(0, 255);
        $b = rand(0, 255);
        return "rgba(" . $r . "," . $g . "," . $b . "," . $a . ")";
    }

    public static function hexToGRB($hexColor){
        list($r, $g, $b) = sscanf($hexColor, "#%02x%02x%02x");
        return "rgb($r, $g, $b)";
    }

    public static function hexToGRBA($hexColor, $a = 0.8){
        list($r, $g, $b) = sscanf($hexColor, "#%02x%02x%02x");
        return "rgba($r, $g, $b, $a)";
    }

    public static function colors()
    {
        return [
            '#3366cc',
            '#dc3912',
            '#ff9900',
            '#109618',
            '#990099',
            '#0099c6',
            '#dd4477',
            '#B5CA92',
            '#66aa00',
            '#b82e2e',
            '#316395',
            '#ED561B',
            '#3366cc',
            '#994499',
            '#22aa99',
            '#aaaa11',
            '#6633cc',
            '#e67300',
            '#8b0707',
            '#651067',
            '#329262',
            '#5574a6',
            '#3b3eac',
            '#b77322',
            '#16d620',
            '#b91383',
            '#f4359e',
            '#9c5935',
            '#a9c413',
            '#2a778d',
            '#668d1c',
            '#bea413',
            '#0c5922',
            '#743411',
        ];
    }

    public static function randomCode($countItem, $lengthItem)
    {
        $result = [];
        for($i = 0; $i < $countItem; $i++)
        {
            $item = strtoupper(substr(sha1(uniqid(mt_rand(), true).microtime(true)), 0, $lengthItem));
            if (!in_array($item, $result)){
                array_push($result, $item);
            }
        }
        return $result;
    }

    public static function daysInMonth($year, $month) {
        $lastDay =  date("t", mktime (0,0,0,$month,1,$year));
        $list = [];
        for ($i = 1; $i <= $lastDay; $i++) {
            $list[$i] = str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        return $list;
    }

    public static function isValidPhoneNumber($phone)
    {
        return true;
    }

    public static function secondsToTime($inputSeconds, $niceFormat = false) {
        $then = new \DateTime(date('Y-m-d H:i:s', time()+$inputSeconds));
        $now = new \DateTime(date('Y-m-d H:i:s', time()));
        $diff = $then->diff($now);

        $ret = array('years' => $diff->y, 'months' => $diff->m, 'days' => $diff->d, 'hours' => $diff->h, 'minutes' => $diff->i, 'seconds' => $diff->s);
        if($niceFormat){
            $niceString = [];
            foreach($ret as $t => $p){
                if($p != 0){
                    $niceString[] = $p.' '.ucfirst(($p > 1) ? $t : substr($t, 0, -1));
                }
            }
            if(empty($niceString)){
                $niceString[] = '0 Second';
            }
            return implode(' ', $niceString);
        }
        return $ret;
    }

    /**
     * @param $qryString
     * @return string
     */
    public static function subOperator($qryString){
        switch ($qryString){
            case strpos($qryString,'>=') === 0:
                $operator = '>=';
                break;
            case strpos($qryString,'>') === 0:
                $operator = '>';
                break;
            case strpos($qryString,'<=') === 0:
                $operator = '<=';
                break;
            case strpos($qryString,'<') === 0:
                $operator = '<';
                break;
            default:
                $operator =  'like';
                break;
        }
        return $operator;
    }

    public static function getMimeType($filename)
    {
        $mimetype = false;
        if(!function_exists('finfo_open')) {
            // open with FileInfo
            if(false === ($info = finfo_open(FILEINFO_MIME_TYPE))){
                throw new Exception('"Opening fileinfo database failed"');
            }
            $mimetype = finfo_file($info, $filename);
            finfo_close($info);
            if ($mimetype === false) {
                return false;
            }
        } elseif(extension_loaded('gd') && function_exists('getimagesize')) {
            // open with GD
            if (false === (list($width, $height, $type, $attr) = getimagesize($filename))) {
                return false;
            }
            $mimetype = image_type_to_mime_type($type);
        } elseif(function_exists('exif_imagetype')) {
            // open with EXIF
            if(false === ($type = exif_imagetype($filename))){
                return false;
            }
            $mimetypes = [
                IMAGETYPE_GIF	=>	'image/gif',
                IMAGETYPE_JPEG	=>	'image/jpeg',
                IMAGETYPE_PNG	=>	'image/png',
                IMAGETYPE_SWF	=>	'application/x-shockwave-flash',
                IMAGETYPE_PSD	=>	'image/psd',
                IMAGETYPE_BMP	=>	'image/bmp',
                IMAGETYPE_TIFF_II 	=>	'image/tiff',
                IMAGETYPE_TIFF_MM 	=>	'image/tiff',
                IMAGETYPE_JPC	=>	'application/octet-stream',
                IMAGETYPE_JP2	=>	'image/jp2',
                IMAGETYPE_JPX	=>	'application/octet-stream',
                IMAGETYPE_JB2	=>	'application/octet-stream',
                IMAGETYPE_SWC	=>	'application/x-shockwave-flash',
                IMAGETYPE_IFF	=>	'image/iff',
                IMAGETYPE_WBMP	=>	'image/vnd.wap.wbmp',
                IMAGETYPE_XBM	=>	'image/xbm',
                IMAGETYPE_ICO	=>	'image/vnd.microsoft.icon',
                IMAGETYPE_WEBP	=>	'image/webp',
            ];
            $mimetype = @$mimetypes[$type];
        } elseif(function_exists('mime_content_type')) {
            $mimetype = mime_content_type($filename);
        }

        return $mimetype;
    }

    public static function timezoneList() {
        $zones_array = array();
        $timestamp = time();
        foreach(timezone_identifiers_list() as $key => $zone) {
            date_default_timezone_set($zone);
            $zones_array[$key]['zone'] = $zone;
            $zones_array[$key]['offset'] = (int) ((int) date('O', $timestamp))/100;
            $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
            $zones_array[$key]['diff_from_GMT_zone'] = 'UTC/GMT ' . date('P', $timestamp) .' - ' . $zone;
        }
        return $zones_array;
    }

    public static function dateFormatList(){
        return array(
            'd-m-Y' => 'd-m-Y - ' . date('d-m-Y', time()),
            'd/m/Y' => 'd/m/Y - ' . date('d/m/Y', time()),
            'Y-m-d' => 'Y-m-d - ' . date('Y-m-d', time()),
            'm/d/Y' => 'm/d/Y - ' . date('m/d/Y', time()),
            'F j, Y' => 'F j, Y - ' . date('F j, Y', time()),
        );
    }

    public static function timeFormatList(){
        return array(
            'H:i' => 'H:i - '.date('H:i', time()),
            'H:i:s' => 'H:i:s - '.date('H:i:s', time()),
            'g:i a' => 'g:i a - '.date('g:i a', time()),
            'g:i:s a' => 'g:i:s a - '.date('g:i:s a', time()),
            'g:i A' => 'g:i A - '.date('g:i A', time()),
            'g:i:s A' => 'g:i:s A - '.date('g:i:s A', time())
        );
    }

}
