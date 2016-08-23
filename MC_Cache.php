<?php

class MC_Cache {

    /**
     * @var Memcache|null
     *
     * @desc Cache interaction methods to power MCMyAdmin.
     */

    protected $instance = null;

    /**
     * MC_Cache constructor.
     * @param string $host
     * @param mixed $port
     */
    public function __construct($host = 'localhost', $port = 11211)
    {
        $memcache = new Memcached();

        // @TODO add array of servers support
        $memcache->addServer($host, $port);

        $this->instance = $memcache;
    }

    /**
     * @param array $keyArray
     * @return bool
     */
    public function deleteByArray($keyArray)
    {
        if(!is_array($keyArray)) {
            return false;
        }

        return $this->instance->deleteMulti($keyArray);
    }

    /**
     * @param string|array $key
     * @return bool
     */
    public function deleteByKey($key)
    {
        if(is_array($key)) {
            return $this->deleteByArray($key);
        }

        return $this->instance->delete($key);
    }

    public function getByArray($keyArray)
    {
        if(!is_array($keyArray)) {
            return false;
        }

        $return = [];

        foreach($keyArray as $key) {
            $return[$key] = $this->getByKey($key);
        }

        return $return;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getByKey($key)
    {
        if(empty($key)) {
            return false;
        }

        return $this->instance->get($key);
    }

    /**
     * @return array
     */
    public function getAllKeys()
    {
        return $this->instance->getAllKeys();
    }

    /**
     * @param array $keyArray
     * @param array $valArray
     * @param int $exp
     * @return bool
     */
    public function setByArray($keyArray, $valArray, $exp = 300)
    {
        if((!is_array($keyArray) || !is_array($valArray))
            || (count($keyArray) != count($valArray))) {
            return false;
        }

        for($i = 0; $i < count($keyArray); $i++) {
            $this->setByKey($keyArray[$i], $valArray[$i], $exp);
        }

        return true;
    }

    /**
     * @param string $key
     * @param mixed $val
     * @param int $exp
     * @return bool
     */
    public function setByKey($key, $val, $exp = 300)
    {
        if(empty($key) || empty($val)) {
            return false;
        }

        return $this->instance->set($key, $val, $exp);
    }


}