<?php

namespace Lhp9916\Utils;

class Utils
{
    /**
     * 钱转换为汉字表示
     * @param $num
     * @return string
     */
    public function num_to_rmb($num)
    {
        $c1 = "零壹贰叁肆伍陆柒捌玖";
        $c2 = "分角元拾佰仟万拾佰仟亿";
        //精确到分后面就不要了，所以只留两个小数位
        $num = round($num, 2);
        //将数字转化为整数
        $num = $num * 100;
        if (strlen($num) > 10) {
            return "金额太大，请检查";
        }
        $i = 0;
        $c = "";
        while (1) {
            if ($i == 0) {
                //获取最后一位数字
                $n = substr($num, strlen($num) - 1, 1);
            } else {
                $n = $num % 10;
            }
            //每次将最后一位数字转化为中文
            $p1 = substr($c1, 3 * $n, 3);
            $p2 = substr($c2, 3 * $i, 3);
            if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
                $c = $p1 . $p2 . $c;
            } else {
                $c = $p1 . $c;
            }
            $i = $i + 1;
            //去掉数字最后一位了
            $num = $num / 10;
            $num = (int)$num;
            //结束循环
            if ($num == 0) {
                break;
            }
        }
        $j = 0;
        $slen = strlen($c);
        while ($j < $slen) {
            //utf8一个汉字相当3个字符
            $m = substr($c, $j, 6);
            //处理数字中很多0的情况,每次循环去掉一个汉字“零”
            if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
                $left = substr($c, 0, $j);
                $right = substr($c, $j + 3);
                $c = $left . $right;
                $j = $j - 3;
                $slen = $slen - 3;
            }
            $j = $j + 3;
        }
        //这个是为了去掉类似23.0中最后一个“零”字
        if (substr($c, strlen($c) - 3, 3) == '零') {
            $c = substr($c, 0, strlen($c) - 3);
        }
        //将处理的汉字加上“整”
        if (empty($c)) {
            return "零元整";
        } else {
            return $c . "整";
        }
    }

    /**
     * 隐藏字符串中间部分
     * @param $str
     * @return string
     */
    public function half_replace($str)
    {
        $len = ceil(mb_strlen($str, "utf8") / 2);
        $prefix = mb_substr($str, 0, ceil($len / 2), "utf8");
        $suffix = mb_substr($str, $len + ceil($len / 2), null, "utf8");
        return $prefix . str_repeat("*", $len) . $suffix;
    }

    //生成一个唯一ID
    public function create_uuid($prefix = '')
    {
        $id = uniqid($prefix, true);
        return str_replace('.', '', $id);
    }

    //获取某个日期所在的月份总有多少天
    public function get_days_count_in_month($date)
    {
        return intval(date('t', strtotime($date)));
    }

    //获得某个日期离这个月第一天有多少天
    public function get_days_count_to_month_head($date)
    {
        return intval(date('d', strtotime($date)));
    }

    //获取某个日期离这个月最后一天还有多少天
    public function get_days_count_to_month_tail($date)
    {
        $daysThisMonth = $this->get_days_count_in_month($date);
        $daysNow = intval(date('d', strtotime($date)));
        return $daysThisMonth - $daysNow;
    }

    //获得两个日期之间相隔多少天
    public function get_days_count($startDate, $endDate)
    {
        $startDate = strtotime($startDate . ' 00:00:00');
        $endDate = strtotime($endDate . ' 00:00:00');
        return round(($endDate - $startDate) / 86400);
    }

    //获取某一天n个月后的日期
    function get_n_months_after_day($date, $monthsCount)
    {
        $date1 = strtotime($date);
        $y1 = intval(date('Y', $date1));
        $m1 = intval(date('m', $date1));
        $d1 = intval(date('d', $date1));

        $y2 = $y1;
        $m2 = $m1 + $monthsCount;
        $d2 = $d1;
        if ($m2 > 12) {
            $y2 = $y2 + floor($m2 / 12);
            $m2 = $m2 % 12;
            if ($m2 == 0) {
                $m2 = 12;
                $y2 = $y2 - 1;
            }
        }
        $date2 = strtotime($y2 . '-' . $m2 . '-01');
        $daysCount = date('t', $date2);

        if ($d2 > $daysCount) {
            $d2 = $daysCount;
        }

        //格式化返回的格式为2017-01-14
        return date('Y-m-d', strtotime($y2 . '-' . $m2 . '-' . $d2));
    }

    //获得某一天n个月前的日期, 日期格式为YYYY-MM-DD
    function get_n_months_before_day($date, $monthsCount)
    {
        list($yy, $mm, $dd) = explode('-', $date);
        $time = mktime(0, 0, 0, $mm - $monthsCount, 1, $yy);
        $t = date('t', $time);
        $day = $dd > $t ? $t : $dd;
        list($yy2, $mm2) = explode('-', date("Y-m", $time));

        //格式化返回的格式为2017-02-14
        return $yy2 . '-' . $mm2 . '-' . $day;
    }

    //获得星期几
    public function get_week_day_index($date)
    {
        $time = strtotime($date);
        $index = date('w', $time);
        $weekIndexString = ['日', '一', '二', '三', '四', '五', '六'];

        return '星期' . $weekIndexString[$index];
    }

    //获得文件扩展名
    public function get_file_extend($fileName)
    {
        $extend = pathinfo($fileName);
        $extend = strtolower($extend['extension']);
        return $extend;
    }

    //计算两个日期相隔多少个月
    public function diff_month($date1, $date2)
    {
        if (strtotime($date1) > strtotime($date2)) {
            $tmp = $date2;
            $date2 = $date1;
            $date1 = $tmp;
        }

        list($y1, $m1) = explode('-', $date1);
        list($y2, $m2) = explode('-', $date2);
        $y = $y2 - $y1;
        $m = $m2 - $m1;
        return $y * 12 + $m;
    }

    /**
     * 计算两个日期相隔多少年，多少月，多少天
     * param string $date1[格式如：2011-11-5]
     * param string $date2[格式如：2012-12-01]
     * return array array('年','月','日');   array('year','..','..');
     */
    public function diff_date($date1, $date2)
    {
        if (strtotime($date1) > strtotime($date2)) {
            $tmp = $date2;
            $date2 = $date1;
            $date1 = $tmp;
        }
        list($y1, $m1, $d1) = explode('-', $date1);
        list($y2, $m2, $d2) = explode('-', $date2);
        $y = $y2 - $y1;
        $m = $m2 - $m1;
        $d = $d2 - $d1;
        if ($d < 0) {
            $d += (int)date('t', strtotime(" -1 month $date2"));
            $m--;
        }
        if ($m < 0) {
            $m += 12;
            $y--;
        }
        return ['year' => $y, 'month' => $m, 'day' => $d];
    }

    /**计算两个日期月差集并返回差集间的月份
     * @param $date1 [格式如：2011-11-5]
     * @param $date2 [格式如：2012-12-01]
     * @return array
     */
    public function diff_date_list($date1, $date2)
    {
        if (strtotime($date1) > strtotime($date2)) {
            $ymd = $date2;
            $date2 = $date1;
            $date1 = $ymd;
        }
        list($y1, $m1, $d1) = explode('-', $date1);
        list($y2, $m2, $d2) = explode('-', $date2);
        $math = ($y2 - $y1) * 12 + $m2 - $m1;
        $my_arr = [];
        if ($y1 == $y2 && $m1 == $m2) {
            if ($m1 < 10) {
                $m1 = intval($m1);
                $m1 = '0' . $m1;
            }
            if ($m2 < 10) {
                $m2 = intval($m2);
                $m2 = '0' . $m2;
            }
            $my_arr[] = $y1 . '-' . $m1;
            $my_arr[] = $y2 . '-' . $m2;
            return $my_arr;
        }
        $p = $m1;
        $x = $y1;
        for ($i = 0; $i <= $math; $i++) {
            if ($p > 12) {
                $x = $x + 1;
                $p = $p - 12;
                if ($p < 10) {
                    $p = intval($p);
                    $p = '0' . $p;
                }
                $my_arr[] = $x . '-' . $p;
            } else {
                if ($p < 10) {
                    $p = intval($p);
                    $p = '0' . $p;
                }
                $my_arr[] = $x . '-' . $p;
            }
            $p = $p + 1;
        }
        return $my_arr;
    }

    /**
     * 获取两个时间参数之间包含的天数列表
     * @param $start_time
     * @param $end_time
     * @return array
     */
    public function diff_day_list($start_time, $end_time)
    {
        $start = strtotime(date('Ymd', $start_time));
        $end = strtotime(date('Ymd', $end_time));
        $return = [];
        while ($start <= $end) {
            $return[] = $start;
            $start += 86400;
        }
        return $return;
    }

    //从身份证号码获取生日
    public function get_birthday_from_identity_card($identityCard)
    {
        $year = substr($identityCard, 6, 4);
        $month = substr($identityCard, 10, 2);
        $day = substr($identityCard, 12, 2);
        return $year . '-' . $month . '-' . $day;
    }

    //根据身份证号码计算年龄
    public function calculate_age_from_identity_card($identityCard, $date)
    {
        $birthday = $this->get_birthday_from_identity_card($identityCard);
        $result = $this->diff_date($birthday, $date);
        return $result['year'];
    }

    //获取年龄
    public function get_age($birthday)
    {
        $age = strtotime($birthday);
        if ($age === false) {
            return false;
        }
        list($y1, $m1, $d1) = explode("-", date("Y-m-d", $age));
        $now = strtotime("now");
        list($y2, $m2, $d2) = explode("-", date("Y-m-d", $now));
        $age = $y2 - $y1;
        if ((int)($m2 . $d2) < (int)($m1 . $d1))
            $age -= 1;
        return $age;
    }

    /**
     * @param $num number 要格式化的数值
     * @param int $round 小数点的四舍五入(保留几位小数) 默认:2
     * @param string $split 千分位分隔符 默认:','
     * @param int|string $unit 必须大于等于1 内部仅直接使用参数$num = $num/$unit;  如:参数$num=1000 其他参数默认情况 则返回1 默认:1
     * @param string $prefix 前缀
     * @param string $postfix 后缀
     * @return string 返回的是string,如果使用全等判断请注意
     */
    function format_number($num, $round = 2, $split = ',', $unit = 1, $prefix = '', $postfix = '')
    {
        $num = $num / $unit;
        $format = number_format($num, $round, '.', $split);
        return $prefix . $format . $postfix;
    }

    /**
     * 数字转换为大写的人民币
     * 如：380.12
     *    叁佰捌拾元壹角贰分整
     * @param $num
     * @return string
     */
    public function num2rmb($num)
    {
        $pre = '';
        if ($num < 0) {
            $pre = '负';
            $num = substr($num, 1);
        }

        if (bccomp($num, 0, 2) === 0) {//两位小数精度对比, $num等于0则成立
            return '零元整';
        }

        $c1 = "零壹贰叁肆伍陆柒捌玖";
        $c2 = "分角元拾佰仟万拾佰仟亿";
        $num = round($num, 2);
        $num = $num * 100;
        if (strlen($num) > 10) {
            return '';
        }

        $i = 0;
        $c = "";

        while (1) {
            if ($i == 0) {
                $n = substr($num, strlen($num) - 1, 1);
            } else {
                $n = $num % 10;
            }

            $p1 = substr($c1, 3 * $n, 3);
            $p2 = substr($c2, 3 * $i, 3);

            if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
                $c = $p1 . $p2 . $c;
            } else {
                $c = $p1 . $c;
            }

            $i = $i + 1;
            $num = $num / 10;
            $num = (int)$num;
            if ($num == 0) {
                break;
            }
        } //end of while| here, we got a chinese string with some useless character
        $j = 0;

        $slen = strlen($c);
        while ($j < $slen) {
            $m = substr($c, $j, 6);
            if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
                $left = substr($c, 0, $j);
                $right = substr($c, $j + 3);
                $c = $left . $right;
                $j = $j - 3;
                $slen = $slen - 3;
            }

            $j = $j + 3;
        }

        if (substr($c, strlen($c) - 3, 3) == '零') {
            $c = substr($c, 0, strlen($c) - 3);
        } // if there is a '0' on the end , chop it out
        return $pre . $c . "整";
    }

    //原型输出var_dump
    public function dump($var)
    {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }


    public function num_format($num)
    {
        if (!is_numeric($num)) {
            return false;
        }
        $num = explode('.', $num); //把整数和小数分开
        $rl = $num[1]; //小数部分的值
        $j = strlen($num[0]) % 3; //整数有多少位
        $sl = substr($num[0], 0, $j); //前面不满三位的数取出来
        $sr = substr($num[0], $j); //后面的满三位的数取出来
        $i = 0;
        $rvalue = '';
        while ($i <= strlen($sr)) {
            $rvalue = $rvalue . ',' . substr($sr, $i, 3); //三位三位取出再合并，按逗号隔开
            $i = $i + 3;
        }

        $rvalue = $sl . $rvalue;
        $rvalue = substr($rvalue, 0, strlen($rvalue) - 1); //去掉最后一个逗号
        $rvalue = explode(',', $rvalue); //分解成数组

        if ($rvalue[0] == 0) {
            array_shift($rvalue); //如果第一个元素为0，删除第一个元素
        }

        $rv = $rvalue[0]; //前面不满三位的数

        for ($i = 1; $i < count($rvalue); $i++) {
            $rv = $rv . ',' . $rvalue[$i];
        }

        if (!empty($rl)) {
            $rvalue = $rv . '.' . $rl; //小数不为空，整数和小数合并
        } else {
            $rvalue = $rv; //小数为空，只有整数
        }
        return $rvalue;
    }

    /**基于curl的http请求方法
     * @param $url
     * @param array $postData 如果传入了该参数 则请求变为post方式
     * @param int $timeout 链接超时秒数
     * @return mixed 一般返回String 超时返回False
     */
    public function http_request($url, array $postData = [], $timeout = 10)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.69 Safari/537.36");
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); //超时设置  单位:秒
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);    //支持重定向
        //设置post数据
        if (!empty($postData)) {
            $postStr = '';
            foreach ($postData as $k => $v) {
                $postStr .= $k . '=' . $v . '&';
            }
            $postStr = rtrim($postStr, '&');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postStr);
        }

        $str = curl_exec($ch);
        curl_close($ch);
        return $str;
    }

    /**
     * 注意超时设置
     * @param $url
     * @param array $postData
     * @param string $post_raw_data
     * @param array $header_arr
     * @return mixed
     */
    public function http_get($url, array $postData = [], $post_raw_data = '', array $header_arr = [], $timeout = 10)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); //超时设置  单位:秒
        curl_setopt($ch, CURLOPT_URL, $url);
        if (!empty($header_arr)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header_arr); //自定义header
        }

        //设置post数据
        if (!empty($postData)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        }

        //设置post raw数据
        if (!empty($post_raw_data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_raw_data);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书

        $str = curl_exec($ch);
        $rinfo = curl_getinfo($ch);
        $curl_err = curl_error($ch);
        if (!empty($curl_err)) {
            return 'err';
        }
        curl_close($ch);
        return $str;
    }

    /**隐藏身份证号码
     * @param $id_card_number
     * @return string
     */
    public function hide_id_card_number($id_card_number)
    {
        for ($i = 4; $i < 14; $i++) {
            $id_card_number{$i} = '*';
        }

        return $id_card_number;
    }

    /**
     * 隐藏手机号码
     * @param $mobile
     * @return mixed
     */
    public function hide_mobile_number($mobile)
    {
        $offset_index = 3;
        $mobile[$offset_index++] = '*';
        $mobile[$offset_index++] = '*';
        $mobile[$offset_index++] = '*';
        $mobile[$offset_index++] = '*';
        return $mobile;
    }

    /**
     * 将文件大小单位转换为方便阅读的格式
     * @param number $size 单位是B
     * @return string
     */
    public function format_bytes($size)
    {
        $units = [' B', ' KB', ' MB', ' GB', ' TB'];
        for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
        return round($size, 2) . $units[$i];
    }

    /**
     * 过滤空数组
     * @param $var
     * @return array|null
     */
    public function filter_array_null($var)
    {
        if (empty($var) || !is_array($var))
            return null;
        foreach ($var as $k => $item) {
            if (empty($item)) {
                unset($var[$k]);
            }
        }
        return count($var) > 0 ? $var : null;
    }

    /**
     * 替换字符
     * @param mixed $var 需要替换的字符串或数组
     * @param int $position 开始替换的位置
     * @param int $len 替换的长度
     * @param string $char 替换后的字符
     * @param bool $direction 正反方向：true表示从左到右，false表示从右到左
     * @return mixed    返回字符串或数组
     */
    public function hidden_replace_chars($var, $position, $len, $char = '*', $direction = true)
    {

        if (is_array($var)) {
            foreach ($var as &$item) {
                if ($direction) {
                    $item = substr($item, 0, $position - 1) . str_repeat($char, $len) . substr($item, $position + $len - 1, strlen($item));
                } else {
                    $item = substr($item, 0, strlen($item) - $position - $len + 1) . str_repeat($char, $len) . substr($item, strlen($item) - $position + 1, strlen($item));
                }
            }
            return $var;
        } else {
            if (empty($var)) {
                return '';
            }
            if ($direction) {
                return substr($var, 0, $position - 1) . str_repeat($char, $len) . substr($var, $position + $len - 1, strlen($var));
            } else {
                return substr($var, 0, strlen($var) - $position - $len + 1) . str_repeat($char, $len) . substr($var, strlen($var) - $position + 1, strlen($var));
            }
        }

    }

    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param string $attr 根节点属性
     * @param string $id 数字索引子节点key转换的属性名
     * @param string $encoding 数据编码
     * @return string
     */
    public function xml_encode($data, $root = 'root', $item = 'item', $attr = '', $id = 'id', $encoding = 'utf-8')
    {
        if (is_array($attr)) {
            $_attr = [];
            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $_attr);
        }
        $attr = trim($attr);
        $attr = empty($attr) ? '' : " {$attr}";
        $xml = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
        $xml .= "<{$root}{$attr}>";
        $xml .= data_to_xml($data, $item, $id);
        $xml .= "</{$root}>";
        return $xml;
    }

    /**
     * 数据XML编码
     * @param mixed $data 数据
     * @param string $item 数字索引时的节点名称
     * @param string $id 数字索引key转换为的属性名
     * @return string
     */
    public function data_to_xml($data, $item = 'item', $id = 'id')
    {
        $xml = $attr = '';
        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $id && $attr = " {$id}=\"{$key}\"";
                $key = $item;
            }
            $xml .= "<{$key}{$attr}>";
            $xml .= (is_array($val) || is_object($val)) ? data_to_xml($val, $item, $id) : $val;
            $xml .= "</{$key}>";
        }
        return $xml;
    }

    /**
     * 获取xml的节点元素
     * @param $xml_data  xml数据
     * @param $node  节点名称
     */
    public function get_data_for_xml($xml_data, $node)
    {
        $xml_data = $this->filter_unicode($xml_data);
        if ($this->is_xml($xml_data)) {
            $xml = simplexml_load_string($xml_data, null, LIBXML_NOCDATA);
            if ($xml) {
                $result = $xml->xpath($node);
                while (list(, $node) = each($result)) {
                    return $node;
                }
            }
        }
        return;
    }

    /**
     * 判断是否为xml
     * @param $str
     * @return bool
     */
    public function is_xml($str)
    {
        $xml_parser = xml_parser_create();
        if (!xml_parse($xml_parser, $str, true)) {
            xml_parser_free($xml_parser);
            return false;
        }
        return true;
    }

    /**过滤无效的unicode字符
     * @param $str
     * @return mixed|string
     */
    public function filter_unicode($str)
    {
        $str = htmlspecialchars_decode($str);
        $str = preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/", '', $str);
        return $str;
    }

    /**
     * xml转成数组
     * @param string $xml
     * @return array|SimpleXMLElement
     */
    public function xml_decode($xml)
    {
        $data = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
        if (is_object($data) && get_class($data) == 'SimpleXMLElement') {
            $data = $this->arrarval($data);
        }
        return $data;
    }

    /**
     * 将对象转成数组
     * @param $obj 对象集合
     * @return array 转换后的数组
     */
    public function arrarval($obj)
    {
        if (is_object($obj)) {
            $obj = (array)$obj;
            $obj = $this->arrarval($obj);
        } else if (is_array($obj)) {
            foreach ($obj as $key => $value) {
                $obj[$key] = $this->arrarval($value);
            }
        }
        return $obj;
    }

    /**
     * 将字符转换为html标签
     * https://www.baidu.com
     * <a href="https://www.baidu.com" target='_blank'>https://www.baidu.com</a>
     * @param string $str
     * @return string
     */
    public function text2links($str = '')
    {

        if ($str == '' or !preg_match('/(http|www\.|@)/i', $str)) {
            return $str;
        }

        $lines = explode("\n", $str);
        $new_text = '';
        while (list($k, $l) = each($lines)) {
            // replace links:
            $l = preg_replace("/([ \t]|^)www\./i", "\\1http://www.", $l);
            $l = preg_replace("/([ \t]|^)ftp\./i", "\\1ftp://ftp.", $l);

            $l = preg_replace("/(http:\/\/[^ )\r\n!]+)/i",
                "<a href=\"\\1\" target='_blank'>\\1</a>", $l);

            $l = preg_replace("/(https:\/\/[^ )\r\n!]+)/i",
                "<a href=\"\\1\" target='_blank'>\\1</a>", $l);

            $l = preg_replace("/(ftp:\/\/[^ )\r\n!]+)/i",
                "<a href=\"\\1\" target='_blank'>\\1</a>", $l);

            $l = preg_replace(
                "/([-a-z0-9_]+(\.[_a-z0-9-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)+))/i",
                "<a href=\"mailto:\\1\">\\1</a>", $l);

            $new_text .= $l . "\n";
        }

        return $new_text;
    }

    /**
     * 根据键组获取值并返回
     * @param array $array
     * @param $key
     * @return array
     */
    function get_value_combination(array $array, $key)
    {
        if (empty($array)) return $array;
        $result = [];
        foreach ($array as $item) {
            if (isset($item[$key])) {
                $result[] = $item[$key];
            }
        }
        return $result;
    }

    /**
     * 获取数组的值 -- 可深度获取
     * $array = ['names' => ['joe' => ['programmer']]];
     * $value = array_get($array, 'names.joe');
     * @param $array
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    function array_get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }
        if (isset($array[$key])) {
            return $array[$key];
        }
        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }
        return $array;
    }

    /**
     * 将多维数组变成一维数组
     * @param $array
     * @param string $prepend
     * @return array
     */
    public function array_dot($array, $prepend = '')
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $results = array_merge($result, array_dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }

    public function get_input($filed_name, $default = null)
    {
        if (empty($filed_name)) {
            return $default;
        }

        if (isset($_GET[$filed_name])) {
            return $_GET[$filed_name];
        }
        if (isset($_POST[$filed_name])) {
            return $_POST[$filed_name];
        }
        return $default;
    }

    public function get_input_all(array $field_list = [], $default = null)
    {
        if (empty($field_list)) {
            return $_REQUEST;
        }
        $result = [];
        foreach ($field_list as $field_name) {
            $value = $this->get_input($field_name, $default);

            $result[$field_name] = $value;
        }
        return $result;
    }

    /**
     * 从一个数组里匹配值
     * @param string $pattern 正则
     * @param array $subject_array 匹配数组
     * @param bool $preg_match_all 是否贪婪匹配
     * @return array
     */
    public function array_preg_match($pattern, $subject_array, $preg_match_all = false)
    {

        $result = [];

        foreach ($subject_array as $value) {

            $temp = [];

            if ($preg_match_all) {

                preg_match_all($pattern, $value, $temp);

            } else {

                preg_match($pattern, $value, $temp);

            }

            $result = array_merge($result, $temp);

        }
        return $result;
    }

    /**
     * 将数组里的某一个键值作为数组的索引并返回
     * @param array $array
     * @param $key
     * @return array
     */
    public function array_key_advance(array $array, $key)
    {
        $result = [];
        foreach ($array as $item) {
            $field = array_get($item, $key);
            if (is_null($field)) {
                $result[] = $item;
            } else {
                $result[$field] = $item;
            }
        }
        return $result;
    }

    /**
     * 根据指定日期段获取日期列表
     * @param string $start_date 开始日期
     * @param string $end_date 结束日期
     * @param string $date_num 指定某一天，默认*代表每月的最后一天
     * @param bool $complete 补全，如果结束日期在该月所在的天数小于指定某一天并且$complete为true时，将取结束日期
     * @return array
     */
    public function get_date_list($start_date, $end_date, $date_num = '*', $complete = false)
    {
        if (!$this->is_date($start_date) || !$this->is_date($end_date)) {
            return [];
        }

        $date_num = $date_num == '*' ? $date_num : intval($date_num);
        $format = 'Y-m-d';
        $current_time = strtotime($start_date);
        $end_time = strtotime($end_date);
        $date_list = [date($format, $current_time)];

        while (1) {
            if ($date_num === '*') {
                $current_time = strtotime('+1 day', $current_time);
                if ($current_time < $end_time) {
                    $date_list[] = date($format, $current_time);
                    continue;
                }
            } else {

                if ($date_num == 0) {

                    $current_time = strtotime(date('Y-m-t', $current_time));
                    if ($current_time < $end_time) {
                        $date_list[] = date($format, $current_time);
                    } else if ($current_time >= $end_time) {
                        if ($complete) {
                            $date_list[] = date($format, $end_time);
                        }
                        break;
                    }

                } else {

                    $days_num = date('t', strtotime($current_time));
                    if ($date_num <= $days_num) {
                        $current_time = strtotime(date('Y-m-' . $date_num, $current_time));
                    }
                    if ($current_time < $end_time) {
                        $date_list[] = date($format, $current_time);
                    } else {
                        if (date('j', $end_time) < $date_num && $complete) {
                            $date_list[] = date($format, $end_time);
                        }
                        break;
                    }

                }

                $current_time = strtotime('+1 month', $current_time);

                continue;

            }

            break;

        }

        if ($date_num === '*') {

            $date_list[] = date($format, $end_time);

        } else {

            unset($date_list[0]);

        }

        return $date_list;
    }

    /**
     * 判断是否为日期
     * @param $date
     * @return bool
     */
    public function is_date($date)
    {
        if ($date == date('Y-m-d', strtotime($date))) {
            return true;
        }
        return false;
    }

    /**
     * 将数组的各个值用字符串拼接起来
     * @param array $array
     * @param string $char
     * @param string $left_delimiter
     * @param string $right_delimiter
     * @return string
     */
    public function str_mosaic_for_array(array $array, $char = ',', $left_delimiter = '', $right_delimiter = '')
    {
        $str = '';
        foreach ($array as $value) {
            if (is_string($value)) {
                $str .= $left_delimiter . "$value" . $right_delimiter . $char;
            }
        }
        return trim($str, $char);
    }

    /**
     * 根据指定的键数组值获取值
     * @param array $key_array 键
     * @param array $value_array 所有值数组
     * @return array
     */
    public function array_get_all(array $key_array, array $value_array)
    {
        $result = [];
        foreach ($key_array as $key) {
            $result[$key] = array_get($value_array, $key);
        }
        return $result;
    }

    public function array_forget(&$array, $keys)
    {
        $original = &$array;
        foreach ((array)$keys as $key) {
            $parts = explode('.', $key);
            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    $parts = [];
                }
            }
            unset($array[array_shift($parts)]);
            $array = &$original;

        }
    }

    public function array_except($array, $keys)
    {
        array_forget($array, $keys);
        return $array;
    }

    /**
     * 批量赋值 用于生成默认数组
     * @param array $keyArr
     * @param null $default
     * @return array
     */
    public function batch_assign_data(array $keyArr, $default = null)
    {
        $result = [];
        foreach ($keyArr as $keyName) {
            $result[$keyName] = $default;
        }
        return $result;
    }

    /**
     * 获取键值列表  -- 例：[键 => 值, 键 => 值, 键 => 值]
     * @param array $data
     * @param string $keyName
     * @param string $valueName
     * @return array
     */
    public function fetch_key_value(array $data, $keyName, $valueName)
    {
        $result = [];
        foreach ($data as $item) {
            $result[array_get($item, $keyName)] = array_get($item, $valueName);
        }
        return $result;
    }

    /**
     * 从数组返回给定的键值对，并移除它
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function array_pull(&$array, $key, $default = null)
    {
        $value = array_get($array, $key, $default);
        array_forget($array, $key);
        return $value;
    }

    /**
     * 将数组数据进行分类处理
     * @param array $array
     * @param $key
     * @return array
     */
    public function array_classify(array $array, $key)
    {
        $result = [];
        foreach ($array as $item) {
            $result[array_get($item, $key, 0)][] = $item;
        }
        return $result;
    }

    /**
     * @param array $array
     * @return array
     */
    public function array_flat(array $array)
    {
        $result = [];
        foreach ($array as $item) {
            if (is_array($item)) {
                $result = array_merge($result, $item);
            } else {
                $result[] = $item;
            }
        }
        return $result;
    }

}