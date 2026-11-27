import { AltRadioInput, AltRadiosListField } from './components/alt_radio';
import { CheckboxInput, CheckboxesListField } from './components/checkbox';
import { DropdownMultiInput, DropdownSingleInput, FilterDropdown } from './components/dropdown';
import { InputTextField, InputTextInput } from './components/input_text';
import { RadioButtonInput, RadioButtonsListField } from './components/radio_button';
import { ToggleButtonField, ToggleButtonInput } from './components/toggle_button';
import { Accordion } from './components/accordion';
import { Alert } from './components/alert';
import { OverflowList } from './components/overflow_list';
import { Tabs } from './components/tabs';
import { hasInstance } from './helpers/object.instances';

interface InitComponentsOptions {
    isTwigMarkerRequired?: boolean;
}

interface InitMatchingOptions extends InitComponentsOptions {
    instanceNodeSelector?: string;
    shouldSkipToggleSource?: boolean;
}

const COMPONENTS_ADDED_EVENT = 'ids:components:added';

const initMatching = <ContainerType extends HTMLElement>(
    root: ParentNode,
    selector: string,
    createComponent: (container: ContainerType) => { init: () => void },
    { instanceNodeSelector, isTwigMarkerRequired = false, shouldSkipToggleSource = false }: InitMatchingOptions,
): void => {
    const checkIsInitialized = (container: HTMLElement): boolean => {
        const instanceNodes = instanceNodeSelector
            ? Array.from(container.querySelectorAll<HTMLElement>(instanceNodeSelector))
            : [container];

        return instanceNodes.some((instanceNode) => hasInstance(instanceNode));
    };

    const fullSelector = `${selector}:not([data-ids-custom-init])`;
    const checkIsMatchingRoot = (node: ParentNode): node is ContainerType => node instanceof HTMLElement && node.matches(fullSelector);
    const matchingRoots = checkIsMatchingRoot(root) ? [root] : [];

    [...matchingRoots, ...Array.from(root.querySelectorAll<ContainerType>(fullSelector))]
        .filter((container) => !checkIsInitialized(container))
        .filter((container) => !isTwigMarkerRequired || container.hasAttribute('data-ids-twig'))
        .filter((container) => !shouldSkipToggleSource || !container.closest('.ids-toggle__source'))
        .forEach((container) => {
            createComponent(container).init();
        });
};

const initComponents = (root: ParentNode, options: InitComponentsOptions = {}): void => {
    const choiceInputOptions = { ...options, shouldSkipToggleSource: true };

    initMatching<HTMLDivElement>(root, '.ids-accordion', (container) => new Accordion(container), options);
    initMatching<HTMLDivElement>(root, '.ids-alert', (container) => new Alert(container), options);
    initMatching<HTMLDivElement>(root, '.ids-alt-radio', (container) => new AltRadioInput(container), options);
    initMatching<HTMLDivElement>(root, '.ids-alt-radio-list-field', (container) => new AltRadiosListField(container), options);
    initMatching<HTMLInputElement>(root, '.ids-input--checkbox', (container) => new CheckboxInput(container), choiceInputOptions);
    initMatching<HTMLDivElement>(root, '.ids-checkboxes-list-field', (container) => new CheckboxesListField(container), options);
    initMatching<HTMLDivElement>(root, '.ids-dropdown--multi', (container) => new DropdownMultiInput(container), options);
    initMatching<HTMLDivElement>(root, '.ids-dropdown--filter', (container) => new FilterDropdown(container), options);
    initMatching<HTMLDivElement>(root, '.ids-dropdown--single', (container) => new DropdownSingleInput(container), options);
    initMatching<HTMLDivElement>(root, '.ids-field--input-text', (container) => new InputTextField(container), options);
    initMatching<HTMLDivElement>(root, '.ids-input-text', (container) => new InputTextInput(container), options);
    initMatching<HTMLInputElement>(root, '.ids-input--radio', (container) => new RadioButtonInput(container), choiceInputOptions);
    initMatching<HTMLDivElement>(root, '.ids-radio-buttons-list-field', (container) => new RadioButtonsListField(container), options);
    initMatching<HTMLDivElement>(root, '.ids-overflow-list', (container) => new OverflowList(container), options);
    initMatching<HTMLDivElement>(root, '.ids-tabs', (container) => new Tabs(container), options);
    initMatching<HTMLDivElement>(root, '.ids-toggle-field', (container) => new ToggleButtonField(container), options);
    initMatching<HTMLDivElement>(root, '.ids-toggle', (container) => new ToggleButtonInput(container), {
        ...options,
        instanceNodeSelector: '.ids-toggle__source input',
    });
};

const getDetailContainer = (event: Event): HTMLElement | null => {
    if (!(event instanceof CustomEvent)) {
        return null;
    }

    const { detail }: { detail: unknown } = event;

    if (typeof detail !== 'object' || detail === null || !('container' in detail)) {
        return null;
    }

    return detail.container instanceof HTMLElement ? detail.container : null;
};

const getAddedComponentsRoot = (event: Event): ParentNode => {
    const detailContainer = getDetailContainer(event);

    if (detailContainer) {
        return detailContainer;
    }

    if (event.target instanceof HTMLElement) {
        return event.target;
    }

    return document;
};

initComponents(document);

document.addEventListener(COMPONENTS_ADDED_EVENT, (event) => {
    initComponents(getAddedComponentsRoot(event), { isTwigMarkerRequired: true });
});
