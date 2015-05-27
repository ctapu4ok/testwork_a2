<a href="?parse=1">Спарись данные</a>
<table border="1">
<?php foreach($Result as $res): ?>
    <tr>
        <td>Описание</td>
        <td>Детали</td>
        <td>Контакты</td>
        <td>Ссылка</td>
    </tr>
    <tr style="vertical-align: top;">
        <td><?=$res['title']?></td>
        <td>
            <table border="1" width="100%">
            <?foreach(unserialize($res['details']) as $detail):?>            
                <tr>
                    <td><?=$detail['param']?></td>
                    <td><?=$detail['value']?></td>
                </tr>
            <?endforeach;?>
        </table>
        </td>
        <td>
            <table border="1" width="100%">
            <?foreach(unserialize($res['contacts']) as $contacts):?>            
                <tr>
                    <td><?=$contacts['param']?></td>
                    <td><?=$contacts['value']?></td>
                </tr>
            <?endforeach;?>
            </table>
        </td>
        <td><?=$res['href']?></td>
    </tr>
<?endforeach;?>
</table>