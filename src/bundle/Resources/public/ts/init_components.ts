import Accordion from './components/accordion';
import InputText from './components/inputs/InputText';

const accordionContainers = document.querySelectorAll<HTMLDivElement>('.ids-accordion:not([custom-init])');

accordionContainers.forEach((accordionContainer: HTMLDivElement) => {
    const accordionInstance = new Accordion(accordionContainer);

    accordionInstance.init();
});

const inputTextContainers = document.querySelectorAll<HTMLDivElement>('.ids-input-text:not([custom-init])');

inputTextContainers.forEach((inputTextContainer: HTMLDivElement) => {
    const inputTextInstance = new InputText(inputTextContainer);

    inputTextInstance.init();
});
