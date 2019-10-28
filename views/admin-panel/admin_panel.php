<div class="admin-panel-head">
    <h1>Панель администратора</h1>
    <h2 class="text-lightgrey-color">Работа с комментариями</h2>
</div>
<div class="admin-panel-content">
    <div class="comments-table-wrapper">
        <?if(!empty($arResult['COMMENTS_LIST'])):?>
            <table class="table table-hover">
                <thead>
                    <tr class="table-tr table-tr-th table-info">
                        <th class="table-th checked-td"></th>
                        <th class="table-th" scope="col">#</th>
                        <th class="table-th" scope="col">Картинка</th>
                        <th class="table-th" scope="col">Имя</th>
                        <th class="table-th" scope="col">Статус</th>
                        <th class="table-th" scope="col">Дата</th>
                    </tr>
                </thead>
                <tbody>
                    <?foreach($arResult['COMMENTS_LIST'] as $comment):?>
                        <tr class="table-tr table-tr-td">
                            <td class="table-td checked-td">
                                <div class="special-checked-wrapper">
                                    <label>
                                        <input type="checkbox" value="<?=$comment['ID'];?>" class="special-checked-input"/>
                                        <div class="special-checked"></div>
                                    </label>
                                </div> 
                            </td>
                            <td class="table-td" scope="row"><?=$comment['ID'];?></td>
                            <td class="table-td">
                                <div class="user-photo" style="background-image: url(<?=( $comment['USER_PHOTO'] != '' ? '/sources/images/users-images/' . $comment['USER_PHOTO'] : '/sources/images/users-images/default.png' );?>);"></div>
                            </td>
                            <td class="table-td"><?=$comment['USER_NAME'];?></td>
                            <td class="table-td">
                                <?if($comment['COMMENT_STATUS'] == 0):?>
                                    В ожидании принятия
                                <?elseif($comment['COMMENT_STATUS'] == 1):?>
                                    Принят
                                <?else:?>
                                    Отклонен
                                <?endif;?>
                            </td>
                            <td class="table-td"><?=date('d.m.Y H:i:s', $comment['COMMENT_DATE']);?></td>
                            <td class="table-tr-url">
                                <a href="/admin_comment_detail?COMMENT_ID=<?=$comment['ID'];?>"></a>
                            </td>
                        </tr>
                    <?endforeach;?>
                </tbody>
            </table>
        <?else:?>
            <div class="comments-list-empty">Комментарии отсутствуют</div>
        <?endif;?>
    </div>
    <?/*<table class="comments-table">
        <tbody class="comments-table-body">
            <tr class="comments-table-tr">
                <td class="comments-table-td">Имя</td>
                <td class="comments-table-td">Емейл</td>
            </tr>
        </tbody>
    </table>*/?>
</div>