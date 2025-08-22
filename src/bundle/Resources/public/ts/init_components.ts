import Accordion from './components/accordion';
import Checkbox from './components/inputs/Checkbox';
import InputText from './components/inputs/InputText';
import ThreeStateCheckbox from './components/inputs/ThreeStateCheckbox';

const accordionContainers = document.querySelectorAll<HTMLDivElement>('.ids-accordion:not([custom-init])');

accordionContainers.forEach((accordionContainer: HTMLDivElement) => {
    const accordionInstance = new Accordion(accordionContainer);

    accordionInstance.init();
});

const checkboxContainers = document.querySelectorAll<HTMLDivElement>('.ids-checkbox:not([custom-init])');

checkboxContainers.forEach((checkboxContainer: HTMLDivElement) => {
    const checkboxInstance = new Checkbox(checkboxContainer);

    checkboxInstance.init();
});

const inputTextContainers = document.querySelectorAll<HTMLDivElement>('.ids-input-text:not([custom-init])');

inputTextContainers.forEach((inputTextContainer: HTMLDivElement) => {
    const inputTextInstance = new InputText(inputTextContainer);

    inputTextInstance.init();
});

const threeStateCheckboxContainers = document.querySelectorAll<HTMLDivElement>('.ids-checkbox.ids-checkbox--three-state:not([custom-init])');

threeStateCheckboxContainers.forEach((threeStateCheckboxContainer: HTMLDivElement) => {
    const threeStateCheckboxInstance = new ThreeStateCheckbox(threeStateCheckboxContainer);

    threeStateCheckboxInstance.init();
});
