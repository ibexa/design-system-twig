import { ChangeEvent } from 'react';
import { BaseInputsList } from '../../partials';

export enum CheckboxesListFieldAction {
    Check = 'check',
    Uncheck = 'uncheck',
}

export class CheckboxesListField extends BaseInputsList<string[]> {
    private _itemsContainer: HTMLDivElement;

    constructor(container: HTMLDivElement) {
        super(container);

        const itemsContainer = container.querySelector<HTMLDivElement>('.ids-choice-inputs-list__items');

        if (!itemsContainer) {
            throw new Error('CheckboxesListField: Required elements are missing in the container.');
        }

        this._itemsContainer = itemsContainer;

        this.onItemChange = this.onItemChange.bind(this);
        // this.onChange = this.onChange.bind(this);
    }

    getItemsCheckboxes() {
        const itemsCheckboxes = [...this._itemsContainer.querySelectorAll<HTMLInputElement>('.ids-choice-input-field .ids-input--checkbox')];

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
        const changeEvent = new CustomEvent('change', {
            bubbles: true,
            detail: [
                nextValue,
                itemValue,
                actionPerformed,
            ],
        });

        this._container.dispatchEvent(changeEvent);
    }


    initCheckboxes() {
        const itemsCheckboxes = this.getItemsCheckboxes();

        itemsCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', this.onItemChange, false);
        });
    }

    init() {
        super.init();

        this.initCheckboxes();
    }
}
