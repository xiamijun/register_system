<?php
class User{
    private $uid;
    private $fields;

    public function __construct()
    {
        $this->uid=null;
        $this->fields=array('username'=>'',
                            'password'=>'',
                            'emailAddr'=>'',
                            'isActive'=>false
                            );
    }

    public function __get($field)
    {
        if ($field=='userId'){
            return $this->uid;
        }else{
            return $this->fields['field'];
        }
    }

    public function __set($field, $value)
    {
        if (array_key_exists($field,$this->fields)){
            $this->fields['field']=$value;
        }
    }

    //验证用户名
    public static function validateUsername($username){
        return preg_match('/^[A-Z0-9]{2,20}$/i',$username);
    }

    //验证邮箱
    public static function validateEmailAddr($email){
        return filter_var($email,FILTER_VALIDATE_EMAIL);
    }

    //从数据库提取记录并填充到有关对象
    public static function getById($user_id){
        $user=new User();
        $query=sprintf("select USERNAME,PASSWORD,EMAIL_ADDR,IS_ACTIVE from worx_user where USER_ID=$user_id");
        $result=mysqli_query($GLOBALS['DB'],$query);
        if (mysqli_num_rows($result)){
            $row=mysqli_fetch_assoc($result);
            $user->username=$row['USERNAME'];
            $user->password=$row['PASSWORD'];
            $user->emailaddr=$row['EMAIL_ADDR'];
            $user->isActive=$row['IS_ACTIVE'];
            $user->uid=$user_id;
        }
        mysqli_free_result($result);
        return $user;
    }

    //从数据库提取记录并填充到有关对象
    public static function getByUsername($username){
        $user=new User();
        $query=sprintf("select USERNAME,PASSWORD,EMAIL_ADDR,IS_ACTIVE from worx_user where USERNAME=$username");
        $result=mysqli_query($GLOBALS['DB'],$query);
        if (mysqli_num_rows($result)){
            $row=mysqli_fetch_assoc($result);
            $user->uid=$row['USER_ID'];
            $user->password=$row['PASSWORD'];
            $user->emailaddr=$row['EMAIL_ADDR'];
            $user->isActive=$row['IS_ACTIVE'];
            $user->username=$username;
        }
        mysqli_free_result($result);
        return $user;
    }

    //写入数据库
    public function save(){
        if ($this->uid){
            $query=sprintf("update wrox_user set USERNAME='{$this->username}',PASSWORD='{$this->password}',EMAIL_ADDR='{$this->emailAddr}',IS_ACTIVE={$this->isActive} where USER_ID=$this->userId");
            return mysqli_query($GLOBALS['DB'],$query);
        }else{
            $query=sprintf("insert into wrox_user (USERNAME,PASSWORD,EMAIL_ADDR,IS_ACTIVE) VALUES ('{$this->username}','{$this->password}','{$this->emailAddr}',{$this->isActive})");
            if (mysqli_query($GLOBALS['DB'],$query)){
                $this->uid=mysqli_insert_id($GLOBALS['DB']);
                return true;
            }else{
                return false;
            }
        }
    }

    //激活
    public function setInactive(){
        $this->isActive=false;
        $this->save();  //确保已保存

        $token=random_text(5);
        $query="select TOKRN from wrox_pending where USER_ID={$this->uid} and TOKEN='{$token}'";
        $result=mysqli_query($GLOBALS['DB'],$query);
        if (!mysqli_num_rows($result)){
            mysqli_free_result($result);
            return false;
        }else{
            mysqli_free_result($result);
            $query="delete from wrox_pending where USER_ID={$this->uid} and TOKEN='{$token}'";
            if (!mysqli_query($GLOBALS['DB'],$query)){
                return false;
            }else{
                $this->isActive=true;
                return $this->save();
            }
        }
    }
}