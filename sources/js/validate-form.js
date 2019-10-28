(function()
{
    function OValidator(checkedInputs, params, topElementWrapperClass, textAreaEnable)
    {
        this.checkedInputs = checkedInputs;
        this.params = params;
        this.topElementWrapperClass = topElementWrapperClass;
        this.textAreaEnable = textAreaEnable;
        
        this.setInputsHandler();
    };
    
    OValidator.prototype = 
    {
        setErrorNotification: function(element, message)
        {
            var messageElement = document.createElement('div');
            messageElement.className = 'validator-error';
            messageElement.innerHTML =
            `
            <div class="validator-error-icon"></div>
            <div class="validator-error-text">` + message + `</div>
            `;
            
            if(this.textAreaEnable !== undefined && element.hasClass(this.textAreaEnable))
            {
                messageElement.style.minHeight = '38px';
            }
            
            var elementWrapper = this.getTopWrapperElement(element, this.topElementWrapperClass);
            
            elementWrapper.append(messageElement);
            
            $(messageElement).animate({
                right: '-100%'
            },
            {
                duration: 500
            });
        },
        
        setSuccessNotification: function(element, message)
        {
            var messageElement = document.createElement('div');
            messageElement.className = 'validator-success';
            messageElement.innerHTML =
            `
            <div class="validator-success-icon"></div>
            <div class="validator-success-text">` + message + `</div>
            `;
            
            if(this.textAreaEnable !== undefined && element.hasClass(this.textAreaEnable))
            {
                messageElement.style.minHeight = '38px';
            }
            
            var elementWrapper = this.getTopWrapperElement(element, this.topElementWrapperClass);
            
            elementWrapper.append(messageElement);
            
            $(messageElement).animate({
                right: '-100%'
            },
            {
                duration: 500
            });
            
            return messageElement;
        },
        
        checkRequiredInputs: function()
        {
            var thisScope = this;
            
            thisScope.checkedInputs.each(function(i)
            {
                if(thisScope.params[thisScope.checkedInputs.eq(i).attr('name')] !== undefined)
                {
                    var params = thisScope.params[thisScope.checkedInputs.eq(i).attr('name')];
                    var inputElement = thisScope.checkedInputs.eq(i);
                    
                    if(inputElement[0].value !== undefined)
                    {
                        for(var p in params)
                        {
                            if(p == 'minSymbolLength')
                            {
                                if(inputElement.val().length < params[p])
                                {
                                    thisScope.setErrorNotification(inputElement, 'Количество введённых символов слишком мало');
                                }
                            }
                            else if(p == 'maxSymbolLength')
                            {
                                if(inputElement.val().length > params[p])
                                {
                                    thisScope.setErrorNotification(inputElement, 'Количество введённых символов слишком много');
                                }
                            }
                            else if(p == 'isEmail')
                            {
                                if(inputElement.val().match(/.+@.+\..+/) == null)
                                {
                                    thisScope.setErrorNotification(inputElement, 'Введённое значение не является электронной почтой');
                                }
                            }
                        }
                    }
                    else
                    {
                        
                    }
                }
            });
        },
        
        setInputsHandler: function()
        {
            var thisScope = this; 
            
            thisScope.checkedInputs.on('keydown', function()
            {
                var wrapperElement = thisScope.getTopWrapperElement($(this), thisScope.topElementWrapperClass);
                var validatorError = wrapperElement.find('.validator-error');
                
                if(validatorError[0] !== undefined)
                {
                    validatorError.remove();
                }
            });
        },
        
        getTopWrapperElement: function(element, className)
        {
            var element = element;
            
            while(!element.hasClass(className))
            {
                element = element.parent();
            }
            
            return element;
        }
    };
    
    window.oValidator = OValidator;
})();