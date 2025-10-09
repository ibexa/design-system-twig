import { BaseInputsList } from '../../partials';

export enum CheckboxesListFieldAction {
    Check = 'check',
    Uncheck = 'uncheck',
}

export class CheckboxesListField extends BaseInputsList<string[]> {
    private _itemsContainer: HTMLDivElement;

    static EVENTS = {
        ...BaseInputsList.EVENTS,
        CHANGE: 'ids:checkboxes-list-field:change',
    };

    constructor(container: HTMLDivElement) {
        super(container);

        const itemsContainer = container.querySelector<HTMLDivElement>('.ids-choice-inputs-list__items');

        if (!itemsContainer) {
            throw new Error('CheckboxesListField: Required elements are missing in the container.');
        }

        this._itemsContainer = itemsContainer;

        this.onItemChange = this.onItemChange.bind(this);
    }

    getItemsCheckboxes() {
        const itemsCheckboxes = [
            ...this._itemsContainer.querySelectorAll<HTMLInputElement>('.ids-choice-input-field .ids-input--checkbox'),
        ];

        return itemsCheckboxes;
    }

    getValue(): string[] {
        const itemsCheckboxes = this.getItemsCheckboxes();
        const checkedValues = itemsCheckboxes.reduce((acc: string[], checkbox) => {
            if (checkbox.checked) {
                acc.push(checkbox.value);
            }

            return acc;
        }, []);

        return checkedValues;
    }

    onItemChange(event: Event) {
        if (!(event.target instanceof HTMLInputElement)) {
            return;
        }

        const item = event.target;
        const nextValue = this.getValue();
        const actionPerformed = item.checked ? CheckboxesListFieldAction.Check : CheckboxesListFieldAction.Uncheck;

        this.onChange(nextValue, item.value, actionPerformed);
    }

    onChange(nextValue: string[], itemValue: string, actionPerformed: CheckboxesListFieldAction) {
        const changeEvent = new CustomEvent(CheckboxesListField.EVENTS.CHANGE, {
            bubbles: true,
            detail: [nextValue, itemValue, actionPerformed],
        });

        console.log(changeEvent.detail);
        this._container.dispatchEvent(changeEvent);
    }

    initCheckboxes() {
        const itemsCheckboxes = this.getItemsCheckboxes();

        itemsCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', this.onItemChange, false);
        });
    }

    unbindCheckboxes() {
        const itemsCheckboxes = this.getItemsCheckboxes();

        itemsCheckboxes.forEach((checkbox) => {
            checkbox.removeEventListener('change', this.onItemChange, false);
        });
    }

    reinit() {
        super.reinit();

        this.unbindCheckboxes();
        this.initCheckboxes();
    }

    init() {
        super.init();

        this.initCheckboxes();
    }
}
