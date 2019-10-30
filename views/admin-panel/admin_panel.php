<div class="admin-panel-head">
    <div class="panel-head-controls">
        <h1>Панель администратора</h1>
        <h2 class="text-lightgrey-color">Работа с комментариями</h2>
        <a href="/admin_logout" title="Выход из панели администратора" class="admin-logout-button"></a>
    </div>
</div>
<div class="admin-panel-content">
    <div class="comments-table-wrapper">
        <?if(!empty($arResult['COMMENTS_LIST'])):?>
            <form action="/admin_comments_delete_handler" method="post" id="comments-delete-form">
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
                                            <input type="checkbox" name="COMMENT_ID[]" value="<?=$comment['ID'];?>" class="special-checked-input delete-comment-id"/>
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
                <?=$arResult['PAGINATION_STRING'];?>
                <div class="comments-actions-wrapper">
                    <input type="hidden" name="CURRENT_PAGE" value="<?=$arResult['CURRENT_PAGE'];?>"/>
                    <button type="submit" class="btn btn-info actions-wrapper-blocks">Удалить комментарии</button>
                    <div class="actions-result-message actions-wrapper-blocks">
                        <?if(isset($arResult['DELETE_COMMENTS'])):?>
                            <?if($arResult['DELETE_COMMENTS'] == 1):?>
                                <div class="validator-success action-result">
                                    <div class="validator-success-icon"></div>
                                    <div class="validator-success-text">Комментарии успешно удалены</div>
                                </div>
                            <?else:?>
                                <div class="validator-error action-result">
                                    <div class="validator-error-icon"></div>
                                    <div class="validator-error-text">При удалении комментариев возникла ошибка</div>
                                </div>
                            <?endif;?>
                        <?endif;?>
                    </div>
                </div>
            </form>
        
            <script type="text/javascript">
                $(window).ready(function()
                {
                    function checkCheckboxInput(inputs)
                    {
                        let isCheckedOneInput = false;
                        
                        inputs.each(function(i)
                        {
                            if(inputs.eq(i).prop('checked') === true)
                            {
                                isCheckedOneInput = true;
                            }
                        });
                        
                        return isCheckedOneInput;
                    }
                    
                    let commentsDeleteForm = $('#comments-delete-form');
                    
                    commentsDeleteForm.on('submit', function()
                    {
                        let deleteCommentID = $('.delete-comment-id');
                        let actionsResultMessage = $('.actions-result-message');
                        
                        let validatorError = actionsResultMessage.find('.validator-error');
                        
                        if(validatorError[0] !== undefined)
                        {
                            validatorError.remove();
                        }
                        
                        if(!checkCheckboxInput(deleteCommentID))
                        {  
                            let errorMessageElement = document.createElement('div');
                            errorMessageElement.classList.add('validator-error');
                            errorMessageElement.innerHTML = 
                            `
                            <div class="validator-error-icon"></div>
                            <div class="validator-error-text">Выберите хотя бы один комментарий</div>
                            `;
                            
                            actionsResultMessage.append(errorMessageElement);
                            
                            $(errorMessageElement).animate({
                                right: '0%'
                            },
                            {
                                duration: 500
                            });

                            return false;
                        }
                    });
                });
            </script>
        <?else:?>
            <div class="comments-list-empty">Комментарии отсутствуют</div>
        <?endif;?>
    </div>
</div>