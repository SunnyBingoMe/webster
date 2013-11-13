<?php 
    $S = isset($_POST['S'])?$_POST['S']:'1800';
    $l = isset($_POST['l'])?$_POST['l']:'5.2';
    $A = isset($_POST['A'])?$_POST['A']:'4';
    $trafficNorth   = isset($_POST['trafficNorth'])?$_POST['trafficNorth']:'600';
    $trafficWest    = isset($_POST['trafficWest'] )?$_POST['trafficWest']:'1200';;
    $trafficEast    = isset($_POST['trafficEast'] )?$_POST['trafficEast']:'800';;
    $trafficSouth   = isset($_POST['trafficSouth'])?$_POST['trafficSouth']:'800';;
?>
<!DOCTYPE html>
<html>

<head>
    <title>Webster 配时示例</title>
    <meta charset='utf-8'>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <link href="http://sunnyboy.me/photos/_.jpg" id="favicon" rel="shortcut icon"> 
    <style>
        div{text-align:center; border:0px; margin:-1.9px; padding:0px; height:20px; display:inline-block}
        div.green{background:green}
        div.red{background:red}
        div.yellow{background:yellow}
    </style>
</head>

<body>
    <a target='_blank' href='https://github.com/SunnyBingoMe/webster'>点击这里</a>可以查看源代码.</br></br>
    参数配置(两相无全红):<br><br>
    <form method="POST">

        饱和交通量 S &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="text" name="S" value="<?= $S ?>">veh/h<br><br>
        每相信号损失时间 l&nbsp;
            <input type="text" name="l" value="<?= $l ?>">s<br><br>
        黄灯时间 A&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="text" name="A" value="<?= $A ?>">s<br><br>

        <table border="1" >
            <th colspan="3">各入口总车流量</th>
            <tr align="center">
                <td> </td>
                <td> <input type="text" name="trafficNorth" value="<?= $trafficNorth ?>"> </td>
                <td> </td>
            </tr>
            
            <tr>
                <td> <input type="text" name="trafficWest" value="<?= $trafficWest ?>"> </td>
                <td><img src="cross.png"></td>
                <td> <input type="text" name="trafficEast" value="<?= $trafficEast ?>"> </td>
            </tr>

            <tr align="center">
                <td> </td>
                <td> <input type="text" name="trafficSouth" value="<?= $trafficSouth ?>"> </td>
                <td> </td>
            </tr>
        </table>
        (东西通行为第一相, 南北通行为第二相) <input type="SUBMIT" value="进行计算"> <a href=".">重置参数</a>
    </form>

<?php if(!isset($_POST)) { ?> </body></html> <?php exit; } ?>

<?php 

    /* 临界车道交通流量 */
    $q1     = max($trafficWest, $trafficEast)/2;
    $q2     = max($trafficNorth, $trafficSouth)/2;

    /* I.  计算最佳周期长度 */
    $L      = 2 * $l;                                       //总损失时间
    $y1     = $q1/$S;
    $y2     = $q2/$S;
    $Y      = $y1 + $y2;
    $C0     = round((1.5 * $L + 5)/(1 - $Y));
    $C0     = max(min(120, $C0), 25);                       //上下限

    /* II. 计算有效绿灯时间 */
    $Ge     = $C0 - $L;
    $ge1    = $Ge * $y1/$Y;
    $ge2    = $Ge * $y2/$Y;

    /* III.    计算各相实际显示绿灯时间 */
    $G1     = $ge1 - $A + $l;
    $G2     = $ge2 - $A + $l;
    
    /* 各相(最小)绿灯时间应按临界车道交通流量作正比例分配。*/
    $Q      = $q1 + $q2;
    $Gt     = $C0 - 2*$A - 2*$l;
    $G1m    = $Gt * $q1/$Q;
    $G2m    = $Gt * $q2/$Q;
    
    /* 损失时间应归入绿灯时间内 */
    $G1 = round($G1m + $l);
    $G2 = round($G2m + $l);

    /* V.   画出这个两相信号的相位图 */
    //echo "$G1, $A; $G2, $A";
?>
    <br/>
    最佳周期长度 C = <?= $C0 ?>s

    <br/> <br/>
    <?php $extendRatio = 10; ?>
    第一相位:
    <div class="green" style="width:<?= $G1*$extendRatio ?>px"><?= $G1 ?>s</div>
    <div class="yellow" style="width:<?= $A*$extendRatio ?>px"><?= $A ?>s</div>
    <div class="red" style="width:<?= ($G2+$A)*$extendRatio ?>px"><?= $G2+$A ?>s</div>
    <br/> <br/>
    第二相位:
    <div class="red" style="width:<?= ($G1+$A)*$extendRatio ?>px"><?= $G1+$A ?>s</div>
    <div class="green" style="width:<?= $G2*$extendRatio ?>px"><?= $G2 ?>s</div>
    <div class="yellow" style="width:<?= $A*$extendRatio ?>px"><?= $A ?>s</div>
</body>

</html> 

