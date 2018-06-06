## 安装
```
composer require lhp9916/utils:dev-master
```
## 使用
```php
$utils = new \Lhp9916\Utils\Utils();

$utils->num_to_rmb(21235.36); //贰万壹仟贰佰叁拾伍元叁角陆分整

$utils->hide_mobile_number('12345678901'); //123****8901

$utils->get_date_list('2018-05-30', '2018-06-04');

```
just a beginning