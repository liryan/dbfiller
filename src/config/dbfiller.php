<?php
/**
 * 数据以UTF格式生成,字段生成顺序根据定义的表的顺序生成
 * config description
 * [
 *  '表名'=>
 *      [
 *      'total'//总数
 *      'key' //主键字段,不写则认为是id
 *      'define'//定义=>
 *          [
 *              '字段名'//字段不写则根据数据表定义的长度自动生成=>[
 *                      'from'//数据来自另外一个表 => '#/%.表名.字段名'  #:一对一的关系,%随机取值
 *                      'format'//string 数据格式=>'
 *                          [%/#][max-min][d/s/f] 
 *                          %:普通取值[可重复]，
 *                          #:唯一取值
 *                          max:最大位数,[max-max]也可以写成[max]表示生成max个字符
 *                          min:最小位数
 *                          d:整数
 *                          s:字符
 *                          f:浮点数
 *
 *                          组合使用
 *                          '例如邮件: #16-4s@%20-1s.%3>2s' 用户名(4-16个字符)唯一@域名1-20字符，后缀2-3字符
 *                      ]
 *                      'format'=>//clouser function(){  //传递一个闭包函数，自己生成数据
 *                          return mt_rand(time()-30*24*3600,time()) 
 *                      }
 *          ]
 *      ]
 *]
 */

return [
    'contact_info'=>[
        'total'=>10000,
        'key'=>'id',
        'define'=>[
            'url'=>['format'=>'http://%8-6s.com/%6s/%10-5s.jpg'],
        ],
    ],
];
