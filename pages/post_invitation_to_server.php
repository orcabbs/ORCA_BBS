<?php
error_reporting(0);
function characet($data){
    if( !empty($data) ){
        $fileType = mb_detect_encoding($data , array('UTF-8','GBK','LATIN1','BIG5')) ;
        if( $fileType != 'UTF-8'){
            $data = mb_convert_encoding($data ,'utf-8' , $fileType);
        }
    }
    return $data;
}
function change_utf8($str) {
    return mb_convert_encoding($str, 'utf-8', 'gbk');
}
function change_gbk($str) {
    return mb_convert_encoding($str, 'gbk', 'utf-8');
}
@$forum_id=$_GET['forum_id'];

try{
    $conn=new mysqli('qdm178341200.my3w.com','qdm178341200','Orcabbs666','qdm178341200_db');
}catch(Exception $e){
    echo $e->getMessage();
}
$title=change_gbk($_POST['post_title']);
$content=change_gbk($_POST['post_content']);
//echo $title.' '.$content.' !! '.$forum_id;
session_start();
// 首先判断Cookie是否有记住了用户信息
if (isset($_COOKIE['user_email'])) {
    # 若记住了用户信息,则直接传给Session
    $_SESSION['user_email'] = $_COOKIE['user_email'];
    $_SESSION['islogin'] = 1;
}
if (isset($_SESSION['islogin'])) {
    // 若已经登录
    $user_email_tmp = $_SESSION['user_email'];
    $result_tmp = $conn->query("select * from bbs_account where bbs_account.user_email='$user_email_tmp'");
    $row_tmp = $result_tmp->fetch_array();
    $user_name_tmp = characet($row_tmp['user_name']);
    $conn->query("insert into bbs_list(forum_id,builder_email,title) values ('$forum_id','$user_email_tmp','$title')");
    $max_id_list=$conn->query("select max(bbs_list.post_id) as max_id from bbs_list");
    $max_id=($max_id_list->fetch_array())['max_id'];
    $conn->query("insert into bbs_content(post_id,floor_user_email,floor_content) values ('$max_id','$user_email_tmp','$content')");

    $conn->close();
    echo "<script type=\"text/javascript\">setTimeout(\"window.location.href='./forum_demo.php?forum_id=${forum_id}'\",0);</script>";
}
?>