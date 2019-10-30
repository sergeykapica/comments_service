<?if(!empty($arResult['COMMENTS_LIST'])):?>
    <?foreach($arResult['COMMENTS_LIST'] as $comment):?>
        <section class="comments-item">
            <div class="comments-item-head">
                <div class="comments-icon rounded-circle" style="background-image: url(<?=( $comment['USER_PHOTO'] != '' ? '/sources/images/users-images/' . $comment['USER_PHOTO'] : '/sources/images/users-images/default.png' );?>);"></div>
                <div class="comments-head-data">
                    <div class="head-data head-data-headline"><?=$comment['USER_NAME'];?></div>
                    <div class="head-data head-data-date"><?=date('d.m.Y H:i:s', $comment['COMMENT_DATE']);?></div>
                </div>
                <button data-comment-id="<?=$comment['ID'];?>" class="comments-reply-button btn btn-info">
                    <div class="comments-reply-text">Цитировать</div>
                    <div class="comments-reply-icon"></div>
                </button>
            </div>
            <div class="comments-item-content"><?=$comment['USER_COMMENT_TEXT'];?></div>

            <?
                if($comment['USER_ATTACHMENT_PHOTOS'] !== '')
                {
                    $comment['USER_ATTACHMENT_PHOTOS'] = explode(',', $comment['USER_ATTACHMENT_PHOTOS']);
                }
            ?>

            <?if(is_array($comment['USER_ATTACHMENT_PHOTOS'])):?>
                <div class="comments-attachment-photos">
                    <?foreach($comment['USER_ATTACHMENT_PHOTOS'] as $photoKey => $photoValue):?>
                            <div class="comments-attachment-photo <?=( ( ( $photoKey + 1 ) % 2 ) !== 0 ? 'attachment-photo-indentation' : '' );?>" style="background-image: url(/sources/images/other-images/<?=trim($photoValue);?>);"></div>
                    <?endforeach;?>
                </div>
            <?endif;?>

            <?// reply comments list one level ?>

            <?if(isset($comment['REPLY_COMMENTS_LIST'])):?>
                <div class="reply-comments-wrapper">
                    <?foreach($comment['REPLY_COMMENTS_LIST'] as $replyComment):?>
                        <section class="comments-item">
                            <div class="comments-item-head">
                                <div class="comments-icon rounded-circle" style="background-image: url(<?=( $replyComment['USER_PHOTO'] != '' ? '/sources/images/users-images/' . $replyComment['USER_PHOTO'] : '/sources/images/users-images/default.png' );?>);"></div>
                                <div class="comments-head-data">
                                    <div class="head-data head-data-headline"><?=$replyComment['USER_NAME'];?></div>
                                    <div class="head-data head-data-date"><?=date('d.m.Y H:i:s', $replyComment['COMMENT_DATE']);?></div>
                                </div>
                            </div>
                            <div class="comments-item-content"><?=$replyComment['USER_COMMENT_TEXT'];?></div>

                            <?
                                if($comment['USER_ATTACHMENT_PHOTOS'] !== '')
                                {
                                    $comment['USER_ATTACHMENT_PHOTOS'] = explode(',', $replyComment['USER_ATTACHMENT_PHOTOS']);
                                }
                            ?>

                            <?if(is_array($replyComment['USER_ATTACHMENT_PHOTOS'])):?>
                                <div class="comments-attachment-photos">
                                    <?foreach($replyComment['USER_ATTACHMENT_PHOTOS'] as $photoKey => $photoValue):?>
                                            <div class="comments-attachment-photo <?=( ( ( $photoKey + 1 ) % 2 ) !== 0 ? 'attachment-photo-indentation' : '' );?>" style="background-image: url(/sources/images/other-images/<?=trim($photoValue);?>);"></div>
                                    <?endforeach;?>
                                </div>
                            <?endif;?>
                        </section>
                    <?endforeach;?>
                </div>
            <?endif;?>
        </section>
    <?endforeach;?>
    <div class="clear-fix-separator"></div>
    <?=$arResult['PAGINATION_STRING'];?>
<?else:?>
    <div class="comments-list-empty">Комментарии отсутствуют</div>
<?endif;?>