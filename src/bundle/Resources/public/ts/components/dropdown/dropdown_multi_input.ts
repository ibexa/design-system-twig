import { BaseDropdown, BaseDropdownItem } from '../../partials';
import { OverflowList } from '../overflow_list';
import { createNodesFromTemplate } from '../../utils/dom';
import { getInstance, hasInstance } from '../../helpers/object.instances';
import { HTMLElementIDSInstance } from '../../shared/types';

export enum DropdownMultiInputAction {
    Check = 'check',
    Uncheck = 'uncheck',
}

export class DropdownMultiInput extends BaseDropdown {
    private _sourceInputNode: HTMLSelectElement;
    private _overflowListInstance: OverflowList | null = null;
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

    protected dispatchChangeEvent() {
        this._sourceInputNode.dispatchEvent(new Event('change', { bubbles: true }));
    }

    protected getOverflowListInstance(): OverflowList | null {
        if (this._overflowListInstance) {
            return this._overflowListInstance;
        }

        const overflowListNode = this._selectionInfoItemsNode.querySelector<HTMLElementIDSInstance<OverflowList>>('.ids-overflow-list');

        if (!overflowListNode) {
            return null;
        }

        if (hasInstance(overflowListNode)) {
            this._overflowListInstance = getInstance<OverflowList>(overflowListNode);
        } else {
            this._overflowListInstance = new OverflowList(overflowListNode as HTMLDivElement);
            this._overflowListInstance.init();
        }

        return this._overflowListInstance;
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
        const selectedValues = new Set(values);
        const items = Array.from(this._itemsMap.values()).filter((item) => selectedValues.has(item.id));
        const overflowItems = items.map(({ id, label }) => ({ id, label }));
        const overflowListInstance = this.getOverflowListInstance();

        if (items.length) {
            this._selectionInfoItemsNode.removeAttribute('hidden');
            this._placeholderNode.setAttribute('hidden', '');
            overflowListInstance?.setItems(overflowItems);
        } else {
            overflowListInstance?.setItems([]);
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

    public getSelectedItems(): HTMLOptionElement[] {
        return Array.from(this._sourceInputNode.selectedOptions);
    }

    public clearCurrentSelection() {
        const values = [...this._value];

        values.forEach((value) => {
            if (this.isSelected(value)) {
                this.setValue(value);
            }
        });

        this.dispatchChangeEvent();
    }

    public onItemClick = (event: MouseEvent) => {
        if (event.currentTarget instanceof HTMLLIElement) {
            const { id } = event.currentTarget.dataset;

            if (!id) {
                return;
            }

            this.setValue(id);
            this.dispatchChangeEvent();
        }
    };

    protected initSelectedItemsDeletion() {
        this._selectionInfoItemsNode.addEventListener('click', (event: MouseEvent) => {
            const deleteBtn = event.target instanceof Element ? event.target.closest<HTMLButtonElement>('.ids-chip__delete') : null;

            if (!deleteBtn) {
                return;
            }

            const chipNode = deleteBtn.closest<HTMLElement>('.ids-chip[data-id]');
            const id = chipNode?.dataset.id;

            event.preventDefault();
            event.stopPropagation();

            if (!id || !this.isSelected(id)) {
                return;
            }

            this.setValue(id);
            this.dispatchChangeEvent();
        });
    }

    public init() {
        this.initSelectedItemsDeletion();

        super.init();
    }
}
