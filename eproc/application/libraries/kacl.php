<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package   eProcurement Application
 * @author    eproc2025
 * @since     25. Version 3.1
 * 
 */
 
require_once 'kloader.php';
require_once 'kzendcache.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Acl
 *
 * @author user
 */
class kacl {
    protected $aclChunk;
    protected $CI;


    function __construct() {
        kloader::load('Zend_Acl');
        
        $this->LoadFromCache();
        if(!is_object($this->aclChunk))
        {
            $this->loadFromDatabase ();
            $this->setToCache();
        }
        else if(get_class($this->aclChunk)!='Zend_Acl')
        {
            $this->loadFromDatabase ();
            $this->setToCache();
        }
    }
    
    private function LoadFromCache(){

        $cc = kzendcache::_init();
        $this->aclChunk = $cc->load('kacl');
    }
     private function loadFromDatabase(){
//        load prereq
        $this->CI =& get_instance();
        $this->CI->load->model('model_kaclrole');
        $this->CI->load->model('model_kaclresource');
        $this->CI->load->model('model_kaclpriv');
        
//        get the ACL instance
        $this->aclChunk = new Zend_Acl;
        
//        //set the role
        foreach ($this->CI->model_kaclrole->getAllParentFirst()->result_array as $v) //getAllParentFirst
        {
            $this->aclChunk->addRole($v['ROLE_ID'],$v['ROLE_PARENT']);
        }
        
        //set the resource
        foreach ($this->CI->model_kaclresource->getAllParentFirst()->result_array as $v)
        {
            $this->aclChunk->addResource($v['RESC_ID'],$v['RESC_PARENT']);
        }
        //SET THE PREVILEGE
        foreach ($this->CI->model_kaclpriv->getAllDecoded()->result_array as $v)
        {
            $this->aclChunk->$v['ALLOW_YN']($v['ROLE_ID'],$v['RESC_ID'],$v['ACTION']);
        }
         
//        $this->aclChunk
//                ->addRole('guest')
//                ->addRole('admin','guest')
//                ->addResource('xmodule')
//                ->addResource('xcontroller','xmodule')
//                ->addResource('ycontroller','xmodule')
//                ->addResource('ocontroller')
//                ->allow('admin', 'xmodule')
//                ->deny('guest','xmodule')
//                ->allow('guest','xcontroller')
//                ;
    }
    private function setToCache(){
        $cc = kzendcache::_init();//get the chaceh object
        $cc->remove('kacl');
        $cc->save($this->aclChunk,'kacl');
    }
    
   function isAllow($role,$resc,$action = '--ALL'){
       
       if ($this->aclChunk->has($resc) and $this->aclChunk->hasRole($role))
           return $this->aclChunk->isAllowed($role,$resc,$action);
       else
           return false;
   }
   function resetCache()
   {
       $this->loadFromDatabase ();
       $this->setToCache();
//       print_r( $this->aclChunk->getResources());
        return TRUE;
   }
   
   function has($q)
   {
       return $this->aclChunk->has($q);
   }
   function hasRole($q)
   {
       return $this->aclChunk->hasRole($q);
   }
}

?>
