composer require chenjiahao/swoftlock
专门封装给swoft框架用的
redis实现的分布式锁


$tid = Co::tid();
$obj = BeanFactory::getRequestBean('lock-control', (string)$tid);


$obj->RedisNewLock()->getUpdateLock('aaaaa',5,5) ;
$obj->RedisNewLock()->unlock('aaaaa' );
$obj->RedisNewLock()->getShareLock('aaaaa',5,6); 
$obj->RedisNewLock()->unlock('aaaaa' );


$obj->RedisLock()->lock('safasfa',10,10);


这是一个独占锁

加锁
$key锁的名称 $expire过期时间 $wait 等待锁超时时间  都是以秒为单位
使用$obj->RedisLock()->lock( $key,$expire,$wait = 0)

解锁
$key锁的名称
$obj->RedisLock()->unlock($key)



 
获取一个独占锁
$key锁的名称 $expire过期时间 $wait 等待锁超时时间  都是以秒为单位
$obj->RedisNewLock()-> getUpdateLock( $key,$expire,$wait = 0)


获取一个共享锁
$key锁的名称 $expire过期时间 $wait 等待锁超时时间  都是以秒为单位
$obj->RedisNewLock()->getShareLock( $key,$expire,$wait = 0)


解锁
$key锁的名称
$obj->RedisNewLock()->unlock($key)

配置

            'lock-control'      => [
                'class'  => LockControl::class,
                [\bean('redis.pool')],
                '__option' => [
                
                 'scope' => Bean::REQUEST
              ///  'scope' => Bean::PROTOTYPE
                ],
            ]
