<?php

namespace EasyPass;

class EasyPass {
    
    private $_config; 
    static private $_defaultConfig = null;
    
    /**
     *
     * @param type $config
     * @return \EasyPass\EasyPass 
     */
    static public function forge($config = array()){
        return new static($config);
        
    }
    
    public function __construct($config = array()) {
        if(self::$_defaultConfig == NULL){
            self::$_defaultConfig = \Fuel\Core\Config::load('easypass', true);
        }
        $this->_config = array_merge(self::$_defaultConfig, (array)$config);
    }

    public function login($username, $password) {
        if ($this->_getRealPass($username) === $password) {//パスワード確認
            $time = time();
            $this->_set("USERNAME", $username);
            $this->_set("PASSWORD", $this->_localPassword($password, $time));
            $this->_set("TIME", $time);
            return true;
        } else {
            $this->_destory();
            return false;
        }
    }

    public function isAuthed() {
        
                
        $username = $this->_get('USERNAME');
        $time = $this->_get('TIME');
        $localPassword = $this->_get('PASSWORD');
        
        
        if ($localPassword == $this->_localPassword($this->_getRealPass($username), $time)) {
            return true;
        } else {
            return false;
        }
    }

    public function logout() {
        $this->_destory();
    }

    protected function _localPassword($password, $time) {
        return md5($password . 'up' . $time);
    }

    protected function _set($key, $val) {
        $session = \Fuel\Core\Session::get($this->_config['session_key']);
        $session[$key] = $val;
        \Fuel\Core\Session::set($this->_config['session_key'], $session);
    }

    protected function _get($key) {
        $session = \Fuel\Core\Session::get($this->_config['session_key']);
        return isset($session[$key]) ? $session[$key] : NULL;
    }

    protected function _destory() {
        \Fuel\Core\Session::destroy();
    }
    
    protected function _getRealPass($username){
        if(isset( $this->_config['users'][$username])){
            return  $this->_config['users'][$username];
        }
        return false;
    }

}