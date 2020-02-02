<?php declare(strict_types=1);


namespace MSwoft\RedisLock;

use Swoft\Bean\Annotation\Mapping\Bean;
use function bean;
use ReflectionException;
use Swoft\Bean\Exception\ContainerException;
use Swoft\SwoftComponent;

/**
 * Class AutoLoader
 *
 * @since 2.0
 */
class AutoLoader extends SwoftComponent
{
    /**
     * @return array
     */
    public function getPrefixDirs(): array
    {
        return [
            __NAMESPACE__ => __DIR__,
        ];
    }

    /**
     * @return array
     */
    public function metadata(): array
    {
        return [];
    }

    /**
     * @return array
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function beans(): array
    {
        return [
            'lock-control'      => [
                'class'  => LockControl::class,
                [\bean('redis.pool')],
                'option' => [
                    'alias' => 'lock-control' ,
                    'serializer' => Bean::REQUEST
                ],
            ]
        ];
    }
}
