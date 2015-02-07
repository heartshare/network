<?php
/**
 * @link https://github.com/yxdj
 * @copyright Copyright (c) 2014 xuyuan All rights reserved.
 * @author xuyuan <1184411413@qq.com>
 */

namespace yxdj\network;

// +----------------------------------------------------------------------
// | yxdj:php-cli
// +----------------------------------------------------------------------
// | Copyright (c) 2014 xuyuan All rights reserved.
// +----------------------------------------------------------------------
// | Author: xuyuan
// +----------------------------------------------------------------------

/*
api����һ����ָ��һ̨�����
�п��ܾ��Ǳ��أ��п��ܶ�̨����ÿ̨�����ṩ�Ľӿڲ�һ��
�����api-client����Ҫ��һ��ͳһ�ĵ������
������Ҫ�������ԣ�
1.���ؽӿ�:localhost
2.Զ��api�ӿڣ�api.yuan37.test,
Զ�̽ӿ�Ҳ����ָ���̨����,api.yuan37.test��Ҫ�����ѵĻ�������
client�ṩһ�׻���ȥ���ʸ��ַ���

Api::yuan37('[user:]login',array('name'=>'xuyuan'));

����˵����server,class,method
server:yuan37
class:yuan37/user
method:login
data:array();

*/

abstract class Api{
	public static $classMap=array();
	public static $obj=array();//ʵ�������API����
	
	//��������������Ϊ˽�з�������֤���ʵ����еķ�������__callstatic����
	/*
    ������$server,
    ������$params[0]
    */
    public static function __callstatic($server,$params){
		
		//��������
		list($server,$class,$method,$data)=self::parseSpace($server,$params);

        $className=$class;

		//echo $file;exit;
		if(!isset(self::$obj[$className])){
				self::$obj[$className]=new $className($method);	
		}
		$obj=self::$obj[$className];
		
		
		//��ʼ����ִ�У�����
		return $obj->_init(array(
			'server'=>'yuan37',
			'class'=>'Yuan37',
			'method'=>$method,
			'data'=>$data
			)
		);
	}
	
	
	
	
	
	
	
	//����������
	private static function parseSpace($server,$params){
        
        $class=$params[0];
        $method=$server;
        
        /*
		//$path=ucfirst($server);						//�ռ���
		$type=explode(':',$params[0]);				//������:������
		if(count($type)==1){
			$class=$server;
			$method=$type[0];		
		}else if(count($type)==2){
			$class=$type[0];
			$method=$type[1];		
		}else{
			$class='self';
			$method='error';
		}
		*/
		$data=isset($params[1])?$params[1]:array(); //����	
		
		return array($server,$class,$method,$data);
	}
	
	
	
	//������������
	private static function error(){
		return 'ng';
	}

    
    
	protected $server;
	protected $class;
	protected $method;
	protected $data=array();
	protected $result='';
	
	//abstract function run($method,$before,$after);
	//����һ�������ڵķ���,�ȸ���������ʽӿ�����ʵ��
	public function __call($method,$params){
		if(method_exists($this,'bootstrap')){
			return $this->bootstrap($method);
		}else{	
			return "can't ask a not exists method '{$method}'";
		}
	}
	
	//�������
	public function _init($params){
		$this->result='';
		foreach($params as $key => $value){
			$this->$key=$value;
		}
		$method=$this->method;
		return $this->$method();
	}
	
	//ӳ�䵽����ˣ�����˿ͻ�����ͬ����ķ����ʹ��
	protected function _mapping(){
		$this->data['api']=$this->class.':'.$this->method;
		//ִ�з���
		$http=new Http();
		if($http->postUrl($this->url,$this->data)=='200'){
			return $http->content;
		}else{
			return $http->getDebug();
		}
	}	
	
	
	//����,���,��Щ��ͬ�����ڴ˴���,���ڴ˽��ղ���
	protected function _ask(){
		$http=H();
		if($http->postUrl($this->url,$this->data)=='200'){
			return $http->content;
		}else{
			return $http->getDebug();
		}	
	}
        
    public static function className()
    {
        return get_called_class();
    }       
}
