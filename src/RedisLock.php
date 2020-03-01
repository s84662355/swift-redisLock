<?php
namespace  cjhRedisLock;
use Swoole\Coroutine as co;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Concern\PrototypeTrait;
/**
 * Class  RedisLock
 *
 * @Bean(scope=Bean::PROTOTYPE)
 *
 * @since 2.0
 */
class RedisLock{

    use PrototypeTrait;
    
    public  $redisClient = null;

    public $client_number ;

    private     $script = <<<script
    redis.replicate_commands();

    function string.split(input, delimiter)
        input = tostring(input)
        delimiter = tostring(delimiter)
        if (delimiter=='') then return false end
        local pos,arr = 0, {}
        -- for each divider found
        for st,sp in function() return string.find(input, delimiter, pos, true) end do
            table.insert(arr, string.sub(input, pos, st - 1))
            pos = sp + 1
        end
        table.insert(arr, string.sub(input, pos))
        return arr
    end
    

    local key   = KEYS[1]  
    local expire =  tonumber( ARGV[1] )
    local client_name =  ARGV[2] 
    local a = redis.call('TIME');

    local cur_timestamp =  tonumber( a[1]  )
    local result=0    

    local lockdata =  expire + cur_timestamp  
    lockdata = lockdata.. "???" .. client_name

    result = redis.call('setnx',key, lockdata) 
     

    if result == 0 then 

       local keydata = string.split(redis.call('get',key),'???')

       local time_out = tonumber(keydata[1])

       local data_client_name =  keydata[2]

       if cur_timestamp >  time_out then  
        


            time_out = expire + cur_timestamp

            lockdata =  time_out .. "???" .. client_name

            if redis.call('setex',key,expire,lockdata)   then 
              return    1
            end 
            
            

           return 0
       end 

       if data_client_name == client_name then
                 
                 return 1
       end





       return 0  
    end  

    if     redis.call('Expire',key,expire)  then
            return 1
    end 
    
    return 0
script;

    private     $unlock_script = <<<script

    function string.split(input, delimiter)
        input = tostring(input)
        delimiter = tostring(delimiter)
        if (delimiter=='') then return false end
        local pos,arr = 0, {}
        -- for each divider found
        for st,sp in function() return string.find(input, delimiter, pos, true) end do
            table.insert(arr, string.sub(input, pos, st - 1))
            pos = sp + 1
        end
        table.insert(arr, string.sub(input, pos))
        return arr
    end



    local key   = KEYS[1]  

    local result = redis.call('get',key)
    local client_name =  ARGV[1] 

    if result == nil   then

       if redis.call('DEL',key) then 
            return  1
       end
       return 0
    end


    local keydata = string.split(result ,'???')

     

    if client_name  == keydata[2] and  redis.call('DEL',key)  then 

         return 1
    end

    return 0
     
script;

    /**
     *
     * @param array $items
     *
     * @return static
     */
    public static function new(array $items = []): self
    {
        $self        = self::__instance();
        [$redisClient] = $items;
        $self->redisClient =  $redisClient ;
        $self->client_number = Random::str( 10 ).microtime();
        return $self;
    }








  //wait 单位是秒
  public function lock( $key,$expire,$wait = 0)
  {
     $key = __CLASS__. $key;
    
     $wait = $wait * 1000 * 1000;
     
            $res = $this->redisClient->eval( $this->script,[$key,$expire,$this->client_number],1);
    
 
           if($res == 0)
           {
              while($wait>0){
                 
            $res = $this->redisClient->eval( $this->script,[$key,$expire,$this->client_number],1);
                 if($res>0) {
                         return $res;
                      }
                   
                       $wait  =  $wait - 1000 * 100;
                       // usleep(1000 * 50); 
                        \Swoole\Coroutine::sleep(0.1);
              }
           }

       
           return $res;
  }


  public function unlock($key)
  {
      $key = __CLASS__. $key;
 
      $res = $this->redisClient->eval( $this->unlock_script,[$key,$this->client_number],1);

      return   $res;
  }
  
}