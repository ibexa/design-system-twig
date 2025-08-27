import Accordion from './components/accordion';
import Checkbox from './components/inputs/Checkbox';
import FormControlInputText from './components/formControls/InputText';
import InputText from './components/inputs/InputText';
import ThreeStateCheckbox from './components/inputs/ThreeStateCheckbox';

const accordionContainers = document.querySelectorAll<HTMLDivElement>('.ids-accordion:not([data-ids-custom-init])');

accordionContainers.forEach((accordionContainer: HTMLDivElement) => {
    const accordionInstance = new Accordion(accordionContainer);

    accordionInstance.init();
});

const checkboxContainers = document.querySelectorAll<HTMLDivElement>('.ids-checkbox:not([data-ids-custom-init]])');

checkboxContainers.forEach((checkboxContainer: HTMLDivElement) => {
    const checkboxInstance = new Checkbox(checkboxContainer);

    checkboxInstance.init();
});

const formControlInputTextContainers = document.querySelectorAll<HTMLDivElement>('.ids-form-control--input-text:not([data-ids-custom-init])');

formControlInputTextContainers.forEach((formControlInputTextContainer: HTMLDivElement) => {
    const formControlInputTextInstance = new FormControlInputText(formControlInputTextContainer);

    formControlInputTextInstance.init();
});

const inputTextContainers = document.querySelectorAll<HTMLDivElement>('.ids-input-text:not([data-ids-custom-init])');

inputTextContainers.forEach((inputTextContainer: HTMLDivElement) => {
    const inputTextInstance = new InputText(inputTextContainer);

    inputTextInstance.init();
});

const threeStateCheckboxContainers = document.querySelectorAll<HTMLDivElement>('.ids-three-state-checkbox:not([data-ids-custom-init]])');

threeStateCheckboxContainers.forEach((threeStateCheckboxContainer: HTMLDivElement) => {
    const threeStateCheckboxInstance = new ThreeStateCheckbox(threeStateCheckboxContainer);

    threeStateCheckboxInstance.init();
});
