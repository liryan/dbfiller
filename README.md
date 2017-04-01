安装说明:
=

`composer require 'liryan/dbfiller' 'dev-master'`

使用说明
=
在config/app.php中添加provider
```php
providers=[
    ...
    Dbfiller\DBFillerProvider::class,
],
```

在Lumen框架中，修改bootstrap/app.php
```php
...
$app->register(Dbfiller\DBFillerProvider::class);
```

运行命令，生成配置文件
=
`php composer dump`

`php artisan vendor:publish`

生成config/dbfiller.php
Lumen中，复制 vendor/liryan/dbfiler/src/config/dbfiller.php 到 项目 config目录，不存在就新建

根据数据库修改此文件
然后运行

`php artisan mysql.filler`

测试format

`php artisan mysql.filler 'format' --count=1`

配置文件说明
```php
<?php
return[
    '表1'=>
    [
        'total'=>'要生成多少数据',
        'key'=>'此表的主键字段名字,默认为id',
        'define'=>[
            '字段名1'=>['format'=>'格式说明','from'=>'引用数据']  
            '字段名2'=>['format'=>'格式说明','from'=>'引用数据']  
            ...
        ]
    ],
    '表2'=>
    [
        'total'=>'要生成多少数据',
        'key'=>'此表的主键字段名字,默认为id',
        'define'=>[
            '字段名1'=>['format'=>'格式说明','from'=>'引用数据']  
            '字段名2'=>['format'=>'格式说明','from'=>'引用数据']  
            //不写字段，则会按照数据库中定义的数据类型自动生成
            ...
        ]
    ],
    ...
];
```

格式说明
=
字符串格式：[%|#][max-min][u/d/s/f/p]

解释
    %   随机数据  #  唯一数据
    max-min:
        数字:最大位数-最小位数
        字符:字符个数
        浮点:总位数，小数位数

    u/d/s/f/p:  
        u:无符号整数
        d:有符号整数
        s:字符串
        f:有符号浮点数
        p:无符号浮点
`
    例如邮件字段格式: 
        'format'=>'#16-4s@%20-1s.%3-2s'
        唯一的用户名(4-16个字符) @ 域名1-20字符，后缀2-3字符

闭包格式：直接传递一个闭包函数，则数据调用你的闭包函数生成

例如
```php
        'format'=>function($row){  //$row :目前生成的数据集，值传递，不要修改，在字段需要与字段产生关系的时候调用
                    return mt_rand(time()-30*24*3600,time())  //时间为最近一年某一刻
                  }
```

引用数据说明:<br>
    格式：[%|#].[表名].[字段名]<br>   %：可以重复[一对多]，#:不重复[两个表一一对应]，
    如 from="#.member.id' 表示此字段的值来自member表中的id字段，并且是顺序取值<br>

配置实例
=
```php
return [
    'member'=>[
        'total'=>10000,
        'key'=>'id',
        'define'=>[
            'avatar'=>['format'=>'http://%8-6s.com/%6s/%10-5s.jpg'],
            'name'=>['format'=>'%16-4s'],
        ],
    ],

    'address'=>[
        'total'=>5000,
        'key'=>'id',
        'define'=>[
            'userid'=>['format'=>'',from=>'#.member.id'] //数据来自上面member表单字段,member一定要先生成
            'address'=>['format'=>'%32-16s'],
            'postcode'=>['format'=>function($row){
                $table=[100012,100013,100023,200010];    //随机返回一个数据
                return $table[mt_rand(0,count($table)-1)];
            }],
        ],
    ],
];
```
