<div id="authorizate-form-wrapper">
    <form action="/admin_authorizate_handler" method="post" id="authorizate-form">
        <section class="authorizate-form-section">
            <span class="authorizate-form-headline">Авторизация</span>
        </section>
        <section class="authorizate-form-section">
            <div class="col">
                <input type="text" name="USER_LOGIN" class="form-control form-input" placeholder="Логин"/>
            </div>
        </section>
        <section class="authorizate-form-section">
            <div class="col">
                <input type="password" name="USER_PASSWORD" class="form-control form-input" placeholder="Пароль"/>
            </div>
        </section>
        <section class="authorizate-form-section">
            <div class="col">
                <button type="submit" class="btn btn-primary btn-lg btn-block">Войти</button>
            </div>
        </section>
    </form>
</div>

<script type="text/javascript" src="/sources/js/validate-form.js"></script>

<script type="text/javascript">
    $(window).ready(function()
    {
        var OValidator = new oValidator($('.form-input'), {
            
            'USER_LOGIN':
            {
                minSymbolLength: 3,
                maxSymbolLength: 100
            },

            'USER_PASSWORD':
            {
                minSymbolLength: 3,
                maxSymbolLength: 100
            },
        }, 'col');
        
        var authorizateForm = $('#authorizate-form');
        
        authorizateForm.on('submit', function()
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
    });
</script>
        