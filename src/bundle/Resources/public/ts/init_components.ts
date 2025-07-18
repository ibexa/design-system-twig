import InputText from './components/ui/InputText';

const inputTextContainers = document.querySelectorAll<HTMLElement>('.ids-input-text:not([custom-init])');

inputTextContainers.forEach((inputTextContainer: HTMLElement) => {
    const inputTextInstance = new InputText(inputTextContainer);

    inputTextInstance.init();
});
