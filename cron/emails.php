<?
//запускать каждую минуту

$_emaillimit=50;//установить лимит отправляемых писем в минуту


include_once '../config.php';
include_once '../functions.php';


$query=@mysqli_query($mysql,"select * from `emails` order by `id` limit ".$_emaillimit);
while($row=@mysqli_fetch_assoc($query)){
    sendmail(@$row["email"],@$row["subject"],@$row["message"]);
    @mysqli_query($mysql,"delete from `emails` where `id`='".@$row["id"]."'");
}
