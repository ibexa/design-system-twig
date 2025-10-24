import { CheckboxInput, CheckboxesListField } from './components/checkbox';
import { InputTextField, InputTextInput } from './components/input_text';
import { ToggleButtonField, ToggleButtonInput } from './components/toggle_button';
import { Accordion } from './components/accordion';
import { AltRadioInput } from './components/alt_radio/alt_radio_input';
import { DropdownSingleInput } from './components/dropdown/dropdown_single_input';
import { OverflowList } from './components/overflow_list';

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

const dropdownContainers = document.querySelectorAll<HTMLDivElement>('.ids-dropdown:not([data-ids-custom-init])');

dropdownContainers.forEach((dropdownContainer: HTMLDivElement) => {
    const dropdownInstance = new DropdownSingleInput(dropdownContainer);

    dropdownInstance.init();
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

const overflowListContainers = document.querySelectorAll<HTMLDivElement>('.ids-overflow-list:not([data-ids-custom-init])');

overflowListContainers.forEach((overflowListContainer: HTMLDivElement) => {
    const overflowListInstance = new OverflowList(overflowListContainer);

    overflowListInstance.init();
});

const toggleButtonFieldContainers = document.querySelectorAll<HTMLDivElement>('.ids-toggle-field:not([data-ids-custom-init])');

toggleButtonFieldContainers.forEach((toggleButtonFieldContainer: HTMLDivElement) => {
    const toggleButtonFieldInstance = new ToggleButtonField(toggleButtonFieldContainer);

    toggleButtonFieldInstance.init();
});

const toggleButtonContainers = document.querySelectorAll<HTMLDivElement>('.ids-toggle:not([data-ids-custom-init])');

toggleButtonContainers.forEach((toggleButtonContainer: HTMLDivElement) => {
    const toggleButtonInstance = new ToggleButtonInput(toggleButtonContainer);

    toggleButtonInstance.init();
});
