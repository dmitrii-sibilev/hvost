BX.ready(function () {

    let timer = setInterval(() => {
        let input = document.querySelector('[data-cid="UF_CRM_1_DURATION"]').querySelector('[name="UF_CRM_1_DURATION"]')
        if (input) {
            var result = new BX.MaskedInput({
                mask: '9:99', // устанавливаем маску
                input: input,
                placeholder: '_' // символ замены +7 ___ ___ __ __
            });
            clearInterval(timer)
        }
    }, 1000)

    BX.addCustomEvent(window, "onBeforeSubmit", (el, options) => {
        let parent = document.querySelector('[data-cid="UF_CRM_1_DURATION"]');
        let input = parent.querySelector('[name="UF_CRM_1_DURATION"]')
        let error = parent.querySelector('.ui-entity-editor-field-error-text');
        if (error) error.remove();
        if (input) {
            let time = input.value.split(':');

            if ((!Number(time[0]) && time[0] !== '0') || Number(time[0]) < 0 || (!Number(time[1]) && time[1] !== '00') || Number(time[1]) < 0 || Number(time[1]) > 59) {
                let newDiv = document.createElement("div");
                newDiv.innerHTML = "Проверьте правильность ввода длительности!"
                newDiv.classList.add('ui-entity-editor-field-error-text');
                parent.append(newDiv);
                options.cancel = true;
            }
        }
    })
})
