import { CheckboxInput, CheckboxesListField } from './components/checkbox';
import { InputTextField, InputTextInput } from './components/input_text';
import { Accordion } from './components/accordion';
import { AltRadioInput } from './components/alt_radio/alt_radio_input';

const accordionContainers = document.querySelectorAll<HTMLDivElement>('.ids-accordion:not([data-ids-custom-init])');

accordionContainers.forEach((accordionContainer: HTMLDivElement) => {
    const accordionInstance = new Accordion(accordionContainer);

    accordionInstance.init();
});

const altRadioContainers = document.querySelectorAll<HTMLDivElement>('.ids-alt-radio:not([data-ids-custom-init])');

altRadioContainers.forEach((altRadioContainer: HTMLDivElement) => {
    const altRadioInstance = new AltRadioInput(altRadioContainer);

    altRadioInstance.init();
});

const checkboxContainers = document.querySelectorAll<HTMLDivElement>('.ids-checkbox:not([data-ids-custom-init])');

checkboxContainers.forEach((checkboxContainer: HTMLDivElement) => {
    const checkboxInstance = new CheckboxInput(checkboxContainer);

    checkboxInstance.init();
});

const checkboxesFieldContainers = document.querySelectorAll<HTMLDivElement>('.ids-field.ids-field--list:not([data-ids-custom-init])');

checkboxesFieldContainers.forEach((checkboxesFieldContainer: HTMLDivElement) => {
    const checkboxesFieldInstance = new CheckboxesListField(checkboxesFieldContainer);

    checkboxesFieldInstance.init();
});

const fieldInputTextContainers = document.querySelectorAll<HTMLDivElement>('.ids-field--input-text:not([data-ids-custom-init])');

fieldInputTextContainers.forEach((fieldInputTextContainer: HTMLDivElement) => {
    const fieldInputTextInstance = new InputTextField(fieldInputTextContainer);

    fieldInputTextInstance.init();
});

const inputTextContainers = document.querySelectorAll<HTMLDivElement>('.ids-input-text:not([data-ids-custom-init])');

inputTextContainers.forEach((inputTextContainer: HTMLDivElement) => {
    const inputTextInstance = new InputTextInput(inputTextContainer);

    inputTextInstance.init();
});
