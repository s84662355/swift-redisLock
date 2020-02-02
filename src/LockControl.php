<?php declare(strict_types=1);


namespace MSwoft\RedisLock;


use function bean;
use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\SwoftComponent;
use Swoft\Redis\Pool;
use LockSpace\RedisLock;
use LockSpace\RedisNewLock;

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
     * @var Pool
     */
    private $redis;

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
     * @param Pool $redis
     */
    public function __construct(Pool $redis)
    {
        $this->redis = $redis;
        $this->redisLock = new RedisLock($this->redis);
        $this->redisNewLock = new RedisNewLock($this->redis);
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
