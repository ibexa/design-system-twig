import { BaseDropdown, BaseDropdownItem } from '../../partials';
import { createNodesFromTemplate } from '../../utils/dom';

export enum DropdownMultiInputAction {
    Check = 'check',
    Uncheck = 'uncheck',
}

export class DropdownMultiInput extends BaseDropdown {
    private _sourceInputNode: HTMLSelectElement;
    private _value: string[];

    constructor(container: HTMLDivElement) {
        super(container);

        const _sourceInputNode = this._sourceNode.querySelector<HTMLSelectElement>('select');

        if (!_sourceInputNode) {
            throw new Error('DropdownMultiInput: Required elements are missing in the container.');
        }

        this._sourceInputNode = _sourceInputNode;
        this._value = this.getSelectedValuesFromSource();

        this.onItemClick = this.onItemClick.bind(this);
    }

    protected getSelectedValuesFromSource(): string[] {
        const selectedValues = Array.from(this._sourceInputNode.selectedOptions).map((option) => option.value);

        return selectedValues;
    }

    protected isSelected(id: string): boolean {
        return this._value.includes(id);
    }

    protected setSource() {
        this._sourceInputNode.innerHTML = '';

        this._itemsMap.forEach((item) => {
            const option = document.createElement('option');

            option.value = item.id;
            option.textContent = item.label;

            if (this._value.includes(item.id)) {
                option.selected = true;
            }

            this._sourceInputNode.appendChild(option);
        });

        this.setValues(this.getSelectedValuesFromSource());
    }

    protected setSourceValue(id: string, actionPerformed: DropdownMultiInputAction) {
        const optionNode = this._sourceInputNode.querySelector<HTMLOptionElement>(`option[value="${id}"]`);

        if (!optionNode) {
            return;
        }

        optionNode.selected = actionPerformed === DropdownMultiInputAction.Check;
    }

    protected setSelectedItem(id: string, actionPerformed: DropdownMultiInputAction) {
        const listItemNode = this._itemsContainerNode.querySelector<HTMLLIElement>(`.ids-dropdown__item[data-id="${id}"]`);
        const checkboxNode = listItemNode?.querySelector<HTMLInputElement>('.ids-input--checkbox');

        if (!checkboxNode) {
            return;
        }

        checkboxNode.checked = actionPerformed === DropdownMultiInputAction.Check;
    }

    protected setSelectionInfo(values: string[]) {
        const items = values.map((value) => this.getItemById(value)).filter((item): item is BaseDropdownItem => item !== undefined);

        if (items.length) {
            // TODO: implement OverflowList when merged
            this._selectionInfoItemsNode.textContent = items.map(({ label }) => label).join(', ');
            this._selectionInfoItemsNode.removeAttribute('hidden');
            this._placeholderNode.setAttribute('hidden', '');
        } else {
            this._selectionInfoItemsNode.textContent = '';
            this._selectionInfoItemsNode.setAttribute('hidden', '');
            this._placeholderNode.removeAttribute('hidden');
        }
    }

    public getItemContent(item: BaseDropdownItem, listItem: HTMLLIElement): NodeListOf<ChildNode> | string {
        const placeholders = {
            '{{ id }}': item.id,
            '{{ label }}': item.label,
        };

        const itemContent = createNodesFromTemplate(listItem.innerHTML, placeholders);

        return itemContent instanceof NodeList ? itemContent : item.label;
    }

    public setItems(items: BaseDropdownItem[]) {
        super.setItems(items);

        const tempValue = this._value;

        this._value = [];

        this.setValues(tempValue);
    }

    public setValues(values: string[]) {
        values.forEach((value) => {
            this.setValue(value);
        });
    }

    public setValue(value: string) {
        const isSelected = this.isSelected(value);
        const nextValue = isSelected ? this._value.filter((iteratedValue) => iteratedValue !== value) : [...this._value, value];
        const actionPerformed = isSelected ? DropdownMultiInputAction.Uncheck : DropdownMultiInputAction.Check;

        this.setSourceValue(value, actionPerformed);
        this.setSelectedItem(value, actionPerformed);
        this.setSelectionInfo(nextValue);

        this._value = nextValue;
    }

    public onItemClick = (event: MouseEvent) => {
        if (event.currentTarget instanceof HTMLLIElement) {
            const { id } = event.currentTarget.dataset;

            if (!id) {
                return;
            }

            this.setValue(id);
        }
    };
}
