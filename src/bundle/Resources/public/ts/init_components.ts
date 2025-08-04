import Accordion from './components/accordion';
import FormControlInputText from './components/formControls/InputText';
import InputText from './components/inputs/InputText';

const accordionContainers = document.querySelectorAll<HTMLDivElement>('.ids-accordion:not([custom-init])');

accordionContainers.forEach((accordionContainer: HTMLDivElement) => {
    const accordionInstance = new Accordion(accordionContainer);

    accordionInstance.init();
});

const inputTextContainers = document.querySelectorAll<HTMLDivElement>('.ids-input-text:not([data-ids-custom-init])');

inputTextContainers.forEach((inputTextContainer: HTMLDivElement) => {
    const inputTextInstance = new InputText(inputTextContainer);

    inputTextInstance.init();
});

const formControlInputTextContainers = document.querySelectorAll<HTMLDivElement>(
    '.ids-form-control--input-text:not([data-ids-custom-init])',
);

formControlInputTextContainers.forEach((inputTextContainer: HTMLDivElement) => {
    const inputTextInstance = new FormControlInputText(inputTextContainer);

    inputTextInstance.init();
});
