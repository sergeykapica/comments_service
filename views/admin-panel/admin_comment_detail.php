<?if(!empty($arResult['COMMENT_DATA'])):?>
    <div class="comment-detail-wrapper">
        <form action="/admin_comment_edit_handler" method="post" class="comment-edit-form">
            <section class="comments-item">
                <div class="comments-item-head">
                    <div class="comments-icon rounded-circle" style="background-image: url(<?=( $arResult['COMMENT_DATA']['USER_PHOTO'] != '' ? '/sources/images/users-images/' . $arResult['COMMENT_DATA']['USER_PHOTO'] : '/sources/images/users-images/default.png' );?>);"></div>
                    <div class="comments-head-data">
                        <div class="head-data head-data-headline form-input" contenteditable="true"><?=$arResult['COMMENT_DATA']['USER_NAME'];?></div>
                        <input type="hidden" name="USER_NAME" value="<?=$arResult['COMMENT_DATA']['USER_NAME'];?>" class="form-input-original"/>
                        <div class="head-data head-data-date"><?=date('d.m.Y H:i:s', $arResult['COMMENT_DATA']['COMMENT_DATE']);?></div>
                    </div>
                    <div class="comments-email"><?=$arResult['COMMENT_DATA']['USER_EMAIL'];?></div>
                </div>
                <div class="comments-head-data">
                    <div class="comments-item-content form-input" contenteditable="true"><?=$arResult['COMMENT_DATA']['USER_COMMENT_TEXT'];?></div>
                    <input type="hidden" name="USER_COMMENT_TEXT" value="<?=$arResult['COMMENT_DATA']['USER_COMMENT_TEXT'];?>" class="form-input-original"/>
                </div>
                <?if(is_array($arResult['COMMENT_DATA']['USER_ATTACHMENT_PHOTOS'])):?>
                    <div class="comments-attachment-photos">
                        <?foreach($arResult['COMMENT_DATA']['USER_ATTACHMENT_PHOTOS'] as $photoKey => $photoValue):?>
                                <div class="comments-attachment-photo <?=( ( ( $photoKey + 1 ) % 3 ) !== 0 ? 'attachment-photo-indentation' : '' );?>" style="background-image: url(/sources/images/other-images/<?=trim($photoValue);?>);"></div>
                        <?endforeach;?>
                    </div>
                <?endif;?>
            </section>
            <section class="comments-extra-options">
                <label>
                    <div class="text-align-middle">
                        <span><?=( $arResult['COMMENT_DATA']['COMMENT_STATUS'] == 0 || $arResult['COMMENT_DATA']['COMMENT_STATUS'] == 2 ? 'Принять' : 'Отклонить' );?></span>
                    </div>
                    <div class="special-checked-wrapper">
                        <label>
                            <input type="checkbox" name="COMMENT_STATUS" value="<?=( $arResult['COMMENT_DATA']['COMMENT_STATUS'] == 1 ? 2 : 1 );?>" class="special-checked-input"/>
                            <div class="special-checked"></div>
                        </label>
                    </div> 
                </label>
            </section>
            <section class="form-submit-wrapper">
                <input type="hidden" name="COMMENT_ID" value="<?=$arResult['COMMENT_DATA']['ID'];?>"/>
                <input type="hidden" name="CURRENT_PAGE" value="<?=$arResult['CURRENT_PAGE'];?>"/>
                <button type="submit" class="btn btn-primary">Изменить</button>
            </section>
            <?if(isset($arResult['UPDATE_COMMENT'])):?>
                <section class="form-result">
                    <?if($arResult['UPDATE_COMMENT'] == 1):?>
                        <div class="validator-success">
                            <div class="validator-success-icon"></div>
                            <div class="validator-success-text">Комментарий успешно обновлён</div>
                        </div>
                    <?else:?>
                        <div class="validator-error">
                            <div class="validator-error-icon"></div>
                            <div class="validator-error-text">При обновлении комментария возникла ошибка</div>
                        </div>
                    <?endif;?>
                </section>
            <?endif;?>
        </form>
    </div>

    <script type="text/javascript" src="/sources/js/validate-form.js"></script>

    <script type="text/javascript">
        $(window).ready(function()
        {
            var OValidator = new oValidator($('.form-input-original'), {
            
                'USER_LOGIN':
                {
                    minSymbolLength: 3,
                    maxSymbolLength: 100
                },

                'USER_COMMENT_TEXT':
                {
                    minSymbolLength: 3,
                    maxSymbolLength: 100
                },
            }, 'comments-head-data');
            
            let commentEditForm = $('.comment-edit-form');
            
            commentEditForm.on('submit', function()
            {
                var thisForm = $(this);
                var errors = thisForm.find('.validator-error');

                if(errors[0] !== undefined)
                {
                    errors.remove();
                }

                OValidator.checkRequiredInputs();

                errors = thisForm.find('.validator-error');

                if(errors[0] !== undefined)
                {
                    return false;
                }     
            });
            
            let formInput = $('.form-input');
            
            formInput.on('input', function()
            {
                let thisInput = $(this);
                
                thisInput.parent().find('.form-input-original').val(thisInput.text());
            });
        });
    </script>
<?else:?>
    <div class="comments-list-empty">Данные комментария отсутствуют</div>
<?endif;?>