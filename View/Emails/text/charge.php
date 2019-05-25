<?php echo $Mypage['name'] ?>様

いつも<?php echo $mailConfig['site_name'] ?>をご利用いただき、ありがとうございます。

ビットキャッシュによる、ポイントご購入が完了しましたので、お知らせいたします。


日時：<?php echo $PointBook['created']."\n" ?>
金額：<?php echo number_format($PointBook['charge']).'円'."\n" ?>
決済番号：<?php echo $PointBook['id']."\n" ?>

会員番号：<?php echo $PointBook['mypage_id']."\n" ?>
現在のポイント：<?php echo number_format($PointBook['point_balance'])."\n" ?>



---

マイページログイン
　<?php echo $mailConfig['site_url'] ?>members/mypages/login

お問合せ
　<?php echo $mailConfig['site_url'] ?>contact/

不具合報告
　<?php echo $mailConfig['site_url'] ?>error_report/

---
　<?php echo $mailConfig['site_name'] ?>　
　<?php echo $mailConfig['site_url'] ?>　
　Mail:<?php echo $mailConfig['site_email'] ?>　