<?php declare(strict_types=1);


namespace  cjhRedisLock;



use function bean;
use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\SwoftComponent;
use Swoft\Redis\Pool;
use LockSpace\RedisLock;
use LockSpace\RedisNewLock;
use Swoft\Redis\Redis as SwoftRedis;

/**
 * Class LockControl
 *
 * @since 2.0
 */
class LockControl  
{
    
    /**
     *  
     *
     * @var string
     */
    private $pool;

    /**
     *  
     *
     * @var RedisLock
     */
    private $redisLock;

    /**
     *  
     *
     * @var RedisNewLock
     */
    private $redisNewLock;


    /**
     * LockControl constructor.
     *
     * @param SwoftRedis $redis
     */
    public function __construct( )
    {   
        $connection = SwoftRedis::connection('redis.pool');
        $this->redis = $redis;
        $this->redisLock = new RedisLock($connection);
        $this->redisNewLock = new RedisNewLock($connection);
    }

 

    public function  RedisLock()
    {
        return  $this->redisLock;
    }

    public function  RedisNewLock()
    {
        return  $this->redisNewLock ;
    }

}
